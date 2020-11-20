const state = () => ({
  filterDialogData: {
    dialog: false,
    filters: {
      deleted: 0,
    },
  },
  statusDeleted: [
    {
      id: 0,
      name: 'Hanya Data Saat Ini',
    },
    {
      id: 1,
      name: 'Data Saat Ini dan Data Yang Dihapus',
    },
    {
      id: 2,
      name: 'Hanya Data Yang Dihapus',
    },
  ],
})

const getters = {
  filterDialogData: (state) => state.filterDialogData,
}

const mutations = {
  SET_FILTER_DIALOG(state, payload) {
    state.filterDialogData.dialog = payload
  },
  SET_FILTER_DIALOG_DATA(state, payload) {
    state.filterDialogData.filters = payload
  },
  SET_FILTER_DIALOG_DELETED(state, payload) {
    state.filterDialogData.filters.deleted = payload
  },
  RESET_FILTER(state, payload) {
    state.filterDialogData = {
      dialog: false,
      filters: {
        deleted: 0,
      },
    }
  },
}

const actions = {
  async setFilterDialog({ commit }, payload) {
    commit('SET_FILTER_DIALOG', payload)
  },
  async setFilterDialogData({ commit }, payload) {
    commit('SET_FILTER_DIALOG_DATA', payload)
  },
  async setFilterDialogDeleted({ commit }, payload) {
    commit('SET_FILTER_DIALOG_DELETED', payload)
  },
  async resetFilter({ commit }, payload) {
    commit('RESET_FILTER', payload)
  },
}

export default {
  namespaced: true,
  state,
  getters,
  actions,
  mutations,
}
