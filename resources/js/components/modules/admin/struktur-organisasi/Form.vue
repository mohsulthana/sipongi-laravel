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
                  v-model="dataForm.date"
                  label="Tanggal"
                  readonly
                  v-bind="attrs"
                  v-on="on"
                ></v-text-field>
              </template>
              <v-date-picker
                v-model="dataForm.date"
              ></v-date-picker>
            </v-menu>
          </v-col>

            <v-col v-if="!showDetail" cols="12" class="pa-2" md="6">
            <v-file-input
              v-model="dataForm.image"
              outlined
              hide-details="auto"
              show-size
              prepend-icon=""
              append-icon="image"
              :rules="rules"
              accept="image/png, image/jpeg"
              hint="File hanya boleh ( PNG | JPEG ) dan ukuran file harus kurang dari 2 MB!"
              persistent-hint
              :error="dataForm.errors.has('image')"
              :error-messages="dataForm.errors.get('image')"
              @change="clearError('image')"
            >
              <template v-slot:label>
                Gambar<span v-if="id === '0'" class="red--text">*</span>
              </template>
            </v-file-input>
          </v-col>
          <v-col v-if="showDetail" cols="12" class="pa-2" md="4">
            <div class="mb-1">
              <strong>
                Gambar
                <span v-if="id === '0'" class="red--text">*</span></strong
              >
            </div>
            <v-card>
              <v-img contain :src="dataForm.image_url" max-height="300px" />
            </v-card>
          </v-col>

          <v-col cols="12" class="pa-2" md="6">
            <v-switch
              v-model="dataForm.active"
              :readonly="showDetail"
              hide-details="auto"
              :error="dataForm.errors.has('active')"
              :error-messages="dataForm.errors.get('active')"
              :disabled="dataForm.processing"
            >
              <template v-slot:label>
                Akfif
              </template>
            </v-switch>
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
            :to="{ name: 'struktur-organisasi' }"
            :loading="dataForm.processing"
            :disabled="dataForm.processing"
            v-html="$ct('buttons.back')"
          />
          <v-btn
            v-if="!showDetail && $can('update-struktur_organisasi')"
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
      date: new Date().toISOString().substr(0, 10),
      image: null,
      image_url: null,
      active: true,
    }),
  }), 

  computed: {
    title() {
      return this.id !== '0'
        ? this.showDetail
          ? 'Detail Struktur Organisasi'
          : 'Ubah Struktur Organisasi'
        : 'Tambah Struktur Organisasi'
    },
    rules() {
      return [
        (value) =>
          !value ||
          value.size < 1024 * 1024 * 2 ||
          'Ukuran file harus kurang dari 2 MB!',
      ]
    },
    submitUrl() {
      return this.id !== '0'
          ? this.$api.path('sturkturOrganisasi.update', {
              id: this.id,
            })
          : this.$api.path('sturkturOrganisasi')
      // return this.id !== '0'
      //   ? 'http://127.0.0.1:8081/api/struktur-organisasi/' + this.id + '/update'
      //   : 'http://127.0.0.1:8081/api/struktur-organisasi/store'
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
      //   .get('http://127.0.0.1:8081/api/struktur-organisasi/' + this.id + '/edit')
      //   .then(({ data }) => {
      //     this.dataForm.populate(data)
      //   })
      //   .catch((err) => {})
        await this.$http
          .get(
            this.$api.path('sturkturOrganisasi.edit', {
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
        .post(this.submitUrl, 
        this.dataForm.hasFiles()
            ? objectToFormData(this.dataForm.data())
            : this.dataForm.data(),
          {
            onUploadProgress: function (progressEvent) {
              //DATA TERSEBUT AKAN DI ASSIGN KE VARIABLE progressBar
              this.$store.commit(
                'loader/PROGRESS_LOADER',
                parseInt(
                  Math.round((progressEvent.loaded * 100) / progressEvent.total)
                )
              )
            }.bind(this),
          }    
        )
        .then(async ({ data }) => {
          this.dataForm.successful = true
          if (data.status === true) {
            this.$toasts('success', `${this.title} ${this.$ct('success')}`)

            this.$router.push({ name: 'struktur-organisasi' })
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
          await this.$store.commit('loader/LOADER_UPLOAD', false)
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
