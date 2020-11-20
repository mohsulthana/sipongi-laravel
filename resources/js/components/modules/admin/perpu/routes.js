export default [
  {
    path: '/perpu',
    component: require('$comp/layouts/RouterView').default,
    children: [
      {
        path: '',
        name: 'perpu',
        component: require('./Index.vue').default,
        meta: {
          title: `Peraturan Perundangan`,
          permission: 'read-perpu',
          layout: 'admin',
        },
      },
      {
        path: 'create',
        name: 'perpu-create',
        component: require('./Create').default,
        meta: {
          title: `Tambah Data Peraturan Perundangan`,
          permission: 'create-perpu',
          layout: 'admin',
          resetFilter: false,
        },
      },
      {
        path: ':id/edit',
        name: 'perpu-edit',
        component: require('./Edit').default,
        meta: {
          title: `Ubah Data Peraturan Perundangan`,
          permission: 'update-perpu',
          layout: 'admin',
          resetFilter: false,
        },
      },
      {
        path: ':id/detail',
        name: 'perpu-detail',
        component: require('./Detail').default,
        meta: {
          title: `Detail Data Peraturan Perundangan`,
          permission: 'read-perpu',
          layout: 'admin',
          resetFilter: false,
        },
      },
    ],
  },
]
