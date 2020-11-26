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
        text: 'Running Text',
        to: { name: 'running-text' },
      },
      {
        text: 'Tambah Running Text',
        to: { name: 'running-text-create' },
      },
    ]
  },
}
</script>
