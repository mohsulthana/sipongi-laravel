export default [
  {
    path: '/berita',
    component: require('$comp/layouts/RouterView').default,
    children: [
      {
        path: '',
        name: 'berita',
        component: require('./Index.vue').default,
        meta: {
          title: `Berita`,
          permission: 'read-berita',
          layout: 'admin',
        },
      },
      {
        path: 'create',
        name: 'berita-create',
        component: require('./Create').default,
        meta: {
          title: `Tambah Data Berita`,
          permission: 'create-berita',
          layout: 'admin',
          resetFilter: false,
        },
      },
      {
        path: ':id/edit',
        name: 'berita-edit',
        component: require('./Edit').default,
        meta: {
          title: `Ubah Data Berita`,
          permission: 'update-berita',
          layout: 'admin',
          resetFilter: false,
        },
      },
      {
        path: ':id/detail',
        name: 'berita-detail',
        component: require('./Detail').default,
        meta: {
          title: `Detail Data Berita`,
          permission: 'read-berita',
          layout: 'admin',
          resetFilter: false,
        },
      },
    ],
  },
]
