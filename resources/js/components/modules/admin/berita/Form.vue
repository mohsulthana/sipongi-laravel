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
          <v-col cols="12" class="pa-2">
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
          <v-col v-if="showDetail" cols="12" class="pa-0" />
          <v-col cols="12" class="pa-2" md="6">
            <v-switch
              v-model="dataForm.publish"
              :readonly="showDetail"
              hide-details="auto"
              :error="dataForm.errors.has('publish')"
              :error-messages="dataForm.errors.get('publish')"
              :disabled="dataForm.processing"
            >
              <template v-slot:label>
                Publish
              </template>
            </v-switch>
          </v-col>
          <v-col cols="12" class="pa-2">
            <div class="mb-1">
              <strong>
                Content
                <span class="red--text">*</span></strong
              >
            </div>
            <tiny-mce
              v-if="!showDetail"
              v-model="dataForm.desc"
              :init="tinyMCEInit"
            />
            <div v-if="showDetail" v-html="dataForm.desc" />
            <div
              v-if="dataForm.errors.has('desc')"
              class="v-text-field__details mt-2"
            >
              <div class="v-messages theme--light error--text">
                <div class="v-messages__wrapper">
                  <div class="v-messages__message">
                    {{ dataForm.errors.first('desc') }}
                  </div>
                </div>
              </div>
            </div>
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
            :to="{ name: 'berita' }"
            :loading="dataForm.processing"
            :disabled="dataForm.processing"
            v-html="$ct('buttons.back')"
          />
          <v-btn
            v-if="!showDetail && $can('update-berita')"
            color="primary"
            :loading="dataForm.processing"
            :disabled="dataForm.processing"
            @click="submit"
            v-html="$ct('buttons.save')"
          />
          <v-btn
            v-if="showDetail && $can('update-berita')"
            color="primary"
            :loading="dataForm.processing"
            :disabled="dataForm.processing"
            :to="{
              name: 'berita-edit',
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
      title: null,
      image: null,
      desc: null,
      publish: true,
      image_url: null,
    }),
    tinyMCEInit: {
      menubar: false,
      height: 350,
      toolbar_sticky: true,
      plugins: [
        'advlist autolink lists link image charmap print preview anchor',
        'searchreplace visualblocks code fullscreen hr emoticons',
        'insertdatetime media table paste code help wordcount',
      ],
      toolbar: [
        'undo redo | bold italic underline strikethrough | fontselect fontsizeselect formatselect | ' +
          'alignleft aligncenter alignright alignjustify | outdent indent ',
        'numlist bullist | forecolor backcolor removeformat hr | ' +
          'charmap emoticons | image media link table | fullscreen preview print code',
      ],
    },
  }),

  computed: {
    title() {
      return this.id !== '0'
        ? this.showDetail
          ? 'Detail Data Berita'
          : 'Ubah Data Berita'
        : 'Tambah Data Berita'
    },
    submitUrl() {
      return this.id !== '0'
        ? this.$api.path('berita.update', {
            id: this.id,
          })
        : this.$api.path('berita')
    },
    rules() {
      return [
        (value) =>
          !value ||
          value.size < 1024 * 1024 * 2 ||
          'Ukuran file harus kurang dari 2 MB!',
      ]
    },
  },

  async created() {
    if (this.id !== '0') {
      await this.loadData()
    }
  },

  methods: {
    async loadData() {
      await this.$http
        .get(
          this.$api.path('berita.edit', {
            id: this.id,
          })
        )
        .then(({ data }) => {
          this.dataForm.populate(data)
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

            this.$router.push({ name: 'berita' })
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
