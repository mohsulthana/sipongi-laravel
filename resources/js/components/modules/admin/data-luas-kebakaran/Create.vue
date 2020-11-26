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
        text: 'Data Luas Area Kebakaran',
        to: { name: 'luas-kebakaran' },
      },
      {
        text: 'Tambah Luas Area Kebakaran',
        to: { name: 'luas-kebakaran-create' },
      },
    ]
  },
}
</script>
