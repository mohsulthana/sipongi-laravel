<template>
  <v-app>
    <!-- Sidebar -->
    <app-nav class="app--drawer" />
    <!-- Sidebar End -->

    <!-- Toolbar Top -->
    <top-menu class="app--toolbar" />
    <!-- Toolbar Top End -->

    <v-main class="mb-5">
      <v-container grid-list-xl fluid>
        <app-breadcrumbs />
        <transition-page>
          <slot />
        </transition-page>
      </v-container>
    </v-main>

    <app-footer />
    <app-fab />
  </v-app>
</template>

<script>
import AppNav from './shared/AppNav'
import TopMenu from './shared/TopMenu'
import AppFooter from './shared/AppFooter'
import AppFab from './shared/AppFab'
import AppBreadcrumbs from './partials/_breadcrumbs'
import TransitionPage from './shared/TransitionPage'

export default {
  name: 'AdminLayout',

  components: {
    AppNav,
    TopMenu,
    AppFooter,
    AppFab,
    AppBreadcrumbs,
    TransitionPage,
  },

  props: {
    source: String,
  },
}
</script>
