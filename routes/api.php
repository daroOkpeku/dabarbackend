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

Route::middleware('auth:sanctum')->group(function(){
    Route::controller(PostController::class)->group(function(){
        Route::get('/logout', 'logout');
    });
});


Route::controller(AuthController::class)->group(function(){
    Route::post('/register', 'register');
    // Route::post('/login', 'login');
    // Route::post('/editor_register', 'editor_register');
    // Route::post("/editor_login", "editor_login");
    Route::post("/admin-register", "admin_register");
     Route::post("/admin_login", "login");
    Route::post("/social-media", "social_media");
    Route::post("/contact", "contact");
    Route::put("/role_confirm",  "role_confirm");
    Route::post("/reset", "reset");
    Route::put("/resetpassword", "resetpassword");
});

Route::controller(PostController::class)->group(function(){
    Route::get("/cryptoapimrk", "cryptoapi");
    Route::post("/subscribe", "subscribe");
    Route::get("/mediadata", "mediadata");
    Route::post('/stories_from_client', 'stories_from_client');

});

// Route::get("/cryptoapimrk", [PostController::class, 'cryptoapi']);
// Route::post("/subscribe", [PostController::class, "subscribe"]);

Route::controller(GetController::class)->group(function(){
Route::get("/searchstories", "searchstories")->where("search", "[a-zA-Z0-9- ]+");
Route::get("/tending", "tending");
Route::get("/popular", "popular");
Route::get("/editor", "editor");
Route::get("/singlestory", "singlestory");
Route::get("/category", "category");
Route::get("/randomcategory", "randomcategory");
Route::get("/categoryfilter", "categoryfilter");
Route::get("/randomstories", "randomstories");
});


// Route::middleware(['auth:sanctum', 'writer'])->group( function(){
//     //  /api/writers/url
//     Route::get('/hello', [AuthController::class, 'postcreate']);
//     // Route::controller(PostController::class)->group(function(){
//     //     Route::post("/createstory", "createstory");
//     // });
//    });



Route::middleware(['auth:sanctum', 'editor'])->prefix('editor')->group(function () {
 //  /api/editor/url
    // Other routes go here

    Route::controller(PostController::class)->group(function(){
        Route::post("/createstory", "createstory");
        Route::put("/editstory", "editstory");
        Route::delete("/deletestory", "deletestory");
        Route::get('/dashbordata', 'dashbordata');
        // Route::get("/userprofile/{id}", "userprofile");
    });

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
        Route::get('/dashboardata', 'dashboardata');
        Route::post("/mediainsert", 'mediainsert');
        Route::get("/recentstories", "recentstories");
        Route::get("/userprofile", "userprofile");
        Route::post("/userprofile", "profilecreate");
        Route::put("/userprofile", "profileupdate");
        Route::get("/profile", 'userprofilex');
        Route::get("/storydatalist", "storydatalist");
        Route::post("/createstory", "createstory");
        Route::get("/storyedit/{id}", "storyedit");
        Route::put("/editstory", "editstory");
        Route::post("/deletesinglestory", "deletesinglestory");
        Route::get("/uploadauth", "uploadauth");
        Route::get('/publishedstories', 'publishedstories');
        Route::get("/searchpublishedstories", "searchpublishedstories");
        Route::get('/unpublishedstories', 'unpublishedstories');
        Route::get('/searchunpublishedstories', 'searchunpublishedstories');
        Route::get("/alluser", "alluser");
        Route::put("/edituser", "edituser");
        Route::delete("/deleteuser", "deleteuser");
        Route::get("/searchuser", "searchuser");
        Route::get("/allusercsv", "allusercsv");
        Route::get("/stories_sections", "stories_sections");
        Route::post("/create_category", "create_category");
        Route::get("/stories_unique_date", "stories_unique_date");
        Route::get("/stories_category", "stories_category");
        Route::get("/all_writer", "all_writer");
        Route::post("/create_writer", "create_writer");
        Route::delete("/delete_writer", "delete_writer");
       });


       Route::controller(AuthController::class)->group(function(){
        Route::put("/edit_editor", "edit_editor");
        Route::put("/edit_writer", "edit_writer");
        Route::get("/single_editor/{id}", "single_editor");
        Route::get("/single_writer/{id}", "single_editor");
        Route::delete("/delete_writer/{id}", "delete_writer");
        Route::delete("/delete_editor/{id}", "delete_editor");
        Route::post('/editor_register', 'editor_register');

    });

    Route::controller(GetController::class)->group(function(){
        Route::get("/allsubscribe", "allsubscribe");
        Route::get("/downloadsubscribe", "downloadsubscribe");
        Route::get("/searchsubsribe", "searchsubsribe");
    });

   });
