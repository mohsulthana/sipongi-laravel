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
        text: 'Pengguna',
        to: { name: 'admin-users' },
      },
      {
        text: 'Tambah Data Pengguna',
        to: { name: 'admin-users-create' },
      },
    ]
  },
}
</script>
