export default [
  {
    path: '/login',
    name: 'login',
    component: require('./Login').default,
    meta: {
      title: `Login`,
      layout: 'default',
    },
  },
]
