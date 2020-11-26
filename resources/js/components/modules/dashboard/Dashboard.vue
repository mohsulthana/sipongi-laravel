<template>
  <div>
    <h3 class="display-1">
      Selamat datang, <strong>{{ auth.name }}</strong> di Panel Dashboard OpsRoom.
    </h3>
    <v-row no-gutters>
      <v-col cols="12" class="pa-2" md="6">
        <v-card outlined>
          <v-card-text>
            <div class="text-subtitle-2 font-weight-black">
              TERRA/AQUA (NASA)
            </div>
            <div class="text-h3 font-weight-black">
              {{ data.totalNasaModis }}
            </div>
          </v-card-text>
        </v-card>
      </v-col>
      <v-col cols="12" class="pa-2" md="6">
        <v-card outlined>
          <v-card-text>
            <div class="text-subtitle-2 font-weight-black">
              TERRA/AQUA (LAPAN)
            </div>
            <div class="text-h3 font-weight-black">
              {{ data.totalLpnModis }}
            </div>
          </v-card-text>
        </v-card>
      </v-col>
    </v-row>
    <v-row no-gutters>
      <v-col cols="12" class="pa-2" md="4">
        <v-card outlined>
          <v-card-text>
            <div class="text-subtitle-2 font-weight-black">
              SNPP (LAPAN)
            </div>
            <div class="text-h3 font-weight-black">
              {{ data.totalLpnSnpp }}
            </div>
          </v-card-text>
        </v-card>
      </v-col>
      <v-col cols="12" class="pa-2" md="4">
        <v-card outlined>
          <v-card-text>
            <div class="text-subtitle-2 font-weight-black">
              NOAA20 (LAPAN)
            </div>
            <div class="text-h3 font-weight-black">
              {{ data.totalLpnNoaa }}
            </div>
          </v-card-text>
        </v-card>
      </v-col>
      <v-col cols="12" class="pa-2" md="4">
        <v-card outlined>
          <v-card-text>
            <div class="text-subtitle-2 font-weight-black">
              LANDSAT8 (LAPAN)
            </div>
            <div class="text-h3 font-weight-black">
              {{ data.totalLpnLandsat }}
            </div>
          </v-card-text>
        </v-card>
      </v-col>
    </v-row>
  </div>
</template>

<script>
import { mapGetters } from 'vuex'

export default {
  breadcrumb() {
    return [
      {
        text: 'Dashboard',
        to: { name: 'dashboard' },
      },
    ]
  },

  data: () => ({
    data: {},
  }),

  computed: mapGetters({
    auth: 'auth/user',
  }),

  async created() {
    await this.loadData()
  },

  methods: {
    async loadData() {
      await this.$http
        .get(this.$api.path('dashboardData'))
        .then(({ data }) => {
          this.data = data
        })
        .catch((err) => {})
    },
  },
}
</script>
