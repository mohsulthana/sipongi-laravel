export default [
  {
    path: '/galeri',
    component: require('$comp/layouts/RouterView').default,
    children: [
      {
        path: '',
        name: 'galeri',
        component: require('./Index.vue').default,
        meta: {
          title: `Galeri`,
          permission: 'read-galeri',
          layout: 'admin',
        },
      },
    ],
  },
]
