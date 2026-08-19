const fs = require("node:fs");
const path = require("node:path");

const hasHan = /[\u3400-\u9fff]/;

function walkSql(root) {
  const files = [];
  function visit(directory) {
    if (!fs.existsSync(directory)) return;
    for (const entry of fs.readdirSync(directory, { withFileTypes: true })) {
      const absolute = path.join(directory, entry.name);
      if (entry.isDirectory()) visit(absolute);
      else if (entry.isFile() && entry.name.endsWith(".sql")) files.push(absolute);
    }
  }
  visit(path.join(root, "database"));
  visit(path.join(root, "public", "install"));
  return files.sort();
}

function decodeSqlString(raw) {
  return raw
    .replace(/^'/, "")
    .replace(/'$/, "")
    .replace(/''/g, "'")
    .replace(/\\'/g, "'")
    .replace(/\\n/g, "\n")
    .replace(/\\r/g, "\r")
    .replace(/\\t/g, "\t")
    .replace(/\\\\/g, "\\");
}

function quotedStrings(source) {
  const values = [];
  let index = 0;
  while (index < source.length) {
    if (source[index] !== "'") {
      index += 1;
      continue;
    }
    const start = index;
    index += 1;
    while (index < source.length) {
      if (source[index] === "\\") {
        index += 2;
        continue;
      }
      if (source[index] === "'" && source[index + 1] === "'") {
        index += 2;
        continue;
      }
      if (source[index] === "'") {
        index += 1;
        break;
      }
      index += 1;
    }
    const raw = source.slice(start, index);
    values.push({ raw, value: decodeSqlString(raw), start, end: index });
  }
  return values;
}

function splitStatements(source) {
  const statements = [];
  let start = 0;
  let quote = false;
  let lineComment = false;
  let blockComment = false;
  for (let index = 0; index < source.length; index += 1) {
    const char = source[index];
    const next = source[index + 1];
    if (lineComment) {
      if (char === "\n") lineComment = false;
      continue;
    }
    if (blockComment) {
      if (char === "*" && next === "/") {
        blockComment = false;
        index += 1;
      }
      continue;
    }
    if (quote) {
      if (char === "\\") {
        index += 1;
      } else if (char === "'" && next === "'") {
        index += 1;
      } else if (char === "'") {
        quote = false;
      }
      continue;
    }
    if (char === "'") quote = true;
    else if ((char === "-" && next === "-") || char === "#") lineComment = true;
    else if (char === "/" && next === "*") {
      blockComment = true;
      index += 1;
    } else if (char === ";") {
      statements.push(source.slice(start, index + 1));
      start = index + 1;
    }
  }
  if (source.slice(start).trim()) statements.push(source.slice(start));
  return statements;
}

function splitTopLevel(source) {
  const parts = [];
  let start = 0;
  let depth = 0;
  let quote = false;
  for (let index = 0; index < source.length; index += 1) {
    const char = source[index];
    const next = source[index + 1];
    if (quote) {
      if (char === "\\") index += 1;
      else if (char === "'" && next === "'") index += 1;
      else if (char === "'") quote = false;
      continue;
    }
    if (char === "'") quote = true;
    else if (char === "(") depth += 1;
    else if (char === ")") depth -= 1;
    else if (char === "," && depth === 0) {
      parts.push(source.slice(start, index).trim());
      start = index + 1;
    }
  }
  parts.push(source.slice(start).trim());
  return parts;
}

function tupleBodies(valuesSource) {
  const tuples = [];
  let quote = false;
  let depth = 0;
  let start = -1;
  for (let index = 0; index < valuesSource.length; index += 1) {
    const char = valuesSource[index];
    const next = valuesSource[index + 1];
    if (quote) {
      if (char === "\\") index += 1;
      else if (char === "'" && next === "'") index += 1;
      else if (char === "'") quote = false;
      continue;
    }
    if (char === "'") quote = true;
    else if (char === "(") {
      if (depth === 0) start = index + 1;
      depth += 1;
    } else if (char === ")") {
      depth -= 1;
      if (depth === 0 && start >= 0) tuples.push(valuesSource.slice(start, index));
    }
  }
  return tuples;
}

function scalarString(expression) {
  const trimmed = expression.trim();
  const strings = quotedStrings(trimmed);
  if (strings.length === 1 && strings[0].start === 0 && strings[0].end === trimmed.length) return strings[0].value;
  return null;
}

function jsonDisplayLeaves(value, displayKeys) {
  const trimmed = String(value || "").trim();
  if (!trimmed.startsWith("{") && !trimmed.startsWith("[")) return [];
  let parsed;
  try {
    parsed = JSON.parse(trimmed);
  } catch {
    return [];
  }
  const leaves = [];
  function visit(node, currentPath, parentKey) {
    if (typeof node === "string") {
      if (hasHan.test(node) && displayKeys.has(String(parentKey || "").toLowerCase())) {
        leaves.push({ value: node, jsonPath: currentPath.join(".") });
      }
      return;
    }
    if (Array.isArray(node)) node.forEach((item, index) => visit(item, [...currentPath, index], parentKey));
    else if (node && typeof node === "object") {
      for (const [key, item] of Object.entries(node)) visit(item, [...currentPath, key], key);
    }
  }
  visit(parsed, [], "");
  return leaves;
}

function classify(policy, relative, table, column, value) {
  if ((policy.testDataValues || []).includes(value)) return "TEST_DATA";
  if (policy.testDataTables.includes(table)) return "TEST_DATA";
  if (policy.identifierColumns.includes(column)) return "IDENTIFIER";
  if (!policy.knownInternalTables.includes(table)) return "UNKNOWN";
  if ((policy.userVisibleColumns[table] || []).includes(column)) return "USER_VISIBLE";
  return "INTERNAL_VALUE";
}

function addOccurrence(collection, occurrence) {
  if (!hasHan.test(occurrence.value || "")) return;
  collection.push(occurrence);
}

function parseInsert(statement, relative, policy, occurrences) {
  const match = statement.match(/INSERT\s+INTO\s+`?([a-zA-Z0-9_]+)`?\s*\(([\s\S]*?)\)\s*VALUES\s*([\s\S]*);?\s*$/i);
  if (!match) return false;
  const table = match[1];
  const columns = [...match[2].matchAll(/`([^`]+)`|\b([a-zA-Z_][a-zA-Z0-9_]*)\b/g)].map((item) => item[1] || item[2]);
  const displayKeys = new Set(policy.jsonDisplayKeys.map((key) => key.toLowerCase()));
  for (const tuple of tupleBodies(match[3])) {
    const expressions = splitTopLevel(tuple);
    const row = {};
    columns.forEach((column, index) => {
      row[column] = scalarString(expressions[index] || "");
    });
    columns.forEach((column, index) => {
      const value = row[column];
      if (value === null || !hasHan.test(value)) return;
      const classification = classify(policy, relative, table, column, value);
      const leaves = classification === "USER_VISIBLE" ? jsonDisplayLeaves(value, displayKeys) : [];
      if (leaves.length) {
        leaves.forEach((leaf) => addOccurrence(occurrences, { file: relative, table, column, ...leaf, classification, area: false }));
        return;
      }
      const areaPolicy = policy.areaDictionary;
      const area = table === areaPolicy.table && column === areaPolicy.labelColumn && row[areaPolicy.typeColumn] === areaPolicy.typeValue;
      addOccurrence(occurrences, {
        file: relative,
        table,
        column,
        value,
        classification,
        area,
        areaCode: area ? row[areaPolicy.valueColumn] : null,
        parentCode: area ? row[areaPolicy.parentColumn] : null,
      });
    });
  }
  return true;
}

function parseInsertSelect(statement, relative, policy, occurrences) {
  const match = statement.match(/INSERT\s+INTO\s+`?([a-zA-Z0-9_]+)`?\s*\(([\s\S]*?)\)\s*SELECT\s+([\s\S]*?)(?:\s+WHERE\s+[\s\S]*)?;?\s*$/i);
  if (!match) return false;
  const table = match[1];
  const columns = [...match[2].matchAll(/`([^`]+)`|\b([a-zA-Z_][a-zA-Z0-9_]*)\b/g)].map((item) => item[1] || item[2]);
  const expressions = splitTopLevel(match[3]);
  const row = {};
  columns.forEach((column, index) => {
    row[column] = scalarString(expressions[index] || "");
  });
  columns.forEach((column) => {
    const value = row[column];
    if (value === null || !hasHan.test(value)) return;
    addOccurrence(occurrences, {
      file: relative,
      table,
      column,
      value,
      classification: classify(policy, relative, table, column, value),
      area: false,
    });
  });
  return true;
}

function parseUpdate(statement, relative, policy, occurrences) {
  const match = statement.match(/UPDATE\s+`?([a-zA-Z0-9_]+)`?[\s\S]*?\s+SET\s+([\s\S]*?)(?:\s+WHERE\s+[\s\S]*)?;?\s*$/i);
  if (!match) return false;
  const table = match[1];
  const directValues = new Set();
  for (const assignment of splitTopLevel(match[2])) {
    const field = assignment.match(/^`?([a-zA-Z0-9_]+)`?\s*=\s*([\s\S]*)$/);
    if (!field) continue;
    const column = field[1];
    const value = scalarString(field[2].replace(/;\s*$/, ""));
    if (value !== null) {
      directValues.add(value);
      addOccurrence(occurrences, { file: relative, table, column, value, classification: classify(policy, relative, table, column, value), area: false });
    }
  }
  for (const item of quotedStrings(statement)) {
    if (!directValues.has(item.value)) {
      addOccurrence(occurrences, {
        file: relative,
        table,
        column: "",
        value: item.value,
        classification: "INTERNAL_VALUE",
        area: false,
      });
    }
  }
  return true;
}

function commentOccurrences(source, relative) {
  const occurrences = [];
  const patterns = [/--[^\r\n]*/g, /#[^\r\n]*/g, /\/\*[\s\S]*?\*\//g];
  for (const pattern of patterns) {
    for (const match of source.matchAll(pattern)) {
      if (hasHan.test(match[0])) occurrences.push({ file: relative, table: "", column: "", value: match[0].trim(), classification: "COMMENT", area: false });
    }
  }
  return occurrences;
}

function auditSqlSource({ source, relative, policy }) {
  const occurrences = commentOccurrences(source, relative);
  for (const statement of splitStatements(source)) {
    if (parseInsert(statement, relative, policy, occurrences) || parseInsertSelect(statement, relative, policy, occurrences) || parseUpdate(statement, relative, policy, occurrences)) continue;
    const normalized = statement.replace(/^(?:\s|--[^\r\n]*|#[^\r\n]*|\/\*[\s\S]*?\*\/)+/, "").trim();
    const metadata = /^(?:CREATE|ALTER)\b/i.test(normalized) || policy.schemaMetadataFiles.includes(relative);
    const internal = /^(?:SET|SELECT|DELETE|DROP|TRUNCATE|CALL)\b/i.test(normalized);
    for (const item of quotedStrings(normalized)) {
      if (!hasHan.test(item.value)) continue;
      addOccurrence(occurrences, {
        file: relative,
        table: "",
        column: "",
        value: item.value,
        classification: metadata ? "DATABASE_METADATA" : internal ? "INTERNAL_VALUE" : "UNKNOWN",
        area: false,
      });
    }
  }
  return occurrences;
}

function summarizeRows(rows, keyFor, runtimeValues) {
  const summaries = new Map();
  const getEnglish = (value) => runtimeValues instanceof Map ? runtimeValues.get(value) : runtimeValues[value];
  for (const row of rows) {
    const key = keyFor(row) || "(statement)";
    const summary = summaries.get(key) || { total: 0, userVisible: 0, mapped: 0, unmapped: 0, classifications: {} };
    summary.total += 1;
    summary.classifications[row.classification] = (summary.classifications[row.classification] || 0) + 1;
    if (row.classification === "USER_VISIBLE") {
      summary.userVisible += 1;
      if (getEnglish(row.value) && !hasHan.test(getEnglish(row.value))) summary.mapped += 1;
      else summary.unmapped += 1;
    }
    summaries.set(key, summary);
  }
  return Object.fromEntries([...summaries].sort(([left], [right]) => left.localeCompare(right)));
}

function auditSql({ repoRoot, runtimeValues = new Map(), throwOnError = true } = {}) {
  const sharedRoot = path.join(repoRoot, "views", "shared-i18n");
  const policy = JSON.parse(fs.readFileSync(path.join(sharedRoot, "sql-audit-policy.json"), "utf8"));
  const sqlFiles = walkSql(repoRoot);
  const occurrences = [];
  for (const file of sqlFiles) {
    const relative = path.relative(repoRoot, file).replace(/\\/g, "/");
    occurrences.push(...auditSqlSource({ source: fs.readFileSync(file, "utf8"), relative, policy }));
  }

  const unique = new Map();
  for (const occurrence of occurrences) {
    const key = [occurrence.file, occurrence.table, occurrence.column, occurrence.classification, occurrence.value, occurrence.jsonPath || ""].join("\u0000");
    if (!unique.has(key)) unique.set(key, occurrence);
  }
  const rows = [...unique.values()];
  const userVisible = rows.filter((row) => row.classification === "USER_VISIBLE");
  const unknown = rows.filter((row) => row.classification === "UNKNOWN");
  const missing = userVisible.filter((row) => {
    const english = runtimeValues instanceof Map ? runtimeValues.get(row.value) : runtimeValues[row.value];
    return !english || hasHan.test(english);
  });
  const classificationCounts = Object.fromEntries(policy.classifications.map((name) => [name, rows.filter((row) => row.classification === name).length]));
  const frontendCoverage = (policy.frontendBoundaries || []).map((boundary) => {
    const file = path.join(repoRoot, boundary.file);
    const source = fs.existsSync(file) ? fs.readFileSync(file, "utf8") : "";
    const missingTokens = (boundary.mustContain || []).filter((token) => !source.includes(token));
    return { file: boundary.file, covered: fs.existsSync(file) && missingTokens.length === 0, missingTokens };
  });
  const uncoveredFrontend = frontendCoverage.filter((boundary) => !boundary.covered);
  const result = {
    files: sqlFiles.length,
    occurrences: rows.length,
    classifications: classificationCounts,
    userVisible: userVisible.length,
    mapped: userVisible.length - missing.length,
    missing,
    unknown,
    areaValues: userVisible.filter((row) => row.area),
    byFile: summarizeRows(rows, (row) => row.file, runtimeValues),
    byTable: summarizeRows(rows, (row) => row.table, runtimeValues),
    byColumn: summarizeRows(rows, (row) => row.table && row.column ? `${row.table}.${row.column}` : "", runtimeValues),
    frontendCoverage,
    uncoveredFrontend,
    rows,
  };
  if (throwOnError && (unknown.length || missing.length || uncoveredFrontend.length)) {
    const details = [
      ...unknown.slice(0, 40).map((row) => `UNKNOWN ${row.file} ${row.table}.${row.column}: ${row.value}`),
      ...missing.slice(0, 80).map((row) => `UNMAPPED ${row.file} ${row.table}.${row.column}: ${row.value}`),
      ...uncoveredFrontend.map((boundary) => `UNCOVERED FRONTEND ${boundary.file}: ${boundary.missingTokens.join(", ")}`),
    ];
    throw new Error(`SQL localization audit failed (${sqlFiles.length} files, ${unknown.length} unknown, ${missing.length} unmapped, ${uncoveredFrontend.length} uncovered frontend boundaries):\n${details.join("\n")}`);
  }
  return result;
}

function formatGroups(groups) {
  return Object.entries(groups).map(([name, values]) => `${name}=${values.total}/${values.userVisible}/${values.mapped}/${values.unmapped}`).join(", ");
}

function formatSqlAuditSummary(result) {
  const preserved = result.occurrences - result.userVisible;
  const classifications = Object.entries(result.classifications).map(([name, count]) => `${name}=${count}`).join(", ");
  return [
    `SQL localization audit: ${result.files} files, ${result.occurrences} classified values, ${result.userVisible} user-visible, ${result.mapped} mapped, ${preserved} intentionally preserved, ${result.unknown.length} unknown`,
    `SQL classifications: ${classifications}`,
    `SQL files (total/user-visible/mapped/unmapped): ${formatGroups(result.byFile)}`,
    `SQL tables (total/user-visible/mapped/unmapped): ${formatGroups(result.byTable)}`,
    `SQL columns (total/user-visible/mapped/unmapped): ${formatGroups(result.byColumn)}`,
    `SQL frontend coverage: ${result.frontendCoverage.filter((item) => item.covered).length}/${result.frontendCoverage.length}`,
  ].join("\n");
}

module.exports = { auditSql, auditSqlSource, decodeSqlString, formatSqlAuditSummary };
