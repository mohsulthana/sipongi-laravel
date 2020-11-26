export default [
  {
    path: '/dokumen_lain',
    component: require('$comp/layouts/RouterView').default,
    children: [
      {
        path: '',
        name: 'dokumen_lain',
        component: require('./Index.vue').default,
        meta: {
          title: `Dokumen Lain`,
          permission: 'read-dokumen_lain',
          layout: 'admin',
        },
      },
      {
        path: 'create',
        name: 'dokumen_lain-create',
        component: require('./Create').default,
        meta: {
          title: `Tambah Data Dokumen Lain`,
          permission: 'create-dokumen_lain',
          layout: 'admin',
          resetFilter: false,
        },
      },
      {
        path: ':id/edit',
        name: 'dokumen_lain-edit',
        component: require('./Edit').default,
        meta: {
          title: `Ubah Data Dokumen Lain`,
          permission: 'update-dokumen_lain',
          layout: 'admin',
          resetFilter: false,
        },
      },
      {
        path: ':id/detail',
        name: 'dokumen_lain-detail',
        component: require('./Detail').default,
        meta: {
          title: `Detail Data Dokumen Lain`,
          permission: 'read-dokumen_lain',
          layout: 'admin',
          resetFilter: false,
        },
      },
    ],
  },
]
