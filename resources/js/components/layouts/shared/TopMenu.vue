<template>
  <v-app-bar
    :clipped-left="$vuetify.breakpoint.lgAndUp"
    light
    app
    height="48"
    class="pl-0 pr-0 v-toolbar-utama"
  >
    <v-app-bar-nav-icon class="ml-1" @click.stop="navToggle()" />
    <img src="/images/logo.png" alt="logo" height="40" width="80" />
    <v-toolbar-title class="ml-2 align-center">
      <span class="title">OpsRoom</span>
    </v-toolbar-title>
    <v-spacer />
    <v-menu
      offset-y
      origin="center center"
      :nudge-right="140"
      :nudge-bottom="10"
      transition="scale-transition"
    >
      <template v-slot:activator="{ on }">
        <v-btn
          text
          large
          class="pl-2 pr-2 user-dropdown hidden-sm-and-down"
          v-on="on"
        >
          <v-avatar size="32px" class="mr-2">
            <img
              :src="[
                auth.avatar_url.status === 'url'
                  ? auth.avatar_url.url
                  : auth.avatar_url.encoded,
              ]"
              alt="avatar"
            />
          </v-avatar>
          <span class="name">
            <h5>{{ auth.name }}</h5>
            <h6>{{ auth.role_name }}</h6>
          </span>
          <v-icon right dark>
            keyboard_arrow_down
          </v-icon>
        </v-btn>
        <v-btn icon v-on="on" text class="hidden-md-and-up">
          <v-avatar size="32px">
            <img
              :src="[
                auth.avatar_url.status === 'url'
                  ? auth.avatar_url.url
                  : auth.avatar_url.encoded,
              ]"
              alt="avatar"
            />
          </v-avatar>
        </v-btn>
      </template>
      <v-list dense class="pa-0">
        <v-list-item ripple="ripple" rel="noopener" @click="showDialogAvatar()">
          <v-list-item-icon class="mr-0">
            <v-icon>keyboard_arrow_right</v-icon>
          </v-list-item-icon>
          <v-list-item-content>
            <v-list-item-title v-text="$ct('change-profile-picture')" />
          </v-list-item-content>
        </v-list-item>

        <v-list-item
          v-for="(item, index) in items"
          :key="index"
          :to="!item.href && item.to ? { name: item.to } : null"
          ripple="ripple"
          :disabled="item.disabled"
          :target="item.target"
          rel="noopener"
          @click="menuClick(item.name)"
        >
          <v-list-item-icon v-if="item.icon" class="mr-0">
            <v-icon v-text="item.icon" />
          </v-list-item-icon>
          <v-list-item-content>
            <v-list-item-title v-text="item.title" />
          </v-list-item-content>
        </v-list-item>
      </v-list>
    </v-menu>

    <top-dialog-avatar v-model="dataDialogAvatar" />
    <change-password v-model="dataDialogPassword" />
  </v-app-bar>
</template>

<script>
import { mapGetters, mapActions } from 'vuex'
import topDialogAvatar from './TopDialogAvatar'
import ChangePassword from './ChangePassword'

export default {
  components: {
    topDialogAvatar,
    ChangePassword,
  },

  data: () => ({
    dataDialogAvatar: {
      dialog: false,
      avatar_url: {},
    },
    dataDialogPassword: {
      dialog: false,
      default_pass: false,
    },
  }),

  computed: {
    ...mapGetters({
      auth: 'auth/user',
    }),
    items() {
      return [
        {
          icon: 'keyboard_arrow_right',
          href: '',
          title: 'Ubah Password',
          to: '',
          name: 'changePass',
        },
        {
          icon: 'keyboard_arrow_right',
          href: '',
          title: this.$ct('buttons.logout'),
          to: '',
          name: 'logout',
        },
      ]
    },
  },

  created() {
    if (this.auth.default_pass) {
      this.dataDialogPassword.default_pass = true
      this.dataDialogPassword.dialog = true
    }
  },

  methods: {
    ...mapActions('loader', ['setLoader']),
    ...mapActions('auth', ['logout']),
    async menuClick(name) {
      if (name === 'logout') {
        // Log out the user.
        this.setLoader(true)
        await this.logout()

        // Redirect to login.
        this.$router.push({ name: 'login' })
      }

      if (name === 'changePass') {
        // Log out the user.
        this.dataDialogPassword.dialog = true
      }
    },
    navToggle() {
      this.$store.commit('loader/TOGGLE_DRAWER')
    },
    showDialogAvatar() {
      this.dataDialogAvatar.avatar_url = this.auth.avatar_url
      this.dataDialogAvatar.dialog = true
    },
  },

  beforeDestroy() {
    this.setLoader(false)
  },
}
</script>
<style lang="scss" scoped>
.v-toolbar-utama::v-deep {
  .v-toolbar__content {
    padding: 0;
  }

  .user-dropdown {
    .name {
      h5 {
        text-align: left;
        font-size: 12px;
        font-weight: 800;
      }

      h6 {
        text-align: left;
        font-size: 9px;
        font-weight: 400;
        text-transform: none;
      }
    }
  }
}
</style>
