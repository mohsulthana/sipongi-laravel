<template>
  <v-text-field
    ref="textField"
    v-currency="options"
    v-bind="attrs"
    @input="$emit('input', $ci.getValue(inputRef))"
  >
    <template v-for="(index, name) in $slots" v-slot:[name]>
      <slot :name="name" />
    </template>
  </v-text-field>
</template>

<script>
import { setValue } from 'vue-currency-input'

export default {
  name: 'VCurrencyField',
  props: {
    value: {
      type: Number,
      default: null,
    },
    options: {
      type: Object,
      default: () => {},
    },
  },
  data() {
    return {
      inputRef: null,
    }
  },
  computed: {
    attrs() {
      // eslint-disable-next-line
      const { value, ...attrs } = this.$attrs // all but input event
      return attrs
    },
  },
  watch: {
    value(value) {
      setValue(this.inputRef, value)
    },
  },
  mounted() {
    this.inputRef = this.$refs.textField.$refs.input
    setValue(this.inputRef, this.value)
  },
}
</script>
