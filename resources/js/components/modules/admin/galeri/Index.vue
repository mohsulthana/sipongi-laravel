<template>
  <div>
    <v-card outlined>
      <v-toolbar flat color="primary" dark>
        <v-toolbar-title class="font-weight-black">
          Pilih Album
        </v-toolbar-title>
      </v-toolbar>
      <v-card-text>
        <v-row>
          <v-col cols="12" md="6">
            <v-autocomplete
              v-model="formDialogData.editData"
              outlined
              clearable
              return-object
              hide-details="auto"
              :items="albums"
              item-text="title"
              @click:clear="$nextTick(() => (formDialogData.editData = {}))"
            >
              <template v-slot:label>
                Album<span class="red--text">*</span>
              </template>
              <template v-slot:item="{ item }">
                <v-list-item-content>
                  <v-list-item-title v-html="item.title" />
                  <v-list-item-subtitle v-html="item.tipe" />
                </v-list-item-content>
              </template>
              <template v-slot:append-outer>
                <v-tooltip v-if="$can('create-galeri')" top>
                  <template v-slot:activator="{ on }">
                    <v-btn small icon v-on="on" @click="addItem">
                      <v-icon>add</v-icon>
                    </v-btn>
                  </template>
                  Tambah Album
                </v-tooltip>
                <v-tooltip
                  v-if="$can('update-galeri') && formDialogData.editData.id"
                  top
                >
                  <template v-slot:activator="{ on }">
                    <v-btn small icon v-on="on" @click="editItem">
                      <v-icon>edit</v-icon>
                    </v-btn>
                  </template>
                  Ubah Album
                </v-tooltip>
                <v-tooltip
                  v-if="$can('delete-galeri') && formDialogData.editData.id"
                  top
                >
                  <template v-slot:activator="{ on }">
                    <v-btn small icon v-on="on" @click="deleteItem">
                      <v-icon>delete</v-icon>
                    </v-btn>
                  </template>
                  Hapus Album
                </v-tooltip>
              </template>
            </v-autocomplete>
          </v-col>
        </v-row>
      </v-card-text>
    </v-card>

    <v-card outlined class="mt-3" v-if="formDialogData.editData.id">
      <v-toolbar flat color="primary" dark>
        <v-toolbar-title class="font-weight-black">
          Album {{ formDialogData.editData.title }}
        </v-toolbar-title>
      </v-toolbar>
      <v-card-text>
        <v-row>
          <v-col cols="12" class="text-right">
            <v-btn
              v-if="$can('create-galeri')"
              color="primary"
              @click="addItemGal"
              v-html="$ct('buttons.create')"
            />
          </v-col>
        </v-row>
        <v-card flat tile>
          <v-container fluid class="pa-0">
            <v-row>
              <v-col v-for="data in listData" :key="data.id" cols="6" md="3">
                <v-card outlined>
                  <v-img
                    :src="data.image_url"
                    :lazy-src="data.image_url"
                    aspect-ratio="1"
                    class="grey lighten-2"
                  >
                    <template v-slot:placeholder>
                      <v-row
                        class="fill-height ma-0"
                        align="center"
                        justify="center"
                      >
                        <v-progress-circular
                          indeterminate
                          color="grey lighten-5"
                        />
                      </v-row>
                    </template>
                  </v-img>
                  <v-card-text>
                    <span v-if="!data.edited">
                      {{ data.keterangan }}
                      <v-tooltip v-if="$can('update-galeri')" top>
                        <template v-slot:activator="{ on }">
                          <v-btn
                            x-small
                            icon
                            v-on="on"
                            @click="editItemGal(data)"
                          >
                            <v-icon>edit</v-icon>
                          </v-btn>
                        </template>
                        Ubah Keterangan
                      </v-tooltip>
                    </span>
                    <v-col v-if="data.edited" cols="12" class="pa-2">
                      <v-textarea
                        v-model="data.keterangan"
                        outlined
                        rows="3"
                        hide-details="auto"
                        :error="data.error"
                        :error-messages="data.message"
                      >
                        <template v-slot:label>
                          Keterangan
                          <span class="red--text">*</span>
                        </template>
                        <template v-slot:append-outer>
                          <v-tooltip v-if="$can('update-galeri')" top>
                            <template v-slot:activator="{ on }">
                              <v-btn
                                small
                                icon
                                v-on="on"
                                @click="saveEditItemGal(data)"
                              >
                                <v-icon>check</v-icon>
                              </v-btn>
                            </template>
                            Simpan
                          </v-tooltip>
                        </template>
                      </v-textarea>
                    </v-col>
                  </v-card-text>
                  <v-card-actions>
                    <v-spacer />

                    <v-tooltip v-if="$can('update-galeri')" top>
                      <template v-slot:activator="{ on }">
                        <v-btn
                          small
                          icon
                          v-on="on"
                          @click="visibleItemGal(data)"
                        >
                          <v-icon>
                            {{ data.publish ? 'visibility_off' : 'visibility' }}
                          </v-icon>
                        </v-btn>
                      </template>
                      {{ data.publish ? 'Sembunyikan' : 'Tampilkan' }}
                    </v-tooltip>

                    <v-tooltip v-if="$can('delete-galeri')" top>
                      <template v-slot:activator="{ on }">
                        <v-btn
                          small
                          icon
                          v-on="on"
                          @click="deleteItemGal(data.id)"
                        >
                          <v-icon>delete</v-icon>
                        </v-btn>
                      </template>
                      Hapus Gambar
                    </v-tooltip>
                  </v-card-actions>
                </v-card>
              </v-col>
            </v-row>
          </v-container>
        </v-card>
      </v-card-text>
      <v-card-actions>
        <v-row>
          <v-col cols="4" class="text-start">
            <v-btn
              depressed
              outlined
              icon
              fab
              dark
              small
              color="grey darken-2"
              :disabled="!btnPrev"
              @click="options.page--"
            >
              <v-icon>
                chevron_left
              </v-icon>
            </v-btn>
          </v-col>
          <v-col
            v-if="listData.length > 0"
            cols="4"
            class="text-center align-self-center"
          >
            Page : {{ options.page }}
          </v-col>
          <v-col cols="4" class="text-end">
            <v-btn
              depressed
              outlined
              icon
              fab
              dark
              small
              color="grey darken-2"
              :disabled="!btnNext"
              @click="options.page++"
            >
              <v-icon>
                chevron_right
              </v-icon>
            </v-btn>
          </v-col>
        </v-row>
      </v-card-actions>
    </v-card>
    <album-dialog v-model="formDialogData" @interface="handleDataFeedback" />
    <form-dialog
      v-model="formDialogDataGal"
      @interface="handleDataFeedbackGal"
    />
  </div>
