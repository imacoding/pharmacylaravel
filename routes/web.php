<?php

use Illuminate\Support\Facades\Route;

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


Route::get('load-page', function () {

    $posted  = [
        'id' => 1,
        'amount' => 190,
        'firstname' => 'Namitha',
        'email' => 'namitha@webandcrafts.in',
        'phone' => '01234567890',
        'productinfo' => 'Paracetamol ,Vicks',
        'invoice' => 'INV_10001'
    ];
    // return View('html-pages.mobile_paypal_payment');
    return View('users.mobile_payment', compact('posted'));
});

Route::get('/system-setup', 'SetupController@index')->name('homepage');
Route::get('/run-migration', 'SetupController@runMigration')->name('run_migration');
Route::get('/run-seeder', 'SetupController@runSeeder')->name('run_seeder');
Route::post('/add-basic-settings', 'SetupController@addBasicSettings')->name('add_basic_settings');
Route::post('/add-mail-settings', 'SetupController@addMailSettings')->name('add_mail_settings');
Route::post('/add-payment-settings', 'SetupController@addPaymentSettings')->name('add_payment_settings');
Route::post('/add-admin-user', 'SetupController@addAdminUser')->name('add_admin_user');
Route::post('/test-mails', 'SetupController@sendTestMails')->name('add_admin_user');
Route::post('/medicine/request-medicine', 'MedicineController@anyAddMedicine');
Route::get('/', 'IndexController@index')->name('homepage');
Route::get('/medicine-detail/{item_code}', 'MedicineController@getMedicineDetail');

Route::group(['prefix' => 'medicine'], function () {
    Route::any('/load-medicine-web/{isweb}/', 'MedicineController@anyLoadMedicineWeb');        
    Route::get('load-sub-medicine', 'MedicineController@anyLoadSubMedicine');
    Route::get('/add-cart/{isWeb}', 'MedicineController@anyAddCart');

        /* payment using paypal - start */
        Route::post('handlePayment', 'PayPalController@handlePayment')->name('handlePayment');
        Route::get('paypal-fail', 'PayPalController@paymentCancel')->name('cancel.payment');
        Route::get('paypal-success', 'PayPalController@paymentSuccess')->name('success.payment');
        /* payment using paypal - end */

        /* payment using pay u money */
        Route::any('make-payment/{invoice}', 'PayUMoneyController@anyMakePayment')->name('make-payu-payment');
        Route::any('pay-success/{invoice}', 'PayUMoneyController@anyPaySuccess')->name('pay-success');
        Route::any('pay-fail/{invoice}', 'PayUMoneyController@anyPayFail')->name('pay-fail');
        /* payment using pay u money */

    });
Route::group(['prefix' => 'user'], function () {
    Route::get('check-session/{isWeb}', 'UserController@getCheckSession');
    Route::get('check-user-name', 'UserController@anyCheckUserName');

    Route::post('user-login/{isWeb}', 'UserController@loginUser');

    Route::post('create-user/{isWeb}', 'UserController@registerUser');
    Route::any('reset-password', 'UserController@anyResetPassword');

    Route::any('web-activate-account/{code}', 'UserController@anyWebActivateAccount');

    Route::post('activate-account', 'UserController@activateAccount');
    Route::post('resend-activation-code/{isWeb}', 'UserController@resendCode');
    });

