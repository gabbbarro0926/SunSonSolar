<?php

use CodeIgniter\Router\RouteCollection;

/** @var RouteCollection $routes */

$routes->get('/', 'Home::index');
$routes->get('login', 'Home::login');      
$routes->get('register', 'Home::register'); 
$routes->get('services', 'Home::services');
$routes->get('about', 'Home::about');
$routes->get('contact', 'Home::contact');
$routes->get('dashboard', 'Home::dashboard');

// CORS preflight for every API URL
$routes->options('api/(:any)', static fn () => service('response')->setStatusCode(204));

$routes->group('api', ['namespace' => 'App\Controllers\Api'], static function (RouteCollection $routes) {

    // ---------------- Public (no login) ----------------
    $routes->post('auth/register', 'AuthController::register');
    $routes->post('auth/login', 'AuthController::login');
    $routes->get('departments', 'AuthController::departments');

    $routes->get('public/home', 'PublicController::home');
    $routes->get('public/services', 'PublicController::services');
    $routes->get('public/products', 'PublicController::products');
    $routes->get('public/posts', 'PublicController::posts');
    $routes->get('public/posts/(:num)', 'PublicController::post/$1');
    $routes->post('public/quotes', 'PublicController::quote');

    // ---------------- Any logged-in user ----------------
    $routes->group('', ['filter' => 'auth'], static function (RouteCollection $routes) {
        $routes->post('auth/logout', 'AuthController::logout');
        $routes->get('auth/me', 'AuthController::whoami');

        $routes->get('profile', 'ProfileController::show');
        $routes->put('profile', 'ProfileController::update');
        $routes->put('profile/password', 'ProfileController::changePassword');
        $routes->put('profile/preferences', 'ProfileController::preferences');

        $routes->get('notifications', 'NotificationController::index');
        $routes->put('notifications/read-all', 'NotificationController::markAllRead');
        $routes->put('notifications/(:num)/read', 'NotificationController::markRead/$1');
    });

    // ---------------- Customer ----------------
    $routes->group('customer', ['filter' => ['auth', 'role:customer']], static function (RouteCollection $routes) {
        $routes->get('bookings', 'Customer\BookingController::index');
        $routes->post('bookings', 'Customer\BookingController::create');
        $routes->get('bookings/(:num)', 'Customer\BookingController::show/$1');
        $routes->put('bookings/(:num)/reschedule', 'Customer\BookingController::reschedule/$1');
        $routes->put('bookings/(:num)/cancel', 'Customer\BookingController::cancel/$1');

        $routes->get('history', 'Customer\HistoryController::index');
        $routes->get('receipts/(:num)', 'Customer\HistoryController::receipt/$1');

        $routes->get('tickets', 'Customer\TicketController::index');
        $routes->post('tickets', 'Customer\TicketController::create');
        $routes->get('tickets/(:num)', 'Customer\TicketController::show/$1');
        $routes->post('tickets/(:num)/reply', 'Customer\TicketController::reply/$1');
    });

    // ---------------- Employees (any non-customer) ----------------
    $routes->group('', ['filter' => ['auth', 'role:employee']], static function (RouteCollection $routes) {
        $routes->get('announcements', 'Employee\AnnouncementController::index');
    });

    // Technician: check-in, jobs, incident reports
    $routes->group('', ['filter' => ['auth', 'role:technician']], static function (RouteCollection $routes) {
        $routes->post('attendance/check-in', 'Employee\AttendanceController::checkIn');
        $routes->get('attendance/mine', 'Employee\AttendanceController::mine');

        $routes->get('technician/jobs', 'Employee\JobController::index');
        $routes->get('technician/jobs/(:num)', 'Employee\JobController::show/$1');
        $routes->put('technician/jobs/(:num)/complete', 'Employee\JobController::complete/$1');

        $routes->get('technician/incidents', 'Employee\IncidentController::mine');
        $routes->post('technician/incidents', 'Employee\IncidentController::create');
    });

    // ---------------- Admin area ----------------
    $routes->group('admin', ['namespace' => 'App\Controllers\Api\Admin'], static function (RouteCollection $routes) {

        // Admin + HR: user management, announcements, attendance log
        $routes->group('', ['filter' => ['auth', 'role:admin,hr']], static function (RouteCollection $routes) {
            $routes->get('users', 'UserController::index');
            $routes->post('users', 'UserController::create');
            $routes->get('users/(:num)', 'UserController::show/$1');
            $routes->put('users/(:num)', 'UserController::update/$1');
            $routes->put('users/(:num)/deactivate', 'UserController::deactivate/$1');
            $routes->put('users/(:num)/activate', 'UserController::activate/$1');

            $routes->get('announcements', 'AnnouncementController::index');
            $routes->get('announcements/(:num)', 'AnnouncementController::show/$1');
            $routes->post('announcements', 'AnnouncementController::create');
            $routes->put('announcements/(:num)', 'AnnouncementController::update/$1');
            $routes->delete('announcements/(:num)', 'AnnouncementController::delete/$1');
        });

        // Admin + Dispatcher + HR: attendance log
        $routes->get('attendance', 'AttendanceController::index', ['filter' => ['auth', 'role:admin,dispatcher,hr']]);

        // Admin + Dispatcher: schedule overview
        $routes->group('', ['filter' => ['auth', 'role:admin,dispatcher']], static function (RouteCollection $routes) {
            $routes->get('bookings', 'ScheduleController::index');
            $routes->get('bookings/(:num)', 'ScheduleController::show/$1');
            $routes->get('technicians', 'ScheduleController::technicians');
            $routes->put('bookings/(:num)/approve', 'ScheduleController::approve/$1');
            $routes->put('bookings/(:num)/decline', 'ScheduleController::decline/$1');
            $routes->put('bookings/(:num)/assign', 'ScheduleController::assign/$1');
            $routes->put('bookings/(:num)/reschedule', 'ScheduleController::reschedule/$1');
            $routes->post('bookings/(:num)/payments', 'ScheduleController::recordPayment/$1');
        });

        // Admin + customer-service staff: support desk and quotes
        $routes->group('', ['filter' => ['auth', 'role:admin,staff']], static function (RouteCollection $routes) {
            $routes->get('tickets', 'TicketController::index');
            $routes->get('tickets/(:num)', 'TicketController::show/$1');
            $routes->post('tickets/(:num)/reply', 'TicketController::reply/$1');
            $routes->put('tickets/(:num)/status', 'TicketController::status/$1');

            $routes->get('quotes', 'QuoteController::index');
            $routes->put('quotes/(:num)', 'QuoteController::update/$1');
        });

        // Admin only: reports, notifications panel, incidents, settings & content
        $routes->group('', ['filter' => ['auth', 'role:admin']], static function (RouteCollection $routes) {
            $routes->get('reports/summary', 'ReportController::summary');
            $routes->get('reports/customer-activity', 'ReportController::customerActivity');
            $routes->get('reports/service-usage', 'ReportController::serviceUsage');
            $routes->get('reports/revenue', 'ReportController::revenue');

            $routes->post('notifications/send', 'NotificationController::send');
            $routes->get('notifications', 'NotificationController::sent');

            $routes->get('incidents', 'IncidentController::index');
            $routes->put('incidents/(:num)', 'IncidentController::update/$1');

            foreach ([
                'services'     => 'ServiceController',
                'products'     => 'ProductController',
                'availability' => 'AvailabilityController',
                'testimonials' => 'TestimonialController',
                'faqs'         => 'FaqController',
                'posts'        => 'PostController',
            ] as $path => $ctl) {
                $routes->get($path, "$ctl::index");
                $routes->get("$path/(:num)", "$ctl::show/$1");
                $routes->post($path, "$ctl::create");
                $routes->put("$path/(:num)", "$ctl::update/$1");
                $routes->delete("$path/(:num)", "$ctl::delete/$1");
            }
        });
    });
});
