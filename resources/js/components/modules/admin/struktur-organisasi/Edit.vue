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
        text: 'Data Laporan Harian',
        to: { name: 'data-laporan-harian' },
      },
      {
        text: 'Ubah Data Laporan Harian',
        to: {
          name: 'data-laporan-harian-edit',
          params: { id: this.$route.params.id },
        },
      },
    ]
  },
}
</script>
