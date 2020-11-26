import Vue from 'vue'
import VueRouter from 'vue-router'
import store from '~/store/index'
import routes from '$comp/routes'

Vue.use(VueRouter)

const router = new VueRouter({
  mode: 'history',
  routes,
})

router.beforeEach(async (to, from, next) => {
  // This goes through the matched routes from last to first, finding the closest route with a title.
  // eg. if we have /some/deep/nested/route and /some, /deep, and /nested have titles, nested's will be chosen.
  const nearestWithTitle = to.matched
    .slice()
    .reverse()
    .find((r) => r.meta && r.meta.title)

  // Find the nearest route element with meta tags.
  const nearestWithMeta = to.matched
    .slice()
    .reverse()
    .find((r) => r.meta && r.meta.metaTags)
  const previousNearestWithMeta = from.matched
    .slice()
    .reverse()
    .find((r) => r.meta && r.meta.metaTags)

  // If a route with a title was found, set the document (page) title to that value.
  if (nearestWithTitle)
    document.title = `${Vue.ct(nearestWithTitle.meta.title)} - ${
      Vue.myEnv.siteName
    }`

  // Remove any stale meta tags from the document using the key attribute we set below.
  Array.from(
    document.querySelectorAll('[data-vue-router-controlled]')
  ).map((el) => el.parentNode.removeChild(el))

  // Skip rendering meta tags if there are none.
  if (nearestWithMeta) {
    // Turn the meta tag definitions into actual elements in the head.
    nearestWithMeta.meta.metaTags
      .map((tagDef) => {
        const tag = document.createElement('meta')

        Object.keys(tagDef).forEach((key) => {
          tag.setAttribute(key, tagDef[key])
        })

        // We use this to track which meta tags we create, so we don't interfere with other ones.
        tag.setAttribute('data-vue-router-controlled', '')

        return tag
      })
      // Add the meta tags to the document head.
      .forEach((tag) => document.head.appendChild(tag))
  }

  store.dispatch('filter/resetFilter')

  await store.dispatch('auth/fetchUser')
  // if (store.getters['auth/token'] && !store.getters['auth/check']) {
  //     try {
  //         await store.dispatch('auth/fetchUser')
  //     } catch (e) {}
  // }

  let route = reroute(to)
  if (route) {
    next({
      name: route,
    })
  } else {
    if (checkPermission(to.meta.permission)) {
      next()
    } else {
      next({
        name: '403',
      })
    }
  }
})

const rules = {
  guest: {
    fail: 'dashboard',
    check: () => !store.getters['auth/check'],
  },
  auth: {
    fail: 'login',
    check: () => store.getters['auth/check'],
  },
}

function reroute(to) {
  let failRoute = false,
    checkResult = false

  to.meta.rules &&
    to.meta.rules.forEach((rule) => {
      let check = false
      if (Array.isArray(rule)) {
        let checks = []
        for (let i in rule) {
          checks[i] = rules[rule[i]].check()
          check = check || checks[i]
        }
        if (!check && !failRoute) {
          failRoute = rules[rule[checks.indexOf(false)]].fail
        }
      } else {
        check = rules[rule].check()
        if (!check && !failRoute) {
          failRoute = rules[rule].fail
        }
      }

      checkResult = checkResult && check
    })

  if (!checkResult && failRoute) {
    return failRoute
  }

  return false
}

export default router

function checkPermission(permissionName) {
  if (
    typeof permissionName !== 'undefined' &&
    permissionName !== null &&
    permissionName !== ''
  ) {
    if (!store.getters['auth/check']) {
      return false
    }

    let user = store.getters['auth/user']
    let permissions = user.permissions
    let permissionArray = permissionName.split('|')
    let res = 0

    if (!user.is_super_admin) {
      if (permissions.length <= 0) {
        return false
      }

      permissionArray.forEach((item) => {
        if (item) {
          if (permissions.indexOf(item) !== -1) {
            res = res + 1
          }
        }
      })

      return res > 0
    }
  }

  return true
}
