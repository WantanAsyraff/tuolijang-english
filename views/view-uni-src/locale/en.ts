import generatedUi from "./generated-ui-en";

export default {
  ui: generatedUi,
  common: {
    language: "Language",
    chinese: "Chinese",
    english: "English",
    translateToEnglish: "Translate to English",
    confirm: "Confirm",
    cancel: "Cancel",
    logout: "Log out",
    confirmLogout: "Confirm log out",
    copied: "Copied successfully",
  },
  user: {
    avatar: "Avatar",
    nickname: "Nickname",
    userId: "User ID",
    phone: "Phone",
    email: "Email",
    password: "Password",
    enterNickname: "Please enter nickname",
    enterEmail: "Please enter email",
  },
  status: {
    networkError: "Network connection unavailable.",
    requestFailed: "Request failed.",
    serverError: "Server error: 500",
    loginExpired: "Login expired. Please log in again.",
    refreshTokenMissing: "Missing refresh token.",
    refreshTokenFailed: "Failed to refresh login credentials.",
  },
  upload: {
    uploading: "Uploading",
    uploadFailed: "Upload failed. Please try again later.",
    chooseFileFailed: "File selection failed.",
    chooseImageFailed: "Image selection failed.",
    fileTooLarge: "The file size cannot exceed {size}MB.",
    imageTooLarge: "The image size cannot exceed {size}MB.",
  },
};