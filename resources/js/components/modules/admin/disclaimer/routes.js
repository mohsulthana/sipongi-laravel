export default [
  {
    path: '/disclaimer',
    component: require('$comp/layouts/RouterView').default,
    children: [
      {
        path: '',
        name: 'disclaimer',
        component: require('./Index.vue').default,
        meta: {
          title: `Disclaimer`,
          permission: 'read-disclaimer',
          layout: 'admin',
        },
      },
      {
        path: 'create',
        name: 'disclaimer-create',
        component: require('./Create').default,
        meta: {
          title: `Tambah Data Disclaimer`,
          permission: 'create-disclaimer',
          layout: 'admin',
          resetFilter: false,
        },
      },
      {
        path: 'edit/:id',
        name: 'disclaimer-edit',
        component: require('./Edit').default,
        meta: {
          title: `Ubah Data Disclaimer`,
          permission: 'update-disclaimer',
          layout: 'admin',
          resetFilter: false,
        },
      },
    ],
  },
]
