const fs = require('fs')
const path = require('path')

const file = path.resolve(__dirname, '../gyro-craftsman-web-own-v2.4/src/lang/en.js')
let source = fs.readFileSync(file, 'utf8')

const replacements = [
  ["    message14: 'No approval record'", "    message14: 'No approval record',\n    message15: 'No work analysis data',\n    message16: 'No data',\n    message17: 'No data'"],
  ["    accountstatus: 'Account status',\n    usersearch: 'User search',", "    accountstatus: 'Account status',\n    education: 'Education',\n    usersearch: 'User search',"],
  ["      title3: 'Delete the member',\n      checkdaily: 'Check daily',", "      title3: 'Delete the member',\n      title4: 'Delete this comment',\n      checkdaily: 'Check daily',"],
  ["      title1: 'Please fill in the company name',\n      enterpriseinformation: 'Enterprise information'", "      title1: 'Please fill in the company name',\n      title2: 'Please enter the enterprise address',\n      title3: 'Please enter the contact number',\n      enterpriseinformation: 'Enterprise information'"],
  ["      invitationtype: 'Type',\n      lookup: 'Lookup',", "      invitationtype: 'Type',\n      likeup: 'Invite by link',\n      lookup: 'Lookup',"],
  ["    accountpay: 'Payment method',\n    keeptype: 'Bookkeeping type',", "    accountpay: 'Payment method',\n    accounttabsubject: 'Accounting subject',\n    keeptype: 'Bookkeeping type',"],
  ["    message4: 'Are you sure you want to delete the fund record',\n    pleaseinput1:", "    message4: 'Are you sure you want to delete the fund record',\n    message5: 'Deleting this record will also delete the related order payments and income/expense entries.',\n    pleaseinput1:"],
  ["    newslide: 'New PPT',\n    documenttemplates:", "    newslide: 'New PPT',\n    newmindmap: 'New mind map',\n    documenttemplates:"],
  ["    contractstatus: 'Contract status',\n    execution:", "    contractstatus: 'Contract status',\n    contractstatus1: 'Please select an order status',\n    execution:"],
  ["    addcustomer: 'Add customer',\n    modify1:", "    addcustomer: 'Add customer',\n    editcustomer: 'Edit customer',\n    modify1:"],
  ["    message07: 'Are you sure you want to delete this contact',\n    addcontract:", "    message07: 'Are you sure you want to delete this contact',\n    message08: 'Are you sure you want to withdraw the invalidation request?',\n    addcontract:"],
  ["    placeholder23: 'Are you sure you want to delete this record',\n    audit:", "    placeholder23: 'Are you sure you want to delete this record',\n    placeholderOne: 'Are you sure you want to withdraw this invalidation record?',\n    audit:"],
  ["    message3: 'Please enter the approval type description'\n  }\n}", "    message3: 'Please enter the approval type description',\n    message10: 'Are you sure you want to delete this employee?'\n  }\n}"]
]

replacements.forEach(([from, to]) => {
  if (!source.includes(from)) throw new Error(`Expected locale anchor was not found: ${from}`)
  source = source.replace(from, to)
})

fs.writeFileSync(file, source, 'utf8')
console.log(`Updated ${replacements.length} English locale sections.`)
