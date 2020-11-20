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
            v-model="dataForm.provinsi_id"
            :items="provinsi"
            :error="dataForm.errors.has('provinsi_id')"
            :error-messages="dataForm.errors.get('provinsi_id')"
            :disabled="dataForm.processing"
            @input="clearError('provinsi_id')"
            v-on:change="getKabupaten"
            outlined
            >
            <template v-slot:label>
                Provinsi<span class="red--text">*</span>
              </template>
             </v-select>
          </v-col>

          <v-col
            cols="6"
            class="pa-2"
          >
            <v-select
            v-model="dataForm.kotakab_id"
            :items="kabupaten"
            :error="dataForm.errors.has('kotakab_id')"
            :error-messages="dataForm.errors.get('kotakab_id')"
            :disabled="dataForm.processing"
            @input="clearError('kotakab_id')"
            v-on:change="getKecamatan"
            outlined
            >
            <template v-slot:label>
                Kabupaten<span class="red--text">*</span>
              </template>
             </v-select>
          </v-col>

          <v-col
            cols="6"
            class="pa-2"
          >
            <v-select
            v-model="dataForm.kecamatan_id"
            :items="kecamatan"
            :error="dataForm.errors.has('kecamatan_id')"
            :error-messages="dataForm.errors.get('kecamatan_id')"
            :disabled="dataForm.processing"
            @input="clearError('kecamatan_id')"
            v-on:change="getKelurahan"
            outlined
            >
            <template v-slot:label>
                Kecamatan<span class="red--text">*</span>
              </template>
             </v-select>
          </v-col>

          <v-col
            cols="6"
            class="pa-2"
          >
            <v-select
            v-model="dataForm.kelurahan_id"
            :items="kelurahan"
            :error="dataForm.errors.has('kelurahan_id')"
            :error-messages="dataForm.errors.get('kelurahan_id')"
            :disabled="dataForm.processing"
            @input="clearError('kelurahan_id')"
            outlined
            >
            <template v-slot:label>
                Kelurahan<span class="red--text">*</span>
              </template>
             </v-select>
          </v-col>
        
          <v-col
            cols="6"
            class="pa-2"
          >
            <v-text-field
              v-model="dataForm.sumber"
              :outlined="!showDetail"
              :readonly="showDetail"
              hide-details="auto"
              :error="dataForm.errors.has('sumber')"
              :error-messages="dataForm.errors.get('sumber')"
              :disabled="dataForm.processing"
              autocomplete="off"
              @input="clearError('sumber')"
            >
              <template v-slot:label>
                Sumber<span class="red--text">*</span>
              </template>
            </v-text-field>
          </v-col>

          <v-col
            cols="6"
            class="pa-2"
          >
            <v-text-field
              v-model="dataForm.source"
              :outlined="!showDetail"
              :readonly="showDetail"
              hide-details="auto"
              :error="dataForm.errors.has('source')"
              :error-messages="dataForm.errors.get('source')"
              :disabled="dataForm.processing"
              autocomplete="off"
              @input="clearError('source')"
            >
              <template v-slot:label>
                Sumber Satelit<span class="red--text">*</span>
              </template>
            </v-text-field>
          </v-col>

          <v-col
            cols="6"
            class="pa-2"
          >
            <v-text-field
              v-model="dataForm.y"
              :outlined="!showDetail"
              :readonly="showDetail"
              hide-details="auto"
              :error="dataForm.errors.has('y')"
              :error-messages="dataForm.errors.get('y')"
              :disabled="dataForm.processing"
              autocomplete="off"
              @input="clearError('y')"
            >
              <template v-slot:label>
                Latitude<span class="red--text">*</span>
              </template>
            </v-text-field>
          </v-col>

          <v-col
            cols="6"
            class="pa-2"
          >
            <v-text-field
              v-model="dataForm.x"
              :outlined="!showDetail"
              :readonly="showDetail"
              hide-details="auto"
              :error="dataForm.errors.has('x')"
              :error-messages="dataForm.errors.get('x')"
              :disabled="dataForm.processing"
              autocomplete="off"
              @input="clearError('x')"
            >
              <template v-slot:label>
                Longitude<span class="red--text">*</span>
              </template>
            </v-text-field>
          </v-col>

          <v-col
            cols="6"
            class="pa-2"
          >
            <v-select
            v-model="dataForm.confidence"
            :items="optionConfidence"
            :error="dataForm.errors.has('confidence')"
            :error-messages="dataForm.errors.get('confidence')"
            :disabled="dataForm.processing"
            @input="clearError('confidence')"
            outlined
            >
            <template v-slot:label>
                Confidence Level<span class="red--text">*</span>
              </template>
             </v-select>
          </v-col>

          <v-col
            cols="6"
            class="pa-2"
          >
            <v-text-field
              v-model="dataForm.brightness"
              :outlined="!showDetail"
              :readonly="showDetail"
              hide-details="auto"
              :error="dataForm.errors.has('brightness')"
              :error-messages="dataForm.errors.get('brightness')"
              :disabled="dataForm.processing"
              autocomplete="off"
              @input="clearError('brightness')"
            >
              <template v-slot:label>
                Brightness<span class="red--text">*</span>
              </template>
            </v-text-field>
          </v-col>

          <v-col
            cols="12"
            sm="6"
            md="4"
          >
            <v-menu
              :close-on-content-click="true"
              :nudge-right="40"
              transition="scale-transition"
              offset-y
              min-width="290px"
            >
              <template v-slot:activator="{ on, attrs }">
                <v-text-field
                  v-model="dataForm.date_hotspot"
                  readonly
                  v-bind="attrs"
                  v-on="on"
                >
                <template v-slot:label>
                Tanggal<span class="red--text">*</span>
                </template>
                </v-text-field>
              </template>
              <v-date-picker
                v-model="dataForm.date_hotspot"
              ></v-date-picker>
            </v-menu>
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
            :to="{ name: 'satelit-hotspot' }"
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
    kabupaten: [],
    kecamatan: [],
    kelurahan: [],
    
    optionConfidence: [
      {
        value : 7,
        text: 'low'
      },
      {
        value : 8,
        text: 'medium'
      },
      {
        value : 9,
        text: 'high'
      }
    ],
    
    dataForm: new Form({
      provinsi_id: null,
      kotakab_id: null,
      kecamatan_id: null,
      kelurahan_id: null,
      x: null,
      y: null,
      sumber: null,
      source: null,
      confidence: null,
      //confidence_level: null,
      brightness: null,
      date_hotspot: null,
    }),
  }),

  computed: {
    title() {
      return this.id !== '0'
        ? this.showDetail
          ? 'Detail Titik Panas'
          : 'Ubah Data Titik Panas'
        : 'Tambah Data Titik Panas'
    },
    submitUrl() {
      return this.id !== '0'
        ? this.$api.path('hotspotSatelit.update', {
            id: this.id,
          })
        : this.$api.path('hotspotSatelit')
      // return this.id !== '0'
      //   ? 'http://127.0.0.1:8082/api/hotspot/satelit/' + this.id + '/update'
      //   : 'http://127.0.0.1:8082/api/hotspot/satelit'
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
    getKabupaten(provinsi_id){
        this.$http
        .get(
          this.$api.path('hotspotSatelit.kabupaten',{id: provinsi_id})
        )
        .then(({ data }) => {
          this.kabupaten = data
        })
        .catch((err) => {})
    },
    getKecamatan(kabupaten_id){
        this.$http
        .get(
          this.$api.path('hotspotSatelit.kecamatan',{id: kabupaten_id})
        )
        .then(({ data }) => {
          this.kecamatan = data
        })
        .catch((err) => {})
    },
    getKelurahan(kecamatan_id){
        this.$http
        .get(
          this.$api.path('hotspotSatelit.kelurahan',{id: kecamatan_id})
        )
        .then(({ data }) => {
          this.kelurahan = data
        })
        .catch((err) => {})
    },
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

            this.$router.push({ name: 'satelit-hotspot' })
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
