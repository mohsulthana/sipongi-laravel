<template>
  <v-navigation-drawer
    v-model="drawer"
    fixed
    :clipped="$vuetify.breakpoint.lgAndUp"
    app
    dark
    width="300"
    class="primary"
  >
    <VuePerfectScrollbar class="drawer-menu--scroll" :settings="scrollSettings">
      <v-list dense nav>
        <v-subheader
          class="mt-3 white--text text--darken-1 font-weight-black text-uppercase"
        >
          <v-list-item>
            <v-list-item-content>
              <v-list-item-title>
                Navigasi
              </v-list-item-title>
            </v-list-item-content>

            <v-list-item-icon class="hidden-md-and-up">
              <v-btn small icon class="mx-0" @click.native.stop="navToggle()">
                <v-icon>chevron_left</v-icon>
              </v-btn>
            </v-list-item-icon>
          </v-list-item>
        </v-subheader>
      </v-list>

      <v-list dense nav>
        <template v-for="(item, i) in menus">
          <v-subheader
            v-if="item.header && item.can"
            :key="i"
            class="mt-3 white--text text--darken-1 font-weight-black text-uppercase"
          >
            {{ item.header }}
          </v-subheader>

          <template v-for="subItem in item.items">
            <v-list-group
              v-if="subItem.items && subItem.can"
              :key="subItem.group"
              v-model="subItem.active"
              :prepend-icon="subItem.icon"
              no-action
              active-class="white--text"
            >
              <template v-slot:activator>
                <v-list-item-title>{{ subItem.title }}</v-list-item-title>
              </template>

              <v-list-item
                v-for="subItem2 in subItem.items"
                v-show="subItem2.can"
                :key="subItem2.title"
                :to="{ name: subItem2.to }"
                active-class="white--text text--darken-1"
              >
                <v-list-item-content>
                  <v-list-item-title>
                    {{ subItem2.title }}
                  </v-list-item-title>
                </v-list-item-content>
              </v-list-item>
            </v-list-group>

            <v-list-item
              v-else
              v-show="subItem.can"
              :key="subItem.group"
              :to="{ name: subItem.to }"
              active-class="white--text text--darken-1"
              :exact="subItem.to === 'dashboard'"
            >
              <v-list-item-action>
                <v-icon>{{ subItem.icon }}</v-icon>
              </v-list-item-action>
              <v-list-item-content>
                <v-list-item-title>
                  {{ subItem.title }}
                </v-list-item-title>
              </v-list-item-content>
            </v-list-item>
          </template>
        </template>
      </v-list>
    </VuePerfectScrollbar>
  </v-navigation-drawer>
</template>

<script>
import { mapGetters } from 'vuex'
import VuePerfectScrollbar from 'vue-perfect-scrollbar'

