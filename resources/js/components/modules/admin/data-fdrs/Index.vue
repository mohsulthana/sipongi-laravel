<template>
  <v-card outlined>
    <v-toolbar flat color="primary" dark>
      <v-toolbar-title class="font-weight-black">
        Data FDRS
      </v-toolbar-title>
    </v-toolbar>
    <v-card-text>
      <v-row>
        <v-col cols="12" class="text-right">
          <v-btn
            v-if="$can('delete-running_text') && selected.length > 0"
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
            :show-select="$can('delete-running_text')"
            fixed-header
            :height="listData.length > 10 ? `480px` : ''"
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

            <template v-slot:item.actions="{ item }">
              <span class="text-no-wrap">
                <v-tooltip v-if="$can('update-disclaimer')" top>
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
                        name: 'data-fdrs-detail',
                        params: { id: item.id },
                      }"
                      v-on="on"
                    >
                      <v-icon>visibility</v-icon>
                    </v-btn>
                  </template>
                  <span>Detail</span>
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
import { mapState, mapActions } from 'vuex'

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
        text: 'Data FDRS',
        to: { name: 'data-fdrs' },
      },
    ]
  },

  data: () => ({
    selected: [],
    listData: [],
    loading: false,
    listWatch: false,
    options: {
      sortDesc: [true],
      page: 1,
      itemsPerPage: 10,
      sortBy: ['date'],
    },
    footerProps: {
      showFirstLastPage: true,
      showCurrentPage: true,
      itemsPerPageOptions: [5, 10, 25, 50, 100],
    },
    total: 0,
  }),

  computed: {
    ...mapState('filter', ['filterDialogData']),
    headers() {
      return [
        {
          text: 'Wilayah',
          align: 'start',
          class: 'text-no-wrap',
          value: 'wilayah',
        },
        {
          text: 'Tanggal',
          align: 'start',
          class: 'text-no-wrap',
          value: 'date',
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
  },

  methods: {
    ...mapActions('filter', ['setFilterDialog']),
    formatDate(val) {
      return this.$df(val).format('DD MMMM YYYY HH:mm:ss')
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
      const url = this.$api.path('fdrs.data')
      // const url = 'http://101.50.0.178:8285/api/fdrs/data'
      await this.$http
        .get(url, {
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
          const url = this.$api.path('fdrs.delete')
          // const url = 'http://127.0.0.1:8081/api/fdrs/delete'
          this.$http
            .post(url,{deleteSelected: self.selected})
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
