<template>
  <v-dialog
    v-model="value.dialog"
    persistent
    :fullscreen="$vuetify.breakpoint.xsOnly"
    scrollable
    max-width="500px"
  >
    <v-card>
      <v-toolbar flat color="primary" dark class="flex-grow-0">
        <v-toolbar-title
          class="font-weight-black"
          v-html="$ct('change-profile-picture')"
        />
      </v-toolbar>
      <v-card-text>
        <v-form
          v-model="isFormValid"
          lazy-validation
          :valid="dataForm.errors.any()"
          @submit.prevent="submit"
        >
          <v-container grid-list-md class="px-0">
            <v-row>
              <v-col cols="12" class="text-center pa-2">
                <v-avatar size="128px">
                  <img :src="imageUrl" alt="avatar" />
                </v-avatar>
              </v-col>
              <v-col cols="12" class="pa-2">
                <v-file-input
                  v-model="dataForm.file"
                  outlined
                  hide-details="auto"
                  show-size
                  prepend-icon=""
                  append-icon="mdi-camera"
                  :rules="rules"
                  accept="image/png, image/jpeg"
                  :hint="$ct('avatar-hint', { size: '2' })"
                  persistent-hint
                  :error="dataForm.errors.has('file')"
                  :error-messages="dataForm.errors.get('file')"
                  @change="clearError('file')"
                >
                  <template v-slot:label>
                    Foto profil<span class="red--text">*</span>
                  </template>
                </v-file-input>
              </v-col>

              <v-col cols="12" class="pt-5">
                <small class="red--text">{{ $ct('required_field') }}</small>
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
          :loading="dataForm.processing"
          :disabled="dataForm.processing"
          @click="close"
          v-html="$ct('buttons.cancel')"
        />
        <v-btn
          small
          color="primary"
          :loading="dataForm.processing"
          :disabled="dataForm.processing || !isFormValid"
          @click="submit"
          v-html="$ct('buttons.save')"
        />
      </v-card-actions>
    </v-card>
  </v-dialog>
</template>

<script>
export default {
  name: 'FormDialog',

  props: {
    value: {
      required: true,
      type: Object,
      default: () => ({
        avatar_url: {},
      }),
    },
  },

  data: () => ({
    dataForm: new Form(
      {
        file: null,
      },
      {
        resetOnSuccess: false,
      }
    ),
    isFormValid: true,
    imageUrl: null,
  }),

  computed: {
    rules() {
      return [
        (value) =>
          !value ||
          value.size < 1024 * 1024 * 2 ||
          this.$ct('avatar-limit-size', { size: '2' }),
      ]
    },
  },

  watch: {
    'value.dialog': function (val) {
      val || this.close()
    },
    'value.avatar_url': function (val) {
      this.imageUrl = [
        this.value.avatar_url.status === 'url'
          ? this.value.avatar_url.url
          : this.value.avatar_url.encoded,
      ]
    },
    'dataForm.file': function (val) {
      const files = this.dataForm.file
      this.imageUrl = [
        this.value.avatar_url.status === 'url'
          ? this.value.avatar_url.url
          : this.value.avatar_url.encoded,
      ]
      if (
        files !== undefined &&
        this.dataForm.file !== '' &&
        this.dataForm.file !== null
      ) {
        const fr = new FileReader()
        fr.readAsDataURL(files)
        fr.addEventListener('load', () => {
          this.imageUrl = fr.result
        })
      }
    },
  },

  methods: {
    async submit() {
      await this.dataForm
        .post(this.$api.path('auth.changeAvatar'))
        .then(async (data) => {
          if (data.status === true) {
            this.$toasts(
              'success',
              `${this.$ct('change-profile-picture')} ${this.$ct('success')}`
            )

            this.$store.dispatch('auth/fetchUser')
            this.close()
          }
        })
        .catch((err) => {})
    },
    close() {
      this.value.dialog = false
      setTimeout(() => {
        this.dataForm.clear()
        this.dataForm.reset()
      }, 300)
    },
    clearError(field) {
      this.dataForm.errors.clear(field)
    },
  },
}
</script>
