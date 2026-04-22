<?php

namespace Config;

// Create a new instance of our RouteCollection class.
$routes = Services::routes();

// Load the system's routing file first, so that the app and ENVIRONMENT
// can override as needed.
if (file_exists(SYSTEMPATH . 'Config/Routes.php')) {
    require SYSTEMPATH . 'Config/Routes.php';
}

/*
 * --------------------------------------------------------------------
 * Router Setup
 * --------------------------------------------------------------------
 */
$routes->setDefaultNamespace('App\Controllers');
$routes->setDefaultController('Frontend');
$routes->setDefaultMethod('index');
$routes->setTranslateURIDashes(false);
$routes->set404Override();
$routes->setAutoRoute(true);

/*
 * --------------------------------------------------------------------
 * Route Definitions
 * --------------------------------------------------------------------
 */

// We get a performance increase by specifying the default
// route since we don't have to scan directories.

/*
 * ════════════════════════════════════════════════════════════════
 * Subtask 4.1 — API v1   (all routes under /v1/)
 * ════════════════════════════════════════════════════════════════
 * Filters: api_rate_limit (before) + api_response (before+after)
 * are applied globally to v1/* in Config/Filters.php.
 *
 * Namespace: App\Controllers\Api\V1
 * ════════════════════════════════════════════════════════════════
 */
$routes->group('v1', ['namespace' => 'App\Controllers\Api\V1'], function($routes) {

    // ── Meta / Health ─────────────────────────────────────────────
    $routes->get('health',  'MetaController::health');   // DB + cache health check
    $routes->get('ping',    'MetaController::ping');     // Lightweight liveness probe

    // ── Swagger Documentation ─────────────────────────────────────
    $routes->get('docs',          'DocsController::index');       // Swagger UI
    $routes->get('openapi.json',  'DocsController::openApiSpec'); // OAS 3.0 JSON

    // ── Listings (Public) ─────────────────────────────────────────
    $routes->get('listings',           'ListingsController::index');    // Browse + filter
    $routes->get('listings/search',    'ListingsController::search');   // Keyword search
    $routes->get('listings/carousel',  'ListingsController::carousel'); // Featured carousel
    $routes->get('listings/(:num)',    'ListingsController::show/$1');  // Detail by ID

    // ── Categories (Public) ───────────────────────────────────────
    $routes->get('categories', 'ListingsController::categories');

    // ── Bookings ──────────────────────────────────────────────────
    $routes->get('bookings',                   'BookingsController::index');          // My bookings (auth required)
    $routes->post('bookings/init',             'BookingsController::init');           // Step 1: OTP
    $routes->post('bookings/verify-otp',       'BookingsController::verifyOtp');     // Step 2: verify
    $routes->post('bookings/confirm-payment',  'BookingsController::confirmPayment');// Step 3: Razorpay

    // ── Location ──────────────────────────────────────────────────
    $routes->post('location', 'App\Controllers\Frontend::setLocation');


    // ── Reviews ───────────────────────────────────────────────────
    $routes->post('reviews', 'App\Controllers\Frontend::submitReview');

    // ── Web Push ──────────────────────────────────────────────────
    $routes->post('push/subscribe',   'App\Controllers\Frontend::pushSubscribe');
    $routes->post('push/unsubscribe', 'App\Controllers\Frontend::pushUnsubscribe');

});

// ── PUBLIC FRONTEND (Parent-facing — no auth required) ──
$routes->get('/',                        'Frontend::index');
$routes->get('classes',                  'Frontend::classes');
$routes->get('activity',                 'Frontend::activity');
$routes->get('contact',                  'Frontend::contact');
$routes->post('contact/submit',          'Frontend::contactSubmit');
$routes->post('set-location',            'Frontend::setLocation');       // AJAX: save location
$routes->get('api/listings/nearby',    'Frontend::nearbyListings');    // AJAX: fetch listings
$routes->get('api/listings/search',    'Frontend::searchListings');    // AJAX: keyword search
$routes->get('api/listings/carousel',  'Frontend::carouselApi');       // AJAX: featured carousel
$routes->get('api/listings/(:num)',    'Frontend::listingDetailApi/$1'); // AJAX: single listing
$routes->get('api/subcategories',      'Frontend::getSubcategories');    // AJAX: public subcategories
$routes->get('classes/(:num)',         'Frontend::listingDetail/$1');    // Detail page
$routes->get('search',                 'Frontend::searchPage');         // Search results page

