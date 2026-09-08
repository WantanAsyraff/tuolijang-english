/**
 * Mobile-phone input helpers for the management dashboard. They accept
 * Malaysian local input as well as E.164-style international numbers.
 */
export const internationalMobilePattern = /^\+?[1-9]\d{6,14}$/
export const malaysiaLocalMobilePattern = /^01\d{7,9}$/
export const mobilePhonePattern = /^(?:\+?[1-9]\d{6,14}|01\d{7,9})$/

export function normalizePhoneInput(value) {
  return String(value || '').replace(/[\s()-]/g, '')
}

export function isValidMobilePhone(value) {
  return mobilePhonePattern.test(normalizePhoneInput(value))
}