<?php
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\UnsubscribeController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\LicenseController;  /******** Need to delete all data **********/
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\UnregisteredUserController;
use App\Http\Controllers\AdminUserController;
use App\Http\Controllers\OrganisationController;
use App\Http\Controllers\OffersController;
use App\Http\Controllers\FaqController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\PrizeDrawController;
use App\Http\Controllers\PrizeDrawEntryController;
use App\Http\Controllers\PromoCodeController;
use App\Http\Controllers\PlanController; 
use App\Http\Controllers\ProductPackageController; 
use App\Http\Controllers\SubscriptionPlanController; 
use App\Http\Controllers\PayPalController;
use App\Http\Controllers\AdminLogController;
use App\Http\Controllers\EventsController;
use App\Http\Controllers\StripeWebhookController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\TestController;
use App\Http\Controllers\GoogleReviewController;
use App\Http\Controllers\SalesReportController;





use App\Http\Controllers\GoogleController;
use App\Http\Controllers\FacebookController;


use App\Http\Controllers\Auth\ForgotPasswordController;

use App\Http\Controllers\Auth\AdminTwoFactorController; 
use App\Http\Controllers\Auth\AdminLoginController; 
use App\Http\Controllers\Auth\AdminForgotPasswordController; 
use App\Http\Controllers\Auth\AdminResetPasswordController; 
use App\Http\Controllers\Auth\UserTwoFactorController; 
use App\Http\Controllers\Auth\LoginController;






use App\Http\Controllers\Business\BusinessOrganisationController;
use App\Http\Controllers\Business\BusinessOffersController;
use App\Http\Controllers\Business\BusinessUserController;
use App\Http\Controllers\Business\BusinessMyAccountController;
use App\Http\Controllers\Business\BusinessUserResetPasswordController;
use App\Http\Controllers\Business\BusinessPrizeDrawController;
use App\Http\Controllers\Business\BusinessEventsController;


use App\Http\Controllers\Subscriber\SubscriberDashboardController;













use App\Http\Controllers\AjaxController;


use Illuminate\Http\Request;
use App\Models\User;


/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// Route::get('/', function () {
//     return view('welcome');
// });



Route::get('/testcheck', [TestController::class, 'testcheck'])->name('testcheck');
Route::get('/dashboard/data-path-stats', [TestController::class, 'getDataPathStats']);

Route::get('/sendOtp', [TestController::class, 'sendOtp'])->name('ajax.sendOtp');
Route::get('/reprocessOfferImages', [TestController::class, 'reprocessOfferImages'])->name('reprocessOfferImages');
Route::get('/popup', [TestController::class, 'popupcheck'])->name('popupcheck');





Route::get('auth/google', [GoogleController::class, 'redirectToGoogle']);
Route::get('auth/google/callback', [GoogleController::class, 'handleGoogleCallback']);

Route::controller(FacebookController::class)->group(function(){
    Route::get('auth/facebook', 'redirectToFacebook')->name('auth.facebook');
    Route::get('auth/facebook/callback', 'handleFacebookCallback');
});




//Google
//Route::get('/login/google', [LoginController::class, 'redirectToGoogle'])->name('login.google');
//Route::get('/login/google/callback', [LoginController::class, 'handleGoogleCallback']);
// //Facebook
// Route::get('/login/facebook', [LoginController::class, 'redirectToFacebook'])->name('login.facebook');
// Route::get('/login/facebook/callback', [LoginController::class, 'handleFacebookCallback']);
// //Github
// Route::get('/login/github', [LoginController::class, 'redirectToGithub'])->name('login.github');
// Route::get('/login/github/callback', [LoginController::class, 'handleGithubCallback']);

Route::get('/unsubscribe/sms/{token}', [UnsubscribeController::class, 'sms'])->name('unsubscribe.sms');
Route::post('/UserRegister', [HomeController::class, 'UserRegister'])->name('UserRegister');
Route::get('/registerpackage/{id?}', [HomeController::class, 'showRegisterPage'])->name('showRegister.page');

Route::get('/register-{package}-package/', [HomeController::class, 'showPackageRegister'])->name('register.package');


