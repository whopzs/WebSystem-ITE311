<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Home::index');
$routes->get('home', 'Home::index');
$routes->get('/about', 'Home::about');
$routes->get('/contact', 'Home::contact');

$routes->get('/register', 'Auth::register');
$routes->post('/register', 'Auth::register');
$routes->get('/login', 'Auth::login');
$routes->post('/login', 'Auth::login');
$routes->get('/logout', 'Auth::logout');
$routes->get('/dashboard', 'Auth::dashboard');

$routes->get('/announcements', 'Announcement::index');
$routes->get('/announcements/create', 'Announcement::create');
$routes->post('/announcements/create', 'Announcement::create');

$routes->get('/course', 'Course::index');
$routes->post('/course/enroll', 'Course::enroll');
$routes->get('/course/search', 'Course::search');
$routes->post('/course/search', 'Course::search');

$routes->post('/course/create', 'Course::create');
$routes->post('/course/saveSchedule', 'Course::saveSchedule');
$routes->post('/course/assignTeacher', 'Course::assignTeacher');
$routes->post('/course/approveEnrollment', 'Course::approveEnrollment');
$routes->post('/course/rejectEnrollment', 'Course::rejectEnrollment');
$routes->get('/course/getPendingEnrollments', 'Course::getPendingEnrollments');

$routes->get('/admin/course/(:num)/upload', 'Materials::upload/$1');
$routes->post('/admin/course/(:num)/upload', 'Materials::upload/$1');
$routes->get('/materials/delete/(:num)', 'Materials::delete/$1');
$routes->get('/materials/download/(:num)', 'Materials::download/$1');

$routes->get('/notifications', 'Notifications::get');
$routes->post('/notifications/mark_read/(:num)', 'Notifications::mark_as_read/$1');

$routes->get('/users', 'Users::index');
$routes->post('/users/update/(:num)', 'Users::update/$1');
$routes->post('/users/create', 'Users::create');
$routes->post('/users/delete/(:num)', 'Users::delete/$1');

$routes->get('/settings', 'Settings::index');
$routes->post('/settings/updateProfile', 'Settings::updateProfile');
$routes->post('/settings/updateSystemSettings', 'Settings::updateSystemSettings');
$routes->get('/teacher/settings', 'Settings::teacher');
$routes->get('/student/settings', 'Settings::student');
