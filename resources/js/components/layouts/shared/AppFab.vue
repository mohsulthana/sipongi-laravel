<template>
  <v-fab-transition>
    <v-btn
      v-show="fab"
      fab="fab"
      dark="dark"
      fixed="fixed"
      bottom="bottom"
      right="right"
      color="primary"
      @click="toTop"
    >
      <v-icon>keyboard_arrow_up</v-icon>
    </v-btn>
  </v-fab-transition>
</template>

<script>
export default {
  name: 'AppFab',

  data: () => ({
    fab: false,
  }),

  created() {
    if (process.browser) {
      window.addEventListener('scroll', this.onScroll)
    }
  },

  methods: {
    onScroll() {
      if (typeof window === 'undefined') return

      const top = window.pageYOffset || document.documentElement.offsetTop || 0

      this.fab = top > 300
    },
    toTop() {
      this.$vuetify.goTo(0)
    },
  },
}
</script>