Route::post('/accept-terms', function(){
    $user = auth()->user();
    $user->terms_accepted = 1;
    $user->save();
    return redirect()->back();
})->name('accept.terms')->middleware('auth');



Route::get('/', [HomeController::class, 'index'])->name('consumer');
Route::get('/categories/{categoryId}/offers', [HomeController::class, 'tabOffers'])->name('category.tab.offers');


Route::get('/home', [HomeController::class, 'index'])->name('home.alternate'); 
Route::get('/landing-page', [HomeController::class, 'landingpage'])->name('landingpage');

Route::get('/mazda', [HomeController::class, 'mazdapage'])->name('mazdapage');
Route::get('/grange', [HomeController::class, 'grangepage'])->name('grangepage');

//  Route::get('/landing-page-1', function () {
//     return view('consumer.landingpage1');
// });


Route::match(['get', 'post'], '/members_sigup', [HomeController::class, 'members_sigup'])->name('members_sigup');


Route::get('/optimize-images', [HomeController::class, 'optimizeUploads']);
Route::get('/test-portal', [HomeController::class, 'testportal'])->name('testportal');

Route::get('/thank-you', [HomeController::class, 'thank_you'])->name('thank_you');

Route::get('/thank-you-subscription', [HomeController::class, 'thankyousubscription'])->name('thankyousubscription');
Route::get('/thank-you-gold', [HomeController::class, 'thankyougold'])->name('thankyougold');
Route::get('/thank-you-silver', [HomeController::class, 'thankyousilver'])->name('thankyousilver');
Route::get('/thank-you-bronze', [HomeController::class, 'thankyoubronze'])->name('thankyoubronze');


Route::get('/about-us', [HomeController::class, 'about_us'])->name('about_us');
Route::get('/packages', [HomeController::class, 'packages'])->name('packages');
Route::get('/team-packages', [HomeController::class, 'teampackages'])->name('teampackages');
Route::get('/partners', [HomeController::class, 'partners'])->name('partners');
Route::get('/business-portal', [HomeController::class, 'businessPortal'])->name('businessportal');
Route::get('/privacy-policy', [HomeController::class, 'privacyPolicy'])->name('privacypolicy');


Route::match(['get', 'post'], '/category', [HomeController::class, 'allcategory'])->name('consumer.allcategory');
Route::match(['get', 'post'], '/discounts', [HomeController::class, 'discounts'])->name('discounts');
Route::match(['get', 'post'], '/shop', [HomeController::class, 'shop'])->name('shop');


Route::get('/business/{business_id}', [HomeController::class, 'businessdetails'])->name('organisations.businessdetails');


Route::get('/organisation/{organisation}', [HomeController::class, 'showModalDetails'])->name('organisations.showModalDetails');
Route::get('/redeemoffersite/{offer}', [HomeController::class, 'redeemoffersite'])->name('organisations.redeemoffersite');


Route::get('/offer/{offer}', [HomeController::class, 'showofferModalDetails'])->name('offers.showModalDetails');
Route::get('/logincheckredirection/', [HomeController::class, 'logincheckredirection'])->name('logincheckredirection');




Route::post('/subscribe-ajax', [HomeController::class, 'ajaxSubscribe'])->name('ajax.subscribe');
Route::get('/mailchimp-lists', [HomeController::class, 'getLists']);
Route::get('/mailchimp-subscribers', [HomeController::class, 'getSubscribers']);
Route::get('/events', [HomeController::class, 'events'])->name('events');
Route::get('/support-and-legal', [HomeController::class, 'supportandlegal'])->name('supportandlegal');
Route::get('/terms-and-conditions', [HomeController::class, 'termsandconditions'])->name('termsandconditions');  /*** Need to delete ***/

Route::post('/contact-us', [HomeController::class, 'contactus'])->name('contactus');

Route::post('/create-business-account', [HomeController::class, 'createbusinessaccount'])->name('createbusinessaccount');

Route::post('/check-email', [HomeController::class, 'check_email'])->name('check-email');
Route::post('/check-mobile', [HomeController::class, 'check_mobile'])->name('check-mobile');
Route::post('/validate-password', [HomeController::class, 'validate_password'])->name('validate-password');
Route::post('/unregistered-users/store', [HomeController::class, 'UnregisteredUsersStore'])->name('UnregisteredUsersStore');



