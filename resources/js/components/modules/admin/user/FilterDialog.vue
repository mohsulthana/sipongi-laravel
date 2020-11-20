<template>
  <v-dialog
    v-model="filterDialogData.dialog"
    persistent
    :fullscreen="$vuetify.breakpoint.xsOnly"
    scrollable
    max-width="1000px"
  >
    <v-card>
      <v-toolbar flat color="primary" dark class="flex-grow-0">
        <v-toolbar-title class="font-weight-black">
          Filter Pengguna
        </v-toolbar-title>
      </v-toolbar>
      <v-card-text>
        <v-form lazy-validation @submit.prevent="submit">
          <v-container grid-list-md class="px-0">
            <v-row>
              <v-col cols="12" class="pa-2" md="6">
                <v-text-field
                  v-model="filterForm.name"
                  outlined
                  hide-details="auto"
                >
                  <template v-slot:label>
                    Nama Lengkap
                  </template>
                </v-text-field>
              </v-col>
              <v-col cols="12" class="pa-2" md="6">
                <v-text-field
                  v-model="filterForm.username"
                  outlined
                  hide-details="auto"
                >
                  <template v-slot:label>
                    Nama Pengguna
                  </template>
                </v-text-field>
              </v-col>
              <v-col cols="12" class="pa-2" md="6">
                <v-text-field
                  v-model="filterForm.email"
                  outlined
                  hide-details="auto"
                >
                  <template v-slot:label>
                    Email
                  </template>
                </v-text-field>
              </v-col>
              <v-col cols="12" class="pa-2" md="6">
                <v-autocomplete
                  v-model="filterForm.regional_id"
                  outlined
                  hide-details="auto"
                  :items="regionals"
                  clearable
                  item-text="nama_regional"
                  item-value="id"
                  :search-input.sync="searchReg"
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
                  v-model="filterForm.provinsi_id"
                  outlined
                  hide-details="auto"
                  :items="provs"
                  clearable
                  item-text="nama_provinsi"
                  item-value="id"
                  :disabled="!filterForm.regional_id"
                  :search-input.sync="searchProv"
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
                <v-autocomplete
                  v-model="filterForm.role"
                  outlined
                  hide-details="auto"
                  :items="roles"
                  clearable
                  item-text="display_name"
                  item-value="name"
                  :search-input.sync="search"
                >
                  <template v-slot:label>
                    Hak Akses
                  </template>
                  <template v-slot:item="{ item }">
                    <v-list-item-content>
                      <v-list-item-title v-html="item.display_name" />
                    </v-list-item-content>
                  </template>
                </v-autocomplete>
              </v-col>
              <v-col cols="12" class="pa-2" md="6">
                <v-autocomplete
                  v-model="filterForm.status"
                  outlined
                  hide-details="auto"
                  :items="status"
                  clearable
                  item-text="name"
                  item-value="id"
                  :search-input.sync="searchStatus"
                >
                  <template v-slot:label>
                    Status
                  </template>
                  <template v-slot:item="{ item }">
                    <v-list-item-content>
                      <v-list-item-title v-html="item.name" />
                    </v-list-item-content>
                  </template>
                </v-autocomplete>
              </v-col>
              <v-col cols="12" class="pa-2" md="6">
                <v-autocomplete
                  v-model="filterForm.deleted"
                  outlined
                  hide-details="auto"
                  :items="statusDeleted"
                  clearable
                  item-text="name"
                  item-value="id"
                  :search-input.sync="searchDeleted"
                >
                  <template v-slot:label>
                    Termasuk
                  </template>
                  <template v-slot:item="{ item }">
                    <v-list-item-content>
                      <v-list-item-title v-html="item.name" />
                    </v-list-item-content>
                  </template>
                </v-autocomplete>
              </v-col>
            </v-row>
          </v-container>
        </v-form>
      </v-card-text>
      <v-card-actions>
        <v-spacer />
        <v-btn
          small
          color="grey lighten-1"
          @click="reset"
          v-html="$ct('buttons.reset')"
        />
        <v-btn
          small
          color="secondary"
          @click="close"
          v-html="$ct('buttons.cancel')"
        />
        <v-btn
          small
          color="primary"
          @click="submit"
          v-html="$ct('buttons.search')"
        />
      </v-card-actions>
    </v-card>
  </v-dialog>
