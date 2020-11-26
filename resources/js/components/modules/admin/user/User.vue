<template>
  <v-card outlined>
    <v-toolbar flat color="primary" dark>
      <v-toolbar-title class="font-weight-black">
        List Pengguna
      </v-toolbar-title>
    </v-toolbar>
    <v-card-text>
      <v-row>
        <v-col cols="12" class="text-right">
          <v-btn
            v-if="$can('delete-users') && selected.length > 0"
            dark
            color="red darken-4"
            @click="deleteItem()"
            v-html="$ct('buttons.delete')"
          />
          <v-btn
            color="grey lighten-1"
            @click="filterItem()"
            v-html="$ct('buttons.filter')"
          />
          <v-btn
            v-if="$can('create-users')"
            :to="{ name: 'admin-users-create' }"
            color="primary"
            v-html="$ct('buttons.create')"
          />
        </v-col>
      </v-row>
      <v-card outlined>
        <v-card-text class="pa-0">
          <v-data-table
            v-model="selected"
            must-sort
            :single-expand="true"
            :headers="customHeader"
            :items="listData"
            :options.sync="options"
            :server-items-length="total"
            :loading="loading"
            :footer-props="footerProps"
            :show-select="$can('delete-users') && checkSelectAll"
            fixed-header
            :height="listData.length > 10 ? `480px` : ''"
          >
            <template v-slot:header.data-table-select="{ on, props }">
              <v-simple-checkbox
                v-if="checkSelectAll"
                color="primary"
                v-bind="props"
                v-on="on"
              />
            </template>

            <template
              v-slot:item.data-table-select="{ item, isSelected, select }"
            >
              <v-simple-checkbox
                v-if="item.id !== auth.id && !item.deleted_at"
                color="primary"
                :value="isSelected"
                @input="select($event)"
              />
            </template>

            <template v-slot:item.vAvatar="{ item }">
              <span class="text-no-wrap">
                <v-avatar size="30" color="grey lighten-4">
                  <img
                    :src="[
                      item.avatar_url.status === 'url'
                        ? item.avatar_url.url
                        : item.avatar_url.encoded,
                    ]"
                    alt="avatar"
                  />
                </v-avatar>
              </span>
            </template>
            <template v-slot:item.users.name="{ item }">
              <span :class="`text-no-wrap ${classDeleted(item)}`">
                {{ item.name }}
              </span>
            </template>
            <template v-slot:item.users.username="{ item }">
              <span :class="`text-no-wrap ${classDeleted(item)}`">
                {{ item.username }}
              </span>
            </template>
            <template v-slot:item.users.email="{ item }">
              <span :class="`text-no-wrap ${classDeleted(item)}`">
                {{ item.email }}
              </span>
            </template>
            <template v-slot:item.reg.nama_regional="{ item }">
              <span :class="`text-no-wrap ${classDeleted(item)}`">
                {{ item.regional_name }}
              </span>
            </template>
            <template v-slot:item.pr.nama_provinsi="{ item }">
              <span :class="`text-no-wrap ${classDeleted(item)}`">
                {{ item.provinsi_name }}
              </span>
            </template>
            <template v-slot:item.r.display_name="{ item }">
              <span :class="`text-no-wrap ${classDeleted(item)}`">
                {{ item.role_name }}
              </span>
            </template>
            <template v-slot:item.hr_employee.firstname="{ item }">
              <span :class="`text-no-wrap ${classDeleted(item)}`">
                {{ item.full_name }}
              </span>
            </template>
            <template v-slot:item.users.status="{ item }">
              <span :class="`text-no-wrap ${classDeleted(item)}`">
                {{ item.status ? $ct('enabled') : $ct('disabled') }}
              </span>
            </template>
            <template v-slot:item.users.created_at="{ item }">
              <span :class="`text-no-wrap ${classDeleted(item)}`">
                {{ formatDate(item.created_at) }}
              </span>
            </template>
            <template v-slot:item.users.deleted_at="{ item }">
              <span :class="`text-no-wrap ${classDeleted(item)}`">
                {{ formatDate(item.deleted_at) }}
              </span>
            </template>
            <template v-slot:item.actions="{ item }">
              <span class="text-no-wrap">
                <v-tooltip top>
                  <template v-slot:activator="{ on, attrs }">
                    <v-btn
                      depressed
                      outlined
                      icon
                      fab
                      dark
                      small
                      color="grey darken-2"
                      v-bind="attrs"
                      :to="{
                        name: 'admin-users-detail',
                        params: { id: item.id },
                      }"
                      v-on="on"
                    >
                      <v-icon>visibility</v-icon>
                    </v-btn>
                  </template>
                  <span>Detail</span>
                </v-tooltip>
                <v-tooltip v-if="$can('update-users') && !item.deleted_at" top>
                  <template v-slot:activator="{ on, attrs }">
                    <v-btn
                      depressed
                      outlined
                      icon
                      fab
                      dark
                      small
                      color="grey darken-2"
                      v-bind="attrs"
                      :to="{
                        name: 'admin-users-edit',
                        params: { id: item.id },
                      }"
                      v-on="on"
                    >
                      <v-icon>edit</v-icon>
                    </v-btn>
                  </template>
                  <span>Ubah</span>
                </v-tooltip>
                <v-tooltip v-if="$can('delete-users') && item.deleted_at" top>
                  <template v-slot:activator="{ on, attrs }">
                    <v-btn
                      depressed
                      outlined
                      icon
                      fab
                      dark
                      small
                      color="grey darken-2"
                      v-bind="attrs"
                      v-on="on"
                      @click="restoreItem(item)"
                    >
                      <v-icon>restore</v-icon>
                    </v-btn>
                  </template>
                  <span>Restore</span>
                </v-tooltip>
              </span>
            </template>
          </v-data-table>
        </v-card-text>
      </v-card>
    </v-card-text>

    <filter-dialog @interface="filterDataFeedback" />
  </v-card>
