<template>
  <v-card outlined>
    <v-toolbar
      flat
      color="primary"
      dark
    >
      <v-toolbar-title
        class="font-weight-black"
        v-html="title"
      />
    </v-toolbar>
    <v-card-text>
      <v-form
        lazy-validation
        :valid="dataForm.errors.any()"
        @submit.prevent="submit"
      >
        <v-row>
            
          <v-col
            cols="6"
            class="pa-2"
          >
            <v-select
            v-model="dataForm.bulan"
            :items="options"
            :error="dataForm.errors.has('bulan')"
            :error-messages="dataForm.errors.get('bulan')"
            :disabled="dataForm.processing"
            @input="clearError('bulan')"
            outlined
            >
            <template v-slot:label>
                Bulan<span class="red--text">*</span>
              </template>
        </v-select>
          </v-col>

          <v-col
            cols="6"
            class="pa-2"
          >
            <v-text-field
              v-model="dataForm.tahun"
              :outlined="!showDetail"
              :readonly="showDetail"
              hide-details="auto"
              :error="dataForm.errors.has('tahun')"
              :error-messages="dataForm.errors.get('tahun')"
              :disabled="dataForm.processing"
              autocomplete="off"
              @input="clearError('tahun')"
            >
              <template v-slot:label>
                Tahun<span class="red--text">*</span>
              </template>
            </v-text-field>
          </v-col>

          <v-col
            cols="12"
            class="pa-2"
          >
            <v-text-field
              v-model="dataForm.link"
              :outlined="!showDetail"
              :readonly="showDetail"
              hide-details="auto"
              :error="dataForm.errors.has('link')"
              :error-messages="dataForm.errors.get('link')"
              :disabled="dataForm.processing"
              autocomplete="off"
              @input="clearError('link')"
            >
              <template v-slot:label>
                Link NAS<span class="red--text">*</span>
              </template>
            </v-text-field>
          </v-col>

          <v-col cols="12">
            <small class="red--text">{{ $ct('required_field') }}</small>
          </v-col>
        </v-row>
      </v-form>
    </v-card-text>
    <v-card-actions>
      <v-row>
        <v-col
          cols="12"
          class="text-right"
        >
          <v-btn
            color="secondary"
            :to="{ name: 'data-laporan-harian' }"
            :loading="dataForm.processing"
            :disabled="dataForm.processing"
            v-html="$ct('buttons.back')"
          />
          <v-btn
            v-if="!showDetail && $can('update-data_laporan_harian')"
            color="primary"
            :loading="dataForm.processing"
            :disabled="dataForm.processing"
            @click="submit"
            v-html="$ct('buttons.save')"
          />
        </v-col>
      </v-row>
    </v-card-actions>
  </v-card>
</template>

<script>
import { objectToFormData } from 'form-backend-validation/src/util'

export default {
  props: {
    id: {
      type: String,
      default() {
        return ''
      },
    },
    showDetail: {
      type: Boolean,
      default() {
        return false
      },
    },
  },

  data: () => ({
    options: [
        { value: '1', text: 'Januari' },
        { value: '2', text: 'Februari' },
        { value: '3', text: 'Maret' },
        { value: '4', text: 'April' },
        { value: '5', text: 'Mei' },
        { value: '6', text: 'Juni' },
        { value: '7', text: 'Juli' },
        { value: '8', text: 'Agustus' },
        { value: '9', text: 'September' },
        { value: '10', text: 'Oktober' },
        { value: '11', text: 'November' },
        { value: '12', text: 'Desember' },

    ],
    dataForm: new Form({
      bulan: null,
      tahun: null,
      link: null,
    }),
  }),

  computed: {
    title() {
      return this.id !== '0'
        ? this.showDetail
          ? 'Detail Laporan Harian'
          : 'Ubah Data Laporan Harian'
        : 'Tambah Data Laporan Harian'
    },
    submitUrl() {
      return this.id !== '0'
        ? this.$api.path('laporanHarian.update', {
            id: this.id,
          })
        : this.$api.path('laporanHarian')
      // return this.id !== '0'
      //   ? 'http://127.0.0.1:8081/api/laporan-harian/' + this.id + '/update'
      //   : 'http://127.0.0.1:8081/api/laporan-harian/store'
    },
    method() {
      return this.id !== '0' ? 'put' : 'post'
    },
  },

  async created() {
    if (this.id !== '0') {
      await this.loadData()
    }
  },

  methods: {
    async loadData() {
      // await this.$http
      //   .get('http://127.0.0.1:8081/api/laporan-harian/' + this.id + '/edit')
      //   .then(({ data }) => {
      //     this.dataForm.populate(data)
      //   })
      //   .catch((err) => {})
        await this.$http
          .get(
            this.$api.path('laporanHarian.edit', {
              id: this.id,
            })
          )
          .then(({ data }) => {
            this.dataForm.populate(data)
          })
          .catch((err) => {})
    },
    async submit() {
      this.$store.commit('loader/PROGRESS_LOADER', 0)
      this.dataForm.errors.clear()
      this.dataForm.processing = true
      this.dataForm.successful = false

      await this.$http
        .post(this.submitUrl, this.dataForm.data())
        .then(async ({ data }) => {
          this.dataForm.successful = true
          if (data.status === true) {
            this.$toasts('success', `${this.title} ${this.$ct('success')}`)

            this.$router.push({ name: 'data-laporan-harian' })
          }
        })
        .catch((error) => {
          this.dataForm.successful = false
          this.dataForm.onFail(error)
        })
        .finally(async () => {
          await this.$store.commit('loader/SET_LOADER', true)
          await this.delay()
          await this.$store.commit('loader/SET_LOADER', false)
          await this.$store.commit('loader/PROGRESS_LOADER', 0)
          this.dataForm.processing = false
        })
    },
    delay() {
      return new Promise((resolve) => setTimeout(resolve, 300))
    },
    clearError(field) {
      this.dataForm.errors.clear(field)
    },
  },
}
</script>
