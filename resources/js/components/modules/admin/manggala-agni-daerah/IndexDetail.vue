<template>
  <v-card outlined>
    <v-toolbar flat color="primary" dark>
      <v-toolbar-title class="font-weight-black">
        Manggala Agni - Detail Daerah Operasi
      </v-toolbar-title>
    </v-toolbar>
    <v-card-text>
      <v-row>
        <v-col cols="12" class="text-right">
          <v-btn
            v-if="$can('delete-manggala_agni') && selected.length > 0"
            dark
            color="red darken-4"
            @click="deleteItem()"
            v-html="$ct('buttons.delete')"
          />
          <v-btn
            :to="{ 
              name: 'daerahDetail-create',
              params : {id : $route.params.id}
            }"
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
            :show-select="$can('delete-daerah')"
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

            <template v-slot:item.created_at="{ item }">
              <span class="text-no-wrap">
                {{ formatDate(item.created_at) }}
              </span>
            </template>

            <template v-slot:item.actions="{ item }">
              <span class="text-no-wrap">
                <v-tooltip v-if="$can('update-manggala_agni')" top>
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
                        name: 'daerahDetail-edit',
                        params: { parent: $route.params.id,id: item.id },
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
import { mapState, mapActions } from 'vuex'

export default {
  breadcrumb() {
    return [
      {
        text: 'Dashboard',
        to: { name: 'dashboard' },
      },
      {
        text: 'Manggala Agni Daerah Operasi',
        to: { name: 'daerah' },
      },
      {
        text: 'Detail',
        to: { name: 'daerahDetail' },
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
      sortBy: ['created_at'],
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
          text: 'Daerah',
          align: 'start',
          class: 'text-no-wrap',
          value: 'daerah',
        },
        {
          text: 'Jumlah Regu',
          align: 'start',
          class: 'text-no-wrap',
          value: 'jumlah_regu',
        },
        {
          text: 'Jumlah Anggota',
          align: 'start',
          class: 'text-no-wrap',
          value: 'jumlah_anggota',
        },
        {
          text: 'Telepon',
          align: 'start',
          class: 'text-no-wrap',
          value: 'telepon',
        },
        {
          text: 'Alamat',
          align: 'start',
          class: 'text-no-wrap',
          value: 'alamat',
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
        parent_id: this.$route.params.id,
        direction: this.options.sortDesc[0] ? 'desc' : 'asc',
        sortBy: this.options.sortBy[0],
        page: this.options.page,
        per_page: this.options.itemsPerPage,
        query: this.search,
      }

      Object.assign(params, this.filterDialogData.filters)
      const url =  this.$api.path('daerah.detail')
      //const url = 'http://127.0.0.1:8081/api/manggala-agni/daerah/detail'

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
          const url =   this.$api.path('daerah.delete')
          // const url =  'http://127.0.0.1:8081/api/manggala-agni/daerah/delete'
          this.$http
            .post(url, {
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
