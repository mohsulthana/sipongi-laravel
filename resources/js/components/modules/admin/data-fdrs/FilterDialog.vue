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
          Filter Data FDRS
        </v-toolbar-title>
      </v-toolbar>
      <v-card-text>
        <v-form lazy-validation @submit.prevent="submit">
          <v-container grid-list-md class="px-0">
            <v-row>

              <v-col cols="12" class="pa-2">
                <v-text-field
                  v-model="filterForm.fdrs_option_wilayah_key"
                  outlined
                  hide-details="auto"
                >
                  <template v-slot:label>
                    Wilayah
                  </template>
                </v-text-field>
              </v-col>

              <v-col
                cols="12"
                sm="6"
                md="4"
              >
                <v-menu
                  :close-on-content-click="false"
                  :nudge-right="40"
                  transition="scale-transition"
                  offset-y
                  min-width="290px"
                >
                  <template v-slot:activator="{ on, attrs }">
                    <v-text-field
                      v-model="filterForm.date"
                      outlined
                      label="Tanggal"
                      readonly
                      v-bind="attrs"
                      v-on="on"
                    ></v-text-field>
                  </template>
                  <v-date-picker
                    v-model="filterForm.date"
                  ></v-date-picker>
                </v-menu>
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
      fdrs_option_wilayah_key: null,
      date: null,
    }),
  }),

  computed: {
    ...mapState('filter', ['filterDialogData', 'statusDeleted']),
  },

  watch: {
    'filterDialogData.dialog': function (val) {
      if (val) {
        this.loadData()
      }
    },
  },

  methods: {
    ...mapActions('filter', ['setFilterDialog', 'setFilterDialogData']),
    async loadData() {
      setTimeout(() => {
        this.filterForm.populate(this.filterDialogData.filters)
      }, 300)
    },
    submit() {
      this.setFilterDialogData(this.filterForm.data())
      this.$emit('interface')
      this.close()
    },
    close() {
      this.setFilterDialog(false)
      this.loadData()
    },
    reset() {
      this.searchPublish = ''
      this.filterForm.clear()
      this.filterForm.reset()
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
