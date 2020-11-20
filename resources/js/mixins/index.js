import Vue from 'vue'
import store from '~/store/index'

Vue.mixin({
  methods: {
    $can(permissionName) {
      if (
        typeof permissionName !== 'undefined' &&
        permissionName !== null &&
        permissionName !== ''
      ) {
        if (!store.getters['auth/check']) {
          return false
        }

        let user = store.getters['auth/user']
        let permissions = user.permissions
        let permissionArray = permissionName.split('|')
        let res = 0

        if (!user.is_super_admin) {
          if (permissions.length <= 0) {
            return false
          }

          permissionArray.forEach((item) => {
            if (item) {
              if (permissions.indexOf(item) !== -1) {
                res = res + 1
              }
            }
          })

          return res > 0
        }
      }

      return true
    },

    $toasts(icon, messages) {
      this.$swal({
        toast: true,
        position: 'top-end',
        showConfirmButton: false,
        timer: 3000,
        icon: icon,
        title: messages,
      })
    },
  },
})
