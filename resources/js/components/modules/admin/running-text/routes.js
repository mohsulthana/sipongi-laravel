export default [
  {
    path: '/running-text',
    component: require('$comp/layouts/RouterView').default,
    children: [
      {
        path: '',
        name: 'running-text',
        component: require('./Index.vue').default,
        meta: {
          title: `Running Text`,
          permission: 'read-running_text',
          layout: 'admin',
        },
      },
      {
        path: 'create',
        name: 'running-text-create',
        component: require('./Create').default,
        meta: {
          title: `Tambah Data Running Text`,
          permission: 'create-running_text',
          layout: 'admin',
          resetFilter: false,
        },
      },
      {
        path: 'edit/:id',
        name: 'running-text-edit',
        component: require('./Edit').default,
        meta: {
          title: `Ubah Data Running Text`,
          permission: 'update-running_text',
          layout: 'admin',
          resetFilter: false,
        },
      },
    ],
  },
]
