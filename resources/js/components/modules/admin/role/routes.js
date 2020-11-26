export default [
  {
    path: '/admin/roles',
    component: require('$comp/layouts/RouterView').default,
    children: [
      {
        path: '',
        name: 'admin-roles',
        component: require('./Role').default,
        meta: {
          title: `Hak Akses`,
          permission: 'read-roles',
          layout: 'admin',
        },
      },
      {
        path: 'create',
        name: 'admin-roles-create',
        component: require('./Create').default,
        meta: {
          title: `Tambah Data Hak Akses`,
          permission: 'create-roles',
          layout: 'admin',
        },
      },
      {
        path: ':id/edit',
        name: 'admin-roles-edit',
        component: require('./Edit').default,
        meta: {
          title: `Ubah Data Hak Akses`,
          permission: 'update-roles',
          layout: 'admin',
        },
      },
      {
        path: ':id/detail',
        name: 'admin-roles-detail',
        component: require('./Detail').default,
        meta: {
          title: `Detail Data Hak Akses`,
          permission: 'read-roles',
          layout: 'admin',
        },
      },
    ],
  },
]
