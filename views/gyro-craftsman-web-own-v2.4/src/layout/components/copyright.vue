<template>
  <div class="ivu-global-footer-copyright">
    <div class="mb14">
      <span v-for="(item, index) in links" :key="index" @click="openFn(item)" class="mr14 pointer">{{
        item.title
      }}</span>
      </div>
    
    &copy;{{ $moment().format('YYYY') }} {{ $("legacy.43483565d4471941") }} {{ title || 'v1.5' }}
  </div>
</template>

<script>
import { $ } from '@/lang'
import { getStorageJson } from '@/utils/storage'
export default {
  name: 'CrmebOaEntCopyright',
  data() {
    return {
      title: '',
      links: [
        {
          title: $('legacyScript.officialWebsite'),
          key: '官网',
          href: 'https://tuoluojiang.com',
          blankTarget: true
        },
        {
          title: $('legacyScript.community'),
          key: '社区',
          href: 'https://www.crmeb.com/ask/thread/list/157',
          blankTarget: true
        },
        {
          title: $('file.document'),
          key: '文档',
          href: 'https://tuoluojiang.com/doc/own/0',
          blankTarget: true
        }
      ]
    }
  },
  mounted() {
    const sitedata = getStorageJson('sitedata')
    this.title = sitedata ? sitedata.version_name : ''
  },

  methods: {
    openFn(data) {
      window.open(data.href, '_blank')
    }
  }
}
</script>

<style lang="scss" scoped>
.ivu-global-footer-copyright {
  text-align: center;
  color: #808695;
  font-size: 14px;
  width: 100%;
  height: 65px;
  margin-top: 15px;
}
</style>
