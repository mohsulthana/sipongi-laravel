export default [
  {
    path: '*',
    name: '404',
    component: require('./404').default,
    meta: {
      title: `404`,
      layout: 'default',
    },
  },
  {
    path: '/403',
    name: '403',
    component: require('./403').default,
    meta: {
      title: `403`,
      layout: 'default',
    },
  },
]