// ── BOOKING FLOW (AJAX, 3 steps) ──
$routes->post('booking/init',            'BookingController::init');           // Step 1: student info + OTP
$routes->post('booking/verify-otp',      'BookingController::verifyOtp');      // Step 2: OTP verify / Razorpay order
$routes->post('booking/confirm-payment', 'BookingController::confirmPayment'); // Step 3: Razorpay verify + confirm

// ── FULL-PAGE BOOKING FLOW ──
$routes->get('book/(:num)',              'BookingController::bookingPage/$1');  // Booking form page
$routes->get('booking/success',          'BookingController::bookingSuccess');  // Success page

// ── WEB PUSH SUBSCRIPTIONS ──
$routes->post('api/push/subscribe',   'Frontend::pushSubscribe');   // Save push subscription
$routes->post('api/push/unsubscribe', 'Frontend::pushUnsubscribe'); // Remove push subscription


$routes->get('contact',                 'Frontend::contact');          // Contact page
$routes->post('api/feedback',           'Frontend::contactSubmit');    // AJAX Feedback API
$routes->post('api/reviews/submit',     'Frontend::submitReview');     // AJAX Review submission
$routes->post('submit-review',          'Frontend::submitReview');     // AJAX Review
$routes->get('booking/certificate/(:num)', 'BookingController::downloadCertificate/$1'); 
$routes->get('privacy', 'Frontend::privacyPolicy');
$routes->get('data-deletion', 'Frontend::dataDeletion');
$routes->get('terms', 'Frontend::terms');

// ── AUTHENTICATION ──
$routes->get('auth/login',               'Auth\Login::index');
$routes->post('auth/login/check',        'Auth\Login::check');
$routes->get('auth/logout',              'Auth\Logout::index');
$routes->get('join-as-provider',         'Frontend::joinAsProvider', ['filter' => 'authentication']);

// ── PARENT / USER AUTHENTICATION (Frontend) ──
$routes->get('login',                    'Auth\FrontendAuth::loginPage');
$routes->post('login',                   'Auth\FrontendAuth::loginPost');
$routes->post('login/otp/send',          'Auth\FrontendAuth::sendOtpPost');
$routes->post('login/otp/verify',        'Auth\FrontendAuth::verifyOtpPost');
$routes->get('register',                 'Auth\FrontendAuth::registerPage');
    $routes->post('register', 'Auth\FrontendAuth::registerPost');
    $routes->get('logout',   'Auth\FrontendAuth::logout');
$routes->get('my-bookings',              'Frontend::myBookings');

// ── GOOGLE AUTH ──
$routes->get('auth/google',          'Auth\GoogleAuth::login');
$routes->get('auth/google/callback', 'Auth\GoogleAuth::callback');

// ── EMAIL VERIFICATION ──
$routes->get('verify-email', 'Auth\EmailVerification::verify');
$routes->get('profile/deleteAccount', 'Profile::deleteAccount', ['filter' => 'authentication']);

