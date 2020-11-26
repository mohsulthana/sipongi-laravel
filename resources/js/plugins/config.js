import Vue from 'vue'
import VueI18n from '~/plugins/i18n'
import { settings, api } from '~/config'

Vue.use(
  {
    install: function (Vue, name = 'api') {
      Vue[name] = api
      Object.defineProperty(Vue.prototype, `$${name}`, {
        value: api,
      })
    },
  },
  'api'
)

Vue.use(
  {
    install: function (Vue, name = 'myEnv') {
      Vue[name] = settings
      Object.defineProperty(Vue.prototype, `$${name}`, {
        value: settings,
      })
    },
  },
  'myEnv'
)

Vue.use(
  {
    install: function (Vue, name = 'ct') {
      const toLower = function (text, params) {
        if (VueI18n.te(text)) {
          let trans = VueI18n.t(text, params)
          text = trans.replace(
            /\w\S*/g,
            (txt) =>
              // txt.charAt(0).toUpperCase() + txt.substr(1).toLowerCase()
              txt.charAt(0).toUpperCase() + txt.substr(1)
          )
        }

        return text
      }
      Vue[name] = toLower
      Object.defineProperty(Vue.prototype, `$${name}`, {
        value: toLower,
      })
    },
  },
  'ct'
)
