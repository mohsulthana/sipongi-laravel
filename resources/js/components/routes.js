import { routes as auth } from './modules/auth/index'
import { routes as errors } from './modules/errors/index'
import { routes as dashboard } from './modules/dashboard/index'
import { routes as admin } from './modules/admin/index'

export default [
  ...applyRules(['guest'], [...auth]),
  ...applyRules(['auth'], [...dashboard, ...admin]),
  ...errors,
]

function applyRules(rules, routes) {
  for (let i in routes) {
    routes[i].meta = routes[i].meta || {}

    if (!routes[i].meta.rules) {
      routes[i].meta.rules = []
    }
    routes[i].meta.rules.unshift(...rules)

    if (routes[i].children) {
      routes[i].children = applyRules(rules, routes[i].children)
    }
  }

  return routes
}
