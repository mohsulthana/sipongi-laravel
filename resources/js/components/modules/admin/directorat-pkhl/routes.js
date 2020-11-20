export default [
  {
    path: '/direktorat-pkhl',
    component: require('$comp/layouts/RouterView').default,
    children: [
      {
        path: '',
        name: 'direktorat-pkhl',
        component: require('./Index.vue').default,
        meta: {
          title: `Direktorat PKHL`,
          permission: 'read-direktorat_pkhl',
          layout: 'admin',
        },
      },
      {
        path: 'create',
        name: 'direktorat-pkhl-create',
        component: require('./Create').default,
        meta: {
          title: `Tambah Data Direktorat PKHL`,
          permission: 'create-direktorat_pkhl',
          layout: 'admin',
          resetFilter: false,
        },
      },
      {
        path: 'edit/:id',
        name: 'direktorat-pkhl-edit',
        component: require('./Edit').default,
        meta: {
          title: `Ubah Data Direktorat PKHL`,
          permission: 'update-direktorat_pkhl',
          layout: 'admin',
          resetFilter: false,
        },
      },
    ],
  },
]
