import Vue from 'vue'
const Cookies = require('js-cookie')

const state = () => ({
  user: {
    username: '',
    avatar_url: {},
  },
  token: Cookies.get('__tn'),
  refresh_token: Cookies.get('__rt'),
})

const getters = {
  user: (state) => state.user,
  check: (state) => state.user.username !== '',
  token: (state) => state.token,
  refresh_token: (state) => state.refresh_token,
}

const mutations = {
  SET_USER(state, payload) {
    state.user = payload
  },
  SET_FULL_NAME(state, payload) {
    state.user.full_name = payload
  },
  SET_AVATAR(state, payload) {
    state.user.avatar_url = payload
  },
  LOGOUT(state) {
    state.user = {
      username: '',
      avatar_url: {},
    }
    state.token = ''
    state.refresh_token = ''
    Cookies.remove('__tn')
    Cookies.remove('__rt')
  },
  FETCH_USER_FAILURE(state) {
    state.user = {
      username: '',
      avatar_url: {},
    }
    Cookies.remove('__tn')
    Cookies.remove('__rt')
  },
  SET_TOKEN(state, payload) {
    state.token = payload.access_token
    state.refresh_token = payload.refresh_token
    Cookies.set('__tn', payload.access_token, {
      // expires: payload.expires_in / 86400
      expires: 1,
    })
    Cookies.set('__rt', payload.refresh_token, {
      // expires: payload.expires_in / 86400
      expires: 1,
    })
  },
}

const actions = {
  async saveToken({ commit }, payload) {
    commit('SET_TOKEN', payload)
  },

  async fetchUser({ commit }) {
    commit('loader/DISABLED_LOADER', true, {
      root: true,
    })
    try {
      const { data } = await Vue.http.get(Vue.api.path('auth.me'))
      commit('SET_USER', data)
    } catch (e) {
      commit('FETCH_USER_FAILURE')
    }
    commit('loader/DISABLED_LOADER', false, {
      root: true,
    })
  },

  async setUser({ commit }, payload) {
    commit('SET_USER', payload)
  },

  async logout({ commit }) {
    await Vue.http.post(Vue.api.path('auth.logout'))
    commit('LOGOUT')
  },

  async destroy({ commit }) {
    commit('LOGOUT')
  },
}

export default {
  namespaced: true,
  state,
  getters,
  actions,
  mutations,
}
