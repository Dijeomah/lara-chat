<?php

    use App\Http\Controllers\AdminAuth\AdminAuthController;
    use App\Http\Controllers\Auth\AuthController;
    use App\Http\Controllers\Chat\AdminChatController;
    use App\Http\Controllers\Chat\ChatController;
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

//Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//    return $request->user();
//});

    Route::prefix('auth')->group(function () {
        Route::post('login', [AuthController::class, 'login']);
        Route::post('register', [AuthController::class, 'register']);
        Route::post('logout', [AuthController::class, 'logout']);
        Route::post('refresh', [AuthController::class, 'refresh']);
    });

    Route::prefix('admin/auth')->group(function () {
        Route::post('login', [AdminAuthController::class, 'login']);
        Route::post('register', [AdminAuthController::class, 'register']);
        Route::post('logout', [AdminAuthController::class, 'logout']);
        Route::post('refresh', [AdminAuthController::class, 'refresh']);
    });

    Route::prefix('chat-room')->group(function () {
        Route::get('/', [ChatController::class, 'rooms']);
        Route::get('/{roomId}/messages', [ChatController::class, 'messages']);
        Route::post('/{roomId}/message', [ChatController::class, 'newMessage']);
    });

    Route::prefix('admin/chat-room')->middleware("admin.verify")->group(function () {
//    Route::group(["prefix" => "admin/chat-room", "middleware" => "admin.verify"], function ()  {
        Route::get('/', [AdminChatController::class, 'rooms']);
        Route::get('/{roomId}/messages', [AdminChatController::class, 'allMyMessages']);
        Route::get('/{roomId}/message/{userId}', [AdminChatController::class, 'myMessages']);
        Route::post('/{roomId}/message/{userId}', [AdminChatController::class, 'sendMessage']);
        Route::put('/chat/end', [AdminChatController::class, 'updateEngagementStatus']);

//        Route::post('/{roomId}/message/{userId}', [AdminChatController::class, 'sendMessage']);
    });



