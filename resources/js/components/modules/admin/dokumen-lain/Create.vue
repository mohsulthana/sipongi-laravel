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
        text: 'Dokumen Lain',
        to: { name: 'dokumen_lain' },
      },
      {
        text: 'Tambah Data Dokumen Lain',
        to: { name: 'dokumen_lain-create' },
      },
    ]
  },
}
</script>
