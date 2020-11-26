<template>
  <data-form :id="'0'" />
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
        text: 'Tambah Data Berita',
        to: { name: 'berita-create' },
      },
    ]
  },
}
</script>
