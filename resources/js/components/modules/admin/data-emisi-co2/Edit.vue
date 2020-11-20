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
        text: 'Data Emisi CO2 Tahunan',
        to: { name: 'emisi-co2' },
      },
      {
        text: 'Ubah Emisi CO2 Tahunan',
        to: {
          name: 'emisi-co2-edit',
          params: { id: this.$route.params.id },
        },
      },
    ]
  },
}
</script>
