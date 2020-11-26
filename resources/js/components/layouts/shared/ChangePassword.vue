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
        <v-toolbar-title class="font-weight-black">
          Ubah Kata Sandi
        </v-toolbar-title>
      </v-toolbar>
      <v-card-text>
        <v-form
          lazy-validation
          :valid="dataForm.errors.any()"
          @submit.prevent="submit"
        >
          <v-container grid-list-md class="px-0">
            <v-row>
              <v-col
                v-if="value.default_pass"
                cols="12"
                class="pa-2 font-weight-medium"
              >
                Anda menggunakan kata sandi standar sistem, Harap ubah kata
                sandi terlebih dahulu untuk melanjutkan.<br />
                Terima kasih.
              </v-col>
              <v-col cols="12" class="pa-2">
                <v-text-field
                  v-model="dataForm.current"
                  outlined
                  hide-details="auto"
                  :error="dataForm.errors.has('current')"
                  :error-messages="dataForm.errors.get('current')"
                  :disabled="dataForm.processing"
                  :append-icon="
                    passwordHidden ? 'visibility_off' : 'visibility'
                  "
                  :type="passwordHidden ? 'password' : 'text'"
                  autocomplete="new-password"
                  @input="clearError('current')"
                  @click:append="() => (passwordHidden = !passwordHidden)"
                >
                  <template v-slot:label>
                    Kata Sandi Saat Ini<span class="red--text">*</span>
                  </template>
                </v-text-field>
              </v-col>
              <v-col cols="12" class="pa-2">
                <v-text-field
                  v-model="dataForm.password"
                  outlined
                  hide-details="auto"
                  :error="dataForm.errors.has('password')"
                  :error-messages="dataForm.errors.get('password')"
                  :disabled="dataForm.processing"
                  :append-icon="
                    passwordHidden2 ? 'visibility_off' : 'visibility'
                  "
                  :type="passwordHidden2 ? 'password' : 'text'"
                  autocomplete="new-password"
                  @input="clearError('password')"
                  @click:append="() => (passwordHidden2 = !passwordHidden2)"
                >
                  <template v-slot:label>
                    Kata Sandi Baru<span class="red--text">*</span>
                  </template>
                </v-text-field>
              </v-col>
              <v-col cols="12" class="pa-2">
                <v-text-field
                  v-model="dataForm.password_confirmation"
                  outlined
                  hide-details="auto"
                  :error="dataForm.errors.has('password_confirmation')"
                  :error-messages="dataForm.errors.get('password_confirmation')"
                  :disabled="dataForm.processing"
                  :append-icon="
                    passwordHidden3 ? 'visibility_off' : 'visibility'
                  "
                  :type="passwordHidden3 ? 'password' : 'text'"
                  autocomplete="new-password"
                  @input="clearError('password_confirmation')"
                  @click:append="() => (passwordHidden3 = !passwordHidden3)"
                >
                  <template v-slot:label>
                    Konfirmasi Kata Sandi Baru<span class="red--text">*</span>
                  </template>
                </v-text-field>
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
          v-if="!value.default_pass"
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
          :disabled="dataForm.processing"
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
      default: () => ({}),
    },
  },

  data: () => ({
    dataForm: new Form({
      current: null,
      password: null,
      password_confirmation: null,
    }),
    passwordHidden: false,
    passwordHidden2: false,
    passwordHidden3: false,
  }),

  watch: {
    'value.dialog': function (val) {
      val || this.close()
    },
  },

  methods: {
    async submit() {
      await this.dataForm
        .post(this.$api.path('auth.changePass'))
        .then(async (data) => {
          if (data.status === true) {
            this.$toasts('success', `Ubah Password ${this.$ct('success')}`)

            if (this.value.default_pass) {
              this.$store.dispatch('auth/fetchUser')
            }

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
