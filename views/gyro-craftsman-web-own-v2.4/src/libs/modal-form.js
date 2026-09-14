import { $ } from '@/lang'
import request from '@/api/request'
import Vue from 'vue'
const TRANSLATABLE_SCHEMA_KEYS = new Set([
  'title',
  'label',
  'placeholder',
  'message',
  'description',
  'content',
  'text',
  'name',
  'activeText',
  'inactiveText',
  'field_name',
  'fieldName',
  'key_name',
  'keyName',
  'active_text',
  'inactive_text'
])

export function localizeFormSchema(value, ctx, parentKey = '', englishValue) {
  if (Array.isArray(value)) return value.map((item) => localizeFormSchema(item, ctx, parentKey))
  if (!value || typeof value !== 'object') {
    return typeof value === 'string' && TRANSLATABLE_SCHEMA_KEYS.has(parentKey)
      ? (ctx && ctx.$ ? ctx.$(value, englishValue) : $(value, englishValue))
      : value
  }
  return Object.keys(value).reduce((result, key) => {
    if (key === 'formData') {
      result[key] = value[key]
    } else {
      const englishKey = `${key}_en`
      result[key] = localizeFormSchema(value[key], ctx, key, value[englishKey])
    }
    return result
  }, {})
}

function localizeSystemDictionaryFields(data, ctx) {
  const fields = data.system_dictionary_fields
  if (!fields || !Array.isArray(data.rule)) return data

  data.rule = data.rule.map((rule) => {
    if (!Object.prototype.hasOwnProperty.call(fields, rule.field)) return rule
    const source = fields[rule.field]
    const displayValue = typeof source === 'string' ? ctx.$(source) : source

    return {
      ...rule,
      value: displayValue,
      props: {
        ...(rule.props || {}),
        readonly: true
      }
    }
  })

  return data
}

let unique = 1
const uniqueId = () => ++unique
export default function modalForm(formRequestPromise, config = {}) {
  const h = this.$createElement
  return new Promise((resolve, reject) => {
    let formApi = null
    formRequestPromise
      .then(({ data }) => {
        data = localizeFormSchema(data, this)
        data = localizeSystemDictionaryFields(data, this)
        if (!data.config) data.config = {}
        data.config.submitBtn = false
        data.config.resetBtn = false
        if (!data.config.form) data.config.form = {}
        data.config.form.labelSuffix = this.$language === 'en' ? ':' : '：'
        data.config.form.labelWidth = this.$language === 'en' ? '190px' : '90px'
        if (!data.config.formData) data.config.formData = {}
        data.config.formData = { ...data.config.formData, ...config.formData }
        data.config.global = {
          upload: {
            props: {
              onSuccess(rep, file) {
                if (rep.status === 200) {
                  file.url = rep.data.src
                }
              }
            }
          },
          frame: {
            props: {
              onLoad(e) {
                e.fApi = formApi
              }
            }
          },
          inputNumber: {
            props: {
              controls: false
            }
          }
        }
        data = Vue.observable(data)
        this.$msgbox({
          title: data.title,
          showCancelButton: true,
          customClass: config.class || 'modal-form',
          mask: false,
          message: h('div', { class: 'common-form-create', key: uniqueId() }, [
            h('formCreate', {
              props: {
                rule: data.rule,
                option: data.config
              },
              on: {
                mounted: ($f) => {
                  formApi = $f
                }
              }
            })
          ]),
          beforeClose: (action, instance, done) => {
            if (action === 'confirm') {
              if (!formApi) {
                instance.confirmButtonLoading = false
                return
              }
              instance.confirmButtonLoading = true
              formApi.submit(
                (formData) => {
                  if (data.system_dictionary_fields) {
                    Object.assign(formData, data.system_dictionary_fields)
                  }
                  request[data.method.toLowerCase()](data.action.slice(4), formData)
                    .then((res) => {
                      if (res.status === 200) {
                        instance.confirmButtonLoading = false
                        formApi = null
                        done()
                        resolve(res)
                      } else {
                        instance.confirmButtonLoading = false
                        reject(res)
                      }
                    })
                    .catch((err) => {
                      instance.confirmButtonLoading = false
                      reject(err)
                    })
                },
                () => {
                  instance.confirmButtonLoading = false
                }
              )
            } else {
              instance.confirmButtonLoading = false
              formApi = null
              done()
            }
          }
        })
      })
      .catch((e) => {
        this.$message.error(this.$(e.message || '获取表单配置失败'))
      })
  })
}
