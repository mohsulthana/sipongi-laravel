<template>
  <v-dialog
    v-model="value.dialog"
    persistent
    :fullscreen="$vuetify.breakpoint.xsOnly"
    scrollable
    max-width="720px"
  >
    <v-card>
      <v-toolbar flat color="primary" dark class="flex-grow-0">
        <v-toolbar-title class="font-weight-black" v-html="formTitle" />
      </v-toolbar>
      <v-card-text>
        <v-form
          lazy-validation
          :valid="value.dataForm.errors.any()"
          @submit.prevent="submit"
        >
          <v-container grid-list-md class="px-0">
            <v-row>
              <v-col cols="12" class="pa-2">
                <v-text-field
                  v-model="value.dataForm.title"
                  outlined
                  hide-details="auto"
                  :error="value.dataForm.errors.has('title')"
                  :error-messages="value.dataForm.errors.get('title')"
                  :disabled="value.dataForm.processing"
                  @input="clearError('title')"
                >
                  <template v-slot:label>
                    Title
                    <span class="red--text">*</span>
                  </template>
                </v-text-field>
              </v-col>

              <v-col cols="12" class="pa-2">
                <v-autocomplete
                  v-model="value.dataForm.tipe"
                  outlined
                  hide-details="auto"
                  :items="tipes"
                  clearable
                  item-text="name"
                  item-value="id"
                  :error="value.dataForm.errors.has('tipe')"
                  :error-messages="value.dataForm.errors.get('tipe')"
                  :disabled="value.dataForm.processing"
                  :search-input.sync="searchTipe"
                  @change="clearError('tipe')"
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
          :disabled="value.dataForm.processing"
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
    searchTipe: '',
  }),

  computed: {
    formTitle() {
      return this.value.editedIndex ? 'Ubah Data Album' : 'Tambah Data Album'
    },
    submitUrl() {
      return this.value.editedIndex
        ? this.$api.path('galeri.update', {
            id: this.value.editData.id,
          })
        : this.$api.path('galeri')
    },
    tipes() {
      return [
        {
          id: 'Pemadaman',
          name: 'Pemadaman',
        },
        {
          id: 'Lainnya',
          name: 'Lainnya',
        },
      ]
    },
  },

  watch: {
    'value.dialog': function (val) {
      val || this.close()
    },
  },

  methods: {
    async submit() {
      await this.value.dataForm
        .post(this.submitUrl)
        .then(async (data) => {
          if (data.status === true) {
            this.$toasts('success', `${this.formTitle} ${this.$ct('success')}`)

            this.$emit('interface', data.data)
            this.close()
          }
        })
        .catch((err) => {})
    },
    close() {
      this.value.dialog = false
      setTimeout(() => {
        this.value.dataForm.clear()
        this.value.dataForm.reset()
        this.value.editedIndex = false
      }, 300)
    },
    clearError(field) {
      this.value.dataForm.errors.clear(field)
    },
  },
}
</script>
