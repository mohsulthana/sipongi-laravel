<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::group([
    'namespace' => 'Api',
    'prefix' => 'auth'

], function () {
    Route::post('refresh', 'AuthController@refresh')->middleware(['throttle:10,1']);
    Route::post('login', 'AuthController@login')->middleware(['throttle:10,1']);
    Route::group([
        'middleware' => ['auth:api']
    ], function () {
        Route::post('logout', 'AuthController@logout');
        Route::get('me', 'AuthController@me');
        Route::post('changePass', 'AuthController@updateAuthUserPassword');
        Route::post('changeAvatar', 'AuthController@changeAvatar');
    });
});

Route::group([
    'middleware' => ['auth:api'],
    'namespace' => 'Api',
], function () {
    Route::get('dashboardData', 'DashboardController@getDashboardData');

    // Role
    Route::get('role', 'RoleController@getDt')->middleware(['permission:read-roles']);
    Route::post('role', 'RoleController@store')->middleware(['permission:create-roles']);
    Route::get('role/{id}/edit', 'RoleController@edit')->middleware(['permission:read-roles|update-roles']);
    Route::post('role/{id}/update', 'RoleController@update')->middleware(['permission:update-roles']);
    Route::post('role/delete', 'RoleController@destroy')->middleware(['permission:delete-roles']);
    Route::get('role/permissions', 'ModuleController@getModulesPermissions')->middleware(['permission:read-roles|create-roles|update-roles']);
    Route::get('role/permissions/count', 'PermissionController@count')->middleware(['permission:read-roles|create-roles|update-roles']);
    Route::get('role/all', 'RoleController@getAll');

    // User
    Route::get('user', 'UsersController@getDt')->middleware(['permission:read-users']);
    Route::post('user', 'UsersController@store')->middleware(['permission:create-users']);
    Route::get('user/{id}/edit', 'UsersController@edit')->middleware(['permission:read-users|update-users']);
    Route::post('user/{id}/update', 'UsersController@update')->middleware(['permission:update-users']);
    Route::post('user/delete', 'UsersController@destroy')->middleware(['permission:delete-users']);
    Route::post('user/restore', 'UsersController@restore')->middleware(['permission:delete-users']);
    Route::get('user/count', 'UsersController@count')->middleware(['permission:update-roles']);

    // Regional
    Route::get('regional/all', 'RegionalController@getAll');

    // Provinsi
    Route::get('provinsi/all', 'ProvinsiController@getAll');
    Route::get('provinsi/{reg_id}', 'ProvinsiController@getByRegional');

    //entri luas kebakaran
    Route::get('luasKebakaran/edit', 'LuasKebakaranTahunanController@getDt')->middleware(['permission:read-entri_luas_area_kebakaran']);
    Route::post('luasKebakaran/update', 'LuasKebakaranTahunanController@update')->middleware(['permission:update-entri_luas_area_kebakaran']);

    Route::group([
        'namespace' => 'Publikasi',
    ], function () {
        // Berita
        Route::get('berita', 'BeritaController@getDt')->middleware(['permission:read-berita']);
        Route::post('berita', 'BeritaController@store')->middleware(['permission:create-berita']);
        Route::get('berita/{id}/edit', 'BeritaController@edit')->middleware(['permission:read-berita|update-berita']);
        Route::post('berita/{id}/update', 'BeritaController@update')->middleware(['permission:update-berita']);
        Route::post('berita/delete', 'BeritaController@destroy')->middleware(['permission:delete-berita']);

        // dokumen lainnya
        Route::get('dokumenLain', 'DokumenLainController@getDt')->middleware(['permission:read-dokumen_lain']);
        Route::post('dokumenLain', 'DokumenLainController@store')->middleware(['permission:create-dokumen_lain']);
        Route::get('dokumenLain/{id}/edit', 'DokumenLainController@edit')->middleware(['permission:read-dokumen_lain|update-dokumen_lain']);
        Route::post('dokumenLain/{id}/update', 'DokumenLainController@update')->middleware(['permission:update-dokumen_lain']);
        Route::post('dokumenLain/delete', 'DokumenLainController@destroy')->middleware(['permission:delete-dokumen_lain']);

        // perpu kategori
        Route::get('perpuKategori/all', 'PerpuKategoriController@getAll');

        // perpu
        Route::get('perpu', 'PerpuController@getDt')->middleware(['permission:read-perpu']);
        Route::post('perpu', 'PerpuController@store')->middleware(['permission:create-perpu']);
        Route::get('perpu/{id}/edit', 'PerpuController@edit')->middleware(['permission:read-perpu|update-perpu']);
        Route::post('perpu/{id}/update', 'PerpuController@update')->middleware(['permission:update-perpu']);
        Route::post('perpu/delete', 'PerpuController@destroy')->middleware(['permission:delete-perpu']);

        //galeri
        Route::post('galeri', 'GaleriController@store')->middleware(['permission:create-galeri']);
        Route::get('galeri/{tipe}/all', 'GaleriController@getAll')->middleware(['permission:read-galeri']);
        Route::post('galeri/{id}/update', 'GaleriController@update')->middleware(['permission:update-galeri']);
        Route::post('galeri/delete', 'GaleriController@destroy')->middleware(['permission:delete-galeri']);
        Route::get('galeriDetail', 'GaleriController@detailGaleri')->middleware(['permission:read-galeri']);
        Route::post('galeriDetail', 'GaleriController@storeDetail')->middleware(['permission:create-galeri']);
        Route::post('galeriDetail/{id}/update', 'GaleriController@updateDetail')->middleware(['permission:update-galeri']);
        Route::post('galeriDetail/delete', 'GaleriController@destroyDetail')->middleware(['permission:delete-galeri']);
        Route::post('galeriDetail/publish', 'GaleriController@publishDetail')->middleware(['permission:update-galeri']);
    });
    
    // Emisi Co2 Tahunan
    Route::group(['prefix' => 'emisi-co2'], function () {
        Route::get('/', 'EmisiCo2TahunanController@index');
        Route::post('/', 'EmisiCo2TahunanController@store');
        Route::get('/{id}/edit', 'EmisiCo2TahunanController@edit');
        Route::post('/{id}/update', 'EmisiCo2TahunanController@update');
        Route::post('/delete', 'EmisiCo2TahunanController@destroy');
    });

    // Luas Area Kebakaran
    Route::group(['prefix' => 'luas-kebakaran'], function () {
        Route::get('/','LuasKebakaranTahunanController@index');
        Route::post('/','LuasKebakaranTahunanController@store');
        Route::get('/{id}/edit', 'LuasKebakaranTahunanController@edit');
        Route::post('/{id}/update', 'LuasKebakaranTahunanController@updateData');
        Route::post('delete', 'LuasKebakaranTahunanController@destroy');
    });
	
		// FDRS
    Route::group(['namespace' => 'Fdrs','prefix' => 'fdrs'], function () {
        Route::get('data', 'DataController@index');
        Route::get('detail/{id}', 'DataController@detail');
        Route::post('delete', 'DataController@destroy');
    });

    // Data Hotspot
    Route::group(['namespace' => 'Hotspot' ,'prefix' => 'hotspot'], function () {
        Route::get('satelit','SatelitController@index');
        Route::post('satelit', 'SatelitController@store');
        Route::get('satelit/provinsi','SatelitController@getDataProvinsi');
        Route::get('satelit/kabupaten/{provinsi_id}','SatelitController@getDataKabupaten');
        Route::get('satelit/kecamatan/{kotakab_id}','SatelitController@getDataKecamatan');
        Route::get('satelit/kelurahan/{kecamatan_id}','SatelitController@getDataDesa');
        Route::post('satelit/delete', 'SatelitController@destroy');
    });
	
	// Running text
    Route::group(['prefix' => 'running-text'], function () {
        Route::get('/', 'RunningTextController@index');
        Route::post('/', 'RunningTextController@store');
        Route::get('/{id}/edit', 'RunningTextController@edit');
        Route::post('/{id}/update', 'RunningTextController@update');
        Route::post('/delete', 'RunningTextController@destroy');
    });
    // Data Laporan harian
    Route::group(['prefix' => 'laporan-harian'], function () {
        Route::get('/', 'LaporanHarianController@index');
        Route::post('/', 'LaporanHarianController@store');
        Route::get('/{id}/edit', 'LaporanHarianController@edit');
        Route::post('/{id}/update', 'LaporanHarianController@update');
        Route::post('/delete', 'LaporanHarianController@destroy');
    });
    // Disclaimer
    Route::group(['prefix' => 'disclaimer'], function () {
        Route::get('/', 'DisclaimerController@index');
        Route::post('/', 'DisclaimerController@store');
        Route::get('/{id}/edit', 'DisclaimerController@edit');
        Route::post('/{id}/update', 'DisclaimerController@update');
        Route::post('/delete', 'DisclaimerController@destroy');
    });

    Route::group(['prefix' => 'disclaimer'], function () {
        Route::get('/', 'DisclaimerController@index');
        Route::post('/', 'DisclaimerController@store');
        Route::get('/{id}/edit', 'DisclaimerController@edit');
        Route::post('/{id}/update', 'DisclaimerController@update');
        Route::post('/delete', 'DisclaimerController@destroy');
    });

    /**
     * Tentang SiPongi
     */
    // Manggala Agni
    Route::group(['namespace' => 'ManggalaAgni','prefix' => 'manggala-agni'], function () {
        //Profil
        Route::group(['prefix' => 'profil'], function () {
            Route::get('/', 'ProfilController@index');
            Route::post('/', 'ProfilController@store');
            Route::get('/{id}/edit', 'ProfilController@edit');
            Route::post('/{id}/update', 'ProfilController@update');
            Route::post('/delete', 'ProfilController@destroy');
        });

        //Sarana & Prasarana
        Route::group(['prefix' => 'sarpras'], function () {
            Route::get('/', 'SarprasController@index');
            Route::post('/', 'SarprasController@store');
            Route::get('/{id}/edit', 'SarprasController@edit');
            Route::post('/{id}/update', 'SarprasController@update');
            Route::post('/delete', 'SarprasController@destroy');
        });

        //Daerah Operasi
        Route::group(['prefix' => 'daerah'], function () {
            Route::get('/', 'DaerahOperasiController@index');
            Route::get('detail', 'DaerahOperasiController@daerah');
            Route::post('/', 'DaerahOperasiController@store');
            Route::get('/{id}/edit', 'DaerahOperasiController@edit');
            Route::post('/{id}/update', 'DaerahOperasiController@update');
            Route::post('/delete', 'DaerahOperasiController@destroy');
        });
    });

    // Directorat PKHL
    Route::group(['prefix' => 'direktorat-pkhl'], function () {
        Route::get('/', 'DirektoratPKHLController@index');
        Route::post('/', 'DirektoratPKHLController@store');
        Route::get('/{id}/edit', 'DirektoratPKHLController@edit');
        Route::post('/{id}/update', 'DirektoratPKHLController@update');
        Route::post('/delete', 'DirektoratPKHLController@destroy');
    });

    // Struktur Organisasi
    Route::group(['prefix' => 'struktur-organisasi'], function () {
        Route::get('/', 'StrukturOrganisasiController@index');
        Route::post('/', 'StrukturOrganisasiController@store');
        Route::get('/{id}/edit', 'StrukturOrganisasiController@edit');
        Route::post('/{id}/update', 'StrukturOrganisasiController@update');
        Route::post('/delete', 'StrukturOrganisasiController@destroy');
    });
});

