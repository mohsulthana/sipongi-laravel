<template>
  <v-overlay
    :value="loader.loader && !loader.disabledLoader"
    color="rgb(26, 179, 148, 0.6)"
    z-index="9999999"
  >
    <v-progress-circular
      v-if="!loader.loaderUpload"
      indeterminate
      :size="100"
      :width="10"
      color="primary"
    />
    <v-progress-circular
      v-if="loader.loaderUpload"
      :rotate="360"
      :size="100"
      :width="15"
      :value="loader.percentase"
      color="primary"
    >
      {{ loader.percentase }}
    </v-progress-circular>
    <h3 class="primary--text" v-html="$ct('loading')" />
  </v-overlay>
</template>

<script>
import { mapState } from 'vuex'

export default {
  computed: {
    ...mapState(['loader']),
  },
}
</script>
