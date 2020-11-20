import { routes as role } from './role/index'
import { routes as user } from './user/index'
import { routes as entriLuasKebakaran } from './luas-kebakaran/index'
import { routes as berita } from './berita/index'
import { routes as dokumenLain } from './dokumen-lain/index'
import { routes as perpu } from './perpu/index'
import { routes as galeri } from './galeri/index'

import { routes as dataLuasKebakaran } from './data-luas-kebakaran/index'
import { routes as dataEmisiCo2 } from './data-emisi-co2/index'
import { routes as dataTitikPanasHarian } from './hotspot-daily/index'
import { routes as dataTitikPanasSatelit } from './hotspot-satelit/index'
import { routes as dataFdrs } from './data-fdrs/index'
import { routes as dataLaporanHarian } from './data-laporan-harian/index'
import { routes as disclaimer } from './disclaimer/index'
import { routes as runningText } from './running-text/index'
import { routes as directoratPKHL } from './directorat-pkhl/index'
import { routes as sturkturOrganisasi } from './struktur-organisasi/index'
import { routes as manggalaAgniProfil } from './manggala-agni-profil/index'
import { routes as manggalaAgniSarpras } from './manggala-agni-sarpras/index'
import { routes as manggalaAgniDaerah } from './manggala-agni-daerah/index'

export default [
  ...user,
  ...role,
  ...entriLuasKebakaran,
  ...berita,
  ...dokumenLain,
  ...perpu,
  ...galeri,
  ...dataLaporanHarian,
  ...disclaimer,
  ...runningText,
  ...directoratPKHL,
  ...sturkturOrganisasi,
  ...manggalaAgniProfil,
  ...manggalaAgniSarpras,
  ...manggalaAgniDaerah,
  ...dataTitikPanasHarian,
  ...dataTitikPanasSatelit,
  ...dataFdrs,
  ...dataLuasKebakaran,
  ...dataEmisiCo2,
]
