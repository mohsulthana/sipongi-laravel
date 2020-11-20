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
        text: 'Peraturan Perundangan',
        to: { name: 'perpu' },
      },
      {
        text: 'Ubah Data Peraturan Perundangan',
        to: {
          name: 'perpu-edit',
          params: { id: this.$route.params.id },
        },
      },
    ]
  },
}
</script>
