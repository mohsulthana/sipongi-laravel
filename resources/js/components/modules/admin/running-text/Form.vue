<template>
  <v-card outlined>
    <v-toolbar flat
color="primary" dark
>
      <v-toolbar-title class="font-weight-black"
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
          <v-col v-if="showDetail"
cols="12" class="pa-0"
/>
          <v-col cols="12"
class="pa-2"
>
            <v-text-field
              v-model="dataForm.text"
              :outlined="!showDetail"
              :readonly="showDetail"
              hide-details="auto"
              :error="dataForm.errors.has('text')"
              :error-messages="dataForm.errors.get('text')"
              :disabled="dataForm.processing"
              autocomplete="off"
              @input="clearError('text')"
            >
              <template v-slot:label>
                Text<span class="red--text">*</span>
              </template>
            </v-text-field>
          </v-col>
                    <v-col cols="12"
class="pa-2" md="6">
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
        <v-col cols="12"
class="text-right"
>
          <v-btn
            color="secondary"
            :to="{ name: 'running-text' }"
            :loading="dataForm.processing"
            :disabled="dataForm.processing"
            v-html="$ct('buttons.back')"
          />
          <v-btn
            v-if="!showDetail && $can('update-running-text')"
            color="primary"
            :loading="dataForm.processing"
            :disabled="dataForm.processing"
            @click="submit"
            v-html="$ct('buttons.save')"
          />
          <v-btn
            v-if="showDetail && $can('update-running-text')"
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
      text: null,
      active: true,
    }),
  }),

  computed: {
    title() {
      return this.id !== '0'
        ? this.showDetail
          ? 'Detail Data Running Text'
          : 'Ubah Data Running Text'
        : 'Tambah Data Running Text'
    },
    submitUrl() {
        return this.id !== '0'
          ? this.$api.path('runningText.update', {
              id: this.id,
            })
          : this.$api.path('runningText')
      // return this.id !== '0'
      //   ? 'http://127.0.0.1:8081/api/running-text/' + this.id + '/update'
      //   : 'http://127.0.0.1:8081/api/running-text/store'
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
      //http://127.0.0.1:8081/api/running-text/' + this.id + '/edit'
      await this.$http
        .get(this.$api.path('runningText.edit', {
            id: this.id,
          })
        )
        .then(({ data }) => {
          this.dataForm.populate(data)
        })
        .catch((err) => {})
      //   await this.$http
      //     .get(
      //       this.$api.path('berita.edit', {
      //         id: this.id,
      //       })
      //     )
      //     .then(({ data }) => {
      //       this.dataForm.populate(data)
      //     })
      //     .catch((err) => {})
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

            this.$router.push({ name: 'running-text' })
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
