<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

// Auth Controllers
use App\Http\Controllers\Auth\ClientRegistrationController;
use App\Http\Controllers\Auth\StaffRegistrationController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\ResetPasswordController;

// Other Controllers
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\ApartmentController;
use App\Http\Controllers\ApartmentBookingController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ClientBookingController;
use App\Http\Controllers\StaffBookingController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\BookingsController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\SubCategoryController;
use App\Http\Controllers\SubCategoryAvailabilityController;
use App\Http\Controllers\PaymentHistoryController;
use App\Http\Controllers\TopUpController;
use App\Http\Controllers\AccountManagementController;
use App\Http\Controllers\BankDetailsController;
use App\Http\Controllers\StaffPaymentsController;
use App\Http\Controllers\PaymentSettingsController;
use App\Http\Controllers\AccountSettingsController;
use App\Http\Controllers\NotificationSettingsController;

use App\Models\Category;
use App\Models\Booking;

/*
|--------------------------------------------------------------------------
| Public / Guest Routes
|--------------------------------------------------------------------------
*/

// ========== HOME PAGE + PUBLIC CATEGORIES ==========
Route::get('/', [HomeController::class, 'index'])->name('home');

// Public “browse” categories/subcategories (no auth)
Route::get('/categories', [HomeController::class, 'getCategories'])->name('categories');
Route::get('/categories/{id}/subcategories', [HomeController::class, 'getSubCategories'])->name('subcategories');
Route::get('/subcategories/{id}', [HomeController::class, 'getSubCategory'])->name('singleSubCategory');

// ========== GUEST (Registration & Login) ==========
Route::get('/register_client', [ClientRegistrationController::class, 'showClientRegistrationForm'])
    ->middleware('guest')
    ->name('register_client');

Route::post('/register_client', [ClientRegistrationController::class, 'registerClient'])
    ->name('register_client.submit');

// Staff registration
Route::get('/register_staff', [StaffRegistrationController::class, 'showStaffRegistrationForm'])
    ->middleware('guest')
    ->name('register_staff');

Route::post('/register_staff', [StaffRegistrationController::class, 'registerStaff'])
    ->name('register_staff.submit');

// Login
Route::get('/login', [LoginController::class, 'showLoginForm'])
    ->middleware('guest')
    ->name('login');

Route::post('/login', [LoginController::class, 'submitLogin'])
    ->name('login.submit');

// ========== LOGOUT ==========
Route::post('/logout', [LoginController::class, 'logout'])
    ->name('logout');

// ========== PASSWORD RESET ==========
Route::get('password/reset', [ForgotPasswordController::class, 'showLinkRequestForm'])
    ->name('password.request');

Route::post('password/email', [ForgotPasswordController::class, 'sendResetLinkEmail'])
    ->name('password.email');

Route::get('password/reset/{token}', [ResetPasswordController::class, 'showResetForm'])
    ->name('password.reset');

Route::post('password/reset', [ResetPasswordController::class, 'reset'])
    ->name('password.update');

// “Suspended Notice” page
Route::get('/suspended-notice', function(){
    return view('auth.suspended');
})->name('suspended.notice');

