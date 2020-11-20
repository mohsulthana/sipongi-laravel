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
            <v-text-field
              v-model="dataForm.name"
              :outlined="!showDetail"
              :readonly="showDetail"
              hide-details="auto"
              :error="dataForm.errors.has('name')"
              :error-messages="dataForm.errors.get('name')"
              :disabled="dataForm.processing"
              autocomplete="off"
              @input="clearError('name')"
            >
              <template v-slot:label>
                Nama Lengkap<span class="red--text">*</span>
              </template>
            </v-text-field>
          </v-col>

          <v-col cols="12" class="pa-2" md="6">
            <v-text-field
              v-model="dataForm.email"
              :outlined="!showDetail"
              :readonly="showDetail"
              hide-details="auto"
              :error="dataForm.errors.has('email')"
              :error-messages="dataForm.errors.get('email')"
              :disabled="dataForm.processing"
              autocomplete="off"
              @input="clearError('email')"
            >
              <template v-slot:label>
                Email
              </template>
            </v-text-field>
          </v-col>

          <v-col cols="12" class="pa-2" md="6">
            <v-autocomplete
              v-model="dataForm.regional_id"
              :outlined="!showDetail"
              :readonly="showDetail"
              hide-details="auto"
              :items="regionals"
              clearable
              item-text="nama_regional"
              item-value="id"
              :error="dataForm.errors.has('regional_id')"
              :error-messages="dataForm.errors.get('regional_id')"
              :disabled="dataForm.processing"
              @change="clearError('regional_id')"
            >
              <template v-slot:label>
                Regional
              </template>
              <template v-slot:item="{ item }">
                <v-list-item-content>
                  <v-list-item-title v-html="item.nama_regional" />
                </v-list-item-content>
              </template>
            </v-autocomplete>
          </v-col>

          <v-col cols="12" class="pa-2" md="6">
            <v-autocomplete
              v-model="dataForm.provinsi_id"
              :outlined="!showDetail"
              :readonly="showDetail"
              hide-details="auto"
              :items="provs"
              clearable
              item-text="nama_provinsi"
              item-value="id"
              :error="dataForm.errors.has('provinsi_id')"
              :error-messages="dataForm.errors.get('provinsi_id')"
              :disabled="
                (dataForm.processing || !dataForm.regional_id) && !showDetail
              "
              @change="clearError('provinsi_id')"
            >
              <template v-slot:label>
                Provinsi
              </template>
              <template v-slot:item="{ item }">
                <v-list-item-content>
                  <v-list-item-title v-html="item.nama_provinsi" />
                </v-list-item-content>
              </template>
            </v-autocomplete>
          </v-col>

          <v-col cols="12" class="pa-2" md="6">
            <v-text-field
              v-model="dataForm.unit_kerja"
              :outlined="!showDetail"
              :readonly="showDetail"
              hide-details="auto"
              :error="dataForm.errors.has('unit_kerja')"
              :error-messages="dataForm.errors.get('unit_kerja')"
              :disabled="dataForm.processing"
              autocomplete="off"
              @input="clearError('unit_kerja')"
            >
              <template v-slot:label>
                Unit Kerja
              </template>
            </v-text-field>
          </v-col>

          <v-col cols="12" class="pa-2">
            <v-textarea
              v-model="dataForm.keterangan"
              :outlined="!showDetail"
              :readonly="showDetail"
              hide-details="auto"
              :error="dataForm.errors.has('keterangan')"
              :error-messages="dataForm.errors.get('keterangan')"
              :disabled="dataForm.processing"
              @input="clearError('keterangan')"
            >
              <template v-slot:label>
                Keterangan
              </template>
            </v-textarea>
          </v-col>

          <v-col cols="12" class="pa-2" md="6">
            <v-text-field
              v-model="dataForm.username"
              :outlined="!showDetail"
              :readonly="showDetail"
              hide-details="auto"
              :error="dataForm.errors.has('username')"
              :error-messages="dataForm.errors.get('username')"
              :disabled="dataForm.processing"
              autocomplete="off"
              @input="clearError('username')"
            >
              <template v-slot:label>
                Nama Pengguna<span class="red--text">*</span>
              </template>
            </v-text-field>
          </v-col>

          <v-col cols="12" class="pa-2" v-if="!showDetail" md="6">
            <v-text-field
              v-model="dataForm.password"
              outlined
              hide-details="auto"
              :error="dataForm.errors.has('password')"
              :error-messages="dataForm.errors.get('password')"
              :disabled="dataForm.processing"
              :append-icon="passwordHidden ? 'visibility_off' : 'visibility'"
              :type="passwordHidden ? 'password' : 'text'"
              autocomplete="new-password"
              @input="clearError('password')"
              @click:append="() => (passwordHidden = !passwordHidden)"
            >
              <template v-slot:label>
                Kata Sandi<span v-if="id === '0'" class="red--text">*</span>
              </template>
            </v-text-field>
          </v-col>

          <v-col cols="12" class="pa-2" md="4">
            <v-switch
              v-model="dataForm.is_super_admin"
              :readonly="showDetail"
              hide-details="auto"
              :error="dataForm.errors.has('is_super_admin')"
              :error-messages="dataForm.errors.get('is_super_admin')"
              :disabled="dataForm.processing"
            >
              <template v-slot:label>
                Super Admin
              </template>
            </v-switch>
          </v-col>

          <v-col cols="12" class="pa-2" v-if="!dataForm.is_super_admin" md="4">
            <v-autocomplete
              v-model="dataForm.role"
              :outlined="!showDetail"
              :readonly="showDetail"
              hide-details="auto"
              :items="roles"
              clearable
              item-text="display_name"
              item-value="name"
              :error="dataForm.errors.has('role')"
              :error-messages="dataForm.errors.get('role')"
              :disabled="dataForm.processing"
              @change="clearError('role')"
            >
              <template v-slot:label>
                Hak Akses<span class="red--text">*</span>
              </template>
              <template v-slot:item="{ item }">
                <v-list-item-content>
                  <v-list-item-title v-html="item.display_name" />
                </v-list-item-content>
              </template>
            </v-autocomplete>
          </v-col>

          <v-col cols="12" class="pa-2" md="4">
            <v-autocomplete
              v-model="dataForm.status"
              :outlined="!showDetail"
              :readonly="showDetail"
              hide-details="auto"
              :items="status"
              item-text="name"
              item-value="id"
              :error="dataForm.errors.has('status')"
              :error-messages="dataForm.errors.get('status')"
              :disabled="dataForm.processing"
              @change="clearError('status')"
            >
              <template v-slot:label>
                Status<span class="red--text">*</span>
              </template>
              <template v-slot:item="{ item }">
                <v-list-item-content>
                  <v-list-item-title v-html="item.name" />
                </v-list-item-content>
              </template>
            </v-autocomplete>
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
            :to="{ name: 'admin-users' }"
            :loading="dataForm.processing"
            :disabled="dataForm.processing"
            v-html="$ct('buttons.back')"
          />
          <v-btn
            v-if="!showDetail && !dataForm.deleted_at && $can('update-users')"
            color="primary"
            :loading="dataForm.processing"
            :disabled="dataForm.processing"
            @click="submit"
            v-html="$ct('buttons.save')"
          />
          <v-btn
            v-if="showDetail && !dataForm.deleted_at && $can('update-users')"
            color="primary"
            :loading="dataForm.processing"
            :disabled="dataForm.processing"
            :to="{
              name: 'admin-users-edit',
              params: { id: id },
            }"
            v-html="$ct('buttons.edit')"
          />
          <v-btn
            v-if="showDetail && dataForm.deleted_at && $can('delete-users')"
            color="primary"
            :loading="dataForm.processing"
            :disabled="dataForm.processing"
            @click="restoreItem"
          >
            Restore
          </v-btn>
        </v-col>
      </v-row>
    </v-card-actions>
  </v-card>
