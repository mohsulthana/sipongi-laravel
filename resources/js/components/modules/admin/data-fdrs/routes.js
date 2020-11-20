export default [
  {
    path: '/data-fdrs',
    component: require('$comp/layouts/RouterView').default,
    children: [
      {
        path: '',
        name: 'data-fdrs',
        component: require('./Index.vue').default,
        meta: {
          title: `Data FDRS`,
          permission: 'read-fdrs',
          layout: 'admin',
        },
      },
      {
        path: 'detail/:id',
        name: 'data-fdrs-detail',
        component: require('./Detail').default,
        meta: {
          title: `Detail FDRS`,
          permission: 'read-fdrs',
          layout: 'admin',
          resetFilter: false,
        },
      },
    ],
  },
]
