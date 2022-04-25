<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Http;

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

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

Route::get('/redirect', function (Request $request) {
    $request->session()->put('state', $state = Str::random(40));
    $query = http_build_query([
        'client_id' => '8',
        'redirect_uri' => 'http://192.168.1.182/project/laravel-client/public/callback',
        'response_type' => 'code',
        'scope' => '',
        'state' => $state,
    ]);
    return redirect('http://192.168.1.182/project/laravel-server/public/oauth/authorize?'.$query);
});

Route::get('/callback', function (Request $request) {
    $state = $request->session()->pull('state');
    $response = Http::asForm()->post('http://192.168.1.182/project/laravel-server/public/oauth/token', [
        'grant_type' => 'authorization_code',
        'client_id' => '8',
        'client_secret' => 'AGKtAormheckwXeagqotOaHATOmgJcXmRMVkUlzw',
        'redirect_uri' => 'http://192.168.1.182/project/laravel-client/public/callback',
        'code' => $request->code,
    ]);

    return $response->json();
});
