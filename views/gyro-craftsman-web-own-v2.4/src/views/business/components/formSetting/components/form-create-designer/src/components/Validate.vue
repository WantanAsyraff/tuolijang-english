<template>
  <form-create
    class="_fc-validate"
    :rule="rule"
    :option="option"
    :value="formValue"
    @update:value="onInput"
  ></form-create>
</template>

<script>
import { $ } from '@/lang'
export default {
  name: 'Validate',
  props: {
    value: Array,
  },
  watch: {
    value(n) {
      this.formValue = this.parseValue(n);
    },
  },
  data() {
    return {
      formValue: {},
      option: {
        form: {
          labelPosition: 'top',
          size: 'mini',
          labelWidth: '90px',
        },
        submitBtn: false,
        formData: this.parseValue(this.value),
      },
      rule: [
        {
          type: 'select',
          field: 'type',
          value: undefined,
          title: $('ui.developForeignDocumentFieldType'),
          options: [
            { value: undefined, label: $('finance.pleaseselect') },
            { value: 'string', label: 'String' },
            { value: 'array', label: 'Array' },
            { value: 'number', label: 'Number' },
            { value: 'integer', label: 'Integer' },
            { value: 'float', label: 'Float' },
            { value: 'object', label: 'Object' },
            { value: 'date', label: 'Date' },
            { value: 'url', label: 'url' },
            { value: 'hex', label: 'hex' },
            { value: 'email', label: 'email' },
          ],
          control: [
            {
              handle: (v) => {
                return !!v;
              },
              rule: [
                {
                  type: 'group',
                  field: 'validate',
                  props: {
                    expand: 1,
                    rules: [
                      {
                        type: 'select',
                        title: $('legacyScript.triggerMethod'),
                        field: 'trigger',
                        value: 'change',
                        options: [
                          { label: 'change', value: 'change' },
                          { label: 'submit', value: 'submit' },
                          { label: 'blur', value: 'blur' },
                        ],
                      },
                      {
                        type: 'select',
                        title: $('legacyScript.verificationMethod'),
                        field: 'mode',
                        options: [
                          { value: 'required', label: $('ui.developForeignDocumentRequired') },
                          { value: 'pattern', label: $('legacyScript.regularExpression') },
                          { value: 'min', label: $('legacyScript.minimumValue') },
                          { value: 'max', label: $('legacyScript.maximumValue') },
                          { value: 'len', label: $('legacyScript.length') },
                        ],
                        value: 'required',
                        control: [
                          {
                            value: 'required',
                            rule: [
                              {
                                type: 'hidden',
                                field: 'required',
                                value: true,
                              },
                            ],
                          },
                          {
                            value: 'pattern',
                            rule: [
                              {
                                type: 'input',
                                field: 'pattern',
                                title: $('legacyScript.regularExpression'),
                              },
                            ],
                          },
                          {
                            value: 'min',
                            rule: [
                              {
                                type: 'inputNumber',
                                field: 'min',
                                title: $('legacyScript.minimumValue'),
                              },
                            ],
                          },
                          {
                            value: 'max',
                            rule: [
                              {
                                type: 'inputNumber',
                                field: 'max',
                                title: $('legacyScript.maximumValue'),
                              },
                            ],
                          },
                          {
                            value: 'len',
                            rule: [
                              {
                                type: 'inputNumber',
                                field: 'len',
                                title: $('legacyScript.length'),
                              },
                            ],
                          },
                        ],
                      },
                      {
                        type: 'input',
                        title: $('legacyScript.errorInformation'),
                        field: 'message',
                        value: '',
                      },
                    ],
                  },
                  value: [],
                },
              ],
            },
          ],
        },
      ],
    };
  },
  methods: {
    onInput: function (formData) {
      let val = [];
      const { validate, type } = formData;
      if (type && !validate.length) {
        return;
      } else if (type) {
        validate.forEach((v) => {
          v.type = type;
        });
        val = [...validate];
      }
      this.$emit('input', val);
    },
    parseValue(n) {
      let val = {
        validate: n ? [...n] : [],
        type: n.length ? n[0].type : undefined,
      };
      val.validate.forEach((v) => {
        if (!v.mode) {
          Object.keys(v).forEach((k) => {
            if (['message', 'type', 'trigger', 'mode'].indexOf(k) < 0) {
              v.mode = k;
            }
          });
        }
      });

      return val;
    },
  },
};
</script>

<style>
._fc-validate .form-create .el-form-item {
  margin-bottom: 22px !important;
}
</style>
