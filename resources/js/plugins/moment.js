import Vue from 'vue'
import moment from 'moment'

Vue.use(
  {
    install: function (Vue, name = 'moment') {
      moment.locale('id')
      Vue[name] = moment
      Object.defineProperty(Vue.prototype, `$${name}`, {
        value: moment,
      })
    },
  },
  'df'
)
