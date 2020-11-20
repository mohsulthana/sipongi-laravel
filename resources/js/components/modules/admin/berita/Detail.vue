<template>
  <data-form :id="$route.params.id" :show-detail="true" />
</template>

<script>
import dataForm from './Form'

export default {
  components: { dataForm },

  breadcrumb() {
    return [
      {
        text: 'Dashboard',
        to: { name: 'dashboard' },
      },
      {
        text: 'Berita',
        to: { name: 'berita' },
      },
      {
        text: 'Detail Data Berita',
        to: {
          name: 'berita-detail',
          params: { id: this.$route.params.id },
        },
      },
    ]
  },
}
</script>
