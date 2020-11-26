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
        text: 'Manggala Agni Profil',
        to: { name: 'daerah' },
      },
      {
        text: 'Ubah',
        to: {
          name: 'daerah-edit',
          params: { id: this.$route.params.id },
        },
      },
    ]
  },
}
</script>