</template>

<script>
import AlbumDialog from './AlbumDialog'
import FormDialog from './FormDialog'

export default {
  components: {
    AlbumDialog,
    FormDialog,
  },
  breadcrumb() {
    return [
      {
        text: 'Dashboard',
        to: { name: 'dashboard' },
      },
      {
        text: 'Galeri',
        to: { name: 'galeri' },
      },
    ]
  },

  data: () => ({
    formDialogData: {
      dialog: false,
      editedIndex: false,
      editData: {},
      dataForm: new Form({
        title: null,
        tipe: null,
      }),
    },
    pemadamans: [],
    lains: [],
    options: {
      direction: 'desc',
      sortBy: 'created_at',
      page: 1,
      per_page: 8,
    },
    listWatch: false,
    listData: [],
    btnPrev: false,
    btnNext: false,
    formDialogDataGal: {
      dialog: false,
      dataForm: new Form({
        id: null,
        galeri_id: null,
        keterangan: null,
        image: [],
      }),
    },
    dataFormItem: new Form({
      keterangan: null,
      id: null,
    }),
  }),

  computed: {
    albums() {
      let pemadamans = []
      let lains = []
      if (this.pemadamans.length > 0) {
        pemadamans = [
          { header: 'Pemadaman' },
          ...this.pemadamans,
          { divider: true },
        ]
      }

      if (this.lains.length > 0) {
        lains = [{ header: 'Lainnya' }, ...this.lains]
      }

      return [...pemadamans, ...lains]
    },
  },

  watch: {
    'formDialogData.editData': {
      async handler() {
        this.options.page = 1
        await this.getListDetail()
      },
      deep: true,
    },
    options: {
      async handler() {
        if (this.listWatch) {
          await this.getListDetail()
        }
      },
      deep: true,
    },
  },

  async created() {
    await this.getPemadamans()
    await this.getLain()
  },

  methods: {
    async getPemadamans() {
      await this.$http
        .get(this.$api.path('galeri.byTipe', { tipe: 'Pemadaman' }))
        .then(({ data }) => {
          this.pemadamans = data
        })
        .catch((err) => {})
    },
    async getLain() {
      await this.$http
        .get(this.$api.path('galeri.byTipe', { tipe: 'Lainnya' }))
        .then(({ data }) => {
          this.lains = data
        })
        .catch((err) => {})
    },
    async getListDetail() {
      let params = {
        direction: this.options.direction,
        sortBy: this.options.sortBy,
        page: this.options.page,
        per_page: this.options.per_page,
        galeri_id: this.formDialogData.editData.id,
      }

      await this.$http
        .get(this.$api.path('galeriDetail'), {
          params: params,
        })
        .then(({ data }) => {
          if (data.data.length === 0 && this.options.page > 1) {
            this.options.page = this.options.page - 1
            this.getListDetail()
          }
          this.listData = data.data
          this.btnPrev = data.links.prev ? true : false
          this.btnNext = data.links.next ? true : false
        })
        .catch((err) => {})
        .finally(() => {
          this.listWatch = true
        })
    },
    async handleDataFeedback(callback) {
      this.formDialogData.editData = callback
      await this.getPemadamans()
      await this.getLain()
    },
    addItem() {
      this.formDialogData.dialog = true
    },
    editItem() {
      this.formDialogData.editedIndex = true
      this.formDialogData.dataForm.populate(this.formDialogData.editData)
      this.formDialogData.dialog = true
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
            .post(this.$api.path('galeri.delete'), {
              id: self.formDialogData.editData.id,
            })
            .then(async ({ data }) => {
              if (data.status) {
                self.formDialogData.editData = {}
                await self.getPemadamans()
                await self.getLain()
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
    async handleDataFeedbackGal(callback) {
      this.options.page = 1
      await this.getListDetail()
    },
    addItemGal() {
      this.formDialogDataGal.dialog = true
      this.formDialogDataGal.dataForm.galeri_id = this.formDialogData.editData.id
      this.formDialogDataGal.dataForm.image = []
    },
    editItemGal(item) {
      item.edited = !item.edited
    },
    async saveEditItemGal(item) {
      this.dataFormItem.errors.clear()
      item.error = this.dataFormItem.errors.has('keterangan')
      item.message = this.dataFormItem.errors.first('keterangan')
      item.edited = !item.edited
      this.dataFormItem.id = item.id
      this.dataFormItem.keterangan = item.keterangan
      await this.dataFormItem
        .post(
          this.$api.path('galeriDetail.update', {
            id: item.id,
          })
        )
        .then(async (data) => {})
        .catch((err) => {
          item.edited = !item.edited
          item.error = this.dataFormItem.errors.has('keterangan')
          item.message = this.dataFormItem.errors.first('keterangan')
        })
        .finally(() => {})
    },
    async visibleItemGal(item) {
      await this.$http
        .post(this.$api.path('galeriDetail.publish'), {
          id: item.id,
          publish: !item.publish,
        })
        .then(async (data) => {
          item.publish = !item.publish
        })
        .catch((err) => {})
        .finally(() => {})
    },
    deleteItemGal(id) {
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
            .post(this.$api.path('galeriDetail.delete'), {
              id: id,
            })
            .then(async ({ data }) => {
              if (data.status) {
                await self.getListDetail()
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
