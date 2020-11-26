export default [
  {
    path: '/sarpras',
    component: require('$comp/layouts/RouterView').default,
    children: [
      {
        path: '',
        name: 'sarpras',
        component: require('./Index.vue').default,
        meta: {
          title: `Running Text`,
          permission: 'read-manggala_agni',
          layout: 'admin',
        },
      },
      {
        path: 'create',
        name: 'sarpras-create',
        component: require('./Create').default,
        meta: {
          title: `Tambah Sarana & Prasarana`,
          permission: 'create-manggala_agni',
          layout: 'admin',
          resetFilter: false,
        },
      },
      {
        path: 'edit/:id',
        name: 'sarpras-edit',
        component: require('./Edit').default,
        meta: {
          title: `Ubah Sarana & Prasarana`,
          permission: 'update-manggala_agni',
          layout: 'admin',
          resetFilter: false,
        },
      },
    ],
  },
]
