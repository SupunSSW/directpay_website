<?php

/*
 * Frontend Controllers
 * All route names are prefixed with 'frontend.'.
 */
//Route::get('/', [HomeController::class, 'index'])->name('index');
//Route::get('contact', [ContactController::class, 'index'])->name('contact');
//Route::post('contact/send', [ContactController::class, 'send'])->name('contact.send');
//
//Route::get('pay/{id}','ItemsController@viewItem')->name('payment.viewItem');
//Route::get('paymentsignup/{id}','ItemsController@viewItem')->name('payment.paymentsignup');
//Route::post('payment/{id}','ItemsController@PaymentItem')->name('payment.Pay');
//Route::post('payment/check/{id}','ItemsController@validatePoNo')->name('payment.Check');
//Route::post('paymentRes','ItemsController@schedulePaymentResponse')->name('payment.paymentRes');
//Route::get('addCard','ItemsController@addSecondaryCard')->name('payment.addSecondaryCard');
//Route::post('saveNewCard','ItemsController@saveNewCard')->name('payment.saveNewCard');
//Route::post('statusUpdater','ItemsController@paymentStatusChangers')->name('payment.statusUpdater');
//
//Route::get('update/{id}', 'ItemsController@updateCard')->name('payment.updateCard');
//Route::post('updateRes/{id}', 'ItemsController@updateCardRes')->name('payment.updateCardRes');
//Route::post('failedRes', 'ItemsController@cardErrorRes')->name('payment.failedRes');
//
//Route::get('upload', 'ItemsController@uploadChangers');
//Route::get('agent/app', [HomeController::class, 'getapp'])->name('agent.app');
//
///*
// * These frontend controllers require the user to be logged in
// * All route names are prefixed with 'frontend.'
// * These routes can not be hit if the password is expired
// */
//Route::group(['middleware' => ['auth', 'password_expires']], function () {
//    Route::group(['namespace' => 'User', 'as' => 'user.'], function () {
//        /*
//         * User Dashboard Specific
//         */
//        Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');
//
//        /*
//         * User Account Specific
//         */
//        Route::get('account', [AccountController::class, 'index'])->name('account');
//
//        /*
//         * User Profile Specific
//         */
//        Route::patch('profile/update', [ProfileController::class, 'update'])->name('profile.update');
//    });
//});


Route::get('/', function () {
    return view('consumer');
});

Route::get('/consumer', function () {
    return view('consumer');
});

Route::get('/merchant', function () {
    return view('merchant');
});

Route::get('/about', function () {
    return view('about');
});

Route::get('/billers', function () {
    return view('billers');
});

Route::get('/blog', function () {
    return view('blog');
});

Route::get('/blog2', function () {
    return view('blog2');
});

Route::get('/blog3', function () {
    return view('blog3');
});

Route::get('/blog4', function () {
    return view('blog4');
});

Route::get('/blog5', function () {
    return view('blog5');
});

Route::get('/blog6', function () {
    return view('blog6');
});

Route::get('/blog7', function () {
    return view('blog7');
});

Route::get('/blog8', function () {
    return view('blog8');
});

Route::get('/blog9', function () {
    return view('blog9');
});

Route::get('/news', function () {
    return view('news');
});

Route::get('/Career', function () {
    return view('Career');
});

Route::get('/checkout', function () {
    return view('checkout');
});

Route::get('/contact', function () {
    return view('contact');
});

Route::get('/faq', function () {
    return view('faq');
});

Route::get('/ipg', function () {
    return view('ipg');
});

Route::get('/payment', function () {
    return view('payment');
});

Route::get('/pos', function () {
    return view('pos');
});

Route::get('/privacy', function () {
    return view('privacy');
});

Route::get('/terms', function () {
    return view('terms');
});

Route::get('/test', function () {
    return view('test');
});

Route::get('/vpos', function () {
    return view('vpos');
});

Route::get('/web', function () {
    return view('web');
});

Route::get('/whitelable', function () {
    return view('whitelable');
});

Route::get('/mtest', function () {
    return view('mtest');
});

Route::get('/business', function () {
    return view('business');
});

Route::get('/invoice', function () {

    return view('invoice');


    Route::get('/admin', function () {

        return redirect('login');
    });




    Route::group(['prefix' => '', 'middleware' => 'auth'], function () {
        Route::get('/dashboard', function () {
            return view('dashboard');
        });

        Route::get('/create-article', function () {

            return view('create-article');

        });


    });


});

Route::post('email', [\App\Http\Controllers\ContactController::class, 'sendmail']);
Route::post('create', [\App\Http\Controllers\uplink::class, 'rocket']);
Route::post('create2', [\App\Http\Controllers\accVerification::class, 'verifyAcc'])->name('accountData.submit');

Auth::routes();

Route::get('/home', [\App\Http\Controllers\HomeController::class, 'index'])->name('home');


Route::get('billers', [\App\Http\Controllers\BillerController::class, 'index']);


Route::group(['prefix' => 'agent'], function () {
    Route::get('/view', function () {
        return 'view agent';
    });
    Route::get('/edit', function () {
        return 'edit agent';
    });
});

Route::get('logout', '\App\Http\Controllers\Auth\LoginController@logout');

Route::get('blog6', [\App\Http\Controllers\getData::class, 'index']);


