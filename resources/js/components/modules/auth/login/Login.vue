<template>
  <v-container id="login" fluid class="fill-height">
    <v-row align="center" justify="center">
      <v-col cols="12" class="pa-2" md="4" sm="8" lg="3" xl="2">
        <v-card class="elevation-1 pa-7">
          <div class="layout column align-center">
            <img src="/images/logo.png" alt="logo" width="150" />
            <h3 class="flex my-4 mb-4 text-center">
              Pusat Pengendalian <br />Kebakaran Hutan & Lahan
            </h3>
            <h5 class="flex my-4 mt-0 mb-0 text-center">
              Silahkan Masuk Menggunakan Akun Anda.
            </h5>
          </div>
          <v-card-text class="pa-0">
            <v-form
              lazy-validation
              :valid="loginForm.errors.any()"
              @submit.prevent="submit"
            >
              <v-container grid-list-md class="px-0">
                <v-row>
                  <v-col cols="12" class="pa-2">
                    <v-text-field
                      v-model="loginForm.username"
                      outlined
                      hide-details="auto"
                      label="Nama Pengguna"
                      :error="loginForm.errors.has('username')"
                      :error-messages="loginForm.errors.get('username')"
                      :disabled="loader"
                      append-icon="person"
                      autocomplete="off"
                      @input="loginForm.errors.clear('username')"
                    />
                  </v-col>
                  <v-col cols="12" class="pa-2">
                    <v-text-field
                      v-model="loginForm.password"
                      outlined
                      hide-details="auto"
                      label="Kata Sandi"
                      :error="loginForm.errors.has('password')"
                      :error-messages="loginForm.errors.get('password')"
                      :append-icon="
                        passwordHidden ? 'visibility_off' : 'visibility'
                      "
                      :type="passwordHidden ? 'password' : 'text'"
                      :disabled="loader"
                      autocomplete="new-password"
                      @click:append="() => (passwordHidden = !passwordHidden)"
                      @input="loginForm.errors.clear('password')"
                    />
                  </v-col>
                </v-row>
              </v-container>

              <v-row class="mt-4 mx-0">
                <v-spacer />

                <!-- <v-btn
                                text
                                :disabled="loading"
                                :to="{
                                    name: 'forgot',
                                    query: { email: form.email }
                                }"
                                color="grey darken-2"
                            >
                                Forgot password?
                            </v-btn> -->

                <v-btn
                  type="submit"
                  :loading="loader"
                  :disabled="loader"
                  color="primary"
                  class="ml-4"
                  v-html="$ct('buttons.login')"
                />
              </v-row>
            </v-form>
          </v-card-text>
        </v-card>

        <!-- <div class="text-center mt-4">
                Don't have an account?
                <router-link :to="{ name: 'register' }">Register</router-link>
            </div> -->
      </v-col>
    </v-row>
  </v-container>
</template>

<script>
import { mapActions, mapMutations, mapGetters, mapState } from 'vuex'

export default {
  data: () => ({
    passwordHidden: true,

    loginForm: new Form({
      username: null,
      password: null,
      type: 'web',
    }),
  }),

  computed: {
    ...mapGetters('loader', ['loader']),
  },

  methods: {
    ...mapActions('auth', ['saveToken']),
    submit() {
      this.loginForm.post(this.$api.path('auth.login')).then(async (data) => {
        this.$toasts('success', 'Berhasil Masuk.')

        await this.saveToken(data)
        this.$router.push({ name: 'dashboard' })
      })
    },
  },
}
</script>

<style lang="scss" scoped>
#login {
  width: 100%;
  position: absolute;
  top: 0;
  left: 0;
  content: '';
  z-index: 0;
  background: url('/images/background.png');
  background-size: contain;
  background-repeat: no-repeat;
  background-position: center;
  background-color: #2f4050;
}
</style>
