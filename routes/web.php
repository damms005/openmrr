<?php

declare(strict_types=1);

use App\Livewire\AdvertiserCheckoutSuccess;
use App\Livewire\CategoryListing;
use App\Livewire\FounderProfile;
use App\Livewire\Home;
use App\Livewire\RfcCommentView;
use App\Livewire\RfcInitiation;
use App\Livewire\RfcResponseForm;
use App\Livewire\StartupDetail;
use Illuminate\Support\Facades\Route;

Route::view('/', 'welcome');
Route::get('/directory', Home::class)->name('home');
Route::get('/category/{category:slug}', CategoryListing::class)->name('category.show');
Route::get('/founder/{handle}', FounderProfile::class)->name('founder.show');
Route::get('/startup/{slug}', StartupDetail::class)->name('startup.show');
Route::get('/advertiser/checkout/success', AdvertiserCheckoutSuccess::class)->name('advertiser.checkout.success');

Route::get('/startup/{startup:slug}/rfc', RfcInitiation::class)->name('rfc.initiation');
Route::get('/rfc/{rfc:uuid}', RfcCommentView::class)->name('rfc.comment.view');
Route::get('/rfc/{rfc:uuid}/respond', RfcResponseForm::class)->middleware('signed:rfc.respond')->name('rfc.respond');
