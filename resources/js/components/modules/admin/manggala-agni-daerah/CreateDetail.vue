<template>
  <data-form :id="'0'" />
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
        text: 'Tambah',
        to: { name: 'daerahDetail-create' },
      },
    ]
  },
}
</script>
