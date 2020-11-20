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
        text: 'Running Text',
        to: { name: 'running-text' },
      },
      {
        text: 'Ubah Running Text',
        to: {
          name: 'running-text-edit',
          params: { id: this.$route.params.id },
        },
      },
    ]
  },
}
</script>
