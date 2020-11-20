<template>
  <v-app>
    <v-main>
      <v-container grid-list-md>
        <v-row>
          <v-col cols="12">
            <v-card>
              <v-card-title
                class="primary white--text subheading"
                primary-title
              >
                <span class="title" v-html="$ct('errors-page.title')"></span>
              </v-card-title>
              <v-card-text
                v-html="$ct('errors-page.messages.404')"
              ></v-card-text>
              <v-card-actions>
                <v-btn
                  text
                  class="primary white--text"
                  :to="{ name: 'dashboard' }"
                  v-html="$ct('buttons.back')"
                ></v-btn>
              </v-card-actions>
            </v-card>
          </v-col>
        </v-row>
      </v-container>
    </v-main>
  </v-app>
</template>
