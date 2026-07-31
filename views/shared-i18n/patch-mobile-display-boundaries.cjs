const fs = require('fs')
const path = require('path')

function update(relative, replacements) {
  const file = path.resolve(__dirname, '..', relative)
  let source = fs.readFileSync(file, 'utf8')
  for (const [from, to] of replacements) {
    if (source.includes(from)) source = source.replaceAll(from, to)
  }
  fs.writeFileSync(file, source)
}

update('view-uni-src/components/tabbar/index.vue', [
  ['{{ item.text }}', '{{ $ts(item.text) }}']
])

update('view-uni-src/components/navigationBar/index.vue', [
  ['{{propsData.name ? item[propsData.name] : item.cate_name}}', '{{ $ts(propsData.name ? item[propsData.name] : item.cate_name, item.cate_name_en) }}']
])

update('view-uni-src/components/navigationBar/components/barMore.vue', [
  [':text="item.cate_name"', ':text="String($ts(item.cate_name, item.cate_name_en))"']
])

update('view-uni-src/components/navigationBar/components/siderbar.vue', [
  ['{{item.title}}', '{{ $ts(item.title, item.title_en) }}'],
  ["{{item.selectAll ? '取消全选' : '全选'}}", "{{ $ts(item.selectAll ? '取消全选' : '全选') }}"]
])

update('view-uni-src/components/oaForm/index.vue', [
  ['{{ val.text }}', '{{ $ts(val.text) }}'],
  ['{{ val.text || val.text1 }}', '{{ $ts(val.text || val.text1) }}']
])

update('view-uni-src/components/moduleForm/index.vue', [
  ['{{ val.text }}', '{{ $ts(val.text) }}'],
  ['{{ val.text || val.text1 }}', '{{ $ts(val.text || val.text1) }}']
])
