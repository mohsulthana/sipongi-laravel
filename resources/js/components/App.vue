<template>
  <v-app id="app" light>
    <component :is="layout">
      <router-view />
    </component>
    <!-- set progressbar -->
    <vue-progress-bar />
    <loader />
  </v-app>
</template>

<script>
const default_layout = 'default'

import Loader from './layouts/partials/_loader'
import AdminLayout from './layouts/Admin'
import DefaultLayout from './layouts/Default'

export default {
  components: {
    Loader,
    AdminLayout,
    DefaultLayout,
  },

  computed: {
    layout() {
      return (this.$route.meta.layout || default_layout) + '-layout'
    },
  },
}
</script>
