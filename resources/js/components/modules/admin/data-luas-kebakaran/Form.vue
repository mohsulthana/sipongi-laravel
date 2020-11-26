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
            v-if="id === '0'"
            cols="6"
            class="pa-2"
          >
            <v-select
            v-model="dataForm.provinsi_id"
            :items="provinsi"
            :error="dataForm.errors.has('provinsi_id')"
            :error-messages="dataForm.errors.get('provinsi_id')"
            :disabled="dataForm.processing"
            @input="clearError('provinsi_id')"
            outlined
            >
            <template v-slot:label>
                Provinsi<span class="red--text">*</span>
              </template>
        </v-select>
          </v-col>

          <v-col
            v-else
            cols="6"
            class="pa-2"
          >
            <v-text-field
              v-model="dataForm.provinsi"
              :outlined="!showDetail"
              :readonly="true"
              hide-details="auto"
              :disabled="dataForm.processing"
              autocomplete="off"
            >
              <template v-slot:label>
                Provinsi<span class="red--text">*</span>
              </template>
            </v-text-field>
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
              v-model="dataForm.luas_kebakaran"
              :outlined="!showDetail"
              :readonly="showDetail"
              hide-details="auto"
              :error="dataForm.errors.has('luas_kebakaran')"
              :error-messages="dataForm.errors.get('luas_kebakaran')"
              :disabled="dataForm.processing"
              autocomplete="off"
              @input="clearError('luas_kebakaran')"
            >
              <template v-slot:label>
                Luas Kebakaran<span class="red--text">*</span>
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
            :to="{ name: 'luas-kebakaran' }"
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
    provinsi : [],
    dataForm: new Form({
      provinsi: null,
      provinsi_id: null,
      tahun: null,
      luas_kebakaran: null,
    }),
  }),

  computed: {
    title() {
      return this.id !== '0'
        ? this.showDetail
          ? 'Detail Laporan Harian'
          : 'Ubah Data Luas Area Kebakaran'
        : 'Tambah Data Luas Area Kebakaran'
    },
    submitUrl() {
      return this.id !== '0'
        ? this.$api.path('luasKebakaran.update', {
            id: this.id,
          })
        : this.$api.path('luasKebakaran')
    },
    method() {
      return this.id !== '0' ? 'put' : 'post'
    },
  },

  async created() {
    await this.getProvinsi()
    if (this.id !== '0') {
      await this.loadData()
    }
  },

  methods: {
    async getProvinsi(){
        await this.$http
          .get(
            this.$api.path('hotspotSatelit.provinsi')
          )
          .then(({ data }) => {
            this.provinsi = data
          })
          .catch((err) => {})
    },
    async loadData() {
        await this.$http
          .get(
            this.$api.path('luasKebakaran.edit', {
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

            this.$router.push({ name: 'luas-kebakaran' })
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
