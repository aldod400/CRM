<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LanguageController;
use App\Jobs\SendReminderNotification;
use App\Models\Reminder;
use App\Models\User;
use Illuminate\Http\Request;

Route::get('/', function () {
    return redirect('/admin');
});

// Language Switch
Route::get('language/{lang}', [LanguageController::class, 'switchLang'])->name('language.switch');
Route::post('/notifications/mark-as-read', function (Request $request) {
    $notificationId = $request->input('id');
    $notification = auth('web')->user()->unreadNotifications()->where('id', $notificationId)->first();
    if ($notification) {
        $notification->markAsRead();
    }
    return response()->json(['status' => 'success']);
})->name('notifications.read');

Route::get('/notifications/unread-count', function () {
    return response()->json([
        'count' => auth('web')->user()->unreadNotifications()->count(),
    ]);
})->middleware('auth');

Route::get('/notifications/unread-list', function () {
    return response()->json([
        'notifications' => auth('web')->user()->unreadNotifications->map(function ($n) {
            return [
                'id' => $n->id,
                'title' => $n->data['title'] ?? '',
                'url' => $n->data['url'] ?? '#',
                'time' => \Carbon\Carbon::parse($n->created_at)->diffForHumans(),
                'time_raw' => $n->created_at->toISOString(),
            ];
        }),
    ]);
})->middleware('auth');

Route::get('/notifications/read-and-redirect/{id}', function ($id) {
    $notification = auth('web')->user()->notifications()->findOrFail($id);

    if ($notification->unread()) {
        $notification->markAsRead();
    }

    return redirect($notification->data['url'] ?? '/admin');
})->middleware('auth')->name('notifications.read.redirect');
