<?php

use App\Http\Controllers\TestController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
Route::middleware(['throttle:60,1'])->group(function () {

});
include 'Pages/FAQ/faq.php';
include 'Pages/Contacts/contacts.php';
include 'Pages/BaseOnlyTextPages/base-only-text-pages.php';
include 'Pages/News/news.php';
include 'Pages/Gallery/gallery.php';
include 'Pages/Auth/auth.php';
include 'Pages/Home/home.php';
include 'Pages/User/user.php';
include 'Pages/Profile/profile.php';
include 'Pages/Personal/personal.php';

Route::post('/test', [TestController::class, 'test']);
