import Vue from 'vue'
import VueSweetalert2 from 'vue-sweetalert2'
import 'sweetalert2/dist/sweetalert2.min.css'

const options = {
  customClass: {
    container: 'v-application',
    confirmButton: 'v-btn v-btn--contained theme--light v-size--small primary',
    cancelButton:
      'ml-2 v-btn v-btn--contained theme--light v-size--small grey lighten-2',
  },
  buttonsStyling: false,
}

Vue.use(VueSweetalert2, options)
