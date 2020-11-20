<template>
  <data-form :id="$route.params.id" />
</template>

<script>
import dataForm from './FormDetail'

export default {
  components: { dataForm },
  breadcrumb() {
    return [
      {
        text: 'Dashboard',
        to: { name: 'dashboard' },
      },
      {
        text: 'Manggala Agni Daerah Operasi',
        to: { name: 'daerah-text' },
      },
      {
        text: 'Detail',
        to: { name: 'daerahDetail' },
      },
      {
        text: 'Ubah',
        to: {
          name: 'daerahDetail-edit',
          params: { id: this.$route.params.id },
        },
      },
    ]
  },
}
</script>
