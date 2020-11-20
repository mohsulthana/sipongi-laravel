import 'babel-polyfill'
import Vue from 'vue'

import '~/mixins/index'
import router from '~/router/index'
import store from '~/store/index'
import App from '$comp/App'
import '~/plugins/index'
import vuetify from '~/plugins/vuetify'
import i18n from '~/plugins/i18n'

window.Form = require('form-backend-validation').Form

Vue.config.productionTip = false

export const app = new Vue({
  i18n,
  router,
  store,
  vuetify,
  created() {
    //  [App.vue specific] When App.vue is first loaded start the progress bar
    this.$Progress.start()
    //  hook the progress bar to start before we move router-view
    this.$router.beforeResolve((to, from, next) => {
      //  does the page we want to go to have a meta.progress object
      if (to.meta.progress !== undefined) {
        let meta = to.meta.progress
        // parse meta tags
        this.$Progress.parseMeta(meta)
      }
      //  start the progress bar
      this.$Progress.start()
      //  continue to next page
      next()
    })
    //  hook the progress bar to finish after we've finished moving router-view
    this.$router.afterEach((to, from) => {
      //  finish the progress bar
      if (this.check && Vue.myEnv.wsEnabled) {
        if (typeof window.Echo === 'undefined') {
          this.initialLister()
        }
      }

      this.$Progress.finish()
    })
  },
  render: (h) => h(App),
}).$mount('#app')
