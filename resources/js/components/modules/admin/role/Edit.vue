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
        text: 'Hak Akses',
        to: { name: 'admin-roles' },
      },
      {
        text: 'Ubah Hak Akses',
        to: { name: 'admin-roles-edit', params: { id: this.$route.params.id } },
      },
    ]
  },
}
</script>
