export default [
  {
    path: '/luas-kebarakan',
    component: require('$comp/layouts/RouterView').default,
    children: [
      {
        path: '',
        name: 'luas-kebakaran',
        component: require('./Index.vue').default,
        meta: {
          title: `Data Luas Area Kebakaran`,
          permission: 'read-read_luas_kebakaran',
          layout: 'admin',
        },
      },
      {
        path: 'create',
        name: 'luas-kebakaran-create',
        component: require('./Create').default,
        meta: {
          title: `Tambah Data Luas Area Kebakaran`,
          permission: 'create-luas_kebakaran',
          layout: 'admin',
          resetFilter: false,
        },
      },
      {
        path: ':id/edit',
        name: 'luas-kebakaran-edit',
        component: require('./Edit').default,
        meta: {
          title: `Ubah Data Luas Area Kebakaran`,
          permission: 'update-luas_kebakaran',
          layout: 'admin',
          resetFilter: false,
        },
      },
    ],
  },
]
