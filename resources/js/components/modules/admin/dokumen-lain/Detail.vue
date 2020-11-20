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
        text: 'Dokumen Lain',
        to: { name: 'dokumen_lain' },
      },
      {
        text: 'Detail Data Dokumen Lain',
        to: {
          name: 'dokumen_lain-detail',
          params: { id: this.$route.params.id },
        },
      },
    ]
  },
}
</script>