Route::post('/stripe/webhook', [StripeWebhookController::class, 'handleWebhook'])->name('stripe.webhook');


// Route::resource('events', BusinessEventsController::class)->names('consumer.events');








// use Illuminate\Support\Facades\Mail;
// Route::get('/test-mail', function () {
//     try {
//         Mail::raw('This is a test email from Laravel.', function ($message) {
//             $message->to('your-email@example.com') // change to your email
//                     ->subject('Test Email from Laravel');
//         });
//         return 'Mail sent successfully.';
//     } catch (\Exception $e) {
//         return 'Mail failed: ' . $e->getMessage();
//     }
// });



// Default Laravel UI authentication routes (login, register, password reset for regular users)
// These routes use the 'web' guard by default.
Route::post('/password/email', [ForgotPasswordController::class, 'sendResetLinkEmail'])->name('password.email');
Auth::routes();
Route::post('login', [LoginController::class, 'login'])
    ->middleware('throttle:login')   
    ->name('login');


//Auth::routes(['register' => false]);

Route::get('verify/resend', [UserTwoFactorController::class, 'resend'])->name('verify.resend');
Route::resource('verify', UserTwoFactorController::class)->only(['index', 'store']);




// --- Regular User Portal Routes ---
// 'auth:web' ensures only logged-in regular users can access.
// 'redirectIfAdmin' ensures that if an admin tries to access these routes,
// they are redirected to their admin dashboard.
Route::middleware(['auth:web', 'redirectIfAdmin','2fa.user'])->group(function () {

    //Route::get('/home', [HomeController::class, 'index'])->name('home');

    // Route::resource('my-account', BusinessMyAccountController::class)->names('business.user');
    // Route::resource('/my-account/password', BusinessUserResetPasswordController::class)->names('business.password');

  // --- Business User Portal ---
    Route::middleware(['business.access'])->group(function () {

      Route::resource('users', BusinessUserController::class)->names('business.users');
      Route::resource('my-account', BusinessMyAccountController::class)->names('business.myaccount');
      Route::resource('/my-account/password', BusinessUserResetPasswordController::class)->names('business.password');
      Route::resource('organisations', BusinessOrganisationController::class)->names('business.organisations');
      Route::resource('offers', BusinessOffersController::class)->names('business.offers');
      Route::resource('prize_draws', BusinessPrizeDrawController::class)->names('business.prize_draws');
      

  });

    // --- Member User Portal ---
    Route::middleware(['member.access'])->group(function () {


        Route::get('/notifications-mark-as-read', [SubscriberDashboardController::class, 'markAllAsRead'])->name('notifications.markAllAsRead');
        Route::match(['get', 'post'], '/subscription-package', [SubscriberDashboardController::class, 'subscription_package'])->name('subscription_package');
        Route::match(['post'], '/subscription-package-store', [SubscriberDashboardController::class, 'subscription_package_store'])->name('subscription_package_store');

        Route::post('/profile/image/upload', [SubscriberDashboardController::class, 'uploadImage'])->name('profile.image.upload');
        Route::get('membership-dashboard', [SubscriberDashboardController::class, 'membership_dashboard'])->name('membership.dashboard');
        Route::get('billing-dashboard', [SubscriberDashboardController::class, 'billing_dashboard'])->name('billing.dashboard');


    // Endpoint to update or set default payment method
        Route::post('/payment-methods/set-default', [SubscriberDashboardController::class, 'setDefaultPaymentMethod'])->name('billing.payment_methods_save');

    // Optional: Route to delete a payment method
        Route::delete('/payment-methods-delete/{paymentMethodId}', [SubscriberDashboardController::class, 'deletePaymentMethod'])->name('billing.deletePaymentMethod');





        Route::get('legal-documentation', [SubscriberDashboardController::class, 'legal_documentation'])->name('legal.documentation');
        Route::get('account-dashboard', [SubscriberDashboardController::class, 'account_dashboard'])->name('account.dashboard');
        Route::post('profile-update', [SubscriberDashboardController::class, 'profile_update'])->name('profile.update');
        Route::delete('profile-destroy', [SubscriberDashboardController::class, 'user_destroy'])->name('profile.destroy');


        Route::resource('subscriber-dashboard', SubscriberDashboardController::class)->names('subscriber.dashboard');

        Route::prefix('subscription')->name('subscription.')->group(function () {
            Route::get('manage', [SubscriptionPlanController::class, 'managesubscription'])->name('manage');
            Route::post('purchase-upsell/{plan}', [SubscriptionPlanController::class, 'purchaseUpsell'])->name('purchase_upsell');
            Route::get('swap/{current}/{new}', [SubscriptionPlanController::class, 'confirmswappage'])->name('swap');
            Route::post('swap-post', [SubscriptionPlanController::class, 'swapsubscription'])->name('swap.post');
            Route::post('cancel', [SubscriptionPlanController::class, 'cancelsubscription'])->name('cancel');
            Route::post('resume', [SubscriptionPlanController::class, 'resumesubscription'])->name('resume');
            Route::post('refund', [SubscriptionPlanController::class, 'refundsubscription'])->name('refund');
        });

        // Upsell Initiate Route — popup se click hone pe
       Route::post('/upsell/initiate/{plan}', [SubscriptionPlanController::class, 'upsellInitiate'])->name('upsell.initiate')->middleware('auth');
 

        Route::get('/payment/success', [SubscriptionPlanController::class, 'success'])->name('payment.success');
        Route::get('subscription/detail/{id}', [SubscriptionPlanController::class, 'packagesubscriptiondetail'])->name('subscription.packagesubscriptiondetail');

        Route::get('/notifications/{id}/read', [SubscriberDashboardController::class, 'read'])->name('notifications.read');
        Route::match(['get'], 'subscription/entries/{id}', [SubscriberDashboardController::class, 'packagesubscriptionentries'])->name('packagesubscriptionentries');
        Route::match(['get'], '/entries', [SubscriberDashboardController::class, 'entries'])->name('entries');

        Route::get('/events/api', [SubscriberDashboardController::class, 'api'])->name('events.api');


        Route::match(['get'], '/invoices', [SubscriberDashboardController::class, 'invoices'])->name('invoices');

        Route::get('/invoice/{invoiceId}/download', function ($invoiceId) {
    return Auth::user()->downloadInvoice($invoiceId, [
        'vendor' => 'Your Company',
        'product' => 'Your Product',
    ]);
})->name('invoice.download');
        
        Route::resource('subscription', SubscriptionPlanController::class);

    });



});


