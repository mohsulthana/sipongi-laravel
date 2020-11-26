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
        text: 'Hak Akses',
        to: { name: 'admin-roles' },
      },
      {
        text: 'Tambah Data Hak Akses',
        to: { name: 'admin-roles-create' },
      },
    ]
  },
}
</script>
