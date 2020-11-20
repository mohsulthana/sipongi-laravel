<template>
  <v-row>
    <v-col cols="12">
      <v-breadcrumbs :items="crumbs" class="pa-0">
        <template v-slot:divider>
          <v-icon>forward</v-icon>
        </template>
      </v-breadcrumbs>
    </v-col>
  </v-row>
</template>

<script>
export default {
  name: 'AppBreadcrumbs',

  computed: {
    currentRoute() {
      return this.$route
    },
    crumbs() {
      return this.getBreadcrumb(this.currentRoute)
    },
  },

  methods: {
    getBreadcrumb(route) {
      const matchedRouteRecord = route.matched[route.matched.length - 1]
      const matchedComponent = matchedRouteRecord.components.default
      let bc

      if (typeof matchedComponent === 'function' && !!matchedComponent.super) {
        bc = matchedComponent.options.breadcrumb
      } else {
        bc = matchedComponent.breadcrumb
      }

      let dataBc =
        typeof bc === 'function' ? bc.call(this, this.$route.params) : []

      if (dataBc.length > 0) {
        return dataBc.map(function (el) {
          let rObj = el
          rObj.exact = true

          return rObj
        })
      }

      return dataBc
    },
  },
}
</script>
