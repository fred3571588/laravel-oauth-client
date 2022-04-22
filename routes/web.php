<?php

use Illuminate\Http\Request;
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

Route::get('/redirect', function (Request $request) {
    $request->session()->put('state', $state = Str::random(40));

    // $request->session()->put(
    //     'code_verifier', $code_verifier = Str::random(128)
    // );

    // $codeChallenge = strtr(rtrim(
    //     base64_encode(hash('sha256', $code_verifier, true))
    // , '='), '+/', '-_');

    $query = http_build_query([
        'client_id' => '6',
        'redirect_uri' => 'http://192.168.1.182/project/laravel-client/public/callback',
        'response_type' => 'code',
        'scope' => '',
        'state' => $state,
        // 'code_challenge' => $codeChallenge,
        // 'code_challenge_method' => 'S256',
    ]);
    return redirect('http://192.168.1.182/project/laravel-server/public/oauth/authorize?'.$query);
});

Route::get('/callback', function (Request $request) {
    $state = $request->session()->pull('state');
    // $codeVerifier = $request->session()->pull('code_verifier');

    // dd($request->session()->all());
    // throw_unless(
    //     strlen($state) > 0 && $state === $request->state,
    //     InvalidArgumentException::class
    // );

    $response = Http::asForm()->post('http://192.168.1.182/project/laravel-server/public/oauth/token', [
        'grant_type' => 'authorization_code',
        'client_id' => '6',
        'client_secret' => 'zQfTmUBYMwKBy1tXuASmnaglfUmk7kK6JTWPTFKD',
        'redirect_uri' => 'http://192.168.1.182/project/laravel-client/public/callback',
        // 'code_verifier' => $codeVerifier,
        'code' => $request->code,
    ]);

    return $response->json();
});

// Route::get('/redirect', function () {
//     $query = http_build_query([
//     'client_id' => '7',
//     'redirect_uri' => 'http://192.168.1.182/project/laravel-client/public/callback',
//     'response_type' => 'code',
//     'scope' => '',
//     ]);

//     return redirect('http://192.168.1.182/project/laravel-server/public/oauth/authorize?'.$query);
// });

// Route::get('/callback',function (Request $request) {
//         // dd($request->all());
//         $http = new GuzzleHttp\Client;
//         $response = Http::asForm()->post('http://192.168.1.182/project/laravel-server/public/oauth/token', [
//             'grant_type' => 'authorization_code',
//             'client_id' => '7',
//             'client_secret' => 'zQfTmUBYMwKBy1tXuASmnaglfUmk7kK6JTWPTFKD',      //向server端申請所給的secret
//             'redirect_uri' => 'http://192.168.1.182/project/laravel-client/public/callback',
//             'code' => $request->code,
//     ]);
//      return $response->json();
//     }
// );

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
