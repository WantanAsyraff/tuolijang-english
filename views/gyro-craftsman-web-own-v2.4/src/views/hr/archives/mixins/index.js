import { $ } from '@/lang'
let frameFn = (rule, value, callback) => {
  if (value && value.length > 0) {
    callback()
  } else {
    callback()
    // callback(new Error('请选择部门'))
  }
}
let positionFn = (rule, value, callback) => {
  if (value) {
    callback()
  } else {
    callback(new Error($('职位不能为空')))
  }
}

let formOptions = {
  data() {
    this.FORMOPTIONS = [
      // 基本信息数据集合
      {
        title: $('setting.info.essentialinformation'),
        edit_type: 'basic',
        data: [
          {
            type: 'input',
            label: $('ui.hrEnterpriseJobAnalysisPersonName'),
            value: 'name',
            placeholder: $('legacyScript.pleaseEnterPersonName')
          },
          {
            type: 'input',
            label: $('legacyScript.mobileNumber'),
            value: 'phone',
            placeholder: $('legacyScript.phoneNumberIsTheEmployeeLoginAccountDefaultPassword888888')
          },
          {
            slot: 'cascader',
            label: $('ui.userTrainingPromotionPosition'),
            value: 'position',
            placeholder: $('ui.userDutyAnalyseSelectPosition')
          },
          {
            slot: 'frame_id',
            label: $('ui.hrAttendanceStatisticsDetailsDrawerDepartment'),
            value: 'frame_id',
            placeholder: $('legacyScript.selectDepartment')
          },
          {
            type: 'radio',
            label: $('ui.programProgramListAddProgramOwner'),
            value: 'is_admin',
            placeholder: $('ui.programProgramListAddProgramPleaseSelectOwner')
          },
          {
            label: $('ui.hrEnterpriseGroupEditUserResponsibleDepartments'),
            slot: 'manage_frame'
          },

          {
            label: $('legacyScript.directSuperior'),
            slot: 'superior_uid'
          }
        ]
      },
      // 职工信息数据集合
      {
        title: $('hr.tablist2'),
        edit_type: 'staff',
        data: [
          {
            type: 'select',

            label: $('legacyScript.employmentType'),
            value: 'is_part',
            placeholder: $('legacyScript.pleaseSelectEmploymentType'),
            optionsList: [
              {
                label: $('legacyScript.fullTime'),
                value: 0
              },
              {
                label: $('legacyScript.partTime'),
                value: 1
              },
              {
                label: $('hr.internship'),
                value: 2
              },
              {
                label: $('legacyScript.laborDispatch'),
                value: 3
              },
              {
                label: $('legacyScript.retireeRehired'),
                value: 4
              },
              {
                label: $('legacyScript.laborOutsourcing'),
                value: 5
              },
              {
                label: $('hr.other'),
                value: 6
              }
            ]
          },
          {
            type: 'select',
            label: $('legacyScript.employeeStatus'),
            value: 'type',
            placeholder: $('hr.placeholder19'),
            optionsList: [
              {
                label: $('hr.formal'),
                value: '1'
              },
              {
                label: $('hr.ontrial'),
                value: '2'
              },
              {
                label: $('hr.internship'),
                value: '3'
              },
              {
                label: $('hr.dimission'),
                value: '4'
              }
            ]
          },
          {
            type: 'date',
            label: $('legacyScript.onboardingTime'),
            value: 'work_time',
            placeholder: $('legacyScript.pleaseSelectOnboardingTime')
          },
          {
            type: 'date',
            label: $('legacyScript.probationEndDate'),
            value: 'trial_time',
            placeholder: $('legacyScript.selectProbationEndDate')
          },
          {
            type: 'date',
            label: $('legacyScript.positiveTime'),
            value: 'formal_time',
            placeholder: $('legacyScript.pleaseSelectPositiveTime')
          }
          // {
          //   type: 'date',
          //   label: '订单到期：',
          //   value: 'treaty_time',
          //   placeholder: '请选择订单到期时间',
          // },
        ]
      },
      // 个人信息数据集合
      {
        title: $('hr.tablist1'),
        edit_type: 'user',
        data: [
          {
            type: 'input',
            label: $('legacyScript.iDNumber'),
            value: 'card_id',
            placeholder: $('legacyScript.enterIDNumber')
          },
          {
            type: 'select',
            label: $('legacyScript.gender'),
            value: 'sex',
            placeholder: $('hr.placeholder3'),
            optionsList: [
              {
                label: $('hr.male'),
                value: 1
              },
              {
                label: $('hr.female'),
                value: 2
              },
              {
                label: $('hr.unknown'),
                value: 0
              }
            ]
          },
          {
            type: 'date',
            label: $('legacyScript.dateOfBirth'),
            value: 'birthday',
            placeholder: $('legacyScript.pleaseSelectDateOfBirth')
          },
          {
            type: 'input',
            label: $('legacyScript.age'),
            value: 'age',
            placeholder: $('legacyScript.pleaseEnterAge')
          },
          {
            type: 'input',
            label: $('legacyScript.nation'),
            value: 'nation',
            placeholder: $('legacyScript.pleaseEnterNation')
          },
          {
            type: 'input',
            label: $('legacyScript.politicalOutlook'),
            value: 'politic',
            placeholder: $('legacyScript.pleaseEnterPoliticalOutlook')
          },
          {
            type: 'input',
            label: $('legacyScript.yearsOfExperience'),
            value: 'work_years',
            placeholder: $('legacyScript.enterYearsOfRelevantExperience')
          },
          {
            type: 'input',
            label: $('legacyScript.nativePlace'),
            value: 'native',
            placeholder: $('legacyScript.enterNativePlace')
          },
          {
            type: 'input',
            label: $('legacyScript.currentAddress'),
            value: 'address',
            placeholder: $('legacyScript.enterCurrentResidentialAddress')
          },
          {
            type: 'select',
            label: $('legacyScript.maritalStatus'),
            value: 'marriage',
            placeholder: $('legacyScript.pleaseSelectMaritalStatus'),
            optionsList: [
              {
                label: $('hr.unmarried'),
                value: 0
              },
              {
                label: $('hr.married'),
                value: 1
              },
              {
                label: $('legacyScript.marriedWithChildren'),
                value: 2
              }
            ]
          },
          {
            type: 'input',
            label: $('legacyScript.emailAddress'),
            value: 'email',
            placeholder: $('customer.placeholder55')
          }
        ]
      },
      // 学历信息数据集合
      {
        title: $('legacyScript.educationInformation'),
        edit_type: 'education',
        data: [
          {
            type: 'select',
            label: $('legacyScript.highestEducation'),
            value: 'education',
            placeholder: $('legacyScript.enterHighestEducation'),
            optionsList: [
              {
                label: $('legacyScript.graduateDegree'),
                value: 6
              },
              {
                label: $('legacyScript.bachelorDegree'),
                value: 5
              },
              {
                label: $('legacyScript.associateDegree'),
                value: 4
              },

              {
                label: $('legacyScript.highSchoolOrBelow'),
                value: 2
              }
            ]
          },
          {
            type: 'input',
            label: $('legacyScript.highestDegree'),
            value: 'acad',
            placeholder: $('legacyScript.enterHighestDegree')
          },
          {
            type: 'date',
            label: $('legacyScript.graduationDate'),
            value: 'graduate_date',
            placeholder: $('legacyScript.pleaseSelectGraduationDate')
          },
          {
            type: 'input',
            label: $('legacyScript.schoolOrUniversity'),
            value: 'graduate_name',
            placeholder: $('legacyScript.enterSchoolOrUniversity')
          }
        ]
      },
      // 个人材料
      {
        title: $('legacyScript.personalDocuments'),
        edit_type: 'card',

        slot: 'personalMaterials',
        data: [
          {
            type: 'uploadImg',
            label: $('legacyScript.frontOfIDCard'),
            value: 'card_front'
          },
          {
            type: 'uploadImg',
            label: $('legacyScript.backOfIDCard'),
            value: 'card_both'
          },
          {
            type: 'uploadImg',
            label: $('legacyScript.educationCertificate'),
            value: 'education_image'
          },
          {
            type: 'uploadImg',
            label: $('legacyScript.degreeCertificate'),
            value: 'acad_image'
          }
        ]
      },
      // 银行卡信息
      {
        title: $('legacyScript.bankCardInformation'),
        edit_type: 'bank',
        data: [
          {
            type: 'input',
            label: $('legacyScript.bankCardNumber'),
            value: 'bank_num',
            placeholder: $('legacyScript.enterBankCardNumber')
          },

          {
            type: 'input',
            label: $('legacyScript.bankOfDeposit'),
            value: 'bank_name',
            placeholder: $('legacyScript.enterBankOfDeposit')
          }
        ]
      },
      // 社保信息
      {
        title: $('legacyScript.socialSecurityInformation'),
        edit_type: 'social',
        data: [
          {
            type: 'input',
            label: $('legacyScript.socialSecurityAccount'),
            value: 'social_num',
            placeholder: $('legacyScript.enterSocialSecurityAccount')
          },
          {
            type: 'input',
            label: $('legacyScript.housingProvidentFundAccount'),
            value: 'fund_num',
            placeholder: $('legacyScript.enterHousingProvidentFundAccount')
          }
        ]
      },
      // 紧急联系人
      {
        title: $('hr.emergencycontact'),
        edit_type: 'spare',
        data: [
          {
            type: 'input',
            label: $('legacyScript.contactName'),
            value: 'spare_name',
            placeholder: $('legacyScript.enterEmergencyContactName')
          },
          {
            type: 'input',
            label: $('legacyScript.contactPhone'),
            value: 'spare_tel',
            placeholder: $('legacyScript.enterEmergencyContactPhone')
          }
        ]
      },

      // 工作经历
      {
        title: $('hr.workexperience'),
        type: 1,
        slot: 'workExperience',
        data: []
      },
      // 教育经历
      {
        title: $('hr.educationalexperience'),
        type: 1,
        slot: 'educationalExperience',
        data: []
      },
      {
        title: $('legacyScript.systemInformation'),
        type: 1,
        edit_type: 'sort',
        slot: 'systemInformation',
        data: [
          {
            type: 'num',
            label: $('ui.businessHolidayTypeIndexSort'),
            value: 'sort',
            placeholder: $('legacyScript.pleaseEnterASortingValue')
          }
        ]
      }
    ]
    // 个人简历
    this.userForm = [
      {
        title: $('hr.tablist2'),
        data: [
          {
            type: 'input',
            label: $('ui.userTrainingPromotionPosition'),
            value: 'position',
            placeholder: $('legacyScript.pleaseEnterPosition')
          },
          {
            type: 'select',
            label: this.$('setting.group.employmentType'),
            value: 'is_part',
            placeholder: this.$('setting.group.employmentTypePlaceholder'),
            optionsList: [
              {
                label: $('legacyScript.fullTime'),
                value: 0
              },
              {
                label: $('legacyScript.partTime'),
                value: 1
              }
            ]
          }
        ]
      },
      {
        title: $('hr.tablist1'),
        data: [
          {
            type: 'input',
            label: $('ui.hrEnterpriseJobAnalysisPersonName'),
            value: 'name',
            placeholder: $('legacyScript.enterName')
          },
          {
            type: 'input',
            label: $('ui.customerSigningAddContractSignContactPhone'),
            value: 'phone',
            placeholder: $('customer.placeholder09')
          },

          {
            type: 'input',
            label: $('legacyScript.iDNumber'),
            value: 'card_id',
            placeholder: $('legacyScript.enterIDNumber')
          },
          {
            type: 'select',
            label: $('legacyScript.gender'),
            value: 'sex',
            placeholder: $('hr.placeholder3'),
            optionsList: [
              {
                label: $('hr.male'),
                value: 1
              },
              {
                label: $('hr.female'),
                value: 2
              },
              {
                label: $('hr.unknown'),
                value: 0
              }
            ]
          },
          {
            type: 'date',
            label: $('legacyScript.dateOfBirth'),
            value: 'birthday',
            placeholder: $('legacyScript.pleaseSelectDateOfBirth')
          },
          {
            type: 'input',
            label: $('legacyScript.age'),
            value: 'age',
            placeholder: $('legacyScript.pleaseEnterAge')
          },
          {
            type: 'input',
            label: $('legacyScript.nation'),
            value: 'nation',
            placeholder: $('legacyScript.pleaseEnterNation')
          },
          {
            type: 'input',
            label: $('legacyScript.politicalOutlook'),
            value: 'politic',
            placeholder: $('legacyScript.pleaseEnterPoliticalOutlook')
          },
          {
            type: 'input',
            label: $('legacyScript.yearsOfExperience'),
            value: 'work_years',
            placeholder: $('legacyScript.enterYearsOfRelevantExperience')
          },
          {
            type: 'input',
            label: $('legacyScript.nativePlace'),
            value: 'native',
            placeholder: $('legacyScript.enterNativePlace')
          },
          {
            type: 'input',
            label: $('legacyScript.currentAddress'),
            value: 'address',
            placeholder: $('legacyScript.enterCurrentResidentialAddress')
          },
          {
            type: 'select',
            label: $('legacyScript.maritalStatus'),
            value: 'marriage',
            placeholder: $('legacyScript.pleaseSelectMaritalStatus'),
            optionsList: [
              {
                label: $('hr.unmarried'),
                value: 0
              },
              {
                label: $('hr.married'),
                value: 1
              },
              {
                label: $('legacyScript.marriedWithChildren'),
                value: 2
              }
            ]
          },
          {
            type: 'input',
            label: $('legacyScript.emailAddress'),
            value: 'email',
            placeholder: $('customer.placeholder55')
          }
        ]
      },

      {
        title: $('legacyScript.educationInformation'),
        data: [
          {
            type: 'select',
            label: $('legacyScript.highestEducation'),
            value: 'education',
            placeholder: $('legacyScript.enterHighestEducation'),
            optionsList: [
              {
                label: $('legacyScript.graduateDegree'),
                value: 6
              },
              {
                label: $('legacyScript.bachelorDegree'),
                value: 5
              },
              {
                label: $('legacyScript.associateDegree'),
                value: 4
              },

              {
                label: $('legacyScript.highSchoolOrBelow'),
                value: 2
              }
            ]
          },
          {
            type: 'input',
            label: $('legacyScript.highestDegree'),
            value: 'acad',
            placeholder: $('legacyScript.enterHighestDegree')
          },
          {
            type: 'date',
            label: $('legacyScript.graduationDate'),
            value: 'graduate_date',
            placeholder: $('legacyScript.pleaseSelectGraduationDate')
          },
          {
            type: 'input',
            label: $('legacyScript.schoolOrUniversity'),
            value: 'graduate_name',
            placeholder: $('legacyScript.enterSchoolOrUniversity')
          }
        ]
      },
      {
        title: $('legacyScript.personalDocuments'),

        slot: 'personalMaterials',
        data: [
          {
            type: 'uploadImg',
            label: $('legacyScript.frontOfIDCard'),
            value: 'card_front'
          },
          {
            type: 'uploadImg',
            label: $('legacyScript.backOfIDCard'),
            value: 'card_both'
          },
          {
            type: 'uploadImg',
            label: $('legacyScript.educationCertificate'),
            value: 'education_image'
          },
          {
            type: 'uploadImg',
            label: $('legacyScript.degreeCertificate'),
            value: 'acad_image'
          }
        ]
      },
      {
        title: $('legacyScript.bankCardInformation'),
        data: [
          {
            type: 'input',
            label: $('legacyScript.bankCardNumber'),
            value: 'bank_num',
            placeholder: $('legacyScript.enterBankCardNumber')
          },

          {
            type: 'input',
            label: $('legacyScript.bankOfDeposit'),
            value: 'bank_name',
            placeholder: $('legacyScript.enterBankOfDeposit')
          }
        ]
      },
      {
        title: $('legacyScript.socialSecurityInformation'),
        data: [
          {
            type: 'input',
            label: $('legacyScript.socialSecurityAccount'),
            value: 'social_num',
            placeholder: $('legacyScript.enterSocialSecurityAccount')
          },
          {
            type: 'input',
            label: $('legacyScript.housingProvidentFundAccount'),
            value: 'fund_num',
            placeholder: $('legacyScript.enterHousingProvidentFundAccount')
          }
        ]
      },
      {
        title: $('hr.emergencycontact'),
        data: [
          {
            type: 'input',
            label: $('legacyScript.contactName'),
            value: 'spare_name',
            placeholder: $('legacyScript.enterEmergencyContactName')
          },
          {
            type: 'input',
            label: $('legacyScript.contactPhone'),
            value: 'spare_tel',
            placeholder: $('legacyScript.enterEmergencyContactPhone')
          }
        ]
      },

      // 工作经历
      {
        title: $('hr.workexperience'),
        type: 1,
        slot: 'workExperience',
        data: []
      },
      // 教育经历
      {
        title: $('hr.educationalexperience'),
        type: 1,
        slot: 'educationalExperience',
        data: []
      }
    ]

    // 未入职员工
    ;(this.notEntry = [
      {
        title: $('hr.tablist2'),
        edit_type: 'staff',
        data: [
          {
            type: 'date',
            label: $('legacyScript.interviewTime'),
            value: 'interview_date',
            placeholder: $('legacyScript.pleaseSelectInterviewTime')
          },
          {
            type: 'select',
            label: $('legacyScript.employmentType'),
            value: 'is_part',
            placeholder: $('legacyScript.pleaseSelectEmploymentType'),
            optionsList: [
              {
                label: $('legacyScript.fullTime'),
                value: 0
              },
              {
                label: $('legacyScript.partTime'),
                value: 1
              },
              {
                label: $('hr.internship'),
                value: 2
              },
              {
                label: $('legacyScript.laborDispatch'),
                value: 3
              },
              {
                label: $('legacyScript.retireeRehired'),
                value: 4
              },
              {
                label: $('legacyScript.laborOutsourcing'),
                value: 5
              },
              {
                label: $('hr.other'),
                value: 6
              }
            ]
          },
          {
            type: 'input',
            label: $('legacyScript.interviewPosition'),
            value: 'interview_position',
            placeholder: $('legacyScript.pleaseEnterInterviewPosition')
          },
          {
            type: 'select',
            label: $('legacyScript.employeeStatus'),
            value: 'type',
            placeholder: $('hr.placeholder19'),
            optionsList: [
              {
                label: $('hr.formal'),
                value: '1'
              },
              {
                label: $('hr.ontrial'),
                value: '3'
              },
              {
                label: $('ui.hrArchivesTableNotOnboarded'),
                value: '0'
              },
              {
                label: $('hr.dimission'),
                value: '4'
              }
            ]
          }
        ]
      },
      {
        title: $('hr.tablist1'),
        edit_type: 'user',
        data: [
          {
            type: 'input',
            label: $('ui.hrEnterpriseJobAnalysisPersonName'),
            value: 'name',
            placeholder: $('legacyScript.enterName')
          },
          {
            type: 'input',
            label: $('legacyScript.mobileNumber'),
            value: 'phone',
            placeholder: $('legacyScript.theMobileNumberServesAsTheEmployeeLoginIDDefault')
          },

          {
            type: 'input',
            label: $('legacyScript.iDNumber'),
            value: 'card_id',
            placeholder: $('legacyScript.enterIDNumber')
          },
          {
            type: 'select',
            label: $('legacyScript.gender'),
            value: 'sex',
            placeholder: $('hr.placeholder3'),
            optionsList: [
              {
                label: $('hr.male'),
                value: 1
              },
              {
                label: $('hr.female'),
                value: 2
              },
              {
                label: $('hr.unknown'),
                value: 0
              }
            ]
          },
          {
            type: 'date',
            label: $('legacyScript.dateOfBirth'),
            value: 'birthday',
            placeholder: $('legacyScript.pleaseSelectDateOfBirth')
          },
          {
            type: 'input',
            label: $('legacyScript.age'),
            value: 'age',
            placeholder: $('legacyScript.pleaseEnterAge')
          },
          {
            type: 'input',
            label: $('legacyScript.nation'),
            value: 'nation',
            placeholder: $('legacyScript.pleaseEnterNation')
          },
          {
            type: 'input',
            label: $('legacyScript.politicalOutlook'),
            value: 'politic',
            placeholder: $('legacyScript.pleaseEnterPoliticalOutlook')
          },
          {
            type: 'input',
            label: $('legacyScript.yearsOfExperience'),
            value: 'work_years',
            placeholder: $('legacyScript.enterYearsOfRelevantExperience')
          },
          {
            type: 'input',
            label: $('legacyScript.nativePlace'),
            value: 'native',
            placeholder: $('legacyScript.enterNativePlace')
          },
          {
            type: 'input',
            label: $('legacyScript.currentAddress'),
            value: 'address',
            placeholder: $('legacyScript.enterCurrentResidentialAddress')
          },
          {
            type: 'select',
            label: $('legacyScript.maritalStatus'),
            value: 'marriage',
            placeholder: $('legacyScript.pleaseSelectMaritalStatus'),
            optionsList: [
              {
                label: $('hr.unmarried'),
                value: 0
              },
              {
                label: $('hr.married'),
                value: 1
              },
              {
                label: $('legacyScript.marriedWithChildren'),
                value: 2
              }
            ]
          },
          {
            type: 'input',
            label: $('legacyScript.emailAddress'),
            value: 'email',
            placeholder: $('customer.placeholder55')
          }
        ]
      },
      {
        title: $('legacyScript.educationInformation'),
        edit_type: 'education',
        data: [
          {
            type: 'select',
            label: $('legacyScript.highestEducation'),
            value: 'education',
            placeholder: $('legacyScript.enterHighestEducation'),
            optionsList: [
              {
                label: $('legacyScript.graduateDegree'),
                value: 6
              },
              {
                label: $('legacyScript.bachelorDegree'),
                value: 5
              },
              {
                label: $('legacyScript.associateDegree'),
                value: 4
              },

              {
                label: $('legacyScript.highSchoolOrBelow'),
                value: 2
              }
            ]
          },
          {
            type: 'input',
            label: $('legacyScript.highestDegree'),
            value: 'acad',
            placeholder: $('legacyScript.enterHighestDegree')
          },
          {
            type: 'date',
            label: $('legacyScript.graduationDate'),
            value: 'graduate_date',
            placeholder: $('legacyScript.pleaseSelectGraduationDate')
          },
          {
            type: 'input',
            label: $('legacyScript.schoolOrUniversity'),
            value: 'graduate_name',
            placeholder: $('legacyScript.enterSchoolOrUniversity')
          }
        ]
      },
      {
        title: $('hr.emergencycontact'),
        edit_type: 'spare',
        data: [
          {
            type: 'input',
            label: $('legacyScript.contactName'),
            value: 'spare_name',
            placeholder: $('legacyScript.enterEmergencyContactName')
          },
          {
            type: 'input',
            label: $('legacyScript.contactPhone'),
            value: 'spare_tel',
            placeholder: $('legacyScript.enterEmergencyContactPhone')
          }
        ]
      },
      // 工作经历
      {
        title: $('hr.workexperience'),
        type: 1,
        slot: 'workExperience',
        data: []
      },
      // 教育经历
      {
        title: $('hr.educationalexperience'),
        type: 1,
        slot: 'educationalExperience',
        data: []
      }
    ]),
      // 表单校验
      (this.fromRules = {
        name: [{ required: true, message: $('legacyScript.pleaseEnterPersonName'), trigger: 'blur' }],

        position: [{ required: true, validator: positionFn, trigger: 'change' }],

        is_admin: [{ required: true, message: $('legacyScript.ownerIsRequired'), trigger: 'change' }],
        frame_id: [{ required: true, validator: frameFn, trigger: 'blur' }],
        phone: [
          { required: true, message: $('customer.placeholder09'), trigger: 'blur' },
          {
            pattern: /^[1][3,4,5,6,7,8,9][0-9]{9}$/,
            message: $('legacyScript.pleaseEnterAValidPhoneNumber')
          }
        ],
        is_part: [{ required: true, message: $('legacyScript.pleaseSelectEmploymentType'), trigger: 'change' }],
        email: [
          { required: false, message: $('legacyScript.pleaseSelectEmploymentType'), trigger: 'change' },
          {
            type: 'email',
            message: $('legacyScript.pleaseEnterAValidEmailAddress'),
            trigger: ['blur']
          }
        ],
        type: [{ required: true, message: $('legacyScript.employeeStatusIsRequired'), trigger: 'change' }],
        card_id: [
          {
            pattern: /(^\d{15}$)|(^\d{18}$)|(^\d{17}(\d|X|x)$)/,
            message: $('legacyScript.pleaseEnterAValidIDNumber'),
            trigger: 'blur'
          }
        ],
        work_time: [{ required: true, message: $('legacyScript.pleaseSelectOnboardingTime'), trigger: 'blur' }],
        interview_date: [{ required: true, message: $('legacyScript.pleaseSelectInterviewTime'), trigger: 'blur' }],
        quit_time: [{ required: true, message: $('legacyScript.pleaseSelectResignationTime'), trigger: 'blur' }],
        interview_position: [{ required: true, message: $('legacyScript.pleaseEnterInterviewPosition'), trigger: 'blur' }]
      })
    return {
      // 控制每个表单的是否修改状态
      FORMITEMISEDIT: {
        0: true,
        1: true,
        2: true,
        3: true,
        4: true,
        5: true,
        6: true,
        7: true,
        8: true,
        9: true,
        10: true
      }
    }
  }
}
export default formOptions