</template>

<script>
import { api } from '~/config'
import { mapState, mapActions } from 'vuex'

export default {
  name: 'FormDialog',

  data: () => ({
    filterForm: new Form({
      name: null,
      username: null,
      email: null,
      role: null,
      status: null,
      regional_id: null,
      provinsi_id: null,
      deleted: 0,
    }),
    search: '',
    searchStatus: '',
    searchReg: '',
    searchProv: '',
    searchDeleted: '',
    roles: [],
    regionals: [],
    provs: [],
    loadFilter: true,
    loadFirstRegional: true,
  }),

  computed: {
    ...mapState('filter', ['filterDialogData', 'statusDeleted']),
    status() {
      return [
        {
          id: 1,
          name: this.$ct('enabled'),
        },
        {
          id: 0,
          name: this.$ct('disabled'),
        },
      ]
    },
  },

  watch: {
    'filterDialogData.dialog': function (val) {
      if (val) {
        this.loadData()
      }
    },
    'filterForm.regional_id': {
      async handler() {
        if (!this.loadFirstRegional) {
          this.filterForm.provinsi_id = null
        }
        this.provs = []
        if (this.filterForm.regional_id) {
          await this.getProvs()
        }
      },
    },
  },

  methods: {
    ...mapActions('filter', ['setFilterDialog', 'setFilterDialogData']),
    async loadData() {
      if (this.loadFilter) {
        await this.getRoles()
        await this.getRegional()
        this.loadFilter = false
      }

      setTimeout(() => {
        this.filterForm.populate(this.filterDialogData.filters)
        this.search = this.roleFilter(
          this.filterDialogData.filters.role,
          this.roles
        ).display_name
        this.searchStatus = this.customFilter(
          this.filterDialogData.filters.status,
          this.status
        ).name
        this.searchReg = this.customFilter(
          this.filterDialogData.filters.regional_id,
          this.regionals
        ).nama_regional
        this.searchProv = this.customFilter(
          this.filterDialogData.filters.provinsi_id,
          this.provs
        ).nama_provinsi
        this.searchDeleted = this.customFilter(
          this.filterDialogData.filters.deleted,
          this.statusDeleted
        ).name
      }, 300)
    },
    async getRoles() {
      await this.$http
        .get(this.$api.path('role.all'))
        .then(({ data }) => {
          this.roles = [
            {
              name: 'superAdmin',
              display_name: 'Super Admin',
            },
          ]
          Array.prototype.push.apply(this.roles, data)
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
            id: `${this.filterForm.regional_id}`,
          })
        )
        .then(({ data }) => {
          this.provs = data
          this.loadFirstRegional = false
        })
        .catch((err) => {})
    },
    submit() {
      this.oldSearchAutocomplete = this.searchAutocomplete
      this.setFilterDialogData(this.filterForm.data())
      this.$emit('interface')
      this.close()
    },
    close() {
      this.setFilterDialog(false)
      this.loadData()
    },
    reset() {
      this.search = ''
      this.searchStatus = ''
      this.searchReg = ''
      this.searchProv = ''
      this.searchDeleted = ''
      this.filterForm.clear()
      this.filterForm.reset()
    },
    roleFilter(name) {
      let data = this.roles.filter(function (item) {
        return item.name === name
      })

      return data.length > 0 ? data[0] : ''
    },
    customFilter(id, datas) {
      let data = datas.filter(function (item) {
        return item.id === id
      })

      return data.length > 0 ? data[0] : ''
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
