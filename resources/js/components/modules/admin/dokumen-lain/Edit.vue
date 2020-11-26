<template>
  <data-form :id="$route.params.id" />
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
        text: 'Ubah Data Berita',
        to: {
          name: 'berita-edit',
          params: { id: this.$route.params.id },
        },
      },
    ]
  },
}
</script>