// ── PROVIDER DASHBOARD (Requires authentication) ──
$routes->group('provider', ['filter' => 'authentication'], function($routes) {
    $routes->get('dashboard',            'Provider::dashboard');    // Main Dashboard
    $routes->get('support',              'Provider::support');      // Support & Help
    $routes->get('listings',             'Provider::listings');    // Subtask 2.1
    $routes->get('api/listings',         'Provider::apiListings'); // Subtask 2.1 API
    $routes->get('listings/create',      'Provider::create');      // Subtask 2.2
    $routes->post('listings/store',      'Provider::store');       // Subtask 2.2 Submit
    $routes->get('availability',         'Provider::availability');
    $routes->get('api/listing-details/(:num)', 'Provider::getListingDetails/$1');
    $routes->get('api/cancellation-preview/(:num)', 'Provider::cancellationPreview/$1');
    $routes->post('api/holiday-preview', 'Provider::holidayPreview');
    $routes->post('availability/end-class', 'Provider::endClass');
    $routes->post('availability/declare-holiday', 'Provider::declareHoliday');
    $routes->get('api/subcategories',    'Provider::getSubcategories');
    $routes->get('listings/edit/(:num)', 'Provider::edit/$1');     // Subtask 2.4
    $routes->post('listings/update/(:num)', 'Provider::update/$1'); // Subtask 2.4
    $routes->get('enrollments',          'Provider::enrollments'); // New: Student Enrollments
    $routes->get('payouts',              'Provider::payouts');     // New: Provider Payouts
    $routes->get('instructors',          'Provider::instructors');  // New: Instructor Management
    $routes->post('api/instructors/save', 'Provider::saveInstructor'); // New: Save/Update Instructor
    $routes->post('api/instructors/delete', 'Provider::deleteInstructor'); // New: Delete Instructor
    $routes->get('notifications',        'Provider::notifications'); // New: Notifications
    
    // ── Verification & KYC (Subtask 2.3) ──
    $routes->get('verification',         'Provider::verification');
    $routes->post('api/verify/phone/send', 'Provider::sendPhoneVerification');
    $routes->post('api/verifyphone/mark-verified', 'Provider::markPhoneVerified');
    $routes->post('api/verify/phone/check', 'Provider::checkPhoneVerification');
    $routes->post('api/kyc/upload',      'Provider::uploadKyc');
    $routes->post('api/kyc/delete/(:num)', 'Provider::deleteKyc/$1');
    $routes->post('api/payout/update',   'Provider::updatePayout');
    $routes->post('api/verify/submit',   'Provider::submitVerification');
    $routes->post('api/verification/phone/update', 'Provider::updatePhone');
    $routes->post('api/verification/phone/verify-otp', 'Provider::verifyPhoneOtp');
    $routes->post('api/verification/email/update', 'Provider::updateEmail');
    $routes->post('api/verification/email/verify-otp', 'Provider::verifyEmailOtp');

    // ── Management API (Subtask 2.4) ──
    $routes->post('api/listings/image/delete/(:num)', 'Provider::deleteListingImage/$1');

    // ── Mode Toggle ──
    $routes->get('toggle-mode',          'Provider::toggleMode');
    $routes->post('api/concerns/raise',  'Provider::raiseConcern');
    $routes->get('support/view/(:num)',  'Provider::viewTicket/$1');
    $routes->post('support/reply',       'Provider::replyTicket');
});

