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
            <div class="mb-1">
              <strong>
                Text
                <span class="red--text">*</span></strong
              >
            </div>
            <tiny-mce
              v-if="!showDetail"
              v-model="dataForm.text"
              :init="tinyMCEInit"
            />
            <div v-if="showDetail" v-html="dataForm.text" />
            <div
              v-if="dataForm.errors.has('text')"
              class="v-text-field__details mt-2"
            >
              <div class="v-messages theme--light error--text">
                <div class="v-messages__wrapper">
                  <div class="v-messages__message">
                    {{ dataForm.errors.first('text') }}
                  </div>
                </div>
              </div>
            </div>
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

          <v-col
            cols="12"
            class="pa-2"
          >
            <v-switch
              v-model="dataForm.active"
              :readonly="showDetail"
              hide-details="auto"
              :error="dataForm.errors.has('active')"
              :error-messages="dataForm.errors.get('active')"
              :disabled="dataForm.processing"
            >
              <template v-slot:label>
              Active
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
            :to="{ name: 'disclaimer' }"
            :loading="dataForm.processing"
            :disabled="dataForm.processing"
            v-html="$ct('buttons.back')"
          />
          <v-btn
            v-if="!showDetail && $can('update-disclaimer')"
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
      text: null,
      active: true,
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
          ? 'Detail Disclaimer'
          : 'Ubah Disclaimer'
        : 'Tambah Disclaimer'
    },
    submitUrl() {
        return this.id !== '0'
          ? this.$api.path('disclaimer.update', {
              id: this.id,
            })
          : this.$api.path('disclaimer')
      // return this.id !== '0'
      //   ? 'http://127.0.0.1:8081/api/disclaimer/' + this.id + '/update'
      //   : 'http://127.0.0.1:8081/api/disclaimer/store'
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
      //   .get('http://127.0.0.1:8081/api/disclaimer/' + this.id + '/edit')
      //   .then(({ data }) => {
      //     this.dataForm.populate(data)
      //   })
      //   .catch((err) => {})
        await this.$http
          .get(
            this.$api.path('disclaimer.edit', {
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

            this.$router.push({ name: 'disclaimer' })
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
