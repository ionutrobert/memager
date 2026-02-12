<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

use App\Http\Controllers\CnpCheckController;

Route::get('/cnp-check', [CnpCheckController::class, 'check']);

// Language switcher route
Route::get('lang/{lang}', function ($lang) {
    $availableLocales = ['en', 'ro'];

    if (in_array($lang, $availableLocales)) {
        session()->put('current_lang', $lang);
    }

    return redirect()->back();
})->name('lang.switch');


