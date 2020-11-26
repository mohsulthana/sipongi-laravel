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

          <v-col cols="12" class="pa-2">
            <v-text-field
              v-model="dataForm.daerah"
              :outlined="!showDetail"
              :readonly="showDetail"
              hide-details="auto"
              :error="dataForm.errors.has('daerah')"
              :error-messages="dataForm.errors.get('daerah')"
              :disabled="dataForm.processing"
              autocomplete="off"
              @input="clearError('daerah')"
            >
              <template v-slot:label>
                Daerah<span class="red--text">*</span>
              </template>
            </v-text-field>
          </v-col>

          <v-col cols="12" class="pa-2">
            <v-text-field
              v-model="dataForm.jumlah_regu"
              :outlined="!showDetail"
              :readonly="showDetail"
              hide-details="auto"
              :error="dataForm.errors.has('jumlah_regu')"
              :error-messages="dataForm.errors.get('jumlah_regu')"
              :disabled="dataForm.processing"
              autocomplete="off"
              @input="clearError('jumlah_regu')"
            >
              <template v-slot:label>
                Jumlah Regu<span class="red--text">*</span>
              </template>
            </v-text-field>
          </v-col>

          <v-col cols="12" class="pa-2">
            <v-text-field
              v-model="dataForm.jumlah_anggota"
              :outlined="!showDetail"
              :readonly="showDetail"
              hide-details="auto"
              :error="dataForm.errors.has('jumlah_anggota')"
              :error-messages="dataForm.errors.get('jumlah_anggota')"
              :disabled="dataForm.processing"
              autocomplete="off"
              @input="clearError('jumlah_anggota')"
            >
              <template v-slot:label>
                Jumlah Anggota<span class="red--text">*</span>
              </template>
            </v-text-field>
          </v-col>

          <v-col cols="12" class="pa-2">
            <v-text-field
              v-model="dataForm.telepon"
              :outlined="!showDetail"
              :readonly="showDetail"
              hide-details="auto"
              :error="dataForm.errors.has('telepon')"
              :error-messages="dataForm.errors.get('telepon')"
              :disabled="dataForm.processing"
              autocomplete="off"
              @input="clearError('telepon')"
            >
              <template v-slot:label>
                Telepon
              </template>
            </v-text-field>
          </v-col>


          <v-col cols="12" class="pa-2">
            <v-text-field
              v-model="dataForm.alamat"
              :outlined="!showDetail"
              :readonly="showDetail"
              hide-details="auto"
              :error="dataForm.errors.has('alamat')"
              :error-messages="dataForm.errors.get('alamat')"
              :disabled="dataForm.processing"
              autocomplete="off"
              @input="clearError('alamat')"
            >
              <template v-slot:label>
                Alamat
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
            :to="{ name: 'daerahDetail', params : {id:$route.params.parent} }"
            :loading="dataForm.processing"
            :disabled="dataForm.processing"
            v-html="$ct('buttons.back')"
          />
          <v-btn
            v-if="!showDetail && $can('update-direktorat_pkhl')"
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
    dataForm: new Form({
      parent_id: null,
      status: true,
      daerah: null,
      telepon: null,
      alamat: null,
      jumlah_regu: null,
      jumlah_anggota: null,
    }),
  }),

  computed: {
    title() {
      return this.id !== '0'
        ? this.showDetail
          ? 'Detail Daerah Operasi'
          : 'Ubah Daerah Operasi'
        : 'Tambah Daerah Operasi'
    },
    submitUrl() {
        return this.id !== '0'
          ? this.$api.path('daerah.update', {
              id: this.id,
            })
          : this.$api.path('daerah')
      // return this.id !== '0'
      //   ? 'http://127.0.0.1:8081/api/manggala-agni/daerah/' + this.id + '/update'
      //   : 'http://127.0.0.1:8081/api/manggala-agni/daerah/store'
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
      //   .get('http://127.0.0.1:8081/api/manggala-agni/daerah/' + this.id + '/edit')
      //   .then(({ data }) => {
      //     this.dataForm.populate(data)
      //   })
      //   .catch((err) => {})
        await this.$http
          .get(
            this.$api.path('daerah.edit', {
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
      this.dataForm.parent_id = this.$route.params.id

      await this.$http
        .post(this.submitUrl, this.dataForm.hasFiles()
            ? objectToFormData(this.dataForm.data())
            : this.dataForm.data())
        .then(async ({ data }) => {
          this.dataForm.successful = true
          if (data.status === true) {
            this.$toasts('success', `${this.title} ${this.$ct('success')}`)

            this.$router.push({ name: 'daerahDetail', params: {id : data.parent_id} })
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
