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
        text: 'Sarana & Prasarana',
        to: { name: 'sarpras-text' },
      },
      {
        text: 'Tambah',
        to: { name: 'sarpras-create' },
      },
    ]
  },
}
</script>