Route::post('/business-users/', [AjaxController::class, 'createbusinessusers'])->name('createbusinessusers');
Route::post('/subbusiness-users/', [AjaxController::class, 'createsubbusinessusers'])->name('createsubbusinessusers');




Route::get('/search-business-users', [AjaxController::class, 'searchbusinessUsers'])->name('searchbusinessUsers');
Route::get('/search-sub-business-users/{businessId}',[AjaxController::class, 'searchsubbusinessusers'])->name('searchsubbusinessusers');


Route::get('/search-organisations', [AjaxController::class, 'organisationssearch']);
Route::get('/get-organisations-detail/{id}', [AjaxController::class, 'getorganisationsdetail']);
Route::get('/search-users', [AjaxController::class, 'userssearch']);
Route::get('/get-user-detail/{id}', [AjaxController::class, 'getuserdetail']);


Route::get('/add-organisations-detail/{id}', [AjaxController::class, 'addorganisationsdetail']);
Route::post('/organisations-update-user-status/', [AjaxController::class, 'organisationsupdateusertatus'])->name('organisations.update.status');

Route::post('/items/sort', [AjaxController::class, 'sortItems'])->name('items.sort');
Route::get('/users/export-csv', [UserController::class, 'exportCsv'])->name('users.export_csv');
Route::get('getOrganisation/', [AjaxController::class, 'getOrganisation'])->name('organisations.getOrganisation');









