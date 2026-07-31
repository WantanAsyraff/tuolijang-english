const fs = require("fs");
const path = require("path");
const compiler = require("../gyro-craftsman-web-own-v2.4/node_modules/vue-template-compiler");

const currentRoot = path.resolve(__dirname, "../gyro-craftsman-web-own-v2.4/src");
const archiveRoot = path.resolve(process.argv[2] || "");
if (!archiveRoot || !fs.existsSync(archiveRoot)) {
  throw new Error("Pass the extracted archive src directory as the first argument.");
}

const restored = [];

function walk(directory) {
  for (const entry of fs.readdirSync(directory, { withFileTypes: true })) {
    const file = path.join(directory, entry.name);
    if (entry.isDirectory()) {
      walk(file);
      continue;
    }
    if (!entry.name.endsWith(".vue")) continue;
    const currentSource = fs.readFileSync(file, "utf8");
    if (!/\$t\((?:&quot;|["'])ui\./.test(currentSource)) continue;

    const relative = path.relative(currentRoot, file);
    const archiveFile = path.join(archiveRoot, relative);
    if (!fs.existsSync(archiveFile)) throw new Error(`Archive file missing: ${relative}`);
    const archiveSource = fs.readFileSync(archiveFile, "utf8");
    const currentBlock = compiler.parseComponent(currentSource).template;
    const archiveBlock = compiler.parseComponent(archiveSource).template;
    if (!currentBlock || !archiveBlock) throw new Error(`Template block missing: ${relative}`);

    const next =
      currentSource.slice(0, currentBlock.start) +
      archiveBlock.content +
      currentSource.slice(currentBlock.end);
    fs.writeFileSync(file, next, "utf8");
    restored.push(relative.replace(/\\/g, "/"));
  }
}

walk(currentRoot);
fs.writeFileSync(
  path.join(__dirname, "repaired-web-template-files.json"),
  `${JSON.stringify(restored, null, 2)}\n`,
  "utf8"
);
console.log(`Restored ${restored.length} malformed web template blocks from the repository archive.`);
