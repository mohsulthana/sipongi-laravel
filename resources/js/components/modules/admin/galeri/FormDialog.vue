<template>
  <v-dialog
    v-model="value.dialog"
    persistent
    :fullscreen="$vuetify.breakpoint.xsOnly"
    scrollable
    max-width="720px"
  >
    <v-card>
      <v-toolbar flat color="primary" dark>
        <v-toolbar-title class="font-weight-black">
          Tambah Gambar
        </v-toolbar-title>
      </v-toolbar>
      <v-card-text>
        <v-form
          ref="form"
          v-model="isFormValid"
          lazy-validation
          :valid="value.dataForm.errors.any()"
          @submit.prevent="submit"
        >
          <v-container grid-list-md class="px-0">
            <v-row>
              <v-col cols="12" class="pa-2">
                <v-text-field
                  v-model="value.dataForm.keterangan"
                  outlined
                  hide-details="auto"
                  :error="value.dataForm.errors.has('keterangan')"
                  :error-messages="value.dataForm.errors.get('keterangan')"
                  :disabled="value.dataForm.processing"
                  @input="clearError('keterangan')"
                >
                  <template v-slot:label>
                    Keterangan
                    <span class="red--text">*</span>
                  </template>
                </v-text-field>
              </v-col>
              <v-col cols="12" class="pa-2">
                <v-file-input
                  v-model="value.dataForm.image"
                  outlined
                  counter
                  multiple
                  clearable
                  hide-details="auto"
                  show-size
                  prepend-icon=""
                  append-icon="image"
                  :rules="rules"
                  accept="image/png, image/jpeg"
                  hint="File hanya boleh ( PNG | JPEG ) dan ukuran per file harus kurang dari 2 MB!"
                  persistent-hint
                  :error="value.dataForm.errors.has('image')"
                  :error-messages="value.dataForm.errors.get('image')"
                  @change="clearError('image')"
                >
                  <template v-slot:label>
                    Gambar<span class="red--text">*</span>
                  </template>
                  <template v-slot:selection="{ index, text }">
                    <v-chip
                      v-if="index < 2"
                      color="primary"
                      dark
                      label
                      small
                      close
                      @click:close="deleteChip(index, text)"
                    >
                      {{ text }}
                    </v-chip>

                    <span
                      v-else-if="index === 2"
                      class="overline grey--text text--darken-3 mx-2"
                    >
                      +{{ value.dataForm.image.length - 2 }} File(s)
                    </span>
                  </template>
                </v-file-input>
              </v-col>

              <v-col cols="12" class="pt-5">
                <small class="primary--text">{{ $ct('required_field') }}</small>
              </v-col>
            </v-row>
          </v-container>
        </v-form>
      </v-card-text>
      <v-card-actions>
        <v-spacer />
        <v-btn
          small
          color="secondary"
          :loading="value.dataForm.processing"
          :disabled="value.dataForm.processing"
          @click="close"
          v-html="$ct('buttons.cancel')"
        />
        <v-btn
          small
          color="primary"
          :loading="value.dataForm.processing"
          :disabled="value.dataForm.processing || !isFormValid"
          @click="submit"
          v-html="$ct('buttons.save')"
        />
      </v-card-actions>
    </v-card>
  </v-dialog>
</template>

<script>
import { objectToFormData } from 'form-backend-validation/src/util'
export default {
  name: 'FormDialog',

  props: {
    value: {
      required: true,
      type: Object,
      default: () => ({}),
    },
  },

  data: () => ({
    isFormValid: true,
  }),

  computed: {
    rules() {
      return [
        (value) =>
          !value ||
          this.checkFile(value) ||
          'Ukuran per file harus kurang dari 2 MB!',
      ]
    },
  },

  methods: {
    checkFile(files) {
      let res = true
      files.forEach((file) => {
        if (file) {
          if (file.size > 1024 * 1024 * 2) {
            res = false
          }
        }
      })
      return res
    },
    deleteChip(index, text) {
      this.value.dataForm.image.splice(index, 1)
    },
    async submit() {
      this.$store.commit('loader/LOADER_UPLOAD', true)
      this.$store.commit('loader/PROGRESS_LOADER', 0)
      this.value.dataForm.errors.clear()
      this.value.dataForm.processing = true
      this.value.dataForm.successful = false

      await this.$http
        .post(
          this.$api.path('galeriDetail'),
          this.value.dataForm.hasFiles()
            ? objectToFormData(this.value.dataForm.data())
            : this.value.dataForm.data(),
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
          this.value.dataForm.successful = true
          if (data.status === true) {
            this.$toasts('success', `Tambah gambar berhasil.`)

            this.$emit('interface')
            this.close()
          }
        })
        .catch((error) => {
          this.value.dataForm.successful = false
          this.value.dataForm.onFail(error)
        })
        .finally(async () => {
          await this.$store.commit('loader/SET_LOADER', true)
          await this.delay()
          await this.$store.commit('loader/SET_LOADER', false)
          await this.$store.commit('loader/LOADER_UPLOAD', false)
          await this.$store.commit('loader/PROGRESS_LOADER', 0)
          this.value.dataForm.processing = false
        })
    },
    delay() {
      return new Promise((resolve) => setTimeout(resolve, 300))
    },
    close() {
      this.value.dialog = false
      setTimeout(() => {
        this.value.dataForm.clear()
        this.value.dataForm.reset()
        this.$refs.form.resetValidation()
        this.value.editedIndex = false
        this.value.editData = {}
      }, 300)
    },
    clearError(field) {
      this.value.dataForm.errors.clear(field)
      this.$refs.form.resetValidation()
    },
  },
}
</script>
