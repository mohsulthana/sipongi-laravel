export default [
  {
    path: '/emisi-co2',
    component: require('$comp/layouts/RouterView').default,
    children: [
      {
        path: '',
        name: 'emisi-co2',
        component: require('./Index.vue').default,
        meta: {
          title: `Data Emisi CO2 Tahunan`,
          permission: 'read-emisi_co2',
          layout: 'admin',
        },
      },
      {
        path: 'create',
        name: 'emisi-co2-create',
        component: require('./Create').default,
        meta: {
          title: `Tambah Emisi CO2 Tahunan`,
          permission: 'create-emisi_co2',
          layout: 'admin',
          resetFilter: false,
        },
      },
      {
        path: ':id/edit',
        name: 'emisi-co2-edit',
        component: require('./Edit').default,
        meta: {
          title: `Ubah Emisi CO2 Tahunan`,
          permission: 'update-emisi_co2',
          layout: 'admin',
          resetFilter: false,
        },
      },
    ],
  },
]
