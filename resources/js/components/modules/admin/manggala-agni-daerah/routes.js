export default [
  {
    path: '/daerah',
    component: require('$comp/layouts/RouterView').default,
    children: [
      {
        path: '',
        name: 'daerah',
        component: require('./Index.vue').default,
        meta: {
          title: `Daerah Operasional `,
          permission: 'read-manggala_agni',
          layout: 'admin',
        },
      },
      {
        path: 'create',
        name: 'daerah-create',
        component: require('./Create').default,
        meta: {
          title: `Tambah Daerah Operasional `,
          permission: 'create-manggala_agni',
          layout: 'admin',
          resetFilter: false,
        },
      },
      {
        path: 'edit/:id',
        name: 'daerah-edit',
        component: require('./Edit').default,
        meta: {
          title: `Ubah Daerah Operasional `,
          permission: 'update-manggala_agni',
          layout: 'admin',
          resetFilter: false,
        },
      },
      // Detail
      {
        path: 'detail/:id',
        name: 'daerahDetail',
        component: require('./IndexDetail.vue').default,
        meta: {
          title: `Daerah Operasional `,
          permission: 'read-manggala_agni',
          layout: 'admin',
        },
      },
      {
        path: 'detail/:id/create',
        name: 'daerahDetail-create',
        component: require('./CreateDetail').default,
        meta: {
          title: `Tambah Detail Daerah Operasional `,
          permission: 'create-manggala_agni',
          layout: 'admin',
          resetFilter: false,
        },
      },
      {
        path: 'detail/edit/:parent/:id',
        name: 'daerahDetail-edit',
        component: require('./EditDetail').default,
        meta: {
          title: `Ubah Daerah Operasional `,
          permission: 'update-manggala_agni',
          layout: 'admin',
          resetFilter: false,
        },
      },

    ],
  },
]