export default {
  components: {
    VuePerfectScrollbar,
  },

  data: () => ({
    scrollSettings: {
      maxScrollbarLength: 160,
    },
    items: [
      {
        items: [
          {
            icon: 'dashboard',
            group: 'dashboard',
            title: 'Dashboard',
            to: 'dashboard',
          },
        ],
      },
      {
        header: 'Modul Utama',
        items: [
          //{
          //  icon: 'picture_in_picture_alt',
          //  title: 'Entri Luas Area Kebakaran',
          //  group: 'entri-luas-kebakaran',
          //  to: 'entri-luas-kebakaran',
          //  permission: 'read-entri_luas_area_kebakaran',
          //},
          {
            icon: 'picture_in_picture_alt',
            title: 'Data Luas Kebakaran',
            group: 'luas-kebakaran',
            to: 'luas-kebakaran',
            permission: 'read-read_luas_kebakaran',
          },
          {
            icon: 'cloud_queue',
            title: 'Data Emisi CO2',
            group: 'emisi-co2',
            to: 'emisi-co2',
            permission: 'read-emisi_co2',
          },
          {
            icon: 'whatshot',
            title: 'Data Titik Panas',
            group: 'satelit-hotspot',
            to: 'satelit-hotspot',
            permission: 'read-read_hotspot',
          },
          {
            icon: 'burst_mode',
            title: 'Data FDRS',
            group: 'data-fdrs',
            to: 'data-fdrs',
            permission: 'read-fdrs',
          },
          {
            icon: 'pan_tool',
            title: 'Disclaimer',
            group: 'disclaimer',
            to: 'disclaimer',
            permission: 'read-disclaimer',
          },
          {
            icon: 'text_format',
            title: 'Running Text',
            group: 'running-text',
            to: 'running-text',
            permission: 'read-running_text',
          },
          
          {
            icon: 'public',
            title: 'Manajemen Publikasi',
            active: false,
            group: 'publikasi',
            items: [
              {
                title: 'Berita',
                to: 'berita',
                permission: 'read-berita',
              },
              {
                title: 'Galeri',
                to: 'galeri',
                permission: 'read-galeri',
              },
              {
                title: 'Peraturan Perundangan',
                to: 'perpu',
                permission: 'read-perpu',
              },
              {
                title: 'Dokumen Lain',
                to: 'dokumen_lain',
                permission: 'read-dokumen_lain',
              },
              {
                icon: 'assignment',
                title: 'Laporan Harian Posko',
                to: 'data-laporan-harian',
                permission: 'read-data_laporan_harian',
              },
            ],
          },
          // Tentang Sipongi
          {
            icon: 'info_outline',
            title: 'Tentang Sipongi',
            active: false,
            group: 'tentang',
            items: [
              {
                title: 'Direktorat PKHL',
                to: 'direktorat-pkhl',
                permission: 'read-direktorat',
              },
              {
                title: 'Struktur Organisasi',
                to: 'struktur-organisasi',
                permission: 'read-struktur_organisasi',
              },
            ],
          },
          // Manggala Agni
          {
            icon: 'phonelink',
            title: 'Manggala Agni',
            active: false,
            group: 'manggala-agni',
            items: [
              {
                title: 'Profil',
                to: 'profil',
                permission: 'read-manggala_agni',
              },
              {
                title: 'Daerah Operasi',
                to: 'daerah',
                permission: 'read-manggala_agni',
              },
              {
                title: 'Sarana & Prasaran',
                to: 'sarpras',
                permission: 'read-manggala_agni',
              },
            ],
          },
        ],
      },
      {
        header: 'Modul Sistem',
        items: [
          {
            icon: 'supervisor_account',
            title: 'Manajemen Pengguna',
            active: false,
            group: 'admin',
            items: [
              {
                title: 'Pengguna',
                to: 'admin-users',
                permission: 'read-users',
              },
              {
                title: 'Hak Akses',
                to: 'admin-roles',
                permission: 'read-roles',
              },
            ],
          },
        ],
      },
    ],
  }),

  computed: {
    drawer: {
      get() {
        return this.$store.state.loader.drawer
      },
      set(val) {
        this.$store.commit('loader/SET_DRAWER', val)
      },
    },
    menus() {
      this.items.forEach((item) => {
        if (item.items) {
          let i = 0

          item.items.forEach((child) => {
            if (child.items) {
              let a = 0

              child.items.forEach((child2) => {
                child2.can = child2.permission
                  ? this.$can(child2.permission)
                  : true

                a = child2.can ? a + 1 : a
              })

              child.can = a > 0
            } else {
              child.can = child.permission ? this.$can(child.permission) : true
            }

            i = child.can ? i + 1 : i
          })

          item.can = i > 0
        } else {
          item.can = item.permission ? this.$can(item.permission) : true
        }
      })
      return this.items
    },
  },

  methods: {
    navToggle() {
      // this.$emit("nav-toggle");
      this.$store.commit('loader/TOGGLE_DRAWER')
    },
  },
}
</script>
