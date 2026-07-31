import AuthDialog from '@/components/common/authDialog'

export const AuthDialogPlugin = {
  install(vue) {
    const DialogConstructor = vue.extend(AuthDialog)
    const instance = new DialogConstructor()

    instance.$mount(document.createElement('div'))
    document.body.appendChild(instance.$el)

    vue.prototype.$authDialog = {
      open: (options) => {
        return new Promise((resolve) => {
          instance.open(options)
          instance.handleClosed = () => {
            instance.visible = false
            resolve()
          }
        })
      }
    }
  }
}
