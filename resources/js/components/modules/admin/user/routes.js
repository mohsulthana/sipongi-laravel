export default [
  {
    path: '/admin/users',
    component: require('$comp/layouts/RouterView').default,
    children: [
      {
        path: '',
        name: 'admin-users',
        component: require('./User').default,
        meta: {
          title: `Pengguna`,
          permission: 'read-users',
          layout: 'admin',
        },
      },
      {
        path: 'create',
        name: 'admin-users-create',
        component: require('./Create').default,
        meta: {
          title: `Tambah Data Pengguna`,
          permission: 'create-users',
          layout: 'admin',
          resetFilter: false,
        },
      },
      {
        path: ':id/edit',
        name: 'admin-users-edit',
        component: require('./Edit').default,
        meta: {
          title: `Ubah Data Pengguna`,
          permission: 'update-users',
          layout: 'admin',
          resetFilter: false,
        },
      },
      {
        path: ':id/detail',
        name: 'admin-users-detail',
        component: require('./Detail').default,
        meta: {
          title: `Detail Data Pengguna`,
          permission: 'read-users',
          layout: 'admin',
          resetFilter: false,
        },
      },
    ],
  },
]
