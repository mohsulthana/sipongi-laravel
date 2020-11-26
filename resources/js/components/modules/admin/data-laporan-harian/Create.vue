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
        text: 'Data Laporan Harian',
        to: { name: 'data-laporan-harian' },
      },
      {
        text: 'Tambah Data Laporan Harian',
        to: { name: 'data-laporan-harian-create' },
      },
    ]
  },
}
</script>
