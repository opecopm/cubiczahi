<?php

use App\Livewire\Admin\Dashboard;
use App\Livewire\Admin\Profile;
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\Support\Facades\Route;

// ── Admin routes ─────────────────────────────────────────────────────────────
Route::prefix('admin')->name('admin.')->middleware(['auth', 'verified', 'user_type:backend'])->group(function () {
    Route::get('dashboard', Dashboard::class)->name('dashboard');
    Route::get('profile', Profile::class)->name('profile');

    Route::post('logout', function () {
        \Illuminate\Support\Facades\Auth::guard('web')->logout();
        \Illuminate\Support\Facades\Session::invalidate();
        \Illuminate\Support\Facades\Session::regenerateToken();
        return redirect()->route('admin.login');
    })->name('logout');

    Route::post('notifications/mark-all-read', function () {
        $user = auth()->user();
        if ($user) {
            $user->unreadNotifications->markAsRead();
        }
        return back();
    })->name('notifications.mark-all-read');

    Route::get('notifications/{notification}', function (DatabaseNotification $notification) {
        $user = auth()->user();
        if (! $user || $notification->notifiable_id !== $user->getKey() || $notification->notifiable_type !== $user->getMorphClass()) {
            abort(404);
        }
        $notification->markAsRead();
        $data = (array) ($notification->data ?? []);
        $url  = $data['url'] ?? $data['action_url'] ?? null;
        return $url ? redirect()->to($url) : back();
    })->name('notifications.open');
});

// ── Language switcher ────────────────────────────────────────────────────────
// Redirects the user to the locale-prefixed equivalent of the current page.
// e.g. clicking Arabic on /shop  → /ar/shop
//      clicking English on /ar/shop → /shop
Route::get('/locale/{code}', function (string $code) {
    \Illuminate\Support\Facades\Cache::forget('active_languages');
    return redirect(locale_url($code));
})->name('locale.switch');

// ── Customer routes — English (default, no prefix) ───────────────────────────
$customerRoutes = require __DIR__.'/customer.php';
Route::group([], $customerRoutes);

// ── Customer routes — Arabic (/ar prefix, route names prefixed with 'ar.') ───
Route::prefix('ar')->name('ar.')->group($customerRoutes);

// ── Auth routes (password reset, etc.) ──────────────────────────────────────
require __DIR__.'/auth.php';

// ── Serve media-content files by filename ────────────────────────────────────
Route::get('/media-content/{filename}', function ($filename) {
    $media = \Spatie\MediaLibrary\MediaCollections\Models\Media::where('file_name', $filename)->first();
    if (! $media) {
        $media = \Spatie\MediaLibrary\MediaCollections\Models\Media::where('name', $filename)->first();
    }

    if (! $media || ! file_exists($media->getPath())) {
        abort(404);
    }

    return response()->file($media->getPath(), [
        'Content-Type' => $media->mime_type ?: 'application/octet-stream',
        'Content-Disposition' => 'inline; filename="' . $media->file_name . '"',
    ]);
})->name('media-content.serve');
