<template>
  <v-card outlined>
    <v-toolbar flat color="primary" dark>
      <v-toolbar-title class="font-weight-black">
        List Hak Akses
      </v-toolbar-title>
    </v-toolbar>
    <v-card-text>
      <v-row>
        <v-col cols="12" class="text-right">
          <v-btn
            v-if="$can('delete-roles') && selected.length > 0"
            dark
            color="red darken-4"
            @click="deleteItem()"
            v-html="$ct('buttons.delete')"
          />
          <v-btn
            v-if="$can('create-roles')"
            :to="{ name: 'admin-roles-create' }"
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
            :headers="headers"
            :items="listData"
            :options.sync="options"
            :server-items-length="total"
            :loading="loading"
            :footer-props="footerProps"
            :show-select="$can('delete-roles')"
          >
            <template v-slot:header.data-table-select="{ on, props }">
              <v-simple-checkbox color="primary" v-bind="props" v-on="on" />
            </template>

            <template v-slot:item.data-table-select="{ isSelected, select }">
              <v-simple-checkbox
                color="primary"
                :value="isSelected"
                @input="select($event)"
              />
            </template>

            <template v-slot:item.user_using="{ item }">
              <span class="text-no-wrap">
                <v-row>
                  <v-avatar
                    v-for="(user, index) in item.users"
                    :key="index"
                    size="24px"
                    :class="index > 0 ? 'avatar-role' : ''"
                  >
                    <img
                      :src="[
                        user.avatar_url.status === 'url'
                          ? user.avatar_url.url
                          : user.avatar_url.encoded,
                      ]"
                      alt="avatar"
                    />
                  </v-avatar>
                  <span v-if="item.users_count > 4"
                    >&nbsp;+{{ item.users_count - 4 }}</span
                  >
                </v-row>
              </span>
            </template>
            <template v-slot:item.total_permission="{ item }">
              <span class="text-no-wrap">
                <small
                  >{{ item.permissions_count }} of {{ permissionsCount }}</small
                ><br />
                <v-progress-linear
                  height="5"
                  class="ma-0"
                  color="primary"
                  :value="(item.permissions_count * 100) / permissionsCount"
                />
              </span>
            </template>
            <template v-slot:item.created_at="{ item }">
              <span class="text-no-wrap">
                {{ formatDate(item.created_at) }}
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
                        name: 'admin-roles-detail',
                        params: { id: item.id },
                      }"
                      v-on="on"
                    >
                      <v-icon>visibility</v-icon>
                    </v-btn>
                  </template>
                  <span>Detail</span>
                </v-tooltip>
                <v-tooltip v-if="$can('update-roles')" top>
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
                        name: 'admin-roles-edit',
                        params: { id: item.id },
                      }"
                      v-on="on"
                    >
                      <v-icon>edit</v-icon>
                    </v-btn>
                  </template>
                  <span>Ubah</span>
                </v-tooltip>
              </span>
            </template>
          </v-data-table>
        </v-card-text>
      </v-card>
    </v-card-text>
  </v-card>
</template>

<script>
export default {
  breadcrumb() {
    return [
      {
        text: 'Dashboard',
        to: { name: 'dashboard' },
      },
      {
        text: 'Hak Akses',
        to: { name: 'admin-roles' },
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
      sortBy: ['display_name'],
    },
    footerProps: {
      showFirstLastPage: true,
      showCurrentPage: true,
      itemsPerPageOptions: [5, 10, 25, 50, 100],
    },
    total: 0,
    permissionsCount: 0,
  }),

  computed: {
    headers() {
      return [
        {
          text: 'Nama Hak Akses',
          align: 'start',
          class: 'text-no-wrap',
          value: 'display_name',
        },
        {
          text: 'Slug',
          align: 'start',
          class: 'text-no-wrap',
          value: 'name',
        },
        {
          text: 'Pengguna',
          align: 'start',
          class: 'text-no-wrap',
          sortable: false,
          value: 'user_using',
        },
        {
          text: 'Total Permission',
          align: 'start',
          class: 'text-no-wrap',
          sortable: false,
          value: 'total_permission',
        },
        {
          text: 'Dibuat',
          align: 'start',
          class: 'text-no-wrap',
          value: 'created_at',
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
    await this.getLists()
    await this.getPermissionsCount()
  },

  methods: {
    formatDate(val) {
      return this.$df(val).format('DD MMMM YYYY HH:mm:ss')
    },
    async getPermissionsCount() {
      await this.$http
        .get(this.$api.path('role.permissions.count'))
        .then(({ data }) => {
          this.permissionsCount = data
        })
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

      await this.$http
        .get(this.$api.path('role'), {
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
      }).then((result) => {
        if (result.value) {
          this.$http
            .post(this.$api.path('role.delete'), {
              deleteSelected: self.selected,
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

<style lang="scss" scoped>
.avatar-role {
  margin-left: -12px;
}
</style>
