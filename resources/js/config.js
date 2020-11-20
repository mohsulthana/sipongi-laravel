const siteUrl = Laravel.siteUrl,
  apiUrl = Laravel.apiUrl,
  wsEnabled = Laravel.wsEnabled,
  wsUrl = Laravel.wsUrl,
  wsPort = Laravel.wsPort,
  locale = Laravel.locale,
  fallbackLocale = Laravel.fallbackLocale,
  GoogleMapApiKey = Laravel.GoogleMapApiKey

export const settings = {
  siteName: Laravel.siteName,
  apiUrl: apiUrl,
  siteUrl: siteUrl,
  wsEnabled: wsEnabled,
  wsUrl: wsUrl,
  wsPort: wsPort,
  locale: locale,
  fallbackLocale: fallbackLocale,
  GoogleMapApiKey: GoogleMapApiKey,
}

class URL {
  constructor(base) {
    this.base = base
  }

  path(path, args) {
    path = path.split('.')
    let obj = this,
      url = this.base

    for (let i = 0; i < path.length && obj; i++) {
      if (obj.url) {
        url += '/' + obj.url
      }

      obj = obj[path[i]]
    }
    if (obj) {
      url = url + '/' + (typeof obj === 'string' ? obj : obj.url)
    }

    if (args) {
      for (let key in args) {
        url = url.replace(':' + key, args[key])
      }
    }

    return url
  }
}

export const api = Object.assign(new URL(apiUrl), {
  url: '',

  auth: {
    url: 'auth',
    login: 'login',
    me: 'me',
    refresh: 'refresh',
    logout: 'logout',
    changePass: 'changePass',
    changeAvatar: 'changeAvatar',
  },

  dashboardData: {
    url: 'dashboardData',
  },

  role: {
    url: 'role',
    edit: ':id/edit',
    update: ':id/update',
    delete: 'delete',
    all: 'all',
    permissions: {
      url: 'permissions',
      count: 'count',
    },
  },

  user: {
    url: 'user',
    count: 'count',
    edit: ':id/edit',
    update: ':id/update',
    delete: 'delete',
    restore: 'restore',
  },

  regional: {
    url: 'regional',
    all: 'all',
  },

  provinsi: {
    url: 'provinsi',
    all: 'all',
    byRegional: ':id',
  },

  luasKebakaran: {
    url: 'luasKebakaran',
    edit: 'edit',
    update: 'update',
  },

  berita: {
    url: 'berita',
    edit: ':id/edit',
    update: ':id/update',
    delete: 'delete',
  },

  dokumenLain: {
    url: 'dokumenLain',
    edit: ':id/edit',
    update: ':id/update',
    delete: 'delete',
  },

  perpuKat: {
    url: 'perpuKategori',
    all: 'all',
  },

  perpu: {
    url: 'perpu',
    edit: ':id/edit',
    update: ':id/update',
    delete: 'delete',
  },

  galeri: {
    url: 'galeri',
    byTipe: ':tipe/all',
    update: ':id/update',
    delete: 'delete',
  },

  galeriDetail: {
    url: 'galeriDetail',
    update: ':id/update',
    delete: 'delete',
    publish: 'publish',
  },
  
  //Edit by hendie17@gmail.com
  runningText: {
    url: 'running-text',
    edit: ':id/edit',
    update: ':id/update',
    delete: 'delete',
  },

  disclaimer: {
    url: 'disclaimer',
    edit: ':id/edit',
    update: ':id/update',
    delete: 'delete',
  },

  laporanHarian: {
    url: 'laporan-harian',
    edit: ':id/edit',
    update: ':id/update',
    delete: 'delete',
  },

  direktoratPKHL: {
    url: 'direktorat-pkhl',
    edit: ':id/edit',
    update: ':id/update',
    delete: 'delete',
  },

  sturkturOrganisasi: {
    url: 'struktur-organisasi',
    edit: ':id/edit',
    update: ':id/update',
    delete: 'delete',
  },

  profil: {
    url: 'manggala-agni/profil',
    edit: ':id/edit',
    update: ':id/update',
    delete: 'delete',
  },

  sarpras: {
    url: 'manggala-agni/sarpras',
    edit: ':id/edit',
    update: ':id/update',
    delete: 'delete',
  },

  daerah: {
    url: 'manggala-agni/daerah',
    edit: ':id/edit',
    update: ':id/update',
    delete: 'delete',
    detail: 'detail'
  },

  hotspotSatelit: {
    url: 'hotspot/satelit',
    provinsi: 'provinsi',
    kabupaten: 'kabupaten/:id',
    kecamatan: 'kecamatan/:id',
    kelurahan: 'kelurahan/:id',
    edit: ':id/edit',
    update: ':id/update',
    delete: 'delete',
  },
  
  fdrs: {
    url: 'fdrs',
    data: 'data',
    delete: 'delete',
    detail: 'detail/:id',
  },
  
  luasKebakaran: {
    url: 'luas-kebakaran',
    edit: ':id/edit',
    update: ':id/update',
    delete: 'delete',
  },

  emisiCo2: {
    url: 'emisi-co2',
    edit: ':id/edit',
    update: ':id/update',
    delete: 'delete',
  },
})
