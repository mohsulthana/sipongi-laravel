export default [
  {
    path: '/struktur-organisasi',
    component: require('$comp/layouts/RouterView').default,
    children: [
      {
        path: '',
        name: 'struktur-organisasi',
        component: require('./Index.vue').default,
        meta: {
          title: `Data Laporan harian`,
          permission: 'read-struktur_organisasi',
          layout: 'admin',
        },
      },
      {
        path: 'create',
        name: 'struktur-organisasi-create',
        component: require('./Create').default,
        meta: {
          title: `Tambah Struktur Organisasi`,
          permission: 'create-struktur_organisasi',
          layout: 'admin',
          resetFilter: false,
        },
      },
      {
        path: ':id/edit',
        name: 'struktur-organisasi-edit',
        component: require('./Edit').default,
        meta: {
          title: `Ubah Struktur Organisasi`,
          permission: 'update-struktur_organisasi',
          layout: 'admin',
          resetFilter: false,
        },
      },
    ],
  },
]
