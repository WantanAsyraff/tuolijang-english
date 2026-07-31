const fs = require("fs");
const path = require("path");
const compiler = require("../gyro-craftsman-web-own-v2.4/node_modules/vue-template-compiler");

const projectRoot = path.resolve(__dirname, "../gyro-craftsman-web-own-v2.4/src");
const archiveRoot = path.resolve(
  __dirname,
  "../../.tmp-i18n-restore-20260730/gyro-craftsman-web-own-v2.4/src"
);

const files = [
  "components/common/headerSearch.vue",
  "views/customer/components/dragUpload.vue",
  "views/customer/setup/ruleSettings/components/cluePoolConfig.vue",
  "views/customer/weChatMass/addGroupPosting.vue",
  "views/develop/module/components/dragUpload.vue",
  "views/hr/enterprise/job/positionSystemChart.vue",
  "views/user/assessment/components/goalSetting.vue",
];

function getArchiveTemplateBlock(source, file) {
  const descriptor = compiler.parseComponent(source, { pad: "space" });
  if (!descriptor.template) {
    throw new Error(`${file}: archive has no root template`);
  }

  const openStart = source.lastIndexOf("<template", descriptor.template.start);
  const closeStart = source.indexOf("</template>", descriptor.template.end);
  if (openStart < 0 || closeStart < 0) {
    throw new Error(`${file}: cannot resolve archive template boundaries`);
  }
  return source.slice(openStart, closeStart + "</template>".length);
}

function replaceCurrentTemplate(source, templateBlock, file) {
  const templateStart = source.indexOf("<template");
  if (templateStart < 0) {
    throw new Error(`${file}: current file has no template start`);
  }

  const nextScript = source.indexOf("<script", templateStart + 9);
  const nextStyle = source.indexOf("<style", templateStart + 9);
  const candidates = [nextScript, nextStyle].filter((value) => value >= 0);
  const templateEnd = candidates.length ? Math.min(...candidates) : source.length;

  const before = source.slice(0, templateStart);
  const after = source.slice(templateEnd).replace(/^\s*/, "");
  return `${before}${templateBlock}\n\n${after}`;
}

for (const file of files) {
  const currentPath = path.join(projectRoot, file);
  const archivePath = path.join(archiveRoot, file);
  const currentSource = fs.readFileSync(currentPath, "utf8");
  const archiveSource = fs.readFileSync(archivePath, "utf8");
  const templateBlock = getArchiveTemplateBlock(archiveSource, file);
  const repairedSource = replaceCurrentTemplate(currentSource, templateBlock, file);
  fs.writeFileSync(currentPath, repairedSource);
  process.stdout.write(`repaired ${file}\n`);
}
