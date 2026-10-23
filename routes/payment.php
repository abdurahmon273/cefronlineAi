<?php


use Illuminate\Support\Facades\Route;

Route::any('/handle/{paysys}', function ($paysys) {
    (new Goodoneuz\PayUz\PayUz)->driver($paysys)->handle();
});

//redirect to payment system or payment form
Route::any('/pay/{paysys}/{key}/{amount}', function ($paysys, $key, $amount) {
    $url = request('redirect_url', '/'); // redirect url after payment completed

    if ($paysys === Goodoneuz\PayUz\Models\PaymentSystem::CLICK) {
        $config = Goodoneuz\PayUz\Services\PaymentSystemService::getPaymentSystemParamsCollect(
            Goodoneuz\PayUz\Models\PaymentSystem::CLICK
        );

        $pay_url = 'https://my.click.uz/services/pay?' . http_build_query([
                'service_id'        => $config['service_id'],
                'merchant_id'       => $config['merchant_id'],
                'amount'            => $amount,
                'transaction_param' => $key, // = merchant_trans_id (model key), Prepare'da convertKeyToModel bilan o'qiladi
                'return_url'        => $url,
            ]);

        return redirect()->away($pay_url);
    }


    $model = Goodoneuz\PayUz\Services\PaymentService::convertKeyToModel($key);
    $pay_uz = new Goodoneuz\PayUz\PayUz;
    $pay_uz
        ->driver($paysys)
        ->redirect($model, $amount, 860, $url);
});


//checking array is empty or null


Route::name('payment.')->prefix('payment')->group(function () {
    if (auth()->check() && auth()->user()->is_admin) {


        Route::any('dashboard', 'PageController@dashboard')->name('dashboard');
        Route::any('editors', 'PageController@editors')->name('editors');
        Route::any('blank', 'PageController@blank')->name('blank');
        Route::any('settings', 'PageController@settings')->name('settings');
        Route::get('payment_params/delete/{param_id}', 'PaymentSystemController@deleteParam')->name('payment_systems.delete_param');
        Route::get('payment_systems/edit/status/{payment_system}', 'PaymentSystemController@editStatus')->name('payment_systems.edit_status');

        // --editable functions
        Route::any('/api/editable/update', 'ApiController@file_put')->name('api.file_put');
        // end --editable functions

        Route::resource('transactions', 'TransactionController');
        Route::resource('projects', 'ProjectController');
        Route::resource('payment_systems', 'PaymentSystemController');
        Route::resource('transactions', 'TransactionController');
    }
});