</template>

<script>
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
      name: null,
      email: null,
      regional_id: null,
      provinsi_id: null,
      unit_kerja: null,
      keterangan: null,
      username: null,
      is_super_admin: false,
      password: null,
      role: null,
      status: true,
      deleted_at: null,
    }),
    passwordHidden: false,
    roles: [],
    regionals: [],
    provs: [],
    loadFirstRegional: true,
  }),

  computed: {
    status() {
      return [
        {
          id: true,
          name: this.$ct('enabled'),
        },
        {
          id: false,
          name: this.$ct('disabled'),
        },
      ]
    },
    title() {
      return this.id !== '0'
        ? this.showDetail
          ? 'Detail Data Pengguna'
          : 'Ubah Data Pengguna'
        : 'Tambah Data Pengguna'
    },
    submitUrl() {
      return this.id !== '0'
        ? this.$api.path('user.update', {
            id: this.id,
          })
        : this.$api.path('user')
    },
  },

  watch: {
    'dataForm.is_super_admin': function (val) {
      this.dataForm.role = null
    },
    'dataForm.regional_id': {
      async handler() {
        if (!this.loadFirstRegional) {
          this.dataForm.provinsi_id = null
        }
        this.provs = []
        if (this.dataForm.regional_id) {
          await this.getProvs()
        }
      },
    },
  },

  async created() {
    if (this.id !== '0') {
      await this.loadData()
    }
    await this.getRoles()
    await this.getRegional()
  },

  methods: {
    async getRoles() {
      await this.$http
        .get(this.$api.path('role.all'))
        .then(({ data }) => {
          this.roles = data
        })
        .catch((err) => {})
    },
    async getRegional() {
      await this.$http
        .get(this.$api.path('regional.all'))
        .then(({ data }) => {
          this.regionals = data
        })
        .catch((err) => {})
    },
    async getProvs() {
      await this.$http
        .get(
          this.$api.path('provinsi.byRegional', {
            id: `${this.dataForm.regional_id}`,
          })
        )
        .then(({ data }) => {
          this.provs = data
          this.loadFirstRegional = false
        })
        .catch((err) => {})
    },
    async loadData() {
      await this.$http
        .get(
          this.$api.path('user.edit', {
            id: this.id,
          })
        )
        .then(({ data }) => {
          this.dataForm.populate(data)
          this.dataForm.role = data.role_name_id
        })
        .catch((err) => {})
    },
    async submit() {
      await this.dataForm
        .post(this.submitUrl)
        .then(async (data) => {
          if (data.status === true) {
            this.$toasts('success', `${this.title} ${this.$ct('success')}`)

            this.$router.push({ name: 'admin-users' })
          }
        })
        .catch((err) => {})
    },
    clearError(field) {
      this.dataForm.errors.clear(field)
    },
    restoreItem() {
      const self = this
      this.$swal({
        title: 'Apakah kamu yakin?',
        text: 'Anda akan mengembalikan data ini.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: self.$ct('yes'),
        cancelButtonText: self.$ct('buttons.cancel'),
        reverseButtons: false,
      }).then(async (result) => {
        if (result.value) {
          await this.$http
            .post(this.$api.path('user.restore'), {
              id: self.id,
            })
            .then(async ({ data }) => {
              if (data.status) {
                await self.loadData()
                self.$toasts('success', 'Data berhasil dikembalikan.')
              } else {
                self.$toasts('error', self.$ct('delete-error'))
              }
            })
            .catch((err) => {})
            .finally(() => {
              self.selected = []
            })
        }
      })
    },
  },
}
</script>

<style lang="scss" scoped>
.v-select.v-select--chips:not(.v-text-field--single-line).v-text-field--enclosed::v-deep
  .v-select__selections {
  min-height: 0px;
}
</style>
