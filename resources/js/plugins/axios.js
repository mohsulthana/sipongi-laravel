import Vue from 'vue'
import axios from 'axios'
import store from '~/store/index'
import router from '~/router/index'

axios.interceptors.request.use(
  (config) => {
    store.commit('loader/SET_LOADER', true)

    config.headers['X-Requested-With'] = 'XMLHttpRequest'

    const token = store.getters['auth/token']
    if (token) {
      config.headers['Authorization'] = 'Bearer ' + token
    }

    return config
  },
  (error) => {
    store.commit('loader/SET_LOADER', false)
    return Promise.reject(error)
  }
)

axios.interceptors.response.use(
  (response) => {
    store.commit('loader/SET_LOADER', false)

    return response
  },
  async (error) => {
    store.commit('loader/SET_LOADER', false)
    if (store.getters['auth/refresh_token']) {
      // TODO: Find more reliable way to determine when Token state
      if (
        error.response.status === 401 &&
        error.response.data.message === 'unauthorized.'
      ) {
        const { data } = await axios.post(Vue.api.path('auth.refresh'), {
          refresh_token: store.getters['auth/refresh_token'],
        })
        store.dispatch('auth/saveToken', data)
        return axios.request(error.config)
      }

      if (
        error.response.status === 400 &&
        error.response.data.message === 'invalid token.'
      ) {
        store.dispatch('auth/destroy')
        router.push({
          name: 'login',
        })
      }
    }

    if (error.response.status !== 401 && error.response.status !== 422) {
      if (error.response.status === 403) {
        Vue.swal({
          icon: 'error',
          title: 'Oops...',
          text: error.response.data.message,
          allowOutsideClick: false,
          showCancelButton: false,
          confirmButtonText: Vue.ct('buttons.close'),
        })
      } else if (error.response.status === 404) {
        Vue.swal({
          icon: 'error',
          title: 'Oops...',
          text: error.response.data.message,
          allowOutsideClick: false,
          showCancelButton: false,
          confirmButtonText: Vue.ct('buttons.close'),
        })
      } else if (error.response.status === 405) {
        Vue.swal({
          icon: 'error',
          title: 'Oops...',
          text: error.response.data.message,
          allowOutsideClick: false,
          showCancelButton: false,
          confirmButtonText: Vue.ct('buttons.close'),
        })
      } else if (error.response.status >= 500) {
        Vue.swal({
          icon: 'error',
          title: 'Oops...',
          text: error.response.data.message,
          allowOutsideClick: false,
          showCancelButton: false,
          confirmButtonText: Vue.ct('buttons.close'),
        })
      } else {
        if (
          error.response.status !== 400 &&
          error.response.data.message !== 'invalid token.'
        ) {
          Vue.swal({
            icon: 'error',
            title: 'Oops...',
            text: error.response.data.message,
            allowOutsideClick: false,
            showCancelButton: false,
            confirmButtonText: Vue.ct('buttons.close'),
          })
        }
      }

      // error.response.data.message !== undefined && app.$toast.error(error.response.data.message || 'Something went wrong.')
      // error.response.data.error !== undefined && app.$toast.error(error.response.data.error || 'Error occurred.')
    }

    return Promise.reject(error)
  }
)

Vue.use(
  {
    install: function (Vue, name = 'axios') {
      Vue[name] = axios
      Object.defineProperty(Vue.prototype, `$${name}`, {
        value: axios,
      })
    },
  },
  'http'
)
