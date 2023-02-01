<?php

use App\Http\Controllers\Api\AccountController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\BookmarkController;
use App\Http\Controllers\Api\KollectionController;
use App\Http\Controllers\Api\GenreController;
use App\Http\Controllers\Api\HomeController;
use App\Http\Controllers\Api\LanguageController;
use App\Http\Controllers\Api\PlanController;
use App\Http\Controllers\Api\ProfileController;
use App\Http\Controllers\Api\SearchController;
use App\Http\Controllers\Api\SectionsController;
use App\Http\Controllers\Api\SeriesController;
use App\Http\Controllers\Api\SubscriptionController;
use App\Http\Controllers\Api\SupportController;
use App\Http\Controllers\Api\VideoController;
use App\Http\Controllers\WebhookController;
use Illuminate\Support\Facades\Route;

Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');

Route::post('/register/send-otp', [AuthController::class, 'registerSendOtp']);
Route::post('/register/verify-otp', [AuthController::class, 'registerVerifyOtp']);
Route::post('/register', [AuthController::class, 'register']);

// Reset Password send-otp and verify-otp endpoint will be deprecated in the future
Route::post('/reset-password/send-otp', [AuthController::class, 'resetPasswordSendOtp']);
Route::post('/reset-password/verify-otp', [AuthController::class, 'resetPasswordVerifyOTP']);
Route::post('/reset-password', [AuthController::class, 'resetPassword']);

Route::post('/send-otp', [AuthController::class, 'sendOtp']);
Route::post('/verify-otp', [AuthController::class, 'verifyOtp']);

Route::group(['middleware' => ['auth:sanctum']], function () {

    // Account
    Route::get('/account', [AccountController::class, 'account'])->name('account');
    Route::post('/account', [AccountController::class, 'update'])->name('account.update');
    Route::get('/is_active_subscriber', [AccountController::class, 'isActiveSubscriber']);
    Route::post('/account/pin', [AuthController::class, 'setAccountPin']);
    Route::post('/account/pin/verify', [AuthController::class, 'verifyAccountPin']);

    // Profiles
    Route::get('/profiles', [ProfileController::class, 'index'])->name('profiles.index');
    Route::get('/profiles/{profile:id}', [ProfileController::class, 'show'])->name('profiles.show');
    Route::get('/profiles/{profile:id}/activate', [ProfileController::class, 'activateProfile'])->name('profiles.activate');
    Route::get('/active-profile', [ProfileController::class, 'activeProfile'])->name('profiles.active');
    Route::post('/profiles', [ProfileController::class, 'store'])->name('profiles.store');
    Route::patch('/profiles/{profile:id}', [ProfileController::class, 'update'])->name('profiles.update');
    Route::delete('/profiles/{profile:id}', [ProfileController::class, 'delete'])->name('profiles.delete');
    Route::get('/avatars', [ProfileController::class, 'avatars'])->name('avatars.index');
    Route::post('/profiles/{profile:id}/toggle/search-history', [ProfileController::class, 'toggleSearchHistory'])->name('profiles.toggle.search-history');
    Route::post('/profiles/{profile:id}/toggle/watch-history', [ProfileController::class, 'toggleWatchHistory'])->name('profiles.toggle.watch-history');
    Route::post('/profiles/{profile:id}/clear-history', [ProfileController::class, 'clearHistory'])->name('profiles.clear-history');
    //TODO - Deprecated endpoint. As it is merged in update profile endpoint
    Route::post('/profiles/{profile:id}/preferences', [ProfileController::class, 'preferences'])->name('profiles.preferences');

    Route::post('/profiles/pin/set', [ProfileController::class, 'setProfilePin'])->name('profiles.pin.set');
    Route::post('/profiles/pin/verify', [ProfileController::class, 'verifyProfilePin'])->name('profiles.pin.verify');
    Route::post('/profiles/pin/clear', [ProfileController::class, 'clearProfilePin'])->name('profiles.pin.clear');

    // Search
    Route::get('/search', [SearchController::class, 'index'])->name('search');
    Route::get('/search-history', [SearchController::class, 'history'])->name('searches.history');
    Route::delete('/search-term/{search:id}', [SearchController::class, 'delete'])->name('searches.delete');

    // Featured
    Route::get('/featured', [HomeController::class, 'featured'])->name('featured');
    Route::get('/latest', [HomeController::class, 'latest'])->name('latest');
    Route::get('/coming-soon', [HomeController::class, 'comingSoon'])->name('comingSoon');
    Route::get('/top-picks-for-you', [HomeController::class, 'topPicksForYou'])->name('topPicksForYou');
    Route::get('/continue-watching', [HomeController::class, 'continueWatching'])->name('continueWatching');

    // Series
    Route::get('/series', [SeriesController::class, 'index'])->name('series.index');
    Route::get('/series/{series:id}', [SeriesController::class, 'show'])->name('series.show');
    Route::post('/series/{series:id}/toggle/{type?}', [SeriesController::class, 'toggleBookmark'])->name('series.toggleBookmark');

    // Videos
    Route::get('/videos/{video:id}', [VideoController::class, 'show'])->name('videos.show');
    Route::post('/videos/{video:id}/toggle/{type?}', [VideoController::class, 'toggleBookmark'])->name('video.toggleBookmark');

    // Shorts
    Route::get('/shorts', [VideoController::class, 'shorts'])->name('shorts.index');

    // Genres
    Route::get('/genres', [GenreController::class, 'index'])->name('genres.index');
    Route::get('/genres/{genre:id}', [GenreController::class, 'show'])->name('genres.show');

    // Languages
    Route::get('/languages', [LanguageController::class, 'index'])->name('languages.index');
    Route::get('/languages/{language:id}', [LanguageController::class, 'show'])->name('languages.show');

    // Collections
    Route::get('/collections', [KollectionController::class, 'index'])->name('collections.index');
    Route::get('/collections/{collection:id}', [KollectionController::class, 'show'])->name('collections.show');

    // Plans
    Route::get('/plans', [PlanController::class, 'index'])->name('plans.index');
    Route::get('/plans/{plan:id}', [PlanController::class, 'show'])->name('plans.show');

    // Subscriptions
    Route::post('/subscriptions/create', [SubscriptionController::class, 'storeSubscription'])->name('subscriptions.create');
    Route::post('/subscriptions', [SubscriptionController::class, 'storeSubscription'])->name('subscriptions.store');
    Route::post('/subscriptions/confirm', [SubscriptionController::class, 'confirmSubscription'])->name('subscriptions.confirm');

    // Bookmarks
    Route::get('/favourites', [BookmarkController::class, 'favourites'])->name('bookmarks.favourites');
    Route::get('/watchlist', [BookmarkController::class, 'watchlist'])->name('bookmarks.watchlist');

    // Support
    Route::get('/support-categories', [SupportController::class, 'supportCategories'])->name('support-categories.index');
    Route::get('/support-categories/{support_category:id}', [SupportController::class, 'showSupportCategories'])->name('support-categories.show');

    // Sections
    // HomeScreenSections
    Route::get('/home-sections', [SectionsController::class, 'homeScreen'])->name('home.sections');
});

// Webhooks
Route::post('/webhook/subscribe', [WebhookController::class, 'subscribe']);
Route::post('/webhook/transcoding-job-status', [WebhookController::class, 'createAwsMediaConvertStatus']);


Route::fallback(function () {
    return response()->json([
        'message' => 'Not Found. If error persists, contact super@admin.com', ], 404);
});
