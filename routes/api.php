<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\GetController;
use App\Http\Controllers\PostController;
use App\Models\User;
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


Route::controller(AuthController::class)->group(function(){
    Route::post('/register', 'register');
    Route::post('/login', 'login');
    Route::post('/editor_register', 'editor_register');
    Route::post("/editor-login", "editor_login");
    Route::post("/admin-register", "admin_register");
    Route::post("/admin-login", "admin_login");
    Route::post("/social-media", "social_media");
    Route::post("/contact", "contact");
    Route::put("/role_confirm/{email}/{verification_code}/{role}",  "role_confirm");
});

Route::controller(PostController::class)->group(function(){
    Route::get("/cryptoapimrk", "cryptoapi");
    Route::post("/subscribe", "subscribe");
});

// Route::get("/cryptoapimrk", [PostController::class, 'cryptoapi']);
// Route::post("/subscribe", [PostController::class, "subscribe"]);

Route::controller(GetController::class)->group(function(){
Route::get("/searchstories", "searchstories")->where("search", "[a-zA-Z0-9- ]+");
Route::get("/tending", "tending");
Route::get("/popular", "popular");
Route::get("/featured", "featured");
Route::get("/singlestory", "singlestory");
Route::get("/downloadsubscribe", "downloadsubscribe");
});


Route::middleware(['auth:sanctum', 'writer'])->group( function(){
    //  /api/writers/url
    Route::get('/hello', [AuthController::class, 'postcreate']);
    Route::controller(PostController::class)->group(function(){
        Route::post("/createstory", "createstory");
    });



   });


Route::middleware(['auth:sanctum', 'editor'])->prefix('editor')->group(function () {
 //  /api/editor/url
    // Other routes go here
});

// 'admin'

Route::middleware(['auth:sanctum', 'admin'])->prefix('admin')->group(function () {
    //  /api/admin/url
       // Other routes go here
       Route::controller(PostController::class)->group(function(){
        Route::post("/topstories", 'topstories');
        Route::post("/featuredstories", "featuredstories");
        Route::post("/tendingstories", "tendingstories");
        Route::post("/populastories", "populastories");
       });


       Route::controller(AuthController::class)->group(function(){
        Route::put("/edit_editor", "edit_editor");
        Route::put("/edit_writer", "edit_writer");
        Route::get("/single_editor/{id}", "single_editor");
        Route::get("/single_writer/{id}", "single_editor");
        Route::delete("/delete_writer/{id}", "delete_writer");
        Route::delete("/delete_editor/{id}", "delete_editor");
    });
   });
