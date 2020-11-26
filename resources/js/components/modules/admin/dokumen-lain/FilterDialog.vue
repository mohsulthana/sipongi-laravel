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
          Filter Berita
        </v-toolbar-title>
      </v-toolbar>
      <v-card-text>
        <v-form lazy-validation @submit.prevent="submit">
          <v-container grid-list-md class="px-0">
            <v-row>
              <v-col cols="12" class="pa-2">
                <v-text-field
                  v-model="filterForm.title"
                  outlined
                  hide-details="auto"
                >
                  <template v-slot:label>
                    Title
                  </template>
                </v-text-field>
              </v-col>
              <v-col cols="12" class="pa-2" md="6">
                <v-autocomplete
                  v-model="filterForm.private"
                  outlined
                  hide-details="auto"
                  :items="privates"
                  clearable
                  item-text="name"
                  item-value="id"
                  :search-input.sync="searchPrivate"
                >
                  <template v-slot:label>
                    Private
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
      title: null,
      private: null,
    }),
    searchPrivate: '',
  }),

  computed: {
    ...mapState('filter', ['filterDialogData', 'statusDeleted']),
    privates() {
      return [
        {
          id: 1,
          name: this.$ct('yes'),
        },
        {
          id: 0,
          name: this.$ct('no'),
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
  },

  methods: {
    ...mapActions('filter', ['setFilterDialog', 'setFilterDialogData']),
    async loadData() {
      setTimeout(() => {
        this.filterForm.populate(this.filterDialogData.filters)
        this.searchPrivate = this.customFilter(
          this.filterDialogData.filters.private,
          this.privates
        ).name
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
      this.searchPrivate = ''
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