Route::group(['middleware' => 'auth'], function () {
    Route::get('account-page', 'UserController@getAccountPage');
    Route::get('my-cart', 'MedicineController@getMyCart');
    Route::get('awaiting-shipment', 'MedicineController@getAwaitingResponse');
    Route::get('shipped-order', 'MedicineController@getShippedOrder');

    Route::get('prescriptions', 'MedicineController@getPrescriptionView');
    Route::post('upload-prescription/{isWeb}', 'MedicineController@anyStorePrescription')->name('upload-prescription');

    Route::group(['prefix' => 'medicine'], function () {
        Route::get('downloading', 'MedicineController@anyDownloading')->name('prescription.download');

        Route::get('getMyPrescriptions', 'MedicineController@getMyPrescription')->name('my.prescription');
            
        Route::get('get-my-cart/{isWeb}','MedicineController@getMyCartItems')->name('get-cart');
        Route::post('remove-from-cart','MedicineController@removeCartItem')->name('remove-from-cart');

            /* payment using paypal - start */
            Route::any('make-paypal-payment/{invoice}', 'PayPalController@anyMakePaypalPayment')->name('make-payment');               
            /* payment using paypal - end */
        });
        
    Route::group(['prefix' => 'user'], function () {
        Route::post('update-profile-pic', 'UserController@storeUserProfilePic');
        Route::post('update-details-user/{isWeb}', 'UserController@postUpdateDetailsUser')->name('user.update-details-user');

        });
        
    Route::get('logout','UserController@logout')->name('user.logout');
    });


    /*Common pages in general*/
Route::get('/contact-us', 'CommonController@contactUs');
Route::post('/submit-contact-us', 'CommonController@sendContactUs');
Route::get('/about-us', 'CommonController@aboutUs');
Route::get('/help-desk', 'CommonController@helpDesk');
Route::get('/privacy-policy', 'CommonController@privacyPolicy');
Route::get('/terms-conditions', 'CommonController@termsConditions');

Route::get('/admin-login', 'AdminController@index')->name('admin.index');