// --- Admin Authentication & Portal Routes ---
// Route::prefix('admin')->group(function () {
    Route::prefix('admin')->middleware(['admin.firewall'])->group(function () {

    Route::get('/login', [AdminLoginController::class, 'showLoginForm'])->name('admin.login')->middleware('guest:admin');
    Route::post('/login', [AdminLoginController::class, 'login'])->name('admin.login.post')->middleware('guest:admin');
    // Admin Logout Route
    Route::post('/logout', [AdminLoginController::class, 'logout'])->name('admin.logout');
    // Admin Password Reset Routes
    Route::get('/password/reset', [AdminForgotPasswordController::class, 'showLinkRequestForm'])->name('admin.password.request');
    Route::post('/password/email', [AdminForgotPasswordController::class, 'sendResetLinkEmail'])->name('admin.password.email');
    Route::get('/password/reset/{token}', [AdminResetPasswordController::class, 'showResetForm'])->name('admin.password.reset');
    Route::post('/password/update', [AdminResetPasswordController::class, 'reset'])->name('admin.password.update');
    
    Route::get('verify/resend', [AdminTwoFactorController::class, 'resend'])->name('admin.verify.resend');
    Route::resource('verify', AdminTwoFactorController::class)->only(['index', 'store'])->names('admin.verify');



    //Route::middleware('auth:admin')->group(function () {

    Route::middleware(['auth:admin','2fa.admin'])->group(function () {
        Route::get('/', function () {  return view('admin.dashboard'); })->name('admin.dashboard');
        Route::get('/dashboard', function () { return view('admin.dashboard'); })->name('admin.dashboard');

        Route::get('/stats', [DashboardController::class, 'index'])->name('admin.statsdashboard');
        Route::get('/stats/data-path-stats', [DashboardController::class, 'getDataPathStats']);


        Route::resource('permissions', PermissionController::class);
        Route::resource('roles', RoleController::class);
        Route::resource('licenses', LicenseController::class);
        Route::resource('categories', CategoryController::class);
        Route::resource('unregisteredusers', UnregisteredUserController::class);

        Route::resource('admins', AdminUserController::class);

         Route::get('/organisations/organisationsuploadcsv', [OrganisationController::class, 'organisationsuploadcsv'])->name('organisations.organisationsuploadcsv');
         Route::post('/organisations/organisationsuploadcsv', [OrganisationController::class, 'organisationsuploadcsvaction'])->name('organisations.organisationsuploadcsvaction');

         
        Route::resource('organisations', OrganisationController::class);


        Route::get('/offers/uploadcsv', [OffersController::class, 'uploadcsv'])->name('offers.uploadcsv');
        Route::post('/offers/uploadcsv', [OffersController::class, 'uploadcsvaction'])->name('offers.uploadcsvaction');
        Route::get('/offers/map-csv-fields', [OffersController::class, 'showCsvMappingForm'])->name('offers.map_csv_fields');
        Route::post('/offers/import-csv', [OffersController::class, 'importCsv'])->name('offers.import_csv');



        Route::get('users/entries/{userID}', [UserController::class, 'user_entries'])->name('users.entries');
        Route::get('users/activity/{userID}', [UserController::class, 'userActivity'])->name('userActivity');
        Route::get('users/packagessubscriptions/{userID}', [UserController::class, 'userpackagessubscriptions'])->name('userpackagessubscriptions');

        Route::get('/entries/uploadcsv', [PrizeDrawEntryController::class, 'uploadcsv'])->name('entries.uploadcsv');
        Route::post('/entries/uploadcsv', [PrizeDrawEntryController::class, 'uploadcsvaction'])->name('entries.uploadcsvaction');
        
        Route::get('/users/export-csv', [UserController::class, 'exportCsv'])->name('users.export_csv');

        Route::resource('offers', OffersController::class);
        Route::resource('faqs', FaqController::class);
        Route::resource('users', UserController::class);
        Route::resource('prize_draws', PrizeDrawController::class);
        Route::resource('entries', PrizeDrawEntryController::class);
        Route::resource('promo_codes', PromoCodeController::class);
        Route::resource('plans', PlanController::class);
        Route::resource('product-packages', ProductPackageController::class);
        Route::resource('events', EventsController::class);

        Route::resource('pages', PageController::class);
        Route::resource('google-reviews', GoogleReviewController::class);


        //Route::get('admins/log/{id}', [AdminLogController::class, 'showLog'])->name('admin.logs.show');

        Route::get('log/{type}/{id}', [AdminLogController::class, 'showLog'])->name('admin.logs.show');
        Route::get('/log/daily-actions/{id}', [AdminLogController::class, 'showUserDailyActions'])->name('admin.user_daily_actions');

        Route::get('/reports/sales', [SalesReportController::class, 'index'])->name('admin.reports.sales');
        Route::get('/prizedrawlogs/', [PrizeDrawController::class, 'prizedrawlog'])->name('admin.prizedrawlog');

    });
});



