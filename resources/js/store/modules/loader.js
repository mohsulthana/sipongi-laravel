const state = () => ({
  drawer: null,
  loader: false,
  disabledLoader: false,
  loaderUpload: false,
  percentase: 0,
})

const getters = {
  loader: (state) => state.loader && !state.disabledLoader,
  loaderUpload: (state) => state.loaderUpload,
  percentase: (state) => state.percentase,
  drawer: (state) => state.drawer,
}

const mutations = {
  LOADER_UPLOAD(state, payload) {
    state.loaderUpload = payload
  },
  PROGRESS_LOADER(state, payload) {
    state.percentase = payload
  },
  DISABLED_LOADER(state, payload) {
    state.disabledLoader = payload
  },
  SET_LOADER(state, payload) {
    state.loader = payload
  },
  SET_DRAWER(state, payload) {
    state.drawer = payload
  },
  TOGGLE_DRAWER(state) {
    state.drawer = !state.drawer
  },
}

const actions = {
  async setLoader({ commit }, val) {
    commit('SET_LOADER', val)
  },
}

export default {
  namespaced: true,
  state,
  getters,
  actions,
  mutations,
}
