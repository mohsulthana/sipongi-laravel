export default [
  {
    path: '/daily-hotspot',
    component: require('$comp/layouts/RouterView').default,
    children: [
      {
        path: '',
        name: 'daily-hotspot',
        component: require('./Index.vue').default,
        meta: {
          title: `Data Titik Panas - Harian`,
          permission: 'read_hotspot',
          layout: 'admin',
        },
      },
    ],
  },
]