Route::prefix('admin')->group(function () {

Route::post('/login', 'AdminController@anyLogin')->name('admin.login');
Route::get('/reset', 'AdminController@getReset');
Route::any('/reset-password', 'AdminController@postResetPassword');
Route::get('/admin-reset-password/{md}', 'AdminController@getAdminResetPassword');
Route::post('/admin-change-password', 'AdminController@anyAdminChangePassword');

Route::get('/dashboard', 'AdminController@getDashboard');

Route::get('/dash-detail', 'AdminController@getDashDetail');
Route::get('/dash-ord', 'AdminController@getDashOrd');
Route::get('/today-pres-dash', 'AdminController@getTodayPresDash');

Route::get('/load-customers', 'AdminController@getLoadCustomers')->name('admin.load-customers');;
Route::get('/load-customers-list', 'AdminController@getLoadCustomersList')->name('admin.userlist');
Route::get('/customer-delete', 'AdminController@getCustomerDelete')->name('admin.customer-delete');
Route::get('/user-change-status', 'AdminController@getUserChangeStatus')->name('admin.user-change-status');

Route::get('/load-medicalprof', 'AdminController@getLoadMedicalprof')->name('admin.load-medicalprof');
Route::get('/load-medicalprof-list', 'AdminController@getLoadMedicalprofList')->name('admin.mproflist');
Route::get('/mprof-delete', 'AdminController@getMedicalprofDelete')->name('admin.mprof-delete');
Route::get('/prof-change-status', 'AdminController@getMedicalprofChangeStatus')->name('admin.prof-change-status');


Route::get('/load-medicines', 'AdminController@getLoadMedicines')->name('admin.load-medicines');;;
Route::get('/load-medicines-list', 'AdminController@getLoadMedicinesList')->name('admin.medicineslist');
Route::get('/medicine-edit', 'AdminController@getMedicineEdit')->name('admin.medicine-edit');
Route::get('/medicine-delete', 'AdminController@getMedicineDelete')->name('admin.medicine-delete');
Route::get('/medicine-prescription', 'AdminController@getMedicinePrescription')->name('admin.medicine-prescription');
Route::get('/add-med', 'AdminController@getAddMed');
Route::post('/new-med', 'AdminController@postNewMed');

Route::post('/upload', 'AdminController@medcineBulkUpload')->name('admin.bulkUpload');

Route::get('/list-top-brands', 'AdminController@getLoadTopBrands')->name('admin.LoadTopBrands');;
Route::get('/load-brands-list', 'AdminController@getLoadTopBrandsList')->name('admin.topbrandslist');
Route::get('/topbrands-edit', 'AdminController@getTopBrandsEdit')->name('admin.topbrands-edit');
Route::get('/topbrands-delete', 'AdminController@getTopBrandsDelete')->name('admin.topbrands-delete');

Route::get('/add-brand', 'AdminController@getAddBrand');
Route::post('/new-brand', 'AdminController@postNewBrand');




Route::get('/load-new-medicines', 'AdminController@getLoadNewMedicines')->name('admin.LoadNewMedicines');;
Route::get('/load-new-medicines-list', 'AdminController@getLoadNewMedicinesList')->name('admin.newMedList');
Route::post('/delete-new-medicine', 'AdminController@getDeleteNewMedicine')->name('admin.delete-new-medicine');
Route::get('/new-medicine-email', 'AdminController@getNewMedicineEmail');

Route::get('/load-medicine-web', 'AdminController@anyLoadMedicineWeb');

Route::get('/load-pending-prescription', 'AdminController@anyLoadPendingPrescription')->name('admin.load-pending-prescription');;
Route::get('/load-pending-prescription-list', 'AdminController@anyLoadPendingPrescriptionList')->name('admin.load-pending-prescription-list');
Route::get('/pres-edit', 'AdminController@getPresEdit')->name('admin.pres-edit');
Route::post('/pres-delete', 'AdminController@anyPresDelete')->name('admin.pres-delete');
Route::get('/ship-order', 'AdminController@anyShipOrder')->name('admin.ship-order');

Route::get('/load-active-prescription', 'AdminController@anyLoadActivePrescription')->name('admin.load-active-prescription');
Route::get('/load-active-prescription-list', 'AdminController@anyLoadActivePrescriptionList')->name('admin.load-active-prescription-list');

Route::get('/load-all-prescription', 'AdminController@anyLoadAllPrescription')->name('admin.load-all-prescription');
Route::get('/load-all-prescription-list', 'AdminController@anyLoadAllPrescriptionList')->name('admin.load-all-prescription-list');

Route::get('/load-paid-prescription', 'AdminController@anyLoadPaidPrescription')->name('admin.load-paid-prescription');
Route::get('/load-paid-prescription-list', 'AdminController@anyLoadPaidPrescriptionList')->name('admin.load-paid-prescription-list');

Route::get('/load-shipped-prescription', 'AdminController@anyLoadShippedPrescription')->name('admin.load-shipped-prescription');
Route::get('/load-shipped-prescription-list', 'AdminController@anyLoadShippedPrescriptionList')->name('admin.load-shipped-prescription-list');

Route::get('/load-deleted-prescription', 'AdminController@anyLoadDeletedPrescription')->name('admin.load-deleted-prescription');
Route::get('/load-deleted-prescription-list', 'AdminController@anyLoadDeletedPrescriptionList')->name('admin.load-deleted-prescription-list');



Route::post('/update-invoice','AdminController@postUpdateInvoice')->name('admin.update-invoice');

Route::get('/load-invoice/{id}', 'AdminController@getLoadInvoice')->name('admin.load-invoice');

Route::get('/load-invoice-items/{id}', 'AdminController@getLoadInvoiceItems')->name('admin.load-invoice-items');


Route::get('/admin-pay-success', 'AdminController@anyAdminPaySuccess')->name('admin.admin-pay-success');





Route::get('/logout', 'AdminController@logOut');
Route::get('/system-setup', 'AdminController@systemSetup');

Route::get('/cache', function() {
    \Illuminate\Support\Facades\Artisan::call('cache:clear');
    \Illuminate\Support\Facades\Artisan::call('config:clear');
    \Illuminate\Support\Facades\Artisan::call('config:cache');
    \Illuminate\Support\Facades\Artisan::call('view:clear');
    return redirect()->back();
});

});

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
