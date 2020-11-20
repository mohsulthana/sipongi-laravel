export default [
  {
    path: '/satelit-hotspot',
    component: require('$comp/layouts/RouterView').default,
    children: [
      {
        path: '',
        name: 'satelit-hotspot',
        component: require('./Index.vue').default,
        meta: {
          title: `Data Titik Panas - Satelit`,
          permission: 'read_hotspot',
          layout: 'admin',
        },
      },
      {
        path: 'create',
        name: 'satelit-hotspot-create',
        component: require('./Create').default,
        meta: {
          title: `Tambah Data Titik Panas`,
          permission: 'create_hotspot',
          layout: 'admin',
          resetFilter: false,
        },
      },
    ],
  }
]
