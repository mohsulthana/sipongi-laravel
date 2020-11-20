export default [
  {
    path: '/data-laporan-harian',
    component: require('$comp/layouts/RouterView').default,
    children: [
      {
        path: '',
        name: 'data-laporan-harian',
        component: require('./Index.vue').default,
        meta: {
          title: `Data Laporan harian`,
          permission: 'read-data_laporan_harian',
          layout: 'admin',
        },
      },
      {
        path: 'create',
        name: 'data-laporan-harian-create',
        component: require('./Create').default,
        meta: {
          title: `Tambah Data Laporan Hairan`,
          permission: 'create-data_laporan_harian',
          layout: 'admin',
          resetFilter: false,
        },
      },
      {
        path: ':id/edit',
        name: 'data-laporan-harian-edit',
        component: require('./Edit').default,
        meta: {
          title: `Ubah Data Laporan Hairan`,
          permission: 'update-data_laporan_harian',
          layout: 'admin',
          resetFilter: false,
        },
      },
    ],
  },
]
