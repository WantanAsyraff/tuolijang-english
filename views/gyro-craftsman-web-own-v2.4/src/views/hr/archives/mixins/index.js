import i18n from '@/lang'
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
    callback(new Error('职位不能为空'))
  }
}

let formOptions = {
  data() {
    this.FORMOPTIONS = [
      // 基本信息数据集合
      {
        title: i18n.t('setting.info.essentialinformation'),
        edit_type: 'basic',
        data: [
          {
            type: 'input',
            label: i18n.t('ui.hrEnterpriseJobAnalysisPersonName'),
            value: 'name',
            placeholder: i18n.t('legacyScript.pleaseEnterPersonName')
          },
          {
            type: 'input',
            label: i18n.t('legacyScript.mobileNumber'),
            value: 'phone',
            placeholder: i18n.t('legacyScript.phoneNumberIsTheEmployeeLoginAccountDefaultPassword888888')
          },
          {
            slot: 'cascader',
            label: i18n.t('ui.userTrainingPromotionPosition'),
            value: 'position',
            placeholder: i18n.t('ui.userDutyAnalyseSelectPosition')
          },
          {
            slot: 'frame_id',
            label: i18n.t('ui.hrAttendanceStatisticsDetailsDrawerDepartment'),
            value: 'frame_id',
            placeholder: i18n.t('legacyScript.selectDepartment')
          },
          {
            type: 'radio',
            label: i18n.t('ui.programProgramListAddProgramOwner'),
            value: 'is_admin',
            placeholder: i18n.t('ui.programProgramListAddProgramPleaseSelectOwner')
          },
          {
            label: i18n.t('ui.hrEnterpriseGroupEditUserResponsibleDepartments'),
            slot: 'manage_frame'
          },

          {
            label: i18n.t('legacyScript.directSuperior'),
            slot: 'superior_uid'
          }
        ]
      },
      // 职工信息数据集合
      {
        title: i18n.t('hr.tablist2'),
        edit_type: 'staff',
        data: [
          {
            type: 'select',

            label: i18n.t('legacyScript.employmentType'),
            value: 'is_part',
            placeholder: i18n.t('legacyScript.pleaseSelectEmploymentType'),
            optionsList: [
              {
                label: i18n.t('legacyScript.fullTime'),
                value: 0
              },
              {
                label: i18n.t('legacyScript.partTime'),
                value: 1
              },
              {
                label: i18n.t('hr.internship'),
                value: 2
              },
              {
                label: i18n.t('legacyScript.laborDispatch'),
                value: 3
              },
              {
                label: i18n.t('legacyScript.retireeRehired'),
                value: 4
              },
              {
                label: i18n.t('legacyScript.laborOutsourcing'),
                value: 5
              },
              {
                label: i18n.t('hr.other'),
                value: 6
              }
            ]
          },
          {
            type: 'select',
            label: i18n.t('legacyScript.employeeStatus'),
            value: 'type',
            placeholder: i18n.t('hr.placeholder19'),
            optionsList: [
              {
                label: i18n.t('hr.formal'),
                value: '1'
              },
              {
                label: i18n.t('hr.ontrial'),
                value: '2'
              },
              {
                label: i18n.t('hr.internship'),
                value: '3'
              },
              {
                label: i18n.t('hr.dimission'),
                value: '4'
              }
            ]
          },
          {
            type: 'date',
            label: i18n.t('legacyScript.onboardingTime'),
            value: 'work_time',
            placeholder: i18n.t('legacyScript.pleaseSelectOnboardingTime')
          },
          {
            type: 'date',
            label: i18n.t('legacyScript.probationEndDate'),
            value: 'trial_time',
            placeholder: i18n.t('legacyScript.selectProbationEndDate')
          },
          {
            type: 'date',
            label: i18n.t('legacyScript.positiveTime'),
            value: 'formal_time',
            placeholder: i18n.t('legacyScript.pleaseSelectPositiveTime')
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
        title: i18n.t('hr.tablist1'),
        edit_type: 'user',
        data: [
          {
            type: 'input',
            label: i18n.t('legacyScript.iDNumber'),
            value: 'card_id',
            placeholder: i18n.t('legacyScript.enterIDNumber')
          },
          {
            type: 'select',
            label: i18n.t('legacyScript.gender'),
            value: 'sex',
            placeholder: i18n.t('hr.placeholder3'),
            optionsList: [
              {
                label: i18n.t('hr.male'),
                value: 1
              },
              {
                label: i18n.t('hr.female'),
                value: 2
              },
              {
                label: i18n.t('hr.unknown'),
                value: 0
              }
            ]
          },
          {
            type: 'date',
            label: i18n.t('legacyScript.dateOfBirth'),
            value: 'birthday',
            placeholder: i18n.t('legacyScript.pleaseSelectDateOfBirth')
          },
          {
            type: 'input',
            label: i18n.t('legacyScript.age'),
            value: 'age',
            placeholder: i18n.t('legacyScript.pleaseEnterAge')
          },
          {
            type: 'input',
            label: i18n.t('legacyScript.nation'),
            value: 'nation',
            placeholder: i18n.t('legacyScript.pleaseEnterNation')
          },
          {
            type: 'input',
            label: i18n.t('legacyScript.politicalOutlook'),
            value: 'politic',
            placeholder: i18n.t('legacyScript.pleaseEnterPoliticalOutlook')
          },
          {
            type: 'input',
            label: i18n.t('legacyScript.yearsOfExperience'),
            value: 'work_years',
            placeholder: i18n.t('legacyScript.enterYearsOfRelevantExperience')
          },
          {
            type: 'input',
            label: i18n.t('legacyScript.nativePlace'),
            value: 'native',
            placeholder: i18n.t('legacyScript.enterNativePlace')
          },
          {
            type: 'input',
            label: i18n.t('legacyScript.currentAddress'),
            value: 'address',
            placeholder: i18n.t('legacyScript.enterCurrentResidentialAddress')
          },
          {
            type: 'select',
            label: i18n.t('legacyScript.maritalStatus'),
            value: 'marriage',
            placeholder: i18n.t('legacyScript.pleaseSelectMaritalStatus'),
            optionsList: [
              {
                label: i18n.t('hr.unmarried'),
                value: 0
              },
              {
                label: i18n.t('hr.married'),
                value: 1
              },
              {
                label: i18n.t('legacyScript.marriedWithChildren'),
                value: 2
              }
            ]
          },
          {
            type: 'input',
            label: i18n.t('legacyScript.emailAddress'),
            value: 'email',
            placeholder: i18n.t('customer.placeholder55')
          }
        ]
      },
      // 学历信息数据集合
      {
        title: i18n.t('legacyScript.educationInformation'),
        edit_type: 'education',
        data: [
          {
            type: 'select',
            label: i18n.t('legacyScript.highestEducation'),
            value: 'education',
            placeholder: i18n.t('legacyScript.enterHighestEducation'),
            optionsList: [
              {
                label: i18n.t('legacyScript.graduateDegree'),
                value: 6
              },
              {
                label: i18n.t('legacyScript.bachelorDegree'),
                value: 5
              },
              {
                label: i18n.t('legacyScript.associateDegree'),
                value: 4
              },

              {
                label: i18n.t('legacyScript.highSchoolOrBelow'),
                value: 2
              }
            ]
          },
          {
            type: 'input',
            label: i18n.t('legacyScript.highestDegree'),
            value: 'acad',
            placeholder: i18n.t('legacyScript.enterHighestDegree')
          },
          {
            type: 'date',
            label: i18n.t('legacyScript.graduationDate'),
            value: 'graduate_date',
            placeholder: i18n.t('legacyScript.pleaseSelectGraduationDate')
          },
          {
            type: 'input',
            label: i18n.t('legacyScript.schoolOrUniversity'),
            value: 'graduate_name',
            placeholder: i18n.t('legacyScript.enterSchoolOrUniversity')
          }
        ]
      },
      // 个人材料
      {
        title: i18n.t('legacyScript.personalDocuments'),
        edit_type: 'card',

        slot: 'personalMaterials',
        data: [
          {
            type: 'uploadImg',
            label: i18n.t('legacyScript.frontOfIDCard'),
            value: 'card_front'
          },
          {
            type: 'uploadImg',
            label: i18n.t('legacyScript.backOfIDCard'),
            value: 'card_both'
          },
          {
            type: 'uploadImg',
            label: i18n.t('legacyScript.educationCertificate'),
            value: 'education_image'
          },
          {
            type: 'uploadImg',
            label: i18n.t('legacyScript.degreeCertificate'),
            value: 'acad_image'
          }
        ]
      },
      // 银行卡信息
      {
        title: i18n.t('legacyScript.bankCardInformation'),
        edit_type: 'bank',
        data: [
          {
            type: 'input',
            label: i18n.t('legacyScript.bankCardNumber'),
            value: 'bank_num',
            placeholder: i18n.t('legacyScript.enterBankCardNumber')
          },

          {
            type: 'input',
            label: i18n.t('legacyScript.bankOfDeposit'),
            value: 'bank_name',
            placeholder: i18n.t('legacyScript.enterBankOfDeposit')
          }
        ]
      },
      // 社保信息
      {
        title: i18n.t('legacyScript.socialSecurityInformation'),
        edit_type: 'social',
        data: [
          {
            type: 'input',
            label: i18n.t('legacyScript.socialSecurityAccount'),
            value: 'social_num',
            placeholder: i18n.t('legacyScript.enterSocialSecurityAccount')
          },
          {
            type: 'input',
            label: i18n.t('legacyScript.housingProvidentFundAccount'),
            value: 'fund_num',
            placeholder: i18n.t('legacyScript.enterHousingProvidentFundAccount')
          }
        ]
      },
      // 紧急联系人
      {
        title: i18n.t('hr.emergencycontact'),
        edit_type: 'spare',
        data: [
          {
            type: 'input',
            label: i18n.t('legacyScript.contactName'),
            value: 'spare_name',
            placeholder: i18n.t('legacyScript.enterEmergencyContactName')
          },
          {
            type: 'input',
            label: i18n.t('legacyScript.contactPhone'),
            value: 'spare_tel',
            placeholder: i18n.t('legacyScript.enterEmergencyContactPhone')
          }
        ]
      },

      // 工作经历
      {
        title: i18n.t('hr.workexperience'),
        type: 1,
        slot: 'workExperience',
        data: []
      },
      // 教育经历
      {
        title: i18n.t('hr.educationalexperience'),
        type: 1,
        slot: 'educationalExperience',
        data: []
      },
      {
        title: i18n.t('legacyScript.systemInformation'),
        type: 1,
        edit_type: 'sort',
        slot: 'systemInformation',
        data: [
          {
            type: 'num',
            label: i18n.t('ui.businessHolidayTypeIndexSort'),
            value: 'sort',
            placeholder: i18n.t('legacyScript.pleaseEnterASortingValue')
          }
        ]
      }
    ]
    // 个人简历
    this.userForm = [
      {
        title: i18n.t('hr.tablist2'),
        data: [
          {
            type: 'input',
            label: i18n.t('ui.userTrainingPromotionPosition'),
            value: 'position',
            placeholder: i18n.t('legacyScript.pleaseEnterPosition')
          },
          {
            type: 'select',
            label: this.$t('setting.group.employmentType'),
            value: 'is_part',
            placeholder: this.$t('setting.group.employmentTypePlaceholder'),
            optionsList: [
              {
                label: i18n.t('legacyScript.fullTime'),
                value: 0
              },
              {
                label: i18n.t('legacyScript.partTime'),
                value: 1
              }
            ]
          }
        ]
      },
      {
        title: i18n.t('hr.tablist1'),
        data: [
          {
            type: 'input',
            label: i18n.t('ui.hrEnterpriseJobAnalysisPersonName'),
            value: 'name',
            placeholder: i18n.t('legacyScript.enterName')
          },
          {
            type: 'input',
            label: i18n.t('ui.customerSigningAddContractSignContactPhone'),
            value: 'phone',
            placeholder: i18n.t('customer.placeholder09')
          },

          {
            type: 'input',
            label: i18n.t('legacyScript.iDNumber'),
            value: 'card_id',
            placeholder: i18n.t('legacyScript.enterIDNumber')
          },
          {
            type: 'select',
            label: i18n.t('legacyScript.gender'),
            value: 'sex',
            placeholder: i18n.t('hr.placeholder3'),
            optionsList: [
              {
                label: i18n.t('hr.male'),
                value: 1
              },
              {
                label: i18n.t('hr.female'),
                value: 2
              },
              {
                label: i18n.t('hr.unknown'),
                value: 0
              }
            ]
          },
          {
            type: 'date',
            label: i18n.t('legacyScript.dateOfBirth'),
            value: 'birthday',
            placeholder: i18n.t('legacyScript.pleaseSelectDateOfBirth')
          },
          {
            type: 'input',
            label: i18n.t('legacyScript.age'),
            value: 'age',
            placeholder: i18n.t('legacyScript.pleaseEnterAge')
          },
          {
            type: 'input',
            label: i18n.t('legacyScript.nation'),
            value: 'nation',
            placeholder: i18n.t('legacyScript.pleaseEnterNation')
          },
          {
            type: 'input',
            label: i18n.t('legacyScript.politicalOutlook'),
            value: 'politic',
            placeholder: i18n.t('legacyScript.pleaseEnterPoliticalOutlook')
          },
          {
            type: 'input',
            label: i18n.t('legacyScript.yearsOfExperience'),
            value: 'work_years',
            placeholder: i18n.t('legacyScript.enterYearsOfRelevantExperience')
          },
          {
            type: 'input',
            label: i18n.t('legacyScript.nativePlace'),
            value: 'native',
            placeholder: i18n.t('legacyScript.enterNativePlace')
          },
          {
            type: 'input',
            label: i18n.t('legacyScript.currentAddress'),
            value: 'address',
            placeholder: i18n.t('legacyScript.enterCurrentResidentialAddress')
          },
          {
            type: 'select',
            label: i18n.t('legacyScript.maritalStatus'),
            value: 'marriage',
            placeholder: i18n.t('legacyScript.pleaseSelectMaritalStatus'),
            optionsList: [
              {
                label: i18n.t('hr.unmarried'),
                value: 0
              },
              {
                label: i18n.t('hr.married'),
                value: 1
              },
              {
                label: i18n.t('legacyScript.marriedWithChildren'),
                value: 2
              }
            ]
          },
          {
            type: 'input',
            label: i18n.t('legacyScript.emailAddress'),
            value: 'email',
            placeholder: i18n.t('customer.placeholder55')
          }
        ]
      },

      {
        title: i18n.t('legacyScript.educationInformation'),
        data: [
          {
            type: 'select',
            label: i18n.t('legacyScript.highestEducation'),
            value: 'education',
            placeholder: i18n.t('legacyScript.enterHighestEducation'),
            optionsList: [
              {
                label: i18n.t('legacyScript.graduateDegree'),
                value: 6
              },
              {
                label: i18n.t('legacyScript.bachelorDegree'),
                value: 5
              },
              {
                label: i18n.t('legacyScript.associateDegree'),
                value: 4
              },

              {
                label: i18n.t('legacyScript.highSchoolOrBelow'),
                value: 2
              }
            ]
          },
          {
            type: 'input',
            label: i18n.t('legacyScript.highestDegree'),
            value: 'acad',
            placeholder: i18n.t('legacyScript.enterHighestDegree')
          },
          {
            type: 'date',
            label: i18n.t('legacyScript.graduationDate'),
            value: 'graduate_date',
            placeholder: i18n.t('legacyScript.pleaseSelectGraduationDate')
          },
          {
            type: 'input',
            label: i18n.t('legacyScript.schoolOrUniversity'),
            value: 'graduate_name',
            placeholder: i18n.t('legacyScript.enterSchoolOrUniversity')
          }
        ]
      },
      {
        title: i18n.t('legacyScript.personalDocuments'),

        slot: 'personalMaterials',
        data: [
          {
            type: 'uploadImg',
            label: i18n.t('legacyScript.frontOfIDCard'),
            value: 'card_front'
          },
          {
            type: 'uploadImg',
            label: i18n.t('legacyScript.backOfIDCard'),
            value: 'card_both'
          },
          {
            type: 'uploadImg',
            label: i18n.t('legacyScript.educationCertificate'),
            value: 'education_image'
          },
          {
            type: 'uploadImg',
            label: i18n.t('legacyScript.degreeCertificate'),
            value: 'acad_image'
          }
        ]
      },
      {
        title: i18n.t('legacyScript.bankCardInformation'),
        data: [
          {
            type: 'input',
            label: i18n.t('legacyScript.bankCardNumber'),
            value: 'bank_num',
            placeholder: i18n.t('legacyScript.enterBankCardNumber')
          },

          {
            type: 'input',
            label: i18n.t('legacyScript.bankOfDeposit'),
            value: 'bank_name',
            placeholder: i18n.t('legacyScript.enterBankOfDeposit')
          }
        ]
      },
      {
        title: i18n.t('legacyScript.socialSecurityInformation'),
        data: [
          {
            type: 'input',
            label: i18n.t('legacyScript.socialSecurityAccount'),
            value: 'social_num',
            placeholder: i18n.t('legacyScript.enterSocialSecurityAccount')
          },
          {
            type: 'input',
            label: i18n.t('legacyScript.housingProvidentFundAccount'),
            value: 'fund_num',
            placeholder: i18n.t('legacyScript.enterHousingProvidentFundAccount')
          }
        ]
      },
      {
        title: i18n.t('hr.emergencycontact'),
        data: [
          {
            type: 'input',
            label: i18n.t('legacyScript.contactName'),
            value: 'spare_name',
            placeholder: i18n.t('legacyScript.enterEmergencyContactName')
          },
          {
            type: 'input',
            label: i18n.t('legacyScript.contactPhone'),
            value: 'spare_tel',
            placeholder: i18n.t('legacyScript.enterEmergencyContactPhone')
          }
        ]
      },

      // 工作经历
      {
        title: i18n.t('hr.workexperience'),
        type: 1,
        slot: 'workExperience',
        data: []
      },
      // 教育经历
      {
        title: i18n.t('hr.educationalexperience'),
        type: 1,
        slot: 'educationalExperience',
        data: []
      }
    ]

    // 未入职员工
    ;(this.notEntry = [
      {
        title: i18n.t('hr.tablist2'),
        edit_type: 'staff',
        data: [
          {
            type: 'date',
            label: i18n.t('legacyScript.interviewTime'),
            value: 'interview_date',
            placeholder: i18n.t('legacyScript.pleaseSelectInterviewTime')
          },
          {
            type: 'select',
            label: i18n.t('legacyScript.employmentType'),
            value: 'is_part',
            placeholder: i18n.t('legacyScript.pleaseSelectEmploymentType'),
            optionsList: [
              {
                label: i18n.t('legacyScript.fullTime'),
                value: 0
              },
              {
                label: i18n.t('legacyScript.partTime'),
                value: 1
              },
              {
                label: i18n.t('hr.internship'),
                value: 2
              },
              {
                label: i18n.t('legacyScript.laborDispatch'),
                value: 3
              },
              {
                label: i18n.t('legacyScript.retireeRehired'),
                value: 4
              },
              {
                label: i18n.t('legacyScript.laborOutsourcing'),
                value: 5
              },
              {
                label: i18n.t('hr.other'),
                value: 6
              }
            ]
          },
          {
            type: 'input',
            label: i18n.t('legacyScript.interviewPosition'),
            value: 'interview_position',
            placeholder: i18n.t('legacyScript.pleaseEnterInterviewPosition')
          },
          {
            type: 'select',
            label: i18n.t('legacyScript.employeeStatus'),
            value: 'type',
            placeholder: i18n.t('hr.placeholder19'),
            optionsList: [
              {
                label: i18n.t('hr.formal'),
                value: '1'
              },
              {
                label: i18n.t('hr.ontrial'),
                value: '3'
              },
              {
                label: i18n.t('ui.hrArchivesTableNotOnboarded'),
                value: '0'
              },
              {
                label: i18n.t('hr.dimission'),
                value: '4'
              }
            ]
          }
        ]
      },
      {
        title: i18n.t('hr.tablist1'),
        edit_type: 'user',
        data: [
          {
            type: 'input',
            label: i18n.t('ui.hrEnterpriseJobAnalysisPersonName'),
            value: 'name',
            placeholder: i18n.t('legacyScript.enterName')
          },
          {
            type: 'input',
            label: i18n.t('legacyScript.mobileNumber'),
            value: 'phone',
            placeholder: i18n.t('legacyScript.theMobileNumberServesAsTheEmployeeLoginIDDefault')
          },

          {
            type: 'input',
            label: i18n.t('legacyScript.iDNumber'),
            value: 'card_id',
            placeholder: i18n.t('legacyScript.enterIDNumber')
          },
          {
            type: 'select',
            label: i18n.t('legacyScript.gender'),
            value: 'sex',
            placeholder: i18n.t('hr.placeholder3'),
            optionsList: [
              {
                label: i18n.t('hr.male'),
                value: 1
              },
              {
                label: i18n.t('hr.female'),
                value: 2
              },
              {
                label: i18n.t('hr.unknown'),
                value: 0
              }
            ]
          },
          {
            type: 'date',
            label: i18n.t('legacyScript.dateOfBirth'),
            value: 'birthday',
            placeholder: i18n.t('legacyScript.pleaseSelectDateOfBirth')
          },
          {
            type: 'input',
            label: i18n.t('legacyScript.age'),
            value: 'age',
            placeholder: i18n.t('legacyScript.pleaseEnterAge')
          },
          {
            type: 'input',
            label: i18n.t('legacyScript.nation'),
            value: 'nation',
            placeholder: i18n.t('legacyScript.pleaseEnterNation')
          },
          {
            type: 'input',
            label: i18n.t('legacyScript.politicalOutlook'),
            value: 'politic',
            placeholder: i18n.t('legacyScript.pleaseEnterPoliticalOutlook')
          },
          {
            type: 'input',
            label: i18n.t('legacyScript.yearsOfExperience'),
            value: 'work_years',
            placeholder: i18n.t('legacyScript.enterYearsOfRelevantExperience')
          },
          {
            type: 'input',
            label: i18n.t('legacyScript.nativePlace'),
            value: 'native',
            placeholder: i18n.t('legacyScript.enterNativePlace')
          },
          {
            type: 'input',
            label: i18n.t('legacyScript.currentAddress'),
            value: 'address',
            placeholder: i18n.t('legacyScript.enterCurrentResidentialAddress')
          },
          {
            type: 'select',
            label: i18n.t('legacyScript.maritalStatus'),
            value: 'marriage',
            placeholder: i18n.t('legacyScript.pleaseSelectMaritalStatus'),
            optionsList: [
              {
                label: i18n.t('hr.unmarried'),
                value: 0
              },
              {
                label: i18n.t('hr.married'),
                value: 1
              },
              {
                label: i18n.t('legacyScript.marriedWithChildren'),
                value: 2
              }
            ]
          },
          {
            type: 'input',
            label: i18n.t('legacyScript.emailAddress'),
            value: 'email',
            placeholder: i18n.t('customer.placeholder55')
          }
        ]
      },
      {
        title: i18n.t('legacyScript.educationInformation'),
        edit_type: 'education',
        data: [
          {
            type: 'select',
            label: i18n.t('legacyScript.highestEducation'),
            value: 'education',
            placeholder: i18n.t('legacyScript.enterHighestEducation'),
            optionsList: [
              {
                label: i18n.t('legacyScript.graduateDegree'),
                value: 6
              },
              {
                label: i18n.t('legacyScript.bachelorDegree'),
                value: 5
              },
              {
                label: i18n.t('legacyScript.associateDegree'),
                value: 4
              },

              {
                label: i18n.t('legacyScript.highSchoolOrBelow'),
                value: 2
              }
            ]
          },
          {
            type: 'input',
            label: i18n.t('legacyScript.highestDegree'),
            value: 'acad',
            placeholder: i18n.t('legacyScript.enterHighestDegree')
          },
          {
            type: 'date',
            label: i18n.t('legacyScript.graduationDate'),
            value: 'graduate_date',
            placeholder: i18n.t('legacyScript.pleaseSelectGraduationDate')
          },
          {
            type: 'input',
            label: i18n.t('legacyScript.schoolOrUniversity'),
            value: 'graduate_name',
            placeholder: i18n.t('legacyScript.enterSchoolOrUniversity')
          }
        ]
      },
      {
        title: i18n.t('hr.emergencycontact'),
        edit_type: 'spare',
        data: [
          {
            type: 'input',
            label: i18n.t('legacyScript.contactName'),
            value: 'spare_name',
            placeholder: i18n.t('legacyScript.enterEmergencyContactName')
          },
          {
            type: 'input',
            label: i18n.t('legacyScript.contactPhone'),
            value: 'spare_tel',
            placeholder: i18n.t('legacyScript.enterEmergencyContactPhone')
          }
        ]
      },
      // 工作经历
      {
        title: i18n.t('hr.workexperience'),
        type: 1,
        slot: 'workExperience',
        data: []
      },
      // 教育经历
      {
        title: i18n.t('hr.educationalexperience'),
        type: 1,
        slot: 'educationalExperience',
        data: []
      }
    ]),
      // 表单校验
      (this.fromRules = {
        name: [{ required: true, message: i18n.t('legacyScript.pleaseEnterPersonName'), trigger: 'blur' }],

        position: [{ required: true, validator: positionFn, trigger: 'change' }],

        is_admin: [{ required: true, message: i18n.t('legacyScript.ownerIsRequired'), trigger: 'change' }],
        frame_id: [{ required: true, validator: frameFn, trigger: 'blur' }],
        phone: [
          { required: true, message: i18n.t('customer.placeholder09'), trigger: 'blur' },
          {
            pattern: /^[1][3,4,5,6,7,8,9][0-9]{9}$/,
            message: i18n.t('legacyScript.pleaseEnterAValidPhoneNumber')
          }
        ],
        is_part: [{ required: true, message: i18n.t('legacyScript.pleaseSelectEmploymentType'), trigger: 'change' }],
        email: [
          { required: false, message: i18n.t('legacyScript.pleaseSelectEmploymentType'), trigger: 'change' },
          {
            type: 'email',
            message: i18n.t('legacyScript.pleaseEnterAValidEmailAddress'),
            trigger: ['blur']
          }
        ],
        type: [{ required: true, message: i18n.t('legacyScript.employeeStatusIsRequired'), trigger: 'change' }],
        card_id: [
          {
            pattern: /(^\d{15}$)|(^\d{18}$)|(^\d{17}(\d|X|x)$)/,
            message: i18n.t('legacyScript.pleaseEnterAValidIDNumber'),
            trigger: 'blur'
          }
        ],
        work_time: [{ required: true, message: i18n.t('legacyScript.pleaseSelectOnboardingTime'), trigger: 'blur' }],
        interview_date: [{ required: true, message: i18n.t('legacyScript.pleaseSelectInterviewTime'), trigger: 'blur' }],
        quit_time: [{ required: true, message: i18n.t('legacyScript.pleaseSelectResignationTime'), trigger: 'blur' }],
        interview_position: [{ required: true, message: i18n.t('legacyScript.pleaseEnterInterviewPosition'), trigger: 'blur' }]
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
