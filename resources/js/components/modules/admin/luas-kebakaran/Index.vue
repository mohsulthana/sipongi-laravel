<template>
  <v-card outlined>
    <v-toolbar flat color="primary" dark>
      <v-toolbar-title class="font-weight-black">
        Entri Luas Area Kebakaran
      </v-toolbar-title>
    </v-toolbar>
    <v-card-text>
      <v-form
        lazy-validation
        :valid="dataForm.errors.any()"
        @submit.prevent="submit"
      >
        <v-row>
          <v-col cols="12" md="6">
            <v-autocomplete
              v-model="dataForm.tahun"
              outlined
              hide-details="auto"
              :items="tahun"
              item-text="name"
              item-value="id"
              :error="dataForm.errors.has('tahun')"
              :error-messages="dataForm.errors.get('tahun')"
              :disabled="
                dataForm.processing || !$can('update-entri_luas_area_kebakaran')
              "
              @change="clearError('tahun')"
            >
              <template v-slot:label>
                Tahun<span class="red--text">*</span>
              </template>
              <template v-slot:item="{ item }">
                <v-list-item-content>
                  <v-list-item-title v-html="item.name" />
                </v-list-item-content>
              </template>
            </v-autocomplete>
          </v-col>
        </v-row>
        <v-row>
          <v-col
            v-for="(item, index) in dataForm.provinsi"
            :key="item.id"
            cols="12"
            md="6"
          >
            <v-currency-field
              v-model="item.luas"
              outlined
              hide-details="auto"
              :disabled="
                dataForm.processing ||
                disabled ||
                !$can('update-entri_luas_area_kebakaran')
              "
              autocomplete="off"
              :error="dataForm.errors.has(`provinsi.${index}.luas`)"
              :error-messages="dataForm.errors.get(`provinsi.${index}.luas`)"
              :options="options"
              @change="clearError(`provinsi.${index}.luas`)"
            >
              <template v-slot:label>
                {{ item.nama }}<span class="red--text">*</span>
              </template>
            </v-currency-field>
          </v-col>
        </v-row>
      </v-form>
    </v-card-text>
    <v-card-actions>
      <v-spacer />
      <v-btn
        v-show="disabled && $can('update-entri_luas_area_kebakaran')"
        color="primary"
        :loading="dataForm.processing"
        :disabled="dataForm.processing"
        @click="() => (disabled = !disabled)"
        v-html="$ct('buttons.edit')"
      />
      <v-btn
        v-show="!disabled && $can('update-entri_luas_area_kebakaran')"
        color="primary"
        :loading="dataForm.processing"
        :disabled="dataForm.processing"
        @click="submit()"
        v-html="$ct('buttons.save')"
      />
    </v-card-actions>
  </v-card>
</template>

<script>
import VCurrencyField from '$comp/layouts/shared/VCurrencyField'

export default {
  components: { VCurrencyField },
  breadcrumb() {
    return [
      {
        text: 'Dashboard',
        to: { name: 'dashboard' },
      },
      {
        text: 'Entri Luas Area Kebakaran',
        to: { name: 'entri-luas-kebakaran' },
      },
    ]
  },

  data: () => ({
    dataForm: new Form(
      {
        tahun: null,
        provinsi: [],
      },
      {
        resetOnSuccess: false,
      }
    ),
    tahun: [],
    disabled: true,
    options: {
      currency: {
        prefix: '',
        suffix: ' Ha',
      },
      valueRange: false,
      precision: 3,
      distractionFree: false,
      autoDecimalMode: true,
      valueAsInteger: false,
      allowNegative: false,
    },
  }),

  watch: {
    'dataForm.tahun': {
      async handler() {
        await this.loadData()
      },
    },
  },

  async created() {
    let dariTahun = 1997
    let sampaiTahun = parseInt(this.$df().format('YYYY')) + 1
    for (let x = sampaiTahun; x >= dariTahun; x--) {
      this.tahun.push({
        id: x,
        name: x,
      })
    }

    this.dataForm.tahun = parseInt(this.$df().format('YYYY'))
  },

  methods: {
    async loadData() {
      await this.$http
        .get(this.$api.path('luasKebakaran.edit'), {
          params: {
            year: this.dataForm.tahun,
          },
        })
        .then(({ data }) => {
          this.dataForm.provinsi = data.data
        })
        .catch((err) => {})
    },
    async submit() {
      await this.dataForm
        .post(this.$api.path('luasKebakaran.update'))
        .then(async (data) => {
          if (data.status === true) {
            this.$toasts(
              'success',
              `Luas Kebakaran Tahun ${this.dataForm.tahun} Berhasil Disimpan.`
            )
            this.disabled = true
          }
        })
        .catch((err) => {})
        .finally(() => {})
    },
    clearError(field) {
      this.dataForm.errors.clear(field)
    },
  },
}
</script>
