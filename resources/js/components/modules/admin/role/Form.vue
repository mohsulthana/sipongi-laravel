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
              v-model="dataForm.display_name"
              :outlined="!showDetail"
              hide-details="auto"
              :error="dataForm.errors.has('display_name')"
              :error-messages="dataForm.errors.get('display_name')"
              :disabled="dataForm.processing"
              :readonly="showDetail"
              @input="clearError('display_name')"
            >
              <template v-slot:label>
                Nama Hak Akses<span class="red--text">*</span>
              </template>
            </v-text-field>
          </v-col>
          <v-col cols="12" class="pa-2" md="6">
            <v-text-field
              v-model="dataForm.name"
              :outlined="!showDetail"
              hide-details="auto"
              :error="dataForm.errors.has('name')"
              :error-messages="dataForm.errors.get('name')"
              readonly
              @input="clearError('name')"
            >
              <template v-slot:label>
                Slug<span class="red--text">*</span>
              </template>
            </v-text-field>
          </v-col>

          <v-col cols="12">
            <small class="red--text">{{ $ct('required_field') }}</small>
          </v-col>
        </v-row>

        <v-row>
          <v-col cols="12">
            <v-toolbar flat>
              <v-toolbar-title class="body-1 font-weight-regular">
                <span class="font-weight-black"> Permission </span><br />
                <span class="font-weight-regular caption">
                  Aktifkan atau nonaktifkan izin dan pilih akses ke modul.
                </span>
              </v-toolbar-title>
              <v-spacer />
              <v-col cols="12" md="1" class="pa-2 text-center">
                <small
                  >{{ dataForm.selectedPermissions.length }} of
                  {{ permissionsCount }}</small
                ><br />
                <v-progress-linear
                  height="5"
                  class="ma-0"
                  color="primary"
                  :value="
                    (dataForm.selectedPermissions.length * 100) /
                    permissionsCount
                  "
                />
              </v-col>
            </v-toolbar>
            <v-card-text class="px-0 pb-0">
              <v-list dense class="px-0 pt-0">
                <template v-for="module in getPerm">
                  <v-divider :key="`${module.id}_divider`" />
                  <v-list-item :key="module.id" class="pa-0">
                    <v-list-item-content>
                      <v-row class="px-5">
                        <v-col cols="12" md="4" class="py-2 align-self-center">
                          {{ module.display_name }}
                        </v-col>

                        <v-col
                          v-for="permission in module.permissions"
                          :key="permission.id"
                          cols="12"
                          md="2"
                          class="py-2 align-self-center"
                        >
                          <v-switch
                            v-model="dataForm.selectedPermissions"
                            class="mt-0 pt-0"
                            :label="permission.display_name"
                            color="primary"
                            :value="permission.id"
                            :disabled="
                              (permission.display_name !== 'Akses' &&
                                module.disabled) ||
                              showDetail
                            "
                            hide-details
                          />
                        </v-col>
                      </v-row>
                    </v-list-item-content>
                  </v-list-item>
                </template>
              </v-list>
            </v-card-text>
          </v-col>
        </v-row>
      </v-form>
    </v-card-text>
    <v-card-actions>
      <v-row>
        <v-col cols="12" class="text-right">
          <v-btn
            color="secondary"
            :to="{ name: 'admin-roles' }"
            :loading="dataForm.processing"
            :disabled="dataForm.processing"
            v-html="$ct('buttons.back')"
          />
          <v-btn
            v-if="!showDetail && $can('update-roles')"
            color="primary"
            :loading="dataForm.processing"
            :disabled="dataForm.processing"
            @click="submit"
            v-html="$ct('buttons.save')"
          />
          <v-btn
            v-if="showDetail && $can('update-roles')"
            color="primary"
            :loading="dataForm.processing"
            :disabled="dataForm.processing"
            :to="{
              name: 'admin-roles-edit',
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
      display_name: null,
      name: null,
      selectedPermissions: [],
    }),
    permissionsCount: 0,
    modules: [],
    panel: 0,
  }),

  computed: {
    title() {
      return this.id !== '0'
        ? this.showDetail
          ? 'Detail Data Hak Akses'
          : 'Ubah Data Hak Akses'
        : 'Tambah Data Hak Akses'
    },
    submitUrl() {
      return this.id !== '0'
        ? this.$api.path('role.update', {
            id: this.id,
          })
        : this.$api.path('role')
    },
    getPerm() {
      this.modules.forEach((module) => {
        if (module.permissions) {
          module.permissions.forEach((permission) => {
            if (permission.display_name === 'Akses') {
              let index = this.dataForm.selectedPermissions.indexOf(
                permission.id
              )

              if (index < 0) {
                module.disabled = true
                module.permissions.forEach((permission2) => {
                  let index2 = this.dataForm.selectedPermissions.indexOf(
                    permission2.id
                  )
                  if (index !== index2) {
                    this.dataForm.selectedPermissions.splice(index2, 1)
                  }
                })
              } else {
                module.disabled = false
              }
            }
          })
        }
      })

      return this.modules
    },
  },

  watch: {
    'dataForm.display_name': function (val) {
      if (val) {
        this.dataForm.name = val
          .toString()
          .toLowerCase()
          .replace(/\s+/g, '-') // Replace spaces with -
          .replace(/[^\w\-]+/g, '') // Remove all non-word chars
          .replace(/\-\-+/g, '-') // Replace multiple - with single -
          .replace(/^-+/, '') // Trim - from start of text
          .replace(/-+$/, '')
      } else {
        this.dataForm.name = null
      }
    },
  },

  async created() {
    if (this.id !== '0') {
      await this.loadData()
    }
    await this.getModulesPermissions()
    await this.getPermissionsCount()
  },

  methods: {
    async getModulesPermissions() {
      await this.$http
        .get(this.$api.path('role.permissions'))
        .then((response) => {
          this.modules = response.data.data
        })
    },
    async getPermissionsCount() {
      await this.$http
        .get(this.$api.path('role.permissions.count'))
        .then(({ data }) => {
          this.permissionsCount = data
        })
    },
    async loadData() {
      await this.$http
        .get(
          this.$api.path('role.edit', {
            id: this.id,
          })
        )
        .then(({ data }) => {
          this.dataForm.populate(data)
          this.dataForm.selectedPermissions = data.permission_ids
        })
        .catch((err) => {})
    },
    async submit() {
      await this.dataForm
        .post(this.submitUrl)
        .then(async (data) => {
          if (data.status === true) {
            this.$toasts('success', `${this.title} ${this.$ct('success')}`)

            this.$router.push({ name: 'admin-roles' })
          }
        })
        .catch((err) => {})
    },
    clearError(field) {
      this.dataForm.errors.clear(field)
    },
  },
}
</script>

<style lang="scss" scoped>
.v-expansion-panel-content::v-deep .v-expansion-panel-content__wrap {
  padding: 0;
}
</style>
