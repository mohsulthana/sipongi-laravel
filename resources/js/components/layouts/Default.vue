<template>
  <transition-page>
    <slot />
  </transition-page>
</template>

<script>
import TransitionPage from './shared/TransitionPage'
export default {
  name: 'DefaultLayout',

  components: {
    TransitionPage,
  },
}
</script>