</template>

<script>
import FilterDialog from './FilterDialog'
import { mapGetters, mapState, mapActions } from 'vuex'

export default {
  components: {
    FilterDialog,
  },

  breadcrumb() {
    return [
      {
        text: 'Dashboard',
        to: { name: 'dashboard' },
      },
      {
        text: 'Pengguna',
        to: { name: 'admin-users' },
      },
    ]
  },

  data: () => ({
    selected: [],
    listData: [],
    loading: false,
    listWatch: false,
    options: {
      sortDesc: [false],
      page: 1,
      itemsPerPage: 10,
      sortBy: ['users.name'],
    },
    footerProps: {
      showFirstLastPage: true,
      showCurrentPage: true,
      itemsPerPageOptions: [5, 10, 25, 50, 100],
    },
    total: 0,
  }),

  computed: {
    ...mapGetters({
      auth: 'auth/user',
    }),
    ...mapState('filter', ['filterDialogData']),
    headers() {
      return [
        {
          text: '',
          align: 'center',
          class: 'text-no-wrap',
          sortable: false,
          value: 'vAvatar',
          width: '50px',
        },
        {
          text: 'Nama Lengkap',
          align: 'start',
          class: 'text-no-wrap',
          value: 'users.name',
        },
        {
          text: 'Nama Pengguna',
          align: 'start',
          class: 'text-no-wrap',
          value: 'users.username',
        },
        {
          text: 'Email',
          align: 'start',
          class: 'text-no-wrap',
          value: 'users.email',
        },
        {
          text: 'Regional',
          align: 'start',
          class: 'text-no-wrap',
          value: 'reg.nama_regional',
        },
        {
          text: 'Provinsi',
          align: 'start',
          class: 'text-no-wrap',
          value: 'pr.nama_provinsi',
        },
        {
          text: 'Hak Akses',
          align: 'start',
          class: 'text-no-wrap',
          value: 'r.display_name',
        },
        {
          text: 'Status',
          align: 'start',
          class: 'text-no-wrap',
          value: 'users.status',
        },
        {
          text: 'Dibuat',
          align: 'start',
          class: 'text-no-wrap',
          value: 'users.created_at',
        },
        {
          text: 'Dihapus',
          align: 'start',
          class: 'text-no-wrap',
          value: 'users.deleted_at',
        },
        {
          text: 'Aksi',
          align: 'start',
          class: 'text-no-wrap',
          sortable: false,
          value: 'actions',
        },
      ]
    },
    customHeader() {
      let headers = []
      Array.prototype.push.apply(headers, this.headers)
      if (this.filterDialogData.filters.deleted === 0) {
        headers.splice(9, 1)
      }

      return headers
    },
    checkSelectAll() {
      let res = 0
      this.listData.forEach((item) => {
        if (item.id !== this.auth.id && !item.deleted_at) {
          res = res + 1
        }
      })

      return res > 0
    },
  },

  watch: {
    options: {
      async handler() {
        if (this.listWatch) {
          await this.getLists()
        }
      },
      deep: true,
    },
  },

  async created() {
    this.setFilterDialogDeleted(0)
    await this.getLists()
  },

  methods: {
    ...mapActions('filter', [
      'setFilterDialog',
      'setFilterDialogDeleted',
      'resetFilter',
    ]),
    formatDate(val) {
      return val ? this.$df(val).format('DD MMMM YYYY HH:mm:ss') : '-'
    },
    classDeleted(item) {
      return item.deleted_at ? 'red--text' : null
    },
    async getLists() {
      this.loading = 'primary'
      const direction = this.options.sortDesc[0] ? 'desc' : 'asc'
      let params = {
        direction: this.options.sortDesc[0] ? 'desc' : 'asc',
        sortBy: this.options.sortBy[0],
        page: this.options.page,
        per_page: this.options.itemsPerPage,
        query: this.search,
      }

      Object.assign(params, this.filterDialogData.filters)

      await this.$http
        .get(this.$api.path('user'), {
          params: params,
        })
        .then(({ data }) => {
          if (data.data.length === 0 && this.options.page > 1) {
            this.options.page = this.options.page - 1
            this.getLists()
          }
          this.listData = data.data
          this.total = data.meta.total
        })
        .catch((err) => {})
        .finally(() => {
          this.loading = false
          this.listWatch = true
        })
    },
    async filterItem() {
      this.setFilterDialog(true)
    },
    async filterDataFeedback() {
      await this.getLists()
    },
    restoreItem(item) {
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
              id: item.id,
            })
            .then(async ({ data }) => {
              if (data.status) {
                await self.getLists()
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
    deleteItem() {
      const self = this
      this.$swal({
        title: self.$ct('delete-title'),
        text: self.$ct('delete-text'),
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: self.$ct('buttons.delete-ok'),
        cancelButtonText: self.$ct('buttons.cancel'),
        reverseButtons: false,
      }).then(async (result) => {
        if (result.value) {
          let deletedData = await Promise.all(
            self.selected.filter(function (item) {
              if (item.id !== self.auth.id && !item.deleted_at) {
                return item
              }
            })
          )

          await this.$http
            .post(this.$api.path('user.delete'), {
              deleteSelected: deletedData,
            })
            .then(async ({ data }) => {
              if (data.status) {
                await self.getLists()
                self.$toasts('success', self.$ct('delete-success'))
              } else {
                self.$toasts('error', self.$ct('delete-error'))
              }
            })
            .catch((err) => {})
            .finally(() => {
              self.selected = []
            })
        } else {
          self.$toasts('info', self.$ct('delete-cancel'))
        }
      })
    },
  },
}
</script>
