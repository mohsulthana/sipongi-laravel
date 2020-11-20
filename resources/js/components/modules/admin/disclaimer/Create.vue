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
        text: 'Disclaimer',
        to: { name: 'disclaimer' },
      },
      {
        text: 'Tambah Disclaimer',
        to: { name: 'disclaimer-create' },
      },
    ]
  },
}
</script>
