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
        text: 'Pengguna',
        to: { name: 'admin-users' },
      },
      {
        text: 'Detail Data Pengguna',
        to: {
          name: 'admin-users-detail',
          params: { id: this.$route.params.id },
        },
      },
    ]
  },
}
</script>