Route::middleware(['auth'])->group(function () {
    Route::post('/paypal/redirect',  [PayPalController::class, 'redirect'])->name('paypal.redirect');
    Route::get('/paypal/success',    [PayPalController::class, 'success'])->name('paypal.success');
    Route::get('/paypal/cancel',     [PayPalController::class, 'cancel'])->name('paypal.cancel');
});
 
// Webhook is public (no auth middleware) — PayPal calls this server-to-server
// Add CSRF exception in App\Http\Middleware\VerifyCsrfToken -> $except array
Route::post('/paypal/webhook', [PayPalController::class, 'webhook'])->name('paypal.webhook');


/*
|--------------------------------------------------------------------------
| Shop / Merchandise Routes
|--------------------------------------------------------------------------
*/

use App\Http\Controllers\Shop\AccountController;
use App\Http\Controllers\Shop\ShopController;
use App\Http\Controllers\Shop\CartController;
use App\Http\Controllers\Shop\CheckoutController;
use App\Http\Controllers\Admin\Shop\ProductController as AdminProductController;
use App\Http\Controllers\Admin\Shop\OrderController as AdminOrderController;
use App\Http\Controllers\Admin\Shop\ShippingController as AdminShippingController;
use App\Http\Controllers\Admin\Shop\TaxController as AdminTaxController;
use App\Http\Controllers\Admin\Shop\CouponController as AdminCouponController;
use App\Http\Controllers\Admin\Shop\ShopCategoryController as AdminShopCategoryController;

// ── Frontend: Account / Orders (auth required) ────────────────────────────
Route::prefix('account')->name('shop.account.')->middleware('auth:web')->group(function () {
    Route::get('/orders',                         [AccountController::class, 'index'])->name('orders');
    Route::get('/orders/{order}',                 [AccountController::class, 'show'])->name('order');
    Route::post('/orders/{order}/reorder',        [AccountController::class, 'reorder'])->name('reorder');
});

// ── Frontend: Public Shop ──────────────────────────────────────────────────
Route::prefix('merchandise')->name('shop.merchandise.')->group(function () {
    Route::get('/',            [ShopController::class, 'index'])->name('index');
    Route::get('/{product}',   [ShopController::class, 'show'])->name('show');
});

// ── Frontend: Cart (session-based, no auth required) ──────────────────────
Route::prefix('cart')->name('shop.cart.')->group(function () {
    Route::get('/',                      [CartController::class, 'index'])->name('index');
    Route::post('/add',                  [CartController::class, 'add'])->name('add');
    Route::patch('/{item}',              [CartController::class, 'update'])->name('update');
    Route::delete('/{item}',             [CartController::class, 'remove'])->name('remove');
    Route::post('/coupon/apply',         [CartController::class, 'applyCoupon'])->name('apply-coupon');
    Route::delete('/coupon/remove',      [CartController::class, 'removeCoupon'])->name('remove-coupon');
    Route::get('/count',                 [CartController::class, 'count'])->name('count');
});

// ── Frontend: Checkout ─────────────────────────────────────────────────────
Route::prefix('checkout')->name('shop.checkout.')->group(function () {
    Route::get('/',                      [CheckoutController::class, 'index'])->name('index');
    Route::post('/',                     [CheckoutController::class, 'store'])->name('store');
    Route::get('/shipping-rates',        [CheckoutController::class, 'getShippingRates'])->name('shipping-rates');
    Route::post('/payment-intent',       [CheckoutController::class, 'createPaymentIntent'])->name('payment-intent');
    Route::get('/success/{orderNumber}', [CheckoutController::class, 'success'])->name('success');
});

