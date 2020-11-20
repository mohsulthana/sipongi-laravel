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
        text: 'Hak Akses',
        to: { name: 'admin-roles' },
      },
      {
        text: 'Detail Data Hak Akses',
        to: {
          name: 'admin-roles-detail',
          params: { id: this.$route.params.id },
        },
      },
    ]
  },
}
</script>