Route::group([
    'namespace' => 'Api',
], function () {
    Route::get('gfs', 'ForecastMapController@getGfs');
    Route::get('aqms', 'ForecastMapController@getAqms');
    Route::get('getCluster/{kotaKabId}/kotakab', 'KotaKabController@getClusterKotaKab');
    Route::get('getCluster/{desaId}/desa', 'KelurahanController@getClusterDesa');
    Route::get('daops/all', 'DaopsController@getMarkerAll');
    Route::get('indoHotspot', 'HotspotController@getIndoHotspot');
    Route::get('getProvinsi/all', 'ProvinsiController@getAll');
    Route::get('getKotaKab/{provId}', 'KotaKabController@getByProv');
    Route::get('totalIndoHotspot', 'HotspotController@getTotalHotspot');
    Route::get('totalIndoHotspotProv', 'HotspotController@getTotalHotspotProv');
    Route::get('totalLuasKebakaran', 'LuasKebakaranTahunanController@getTotalLuasKebakaran');
    Route::get('grafikMingguan', 'HotspotController@getGrafikMingguan');
    Route::get('grafikKumulatif', 'HotspotDailyController@getGrafikKumulatif');
    Route::get('grafikLuasKebakaran', 'LuasKebakaranTahunanController@getGrafikLuasKebakaran');

    Route::group([
        'namespace' => 'Publikasi',
    ], function () {
        Route::get('listBerita', 'BeritaController@listBerita');
        Route::get('detailBerita/{slug}', 'BeritaController@detailBerita');
        Route::get('listDokumen', 'DokumenLainController@listDokumen');
        Route::get('dokumen-lain/file/{slug}', 'DokumenLainController@getFile');
        Route::get('listPerpu', 'PerpuController@listPerpu');
        Route::get('peraturan-perundangan/file/{slug}', 'PerpuController@getFile');
        Route::get('listGaleri', 'GaleriController@listGaleri');
        Route::get('detailGaleries/{slug}', 'GaleriController@detailGaleries');
    });
    
    Route::group(['namespace' => 'Fdrs','prefix' => 'fdrs'], function () {
        Route::get('getData', 'BmkgController@index');
        // Route::get('fdrs', 'FdrsController@index');
        // Route::get('fdrs/detail', 'FdrsController@Detail');
    });

    // Visitor
    Route::get('visitor', 'VisitorController@index');
    Route::get('visitor/count', 'VisitorController@count');
    Route::get('data/disclaimer','DisclaimerController@getDataApi');
    Route::get('data/running-text','RunningTextController@getDataApi');
    Route::get('data/laporan-harian','LaporanHarianController@getDataApi');
    Route::get('data/direktorat-pkhl','DirektoratPKHLController@getDataApi');
    Route::get('data/struktur-organisasi','StrukturOrganisasiController@getDataApi');
    Route::get('data/luas-kebakaran','LuasKebakaranTahunanController@getDataApi');
    Route::get('data/emisi-co2','EmisiCo2TahunanController@getDataApi');

    // Manggala Agni
    Route::group(['prefix' => 'data','namespace' => 'ManggalaAgni'], function () {
        Route::get('profil','ProfilController@getDataApi');
        Route::get('sarpras','SarprasController@getDataApi');
        Route::get('daerah', 'DaerahOperasiController@getApiData');
    });
});
