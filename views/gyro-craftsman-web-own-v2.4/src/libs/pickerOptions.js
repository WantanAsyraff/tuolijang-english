import i18n from '@/lang'
//日历搜索快捷键
export default {
  shortcuts: [
    {
      text: i18n.t('toptable.today'),
      onClick(picker) {
        const end = new Date();
        const start = new Date();
        picker.$emit('pick', [start, end]);
      },
    },
    {
      text: i18n.t('toptable.yesterday'),
      onClick(picker) {
        const end = new Date();
        const start = new Date();
        start.setTime(start.getTime() - 3600 * 1000 * 24 * 1);
        picker.$emit('pick', [start, start]);
      },
    },
    {
      text: i18n.t('hr.month'),
      onClick(picker) {
        const start = new Date();
        const end = new Date();
        start.setMonth(start.getMonth());
        start.setDate(1);
        end.setMonth(end.getMonth() + 1);
        end.setDate(0);
        picker.$emit('pick', [start, end]);
      },
    },
    {
      text: i18n.t('toptable.lastmonth'),
      onClick(picker) {
        const start = picker.$moment().subtract(1, 'month').startOf('month').format('YYYY/MM/DD');
        const end = picker.$moment().subtract(1, 'month').endOf('month').format('YYYY/MM/DD');
        picker.$emit('pick', [start, end]);
      },
    },
    {
      text: i18n.t('toptable.day7'),
      onClick(picker) {
        const end = new Date();
        const start = new Date();
        start.setTime(start.getTime() - 3600 * 1000 * 24 * 7);
        picker.$emit('pick', [start, end]);
      },
    },
    {
      text: i18n.t('toptable.day30'),
      onClick(picker) {
        const end = new Date();
        const start = new Date();
        start.setTime(start.getTime() - 3600 * 1000 * 24 * 30);
        picker.$emit('pick', [start, end]);
      },
    },
    {
      text: i18n.t('legacyScript.last90Days'),
      onClick(picker) {
        const end = new Date();
        const start = new Date();
        start.setTime(start.getTime() - 3600 * 1000 * 24 * 90);
        picker.$emit('pick', [start, end]);
      },
    },
    {
      text: i18n.t('legacyScript.lastYear2'),
      onClick(picker) {
        const end = new Date();
        const start = new Date();
        start.setTime(start.getTime() - 3600 * 1000 * 24 * 365);
        picker.$emit('pick', [start, end]);
      },
    },
    {
      text: i18n.t('toptable.thisyear'),
      onClick(picker) {
        const start = picker.$moment().startOf('year').format('YYYY/MM/DD HH:mm:ss');
        const end = picker.$moment().format('YYYY/MM/DD HH:mm:ss');
        picker.$emit('pick', [start, end]);
      },
    },
    {
      text: i18n.t('legacyScript.lastYear'),
      onClick(picker) {
        const start = picker.$moment().subtract(1, 'year').startOf('year').format('YYYY/MM/DD HH:mm:ss');
        const end = picker.$moment().subtract(1, 'year').endOf('year').format('YYYY/MM/DD HH:mm:ss');
        picker.$emit('pick', [start, end]);
      },
    },
  ]
};