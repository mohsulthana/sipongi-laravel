export default [
  {
    path: '/entri-luas-kebakaran',
    component: require('$comp/layouts/RouterView').default,
    children: [
      {
        path: '',
        name: 'entri-luas-kebakaran',
        component: require('./Index.vue').default,
        meta: {
          title: `Entri Luas Area Kebakaran`,
          permission: 'read-entri_luas_area_kebakaran',
          layout: 'admin',
        },
      },
    ],
  },
]
