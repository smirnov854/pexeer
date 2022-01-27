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

Route::group(['middleware'=> 'installation'],function () {
    Route::group(['middleware' => 'default_lang'], function () {

        Route::get('/', 'LandingController@home')->name('home');
        Route::get('login', 'AuthController@login')->name('login');
        Route::get('sign-up', 'AuthController@signUp')->name('signUp');
        Route::post('sign-up-process', 'AuthController@signUpProcess')->name('signUpProcess');
        Route::post('login-process', 'AuthController@loginProcess')->name('loginProcess');
        Route::get('forgot-password', 'AuthController@forgotPassword')->name('forgotPassword');
        Route::get('verify-email', 'AuthController@verifyEmailPost')->name('verifyWeb');
        Route::get('reset-password', 'AuthController@resetPasswordPage')->name('resetPasswordPage');
        Route::post('send-forgot-mail', 'AuthController@sendForgotMail')->name('sendForgotMail');
        Route::post('reset-password-save-process', 'AuthController@resetPasswordSave')->name('resetPasswordSave');
        Route::get('/g2f-checked', 'AuthController@g2fChecked')->name('g2fChecked');
        Route::post('/g2f-verify', 'AuthController@g2fVerify')->name('g2fVerify');
        Route::post('subscription-process', 'AuthController@subscriptionProcess')->name('subscriptionProcess');
        Route::get('/see-details/{slug}','LandingController@seeDetails')->name('seeDetails');


        // Referral Registration
        Route::get('referral-reg', 'user\ReferralController@signup')->name('referralRegistration');
    });

    require base_path('routes/link/admin.php');
    require base_path('routes/link/user.php');

    Route::group(['middleware' => ['auth']], function () {
        Route::get('logout', 'AuthController@logOut')->name('logOut');
    });
});

