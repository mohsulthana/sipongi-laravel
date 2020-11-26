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
        text: 'Data Emisi CO2 Tahunan',
        to: { name: 'emisi-co2' },
      },
      {
        text: 'Tambah  Emisi CO2 Tahunan',
        to: { name: 'emisi-co2-create' },
      },
    ]
  },
}
</script>
