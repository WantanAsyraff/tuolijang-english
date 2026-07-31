const fs = require("fs");
const path = require("path");

const projectRoot = path.resolve(__dirname, "../view-uni-src");
const archiveRoot = path.resolve(__dirname, "../../.tmp-i18n-mobile-restore-20260730");
const skipDirectories = new Set(["node_modules", "dist", "unpackage", "static", "__MACOSX"]);

function vueFiles(directory) {
  return fs.readdirSync(directory, { withFileTypes: true }).flatMap((entry) => {
    if (entry.isDirectory() && skipDirectories.has(entry.name)) return [];
    const fullPath = path.join(directory, entry.name);
    if (entry.isDirectory()) return vueFiles(fullPath);
    return entry.name.endsWith(".vue") ? [fullPath] : [];
  });
}

function templateBlock(source, file) {
  const templateStart = source.indexOf("<template");
  if (templateStart < 0) throw new Error(`${file}: missing root template`);
  const nextScript = source.indexOf("<script", templateStart + 9);
  const nextStyle = source.indexOf("<style", templateStart + 9);
  const candidates = [nextScript, nextStyle].filter((value) => value >= 0);
  const boundary = candidates.length ? Math.min(...candidates) : source.length;
  const closeStart = source.lastIndexOf("</template>", boundary);
  if (closeStart < templateStart) throw new Error(`${file}: missing root template close`);
  return {
    start: templateStart,
    end: boundary,
    value: source.slice(templateStart, closeStart + "</template>".length),
  };
}

let repaired = 0;
for (const currentPath of vueFiles(projectRoot)) {
  const source = fs.readFileSync(currentPath, "utf8");
  if (!/\bui\.[A-Za-z0-9_.]+/.test(source)) continue;

  const relative = path.relative(projectRoot, currentPath);
  const archivePath = path.join(archiveRoot, relative);
  if (!fs.existsSync(archivePath)) {
    throw new Error(`${relative}: archive source is missing`);
  }

  const current = templateBlock(source, relative);
  const archived = templateBlock(fs.readFileSync(archivePath, "utf8"), relative);
  const next =
    source.slice(0, current.start) +
    archived.value +
    "\n\n" +
    source.slice(current.end).replace(/^\s*/, "");
  fs.writeFileSync(currentPath, next);
  repaired += 1;
}

console.log(`repaired ${repaired} mobile root templates`);
