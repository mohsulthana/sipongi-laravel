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
        text: 'Direktorat PKHL',
        to: { name: 'direktorat-pkhl' },
      },
      {
        text: 'Tambah Direktorat PKHL',
        to: { name: 'direktorat-pkhl-create' },
      },
    ]
  },
}
</script>