// ── ADMIN DASHBOARD (requires authentication) ──
$routes->group('admin', ['filter' => 'authentication'], function($routes) {
    $routes->get('dashboard',            'Dashboard::index');
    
    // ── Listing Review & Management (Subtask 3.2) ──
    $routes->get('listings',             'AdminController::index');
    $routes->get('listings/edit/(:num)', 'AdminController::editListing/$1');
    $routes->post('api/listings/review', 'AdminController::reviewListing');
    $routes->post('api/listings/toggle-block', 'AdminController::toggleListingBlock');
    $routes->post('api/listings/save',   'AdminController::saveListing');
    $routes->get('verifications',        'AdminController::verifications');
    $routes->get('provider-verifications', 'AdminController::verifications'); // Alias as requested
    $routes->get('provider/(:num)',      'AdminController::providerDetail/$1');
    $routes->post('api/listings/review', 'AdminController::reviewListing');
    $routes->post('api/deactivate',      'AdminController::deactivateListing');
    $routes->post('api/block-user',          'AdminController::blockUser');
    $routes->post('api/document/verify',     'AdminController::verifyDocument');
    $routes->post('api/update-rzp-account',  'AdminController::updateRzpAccount');
    $routes->post('api/promote-user',        'AdminController::promoteUser');
    $routes->post('api/provider/review',     'AdminController::reviewProvider');

    // ── Settlement Management (Subtask 3.4) ──
    $routes->get('settlements',          'AdminController::settlements');
    $routes->post('api/settlements/block', 'AdminController::toggleSettlementBlock');
    $routes->post('api/settlements/complete', 'AdminController::completeSettlement');

    // ── Refund Management ──
    $routes->get('bookings',                 'AdminController::bookings');
    $routes->get('refunds',                  'AdminController::refunds');
    $routes->post('api/refunds/initiate',    'AdminController::initiateRefund');
    $routes->post('api/refunds/process',     'AdminController::processRefund');

    // ── Concern Management ──
    $routes->get('concerns',                 'AdminController::concerns');
    $routes->post('api/concerns/resolve',    'AdminController::resolveConcern');

    // ── Carousel Management (Subtask 3.5) ──
    $routes->get('carousel',             'AdminController::carousel');
    $routes->post('api/carousel/add',    'AdminController::addCarouselListing');
    $routes->post('api/carousel/remove', 'AdminController::removeCarouselListing');
    $routes->post('api/carousel/reorder','AdminController::reorderCarousel');

    // ── Testimonials Management ──
    $routes->get('testimonials',               'AdminController::testimonials');
    $routes->post('testimonials/save',         'AdminController::saveTestimonial');
    $routes->get('testimonials/delete/(:num)', 'AdminController::deleteTestimonial');
    $routes->post('api/testimonials/reorder',  'AdminController::reorderTestimonials');

    // ── Category & Subcategory Management ──
    $routes->get('categories',           'AdminController::categories');
    $routes->post('categories/save',     'AdminController::saveCategory');
    $routes->get('categories/delete/(:num)', 'AdminController::deleteCategory');
    
    $routes->get('subcategories',        'AdminController::subcategories');
    $routes->post('subcategories/save',  'AdminController::saveSubcategory');
    $routes->get('subcategories/delete/(:num)', 'AdminController::deleteSubcategory');

    // ── Support Tickets (Feedback) ──
    $routes->get('feedback',             'Feedback::index');
    $routes->get('feedback/view/(:num)',  'Feedback::view/$1');

    // ── User Management ──
    $routes->get('users',                  'Users::index');
    $routes->post('users/change_status/(:num)', 'Users::change_status/$1');
    $routes->get('users/delete/(:num)',    'Users::delete/$1');
    $routes->get('users/edit/(:num)',      'Users::edit/$1');

    // ── System Oversight & Settings ──
    $routes->get('stats',                'AdminController::stats');
    $routes->get('activity-log',         'AdminController::activityLog');
    $routes->get('whatsapp-logs',         'AdminController::whatsappLogs');
    $routes->get('settings',             'AdminController::settings');
    $routes->post('api/settings/verify-auth',        'AdminController::verifyAuth');
    $routes->post('api/settings/toggle-admin-status','AdminController::toggleAdminStatus');
    $routes->post('api/settings/create-admin',       'AdminController::createAdmin');
    $routes->post('users/update/(:num)',   'Users::update/$1');
    $routes->get('users/add',              'Users::add');
    $routes->post('users/save',            'Users::save');

    // ── API for Dashboard Internal Use ──
    $routes->get('api/stats',            'Dashboard::apiStats');
    $routes->get('api/users/count',      'Dashboard::apiUserCount');
    $routes->get('api/providers/count',  'Dashboard::apiProviderCount');
    $routes->get('api/bookings/count',   'Dashboard::apiBookingCount');
    $routes->get('api/revenue/total',    'Dashboard::apiRevenueTotal');
});

// ── Webhooks (Public access required for third-party services) ──
$routes->match(['get', 'post'], 'webhooks/whatsapp', 'Webhooks\WhatsApp::index');

$routes->get('dashboard', 'Dashboard::index');

/*
 * --------------------------------------------------------------------
 * Additional Routing
 * --------------------------------------------------------------------
 *
 * There will often be times that you need additional routing and you
 * need it to be able to override any defaults in this file. Environment
 * based routes is one such time. require() additional route files here
 * to make that happen.
 *
 * You will have access to the $routes object within that file without
 * needing to reload it.
 */
if (file_exists(APPPATH . 'Config/' . ENVIRONMENT . '/Routes.php')) {
    require APPPATH . 'Config/' . ENVIRONMENT . '/Routes.php';
}
