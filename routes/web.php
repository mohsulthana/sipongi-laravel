<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

// Route::get('/', function () {
//     // $activity = Activity::with([
//     //     'causer' => function ($q) {
//     //         $q->withTrashed();
//     //     }
//     // ])->get()->last();
//     // dd($activity->changes);
//     // AppServiceProvider . php
//     // Activity::saving(function (Activity $activity) {
//     //     $activity->properties = $activity->properties->put('ip', request()->ip());
//     // });
// });

$horizonUrl = config('horizon.path');
$logViewer = config('log-viewer.route.attributes.prefix');
Route::any('{all}', 'AppController@index')->where('all', "^(?!api|$logViewer|$horizonUrl).*$");
