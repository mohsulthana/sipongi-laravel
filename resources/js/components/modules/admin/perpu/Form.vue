<template>
  <v-card outlined>
    <v-toolbar flat color="primary" dark>
      <v-toolbar-title class="font-weight-black" v-html="title" />
    </v-toolbar>
    <v-card-text>
      <v-form
        lazy-validation
        :valid="dataForm.errors.any()"
        @submit.prevent="submit"
      >
        <v-row>
          <v-col cols="12" class="pa-2" md="6">
            <v-autocomplete
              v-model="dataForm.kategori_id"
              :outlined="!showDetail"
              :readonly="showDetail"
              hide-details="auto"
              :items="kategories"
              clearable
              item-text="name"
              item-value="id"
              :error="dataForm.errors.has('kategori_id')"
              :error-messages="dataForm.errors.get('kategori_id')"
              :disabled="dataForm.processing"
              @change="clearError('kategori_id')"
            >
              <template v-slot:label>
                Kategori<span class="red--text">*</span>
              </template>
              <template v-slot:item="{ item }">
                <v-list-item-content>
                  <v-list-item-title v-html="item.name" />
                </v-list-item-content>
              </template>
            </v-autocomplete>
          </v-col>
          <v-col cols="12" class="pa-2" md="6">
            <v-text-field
              v-model="dataForm.nomor"
              :outlined="!showDetail"
              :readonly="showDetail"
              hide-details="auto"
              :error="dataForm.errors.has('nomor')"
              :error-messages="dataForm.errors.get('nomor')"
              :disabled="dataForm.processing"
              autocomplete="off"
              @input="clearError('nomor')"
            >
              <template v-slot:label>
                Nomor<span class="red--text">*</span>
              </template>
            </v-text-field>
          </v-col>
          <v-col cols="12" class="pa-2" md="6">
            <v-text-field
              v-model="dataForm.title"
              :outlined="!showDetail"
              :readonly="showDetail"
              hide-details="auto"
              :error="dataForm.errors.has('title')"
              :error-messages="dataForm.errors.get('title')"
              :disabled="dataForm.processing"
              autocomplete="off"
              @input="clearError('title')"
            >
              <template v-slot:label>
                Title<span class="red--text">*</span>
              </template>
            </v-text-field>
          </v-col>
          <v-col cols="12" class="pa-2" md="6">
            <v-autocomplete
              v-model="dataForm.tipe"
              :outlined="!showDetail"
              :readonly="showDetail"
              hide-details="auto"
              :items="tipes"
              item-text="name"
              item-value="id"
              :error="dataForm.errors.has('tipe')"
              :error-messages="dataForm.errors.get('tipe')"
              :disabled="dataForm.processing"
              @change="clearError('tipe')"
            >
              <template v-slot:label>
                Tipe<span class="red--text">*</span>
              </template>
              <template v-slot:item="{ item }">
                <v-list-item-content>
                  <v-list-item-title v-html="item.name" />
                </v-list-item-content>
              </template>
            </v-autocomplete>
          </v-col>
          <v-col
            v-if="!showDetail && dataForm.tipe === 'file'"
            cols="12"
            class="pa-2"
            md="6"
          >
            <v-file-input
              v-model="dataForm.file"
              outlined
              hide-details="auto"
              show-size
              prepend-icon=""
              append-icon="attach_file"
              :rules="rules"
              accept="application/pdf"
              hint="File hanya boleh ( PDF ) dan ukuran file harus kurang dari 20 MB!"
              persistent-hint
              :error="dataForm.errors.has('file')"
              :error-messages="dataForm.errors.get('file')"
              @change="clearError('file')"
            >
              <template v-slot:label>
                File<span v-if="dataForm.tipe_old !== 'file'" class="red--text"
                  >*</span
                >
              </template>
            </v-file-input>
          </v-col>
          <v-col
            v-if="!showDetail && dataForm.tipe === 'url'"
            cols="12"
            class="pa-2"
            md="6"
          >
            <v-text-field
              v-model="dataForm.url"
              :outlined="!showDetail"
              :readonly="showDetail"
              hide-details="auto"
              :error="dataForm.errors.has('url')"
              :error-messages="dataForm.errors.get('url')"
              :disabled="dataForm.processing"
              autocomplete="off"
              @input="clearError('url')"
            >
              <template v-slot:label>
                URL<span class="red--text">*</span>
              </template>
            </v-text-field>
          </v-col>
          <v-col
            v-if="showDetail && dataForm.file_url"
            cols="12"
            class="pa-2"
            md="6"
          >
            <v-text-field
              v-model="dataForm.file"
              :readonly="showDetail"
              hide-details="auto"
            >
              <template v-slot:label>
                URL<span class="red--text">*</span>
              </template>
              <template v-slot:append>
                <v-tooltip top>
                  <template v-slot:activator="{ on }">
                    <v-icon
                      v-clipboard="dataForm.file_url"
                      v-clipboard:success="clipboardSuccessHandler"
                      @click="() => true"
                      v-on="on"
                    >
                      content_copy
                    </v-icon>
                  </template>
                  Copy URL
                </v-tooltip>
                <v-tooltip top>
                  <template v-slot:activator="{ on }">
                    <v-btn
                      :href="dataForm.file_url"
                      target="_blank"
                      small
                      icon
                      v-on="on"
                    >
                      <v-icon>open_in_new</v-icon>
                    </v-btn>
                  </template>
                  Open URL
                </v-tooltip>
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
        <v-col cols="12" class="text-right">
          <v-btn
            color="secondary"
            :to="{ name: 'perpu' }"
            :loading="dataForm.processing"
            :disabled="dataForm.processing"
            v-html="$ct('buttons.back')"
          />
          <v-btn
            v-if="!showDetail && $can('update-perpu')"
            color="primary"
            :loading="dataForm.processing"
            :disabled="dataForm.processing"
            @click="submit"
            v-html="$ct('buttons.save')"
          />
          <v-btn
            v-if="showDetail && $can('update-perpu')"
            color="primary"
            :loading="dataForm.processing"
            :disabled="dataForm.processing"
            :to="{
              name: 'perpu-edit',
              params: { id: id },
            }"
            v-html="$ct('buttons.edit')"
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
      kategori_id: null,
      nomor: null,
      title: null,
      file: null,
      tipe: 'file',
      tipe_old: null,
      file_url: null,
      url: null,
    }),
    data: {},
    kategories: [],
  }),

  computed: {
    tipes() {
      return [
        {
          id: 'file',
          name: 'File Upload',
        },
        {
          id: 'url',
          name: 'URL',
        },
      ]
    },
    title() {
      return this.id !== '0'
        ? this.showDetail
          ? 'Detail Data Peraturan Perundangan'
          : 'Ubah Data Peraturan Perundangan'
        : 'Tambah Data Peraturan Perundangan'
    },
    submitUrl() {
      return this.id !== '0'
        ? this.$api.path('perpu.update', {
            id: this.id,
          })
        : this.$api.path('perpu')
    },
    rules() {
      return [
        (value) =>
          !value ||
          value.size < 1024 * 1024 * 20 ||
          'Ukuran file harus kurang dari 20 MB!',
      ]
    },
  },

  watch: {
    'dataForm.tipe': {
      async handler() {
        if (this.dataForm.tipe === 'file') {
          this.dataForm.file = null
        } else {
          if (this.id !== '0' && this.data.tipe === 'url') {
            this.dataForm.url = this.data.file_url
          } else {
            this.dataForm.url = null
          }
        }
      },
    },
  },

  async created() {
    if (this.id !== '0') {
      await this.loadData()
    }

    await this.getKat()
  },

  methods: {
    clipboardSuccessHandler({ value, event }) {
      this.$toasts('success', `${value}`)
    },
    async getKat() {
      await this.$http
        .get(this.$api.path('perpuKat.all'))
        .then(({ data }) => {
          this.kategories = data
        })
        .catch((err) => {})
    },
    async loadData() {
      await this.$http
        .get(
          this.$api.path('perpu.edit', {
            id: this.id,
          })
        )
        .then(({ data }) => {
          this.dataForm.populate(data)
          this.data = data
          this.dataForm.tipe_old = data.tipe
          if (!this.showDetail) {
            this.dataForm.file = null
          }
        })
        .catch((err) => {})
    },
    async submit() {
      this.$store.commit('loader/LOADER_UPLOAD', true)
      this.$store.commit('loader/PROGRESS_LOADER', 0)
      this.dataForm.errors.clear()
      this.dataForm.processing = true
      this.dataForm.successful = false

      await this.$http
        .post(
          this.submitUrl,
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

            this.$router.push({ name: 'perpu' })
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
