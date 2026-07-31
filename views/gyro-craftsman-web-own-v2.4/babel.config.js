module.exports = {
  compact: false,
  presets: [['@vue/app', { useBuiltIns: 'entry', corejs: 3 }]],
  env: { development: { plugins: ['dynamic-import-node'] } }
}
