<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return Redirect::to('login');
});

Route::get('profile/{username}', 'AuthorController@profile')->name('author.profile');

Auth::routes();


Route::group(['as' => 'admin.', 'prefix' => 'admin', 'namespace' => 'Admin', 'middleware' => ['auth', 'admin']], function () {
    Route::get('dashboard', 'DashboardController@index')->name('dashboard');

    Route::group(
        [
            'prefix' => 'doctors',
            'as' => 'doctors.',
        ],
        function () {
            Route::get('', 'DoctorController@index')->name('index');
        }
    );
    Route::group(
        [
            'prefix' => 'users',
            'as' => 'users.',
        ],
        function () {
            Route::get('', 'UserController@index')->name('index');
        }
    );
    Route::group(
        [
            'prefix' => 'consultations',
            'as' => 'consultations.',
        ],
        function () {
            Route::get('', 'ConsultationController@index')->name('index');
        }
    );
    Route::get('dashboard/doctor/{id}', 'DashboardController@showDoctor')->name('dashboard-show-doctor');
    Route::get('dashboard/user/{id}', 'DashboardController@showUser')->name('dashboard-show-user');
    Route::get('dashboard/consultation/{id}', 'DashboardController@showConsultation')->name('dashboard-show-consultation');

    Route::group(
        [
            'prefix' => 'approvals',
            'as' => 'approvals.'
        ],
        function () {
            Route::get('', 'ApprovalController@indexDoctors')->name('index');
            Route::put('approve', 'ApprovalController@approve')->name('approve');
        }
    );

    Route::get('settings', 'SettingsController@index')->name('settings');
    Route::put('profile-update', 'SettingsController@updateProfile')->name('profile.update');
    Route::put('password-update', 'SettingsController@updatePassword')->name('password.update');

    Route::resource('banner', 'BannerController');
    Route::resource('payment', 'PaymentController');
    Route::post('payment/status/{id}', 'PaymentController@status')->name('payment.status');
    Route::post('payment/status_deny/{id}', 'PaymentController@statusDeny')->name('payment.status_deny');
});

Route::group(['as' => 'author.', 'prefix' => 'author', 'namespace' => 'Author', 'middleware' => ['auth', 'author']], function () {
    Route::get('dashboard', 'DashboardController@index')->name('dashboard');
    Route::get('dashboard/consultation/{id}', 'DashboardController@showConsultation')->name('dashboard-show-consultation');
    Route::get('dashboard/review/{id}', 'DashboardController@showReview')->name('dashboard-show-review');

    Route::get('settings', 'SettingsController@index')->name('settings');
    Route::put('profile-update', 'SettingsController@updateProfile')->name('profile.update');
    Route::put('password-update', 'SettingsController@updatePassword')->name('password.update');

    Route::resource('payment', 'PaymentController');

    Route::group([
            'prefix' => 'consultations',
            'as' => 'consultations.',
        ],
        function () {
            Route::get('', 'ConsultationController@index')->name('index');
        }
    );

     Route::group([
            'prefix' => 'reviews',
            'as' => 'reviews.',
        ],
        function () {
            Route::get('', 'ReviewController@index')->name('index');
        }
    );
});