// ── Admin: Shop Management ─────────────────────────────────────────────────
Route::prefix('admin/shop')->name('admin.shop.')->middleware(['admin.firewall', 'auth:admin', '2fa.admin'])->group(function () {

    // Products
    Route::get('/products',                    [AdminProductController::class, 'index'])->name('products.index');
    Route::get('/products/create',             [AdminProductController::class, 'create'])->name('products.create');
    Route::post('/products',                   [AdminProductController::class, 'store'])->name('products.store');
    Route::get('/products/{product}/edit',     [AdminProductController::class, 'edit'])->name('products.edit');
    Route::put('/products/{product}',          [AdminProductController::class, 'update'])->name('products.update');
    Route::delete('/products/{product}',       [AdminProductController::class, 'destroy'])->name('products.destroy');
    Route::delete('/product-images/{image}',       [AdminProductController::class, 'deleteImage'])->name('products.delete-image');
    Route::delete('/product-color-images/{image}', [AdminProductController::class, 'deleteColorImage'])->name('products.delete-color-image');

    // Orders
    Route::get('/orders',                      [AdminOrderController::class, 'index'])->name('orders.index');
    Route::get('/orders/{order}',              [AdminOrderController::class, 'show'])->name('orders.show');
    Route::put('/orders/{order}',              [AdminOrderController::class, 'update'])->name('orders.update');

    // Shipping
    Route::get('/shipping',                    [AdminShippingController::class, 'index'])->name('shipping.index');
    Route::get('/shipping/create-zone',        [AdminShippingController::class, 'createZone'])->name('shipping.create-zone');
    Route::post('/shipping/zones',             [AdminShippingController::class, 'storeZone'])->name('shipping.store-zone');
    Route::get('/shipping/zones/{zone}/edit',  [AdminShippingController::class, 'editZone'])->name('shipping.edit-zone');
    Route::put('/shipping/zones/{zone}',       [AdminShippingController::class, 'updateZone'])->name('shipping.update-zone');
    Route::delete('/shipping/zones/{zone}',    [AdminShippingController::class, 'destroyZone'])->name('shipping.destroy-zone');
    Route::post('/shipping/zones/{zone}/rates',[AdminShippingController::class, 'storeRate'])->name('shipping.store-rate');
    Route::delete('/shipping/rates/{rate}',    [AdminShippingController::class, 'destroyRate'])->name('shipping.destroy-rate');

    // Taxes
    Route::get('/taxes',                       [AdminTaxController::class, 'index'])->name('taxes.index');
    Route::post('/taxes',                      [AdminTaxController::class, 'store'])->name('taxes.store');
    Route::put('/taxes/{tax}',                 [AdminTaxController::class, 'update'])->name('taxes.update');
    Route::delete('/taxes/{tax}',              [AdminTaxController::class, 'destroy'])->name('taxes.destroy');

    // Coupons
    Route::get('/coupons',                     [AdminCouponController::class, 'index'])->name('coupons.index');
    Route::get('/coupons/create',              [AdminCouponController::class, 'create'])->name('coupons.create');
    Route::post('/coupons',                    [AdminCouponController::class, 'store'])->name('coupons.store');
    Route::get('/coupons/{coupon}/edit',       [AdminCouponController::class, 'edit'])->name('coupons.edit');
    Route::put('/coupons/{coupon}',            [AdminCouponController::class, 'update'])->name('coupons.update');
    Route::delete('/coupons/{coupon}',         [AdminCouponController::class, 'destroy'])->name('coupons.destroy');
    Route::get('/coupons/generate-code',       [AdminCouponController::class, 'generate'])->name('coupons.generate');

    // Shop Categories
    Route::get('/categories',                  [AdminShopCategoryController::class, 'index'])->name('categories.index');
    Route::get('/categories/create',           [AdminShopCategoryController::class, 'create'])->name('categories.create');
    Route::post('/categories',                 [AdminShopCategoryController::class, 'store'])->name('categories.store');
    Route::get('/categories/{category}/edit',  [AdminShopCategoryController::class, 'edit'])->name('categories.edit');
    Route::put('/categories/{category}',       [AdminShopCategoryController::class, 'update'])->name('categories.update');
    Route::delete('/categories/{category}',    [AdminShopCategoryController::class, 'destroy'])->name('categories.destroy');
});

