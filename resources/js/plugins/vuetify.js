import 'material-design-icons-iconfont/dist/material-design-icons.css'
import Vue from 'vue'
import Vuetify from 'vuetify'
import VuetifyToast from 'vuetify-toast-snackbar'
import i18n from './i18n'
import LRU from 'lru-cache'
import 'vuetify/dist/vuetify.min.css'

const themeCache = new LRU({
  max: 10,
  maxAge: 1000 * 60 * 60, // 1 hour
})

Vue.use(Vuetify)
Vue.use(VuetifyToast)

export default new Vuetify({
  icons: {
    iconfont: 'md',
  },
  theme: {
    themes: {
      light: {
        primary: '#1AB394',
        secondary: '#424242',
        accent: '#1FCCA9',
        error: '#FF5252',
        info: '#2196F3',
        success: '#4CAF50',
        warning: '#FFC107',
      },
      dark: {
        primary: '#1AB394',
        secondary: '#424242',
        accent: '#1FCCA9',
        error: '#FF5252',
        info: '#2196F3',
        success: '#4CAF50',
        warning: '#FFC107',
      },
    },
    options: {
      minifyTheme: function (css) {
        return css.replace(/[\r\n|\r|\n]/g, '')
      },
      themeCache,
      customProperties: true,
      cspNonce: 'dQw4w9WgXcQ',
    },
  },
  lang: {
    t: (key, ...params) => i18n.t(key, params),
  },
})
