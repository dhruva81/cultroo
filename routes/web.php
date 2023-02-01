<?php

use App\Http\Controllers\OTPController;
use App\Http\Controllers\ReleasesController;
use App\Livewire\Users\RegisterAdminMember;
use App\Livewire\Users\RegisterSuperAdmin;
use App\Notifications\SendOTPBeforeRegistrationNotification;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/login');
Route::get('/otp', OTPController::class);
Route::get('/releases', ReleasesController::class);

Route::get('/register', RegisterAdminMember::class)->middleware('guest')->name('admin.register');

Route::get('/register-super-admin', RegisterSuperAdmin::class)->middleware('guest')->name('super-admin.register');

Route::view('/privacy-policy', 'privacy-policy')->name('privacy-policy');
Route::view('/cancellation-and-refund-policy', 'cancellation-policy')->name('cancellation-policy');
Route::view('/terms-of-use', 'terms-of-use')->name('terms-of-use');
Route::redirect('/docs', '/docs/api');

Route::group([
    'middleware' => [
        'auth:sanctum',
        'admin',
        config('jetstream.auth_session'),
        'verified',
    ],
    'prefix' => 'admin',
], function () {

    Route::redirect('/', '/admin/dashboard');
    Route::get('/dashboard', \App\Livewire\Dashboard\Dashboard::class)->name('dashboard');

    // Import
    Route::get('/import', [\App\Http\Controllers\ImportController::class, 'importPage'])->name('import.page');
    Route::post('/import', [\App\Http\Controllers\ImportController::class, 'importStore'])->name('import.store');

    //Users
    Route::get('/users', \App\Livewire\Users\UsersDatatable::class)->name('users.datatable');
    Route::get('/members', \App\Livewire\Users\AdminMembersDatatable::class)->name('members.datatable');
    Route::get('/members/pending', \App\Livewire\Users\PendingMembersDatatable::class)->name('members.pending.datatable');
    Route::get('/members/invite', \App\Livewire\Users\InviteAdminMembers::class)->name('members.invite');
    Route::get('/users/{user}', \App\Livewire\Users\UserShow::class)->name('users.show');

    // Logs
    Route::get('/logs/auth-logs', \App\Livewire\Logs\AuthenticationLogsDatatable::class)->name('auth-logs.datatable');
    Route::get('/logs/activity-logs', \App\Livewire\Logs\ActivityLogsDatatable::class)->name('activity-logs.datatable');
    Route::get('/logs/watch-history', \App\Livewire\Logs\WatchHistoriesDatatable::class)->name('watch-history.datatable');
    Route::get('/logs/access-tokens', \App\Livewire\Logs\AccessTokensDatatable::class)->name('access-tokens.datatable');
    Route::get('/logs/email-logs', \App\Livewire\Logs\EmailLogsDatatable::class)->name('email-logs.datatable');

    // Videos
    Route::get('/shorts', \App\Livewire\Videos\VideosDatatable::class)->name('shorts.datatable');
    Route::get('/shorts/create', \App\Livewire\Videos\VideoCreateOrUpdate::class)->name('shorts.create');
    Route::get('/series/{series}/episodes/create', \App\Livewire\Videos\VideoCreateOrUpdate::class)->name('episodes.create');
    Route::get('/videos/untranscoded', \App\Livewire\Videos\VideosUntranscodedDatatable::class)->name('videos.untranscoded.datatable');
    Route::get('/videos/{video}', \App\Livewire\Videos\VideoShow::class)->name('videos.show');
    Route::get('/videos/{video}/edit', \App\Livewire\Videos\VideoCreateOrUpdate::class)->name('videos.edit');
    Route::get('/videos/{video}/update-video-file', \App\Livewire\Videos\VideoReplace::class)->name('videos.replace');
    Route::get('/videos/create/aws', \App\Livewire\Videos\VideoCreateFromAws::class)->name('videos.create-from-aws');
    Route::get('/sync', \App\Livewire\Videos\VideosSync::class)->name('videos.sync');

    Route::get('/media-convert-responses', \App\Livewire\Videos\MediaConvertResponsesDatatable::class)->name('media-convert-responses.datatable');

    // Characters
    Route::get('/characters', \App\Livewire\Characters\CharactersDatatable::class)->name('characters.datatable');
    Route::get('/characters/create', \App\Livewire\Characters\CharacterCreateOrUpdate::class)->name('characters.create');
    Route::get('/characters/{character}/edit', \App\Livewire\Characters\CharacterCreateOrUpdate::class)->name('characters.edit');

    // Genres
    Route::get('/categories', \App\Livewire\Genres\GenresDatatable::class)->name('genres.datatable');
    Route::get('/categories/create', \App\Livewire\Genres\GenreCreateOrUpdate::class)->name('genres.create');
    Route::get('/categories/{genre}', \App\Livewire\Genres\GenreShow::class)->name('genres.show');
    Route::get('/categories/{genre}/edit', \App\Livewire\Genres\GenreCreateOrUpdate::class)->name('genres.edit');

    // Series
    Route::get('/series', \App\Livewire\Series\SeriesDatatable::class)->name('series.datatable');
    Route::get('/series/create', \App\Livewire\Series\SeriesCreateOrUpdate::class)->name('series.create');
    Route::get('/series/{series}', \App\Livewire\Series\SeriesShow::class)->name('series.show');

    // Collections
    Route::get('/collections', \App\Livewire\Collections\CollectionsDatatable::class)->name('collections.datatable');
    Route::get('/collections/create', \App\Livewire\Collections\CollectionCreateOrUpdate::class)->name('collections.create');
    Route::get('/collections/{collection}', \App\Livewire\Collections\CollectionShow::class)->name('collections.show');
    Route::get('/collections/{collection}/edit', \App\Livewire\Collections\CollectionCreateOrUpdate::class)->name('collections.edit');

    // Languages
    Route::get('/languages', \App\Livewire\Languages\LanguagesDatatable::class)->name('languages.datatable');
    Route::get('/languages/create', \App\Livewire\Languages\LanguageCreateOrUpdate::class)->name('languages.create');
    Route::get('/languages/{language}/edit', \App\Livewire\Languages\LanguageCreateOrUpdate::class)->name('languages.edit');

    // Avatars
    Route::get('/avatars', \App\Livewire\Avatars\AvatarsDatatable::class)->name('avatars.datatable');
    Route::get('/avatars/create', \App\Livewire\Avatars\AvatarsCreate::class)->name('avatars.create');
    Route::get('/avatars/{avatar}/edit', \App\Livewire\Avatars\AvatarCreateOrUpdate::class)->name('avatars.edit');

    // Tags
    Route::get('/tags', \App\Livewire\Tags\TagsDatatable::class)->name('tags.datatable');
    Route::get('/tags/{tag}', \App\Livewire\Tags\TagShow::class)->name('tags.show');

    // Plans
    Route::get('/plans', \App\Livewire\Plans\PlansDatatable::class)->name('plans.datatable');
    Route::get('/plans/create', \App\Livewire\Plans\PlanCreateOrUpdate::class)->name('plans.create');
    Route::get('/plans/{plan}', \App\Livewire\Plans\PlanShow::class)->name('plans.show');
    Route::get('/plans/{plan}/edit', \App\Livewire\Plans\PlanCreateOrUpdate::class)->name('plans.edit');

    // Subscriptions
    Route::get('/subscriptions', \App\Livewire\Subscriptions\SubscriptionsDatatable::class)->name('subscriptions.datatable');

    Route::get('/more', \App\Livewire\Settings\MoreLinks::class)->name('more-links');

    // Support Categories
    Route::get('/support-categories', \App\Livewire\Support\SupportCategoriesDatatable::class)->name('support-categories.datatable');
    Route::get('/support-categories/create', \App\Livewire\Support\SupportCategoryCreateOrUpdate::class)->name('support-categories.create');
    Route::get('/support-categories/{support_category}/edit', \App\Livewire\Support\SupportCategoryCreateOrUpdate::class)->name('support-categories.edit');

    // Support FAQs
    Route::get('/support-faqs', \App\Livewire\Support\FaqsDatatable::class)->name('support-faqs.datatable');
    Route::get('/support-faqs/create', \App\Livewire\Support\FaqsCreate::class)->name('support-faqs.create');
    Route::get('/support-faqs/{faq}/edit', \App\Livewire\Support\FaqEdit::class)->name('support-faqs.edit');

    //sections
    Route::get('/sections', \App\Livewire\Sections\SectionsDatatable::class)->name('sections.datatable');
    Route::get('/sections/create', \App\Livewire\Sections\SectionCreateOrUpdate::class)->name('sections.create');
    Route::get('/sections/{section}/edit', \App\Livewire\Sections\SectionCreateOrUpdate::class)->name('sections.edit');
    Route::get('/sections/{section}', \App\Livewire\Sections\SectionShow::class)->name('sections.show');


});

require __DIR__.'/customer.php';
