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
        text: 'Peraturan Perundangan',
        to: { name: 'perpu' },
      },
      {
        text: 'Tambah Data Peraturan Perundangan',
        to: { name: 'perpu-create' },
      },
    ]
  },
}
</script>
