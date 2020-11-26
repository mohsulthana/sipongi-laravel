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
        text: 'Data Luas Area Kebakaran',
        to: { name: 'luas-kebakaran' },
      },
      {
        text: 'Ubah Luas Area Kebakaran',
        to: {
          name: 'luas-kebakaran-edit',
          params: { id: this.$route.params.id },
        },
      },
    ]
  },
}
</script>
