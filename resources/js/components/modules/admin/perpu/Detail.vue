<template>
  <data-form :id="$route.params.id" :show-detail="true" />
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
        text: 'Detail Data Peraturan Perundangan',
        to: {
          name: 'perpu-detail',
          params: { id: this.$route.params.id },
        },
      },
    ]
  },
}
</script>
