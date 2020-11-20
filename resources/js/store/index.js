import Vue from 'vue'
import Vuex from 'vuex'

import auth from './modules/auth.js'
import loader from './modules/loader.js'
import filter from './modules/filter.js'

Vue.use(Vuex)

const store = new Vuex.Store({
  modules: {
    auth,
    loader,
    filter,
  },
})

export default store