/*
|--------------------------------------------------------------------------
| Authenticated Routes (must pass 'auth' & 'checkSuspended')
|--------------------------------------------------------------------------
*/
Route::middleware(['auth','checkSuspended'])->group(function() {

    // ========== DASHBOARDS ==========
    Route::get('/client/dashboard', [DashboardController::class, 'clientIndex'])
        ->name('client.dashboard');

    Route::get('/staff/dashboard', [DashboardController::class, 'staffIndex'])
        ->name('staff.dashboard');

    // ========== SETTINGS ==========
    Route::get('/settings', [SettingsController::class, 'index'])
        ->name('client.settings');

    Route::get('/site-settings', [SettingsController::class, 'siteSettings'])
        ->name('site.settings');

    Route::get('/staff/settings', [SettingsController::class, 'staffSettings'])
        ->name('staff.settings');

    // Refund settings (staff only, example)
    Route::get('/staff/settings/refund', [SettingsController::class, 'staffSettings'])
        ->name('staff.refund.settings');

    Route::post('/staff/settings/refund', [SettingsController::class, 'updateRefundPercentage'])
        ->name('staff.refund.update');

    // Notification Settings
    Route::get('/notifications/index', [NotificationSettingsController::class, 'index'])
        ->name('notifications.index');

    Route::post('/notifications/index', [NotificationSettingsController::class, 'update'])
        ->name('notifications.update');

    // Account Settings (Staff, example)
    Route::get('/settings/account', [AccountSettingsController::class, 'index'])
        ->name('staff.settings.account');

    Route::post('/settings/account', [AccountSettingsController::class, 'update'])
        ->name('staff.settings.account.update');

    // ========== APARTMENTS ==========
    Route::get('/apartment/create', [ApartmentController::class, 'create'])
        ->name('apartment.create');

    Route::post('/apartment', [ApartmentController::class, 'store'])
        ->name('apartment.store');

    Route::get('/api/apartments/{id}/bookings', [ApartmentBookingController::class, 'getBookings']);

    // ========== GENERIC BOOKING AN APARTMENT ==========
    Route::get('/book-apartment', [BookingController::class, 'create'])
        ->name('book.apartment');

    Route::post('/book-apartment', [BookingController::class, 'store'])
        ->name('apartment.book');

    // Example “fetch subcategories” API (for booking form)
    Route::get('/api/categories/{catId}/subcategories', [BookingController::class,'fetchSubCategories']);
    Route::get('/api/subcategories/{subCatId}/calendar', [BookingController::class,'fetchCalendar']);
    Route::get('/api/subcategories/{subCatId}', [BookingController::class, 'showSubCategory']);

    // ========== BOOKING MANAGEMENT (BookingController) ==========
    Route::get('/bookings/staff-change', [BookingController::class, 'staffChangeList'])
        ->name('bookings.staff_change');

    Route::get('/bookings/client-change', [BookingController::class, 'clientChangeList'])
        ->name('bookings.client_change');

    Route::get('/bookings/{booking}/edit', [BookingController::class, 'edit'])
        ->name('bookings.edit');

    Route::post('/bookings/{booking}', [BookingController::class, 'update'])
        ->name('bookings.update');

    Route::delete('/bookings/{booking}', [BookingController::class, 'destroy'])
        ->name('bookings.destroy');

    // ========== CLIENT BOOKING ACTIONS ==========
    Route::get('/booking/cancel', [ClientBookingController::class, 'cancel'])
        ->name('booking.cancel');

    Route::post('/booking/cancel/{bookingId}', [ClientBookingController::class, 'cancelBooking'])
        ->name('booking.cancel.do');

    Route::get('/booking/view', [ClientBookingController::class, 'view'])
        ->name('booking.view');

    // ========== STAFF BOOKING ACTIONS ==========
    Route::get('/staff/booking/cancel', [StaffBookingController::class, 'cancel'])
        ->name('staff.booking.cancel');

    Route::post('/staff/booking/cancel/{bookingId}', [StaffBookingController::class, 'doCancel'])
        ->name('staff.booking.cancel.do');

    Route::get('/staff/booking/view', [StaffBookingController::class, 'view'])
        ->name('staff.booking.view');

    // ========== BookingsController ==========
    Route::get('/bookings', [BookingsController::class, 'index'])
        ->name('bookings.index');

    Route::get('/bookings/{booking}', [BookingsController::class, 'show'])
        ->name('bookings.show');

        // New route for public_id
Route::get('/bookings/public/{public_id}', [BookingsController::class, 'showByPublicId'])->name('bookings.show.public');
    Route::delete('/bookings/{bookingId}', [BookingsController::class, 'destroy'])
        ->name('bookings.destroy');

    Route::post('/bookings/{bookingId}/extend', [BookingsController::class, 'extendWaitTime'])
        ->name('bookings.extend');

    Route::post('/bookings/{bookingId}/cancel', [BookingsController::class,'cancelBooking'])
        ->name('bookings.cancel');

    Route::get('/bookings/client-cancel', [BookingsController::class, 'showClientCancelPage'])
        ->name('bookings.client.cancel');
        
        // In routes/web.php
Route::get('/bookings/uuid/{uuid}', [\App\Http\Controllers\BookingsController::class, 'showByUuid'])
->name('bookings.show.uuid');

    // ========== PAYMENTS ==========
    // Payment Settings (Client or global)
    Route::get('/payment/settings', [PaymentController::class, 'index'])
        ->name('payment.settings');

    // Payment flow on a booking
    Route::get('/booking/{booking}/payment', [PaymentController::class, 'confirmPayment'])
        ->name('booking.payment');

    Route::post('/booking/{booking}/pay', [PaymentController::class, 'processPayment'])
        ->name('booking.pay');

    // Pre-book & confirm flow
    Route::post('/pre-book', [PaymentController::class, 'preCheckAndCreate'])
        ->name('pre.book');

    Route::get('/payment/confirm/{bookingId}', [PaymentController::class, 'confirmPayment'])
        ->name('payment.confirm');

    Route::post('/payment/process/{bookingId}', [PaymentController::class, 'processPayment'])
        ->name('payment.process');

    Route::get('/payment/success/{bookingId}', [PaymentController::class, 'paymentSuccess'])
        ->name('payment.success');

    Route::get('/payment/error/{bookingId}', [PaymentController::class, 'paymentError'])
        ->name('payment.error');

    // Staff Payment Settings
    Route::get('/staff/payment/settings', [PaymentController::class, 'staffIndex'])
        ->name('staff.payment.settings');

    Route::post('/staff/payment/settings/toggle-email', [PaymentController::class,'toggleEmailNotify'])
        ->name('staff.payment.toggle_email');

    // Staff daily payments
    Route::get('/staff/payments/daily', [StaffPaymentsController::class, 'dailyPayments'])
        ->name('staff.payments.daily');

    // ========== MANAGE CATEGORIES & SUBCATEGORIES (STAFF) ==========
    // Use a prefix (e.g. "/manage") to avoid clashing with public /categories
    Route::prefix('manage')->group(function() {

        // ----- Categories -----
        Route::get('/categories', [CategoryController::class, 'index'])
            ->name('categories.index');

        Route::post('/categories', [CategoryController::class, 'store'])
            ->name('categories.store');

        Route::get('/categories/{category}/edit', [CategoryController::class, 'edit'])
            ->name('categories.edit');

        Route::post('/categories/{category}/update', [CategoryController::class, 'update'])
            ->name('categories.update');

        Route::delete('/categories/{category}', [CategoryController::class, 'destroy'])
            ->name('categories.destroy');

        // ----- Subcategories -----
        // Create form => GET /manage/categories/{catId}/subcategories/create
        Route::get('/categories/{catId}/subcategories/create', [SubCategoryController::class, 'create'])
            ->name('subcategories.create');

        // Store => POST /manage/categories/{catId}/subcategories
        Route::post('/categories/{catId}/subcategories', [SubCategoryController::class, 'store'])
            ->name('subcategories.store');

        // List all subcategories
        Route::get('/subcategories', [SubCategoryController::class, 'index'])
            ->name('subcategories.index');

        // Edit subcategory => GET /manage/subcategories/{subCategory}/edit
        Route::get('/subcategories/{subCategory}/edit', [SubCategoryController::class, 'edit'])
            ->name('subcategories.edit');

        // Update subcategory => POST /manage/subcategories/{subCategory}
        Route::post('/subcategories/{subCategory}', [SubCategoryController::class, 'update'])
            ->name('subcategories.update');

        // Destroy subcategory => DELETE /manage/subcategories/{subCategory}
        Route::delete('/subcategories/{subCategory}', [SubCategoryController::class, 'destroy'])
            ->name('subcategories.destroy');

        // Subcategory images
        Route::post('/subcategories/{subCategory}/images',
            [SubCategoryController::class,'storeImages']
        )->name('subcategories.images.store');

        Route::delete('/subcategories/images/{image}',
            [SubCategoryController::class,'destroyImage']
        )->name('subcategories.images.destroy');

        // Subcategory availability
        Route::get('/subcategories/{subCategory}/availability',
            [SubCategoryController::class,'availabilityIndex']
        )->name('subcategories.availability.index');

        Route::post('/subcategories/{subCategory}/availability',
            [SubCategoryController::class,'availabilityStore']
        )->name('subcategories.availability.store');

        Route::post('/availability/{availability}/update',
            [SubCategoryController::class,'availabilityUpdate']
        )->name('subcategories.availability.update');

        Route::delete('/availability/{availability}',
            [SubCategoryController::class,'availabilityDestroy']
        )->name('subcategories.availability.destroy');
    });

    // ========== CLIENT vs. STAFF Booking Flows (Optional) ==========
    Route::get('/client/book-apartment', [ClientBookingController::class, 'create'])
        ->name('client.book.apartment');
    Route::post('/client/book-apartment', [ClientBookingController::class, 'store'])
        ->name('client.book.apartment.store');

    Route::get('/staff/book-apartment', [StaffBookingController::class, 'create'])
        ->name('staff.book.apartment');
    Route::post('/staff/book-apartment', [StaffBookingController::class, 'store'])
        ->name('staff.book.apartment.store');

    // ========== PAYMENT HISTORY ==========
    Route::get('/payment/history', [PaymentHistoryController::class,'index'])
        ->name('client.payment_history');

    // ========== TOP-UP (e.g. wallet) ==========
    Route::get('/topup', [TopUpController::class,'showForm'])
        ->name('topup.form');

    Route::post('/topup', [TopUpController::class,'submitTopUp'])
        ->name('topup.submit');

    // Staff: list top-ups
    Route::get('/staff/topups', [TopUpController::class,'indexStaff'])
        ->name('staff.topups.index');

    // Approve / Reject top-ups
    Route::post('/staff/topups/{topUp}/approve', [TopUpController::class,'approve'])
        ->name('staff.topups.approve');

    Route::post('/staff/topups/{topUp}/reject', [TopUpController::class,'reject'])
        ->name('staff.topups.reject');

    // ========== STAFF ACCOUNT MANAGEMENT ==========
    Route::get('/staff/account-management', [AccountManagementController::class,'index'])
        ->name('staff.account_management');

    Route::get('/staff/user/{userId}/profile', [AccountManagementController::class,'showProfile'])
        ->name('staff.show_profile');

    Route::post('/staff/user/{userId}/suspend', [AccountManagementController::class,'suspend'])
        ->name('staff.suspend');

    Route::post('/staff/user/{userId}/unsuspend', [AccountManagementController::class,'unsuspend'])
        ->name('staff.unsuspend');

    Route::get('/staff/user/{userId}/edit', [AccountManagementController::class,'editProfile'])
        ->name('staff.edit_profile');

    Route::post('/staff/user/{userId}/update', [AccountManagementController::class,'updateProfile'])
        ->name('staff.update_profile');

    // ========== STAFF BANK DETAILS ==========
    Route::get('/staff/bank-details', [BankDetailsController::class,'index'])
        ->name('staff.bank_details.index');

    Route::get('/staff/bank-details/create', [BankDetailsController::class,'create'])
        ->name('staff.bank_details.create');

    Route::post('/staff/bank-details', [BankDetailsController::class,'store'])
        ->name('staff.bank_details.store');

    Route::delete('/staff/bank-details/{bankDetail}', [BankDetailsController::class,'destroy'])
        ->name('staff.bank_details.destroy');
});

/*
|--------------------------------------------------------------------------
| Example of a Route Using HashIDs
|--------------------------------------------------------------------------
*/
Route::get('/bookings/{hashid}', function($hashid){
    $id = \Hashids::decode($hashid)[0] ?? null;
    $booking = Booking::findOrFail($id);
    // ...
    return "Booking detail for ID {$id}";
});
