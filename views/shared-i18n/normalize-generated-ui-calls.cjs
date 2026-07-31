const fs = require("fs");
const path = require("path");

const root = path.resolve(__dirname, "../view-uni-src");
const skipped = new Set(["node_modules", "dist", "unpackage", "static"]);
let changed = 0;

function visit(directory) {
  for (const entry of fs.readdirSync(directory, { withFileTypes: true })) {
    if (entry.isDirectory() && skipped.has(entry.name)) continue;
    const file = path.join(directory, entry.name);
    if (entry.isDirectory()) {
      visit(file);
      continue;
    }
    if (!entry.name.endsWith(".vue")) continue;
    const source = fs.readFileSync(file, "utf8");
    const next = source.replace(/\$t\("(ui\.[A-Za-z0-9_.]+)"\)/g, "$t('$1')");
    if (next !== source) {
      fs.writeFileSync(file, next);
      changed += 1;
    }
  }
}

visit(root);
console.log(`normalized ${changed} mobile Vue files`);
