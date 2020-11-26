export default [
  {
    path: '/profil',
    component: require('$comp/layouts/RouterView').default,
    children: [
      {
        path: '',
        name: 'profil',
        component: require('./Index.vue').default,
        meta: {
          title: `Running Text`,
          permission: 'read-manggala_agni',
          layout: 'admin',
        },
      },
      {
        path: 'create',
        name: 'profil-create',
        component: require('./Create').default,
        meta: {
          title: `Tambah Manggala Agni Profil`,
          permission: 'create-manggala_agni',
          layout: 'admin',
          resetFilter: false,
        },
      },
      {
        path: 'edit/:id',
        name: 'profil-edit',
        component: require('./Edit').default,
        meta: {
          title: `Ubah Manggala Agni Profil`,
          permission: 'update-manggala_agni',
          layout: 'admin',
          resetFilter: false,
        },
      },
    ],
  },
]
