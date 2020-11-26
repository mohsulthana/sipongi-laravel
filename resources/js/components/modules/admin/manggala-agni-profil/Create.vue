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
        text: 'Manggala Agni Profil',
        to: { name: 'profil-text' },
      },
      {
        text: 'Tambah',
        to: { name: 'profil-create' },
      },
    ]
  },
}
</script>
