import Vue from 'vue'
import VueCurrencyInput from 'vue-currency-input'

const pluginOptions = {
  /* see config reference */
  globalOptions: {
    currency: null,
    valueRange: false,
    precision: 3,
    distractionFree: false,
    autoDecimalMode: true,
    valueAsInteger: false,
    allowNegative: false,
  },
}

Vue.use(VueCurrencyInput, pluginOptions)
