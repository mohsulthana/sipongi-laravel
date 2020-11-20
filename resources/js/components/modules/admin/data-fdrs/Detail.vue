<template>
  <v-card outlined>
    <v-toolbar
      flat
      color="primary"
      dark
    >
      <v-toolbar-title class="font-weight-black">
        {{data.wilayah}} - {{data.date}}
      </v-toolbar-title>
    </v-toolbar>
    <v-card outlined>
        <v-row>
            <v-col cols="6">
                <v-img contain :src="data.image_url" max-height="500px" />
            </v-col>
            <v-col cols="6">
                <div>Wilayah {{data.wilayah}}</div>
                <div>Tanggal {{data.date}}</div>
                <div style="padding-top:10px;">
                    <a href="#" @click="download()">
                        Download
                    </a>
                </div>
                <div style="font-size:12px;">*Sumber data : Badan Meteorologi Dan Meteorologi Klimatologi dan Geofisika</div>
            </v-col>
        </v-row>
    </v-card>
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
        text: 'Data FDRS',
        to: { name: 'data-fdrs' },
      },
      {
        text: 'Detail',
        to: {
          name: 'data-fdrs-detail',
          params: { id: this.$route.params.id },
        },
      },
    ]
  },
  data() {
      return {
          data: {},
      }
  },
  async created() {
      await this.loadData()
  },
  methods: {
    async loadData() {
    const url = this.$api.path('fdrs.detail', {
            id: this.$route.params.id,
          })
    // const url = 'http://127.0.0.1:8081/api/fdrs/detail/' + this.$route.params.id
      await this.$http
        .get(url)
        .then((resp) => {
          this.data = resp.data
        })
        .catch((err) => {})
    },
    download(){
      const filename = this.data.image

      this.$http({
          url: this.data.image_url,
          method: 'GET',
          responseType: 'blob',
      }).then((response) => {
          var fileURL = window.URL.createObjectURL(new Blob([response.data]));
          var fileLink = document.createElement('a');

          fileLink.href = fileURL;
          fileLink.setAttribute('download',filename );
          document.body.appendChild(fileLink);

          fileLink.click();
      });
    }
  },
}
</script>
