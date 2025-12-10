<?php

namespace App\Controllers;

use CodeIgniter\Controller;
use CodeIgniter\HTTP\CLIRequest;
use CodeIgniter\HTTP\IncomingRequest;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Psr\Log\LoggerInterface;

/**
 * Class BaseController
 *
 * BaseController provides a convenient place for loading components
 * and performing functions that are needed by all your controllers.
 * Extend this class in any new controllers:
 *     class Home extends BaseController
 *
 * For security be sure to declare any new methods as protected or private.
 */
abstract class BaseController extends Controller
{
    /**
     * Instance of the main Request object.
     *
     * @var CLIRequest|IncomingRequest
     */
    protected $request;

    /**
     * An array of helpers to be loaded automatically upon
     * class instantiation. These helpers will be available
     * to all other controllers that extend BaseController.
     *
     * @var list<string>
     */
    protected $helpers = [];

    /**
     * Be sure to declare properties for any property fetch you initialized.
     * The creation of dynamic property is deprecated in PHP 8.2.
     */
    // protected $session;

    /**
     * @return void
     */
    public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger)
    {
        // Do Not Edit This Line
        parent::initController($request, $response, $logger);

        // Preload any models, libraries, etc, here.

        // E.g.: $this->session = service('session');

        // Load notification count for logged-in users
        $this->loadNotificationData();
    }

    /**
     * Load notification data for the current user
     */
    protected function loadNotificationData()
    {
        $session = service('session');
        $userId = $session->get('user_id');

        if ($userId) {
            $notificationModel = new \App\Models\NotificationModel();
            $unreadCount = $notificationModel->getUnreadCount($userId);
            $this->notificationCount = $unreadCount;
        } else {
            $this->notificationCount = 0;
        }
    }

    /**
     * Get recent activities for admin dashboard
     */
    protected function getRecentActivities($limit = 20)
    {
        $activities = [];
        $userModel = new \App\Models\UserModel();
        $courseModel = new \App\Models\CourseModel();
        $enrollmentModel = new \App\Models\EnrollmentModel();

        // Get recent course creations (last 30 days)
        $recentCourses = $courseModel->where('created_at >=', date('Y-m-d H:i:s', strtotime('-30 days')))
                                     ->orderBy('created_at', 'DESC')
                                     ->limit($limit)
                                     ->findAll();
        foreach ($recentCourses as $course) {
            $activities[] = [
                'name' => 'Admin',
                'role' => 'Admin',
                'action' => 'Created',
                'target' => "Course: \"{$course['title']}\"",
                'created_at' => $course['created_at'] ?? date('Y-m-d H:i:s')
            ];
        }

        // Get recent teacher assignments (courses with instructor_id updated in last 30 days)
        $assignedCourses = $courseModel->where('instructor_id IS NOT NULL')
                                      ->where('instructor_id !=', 0)
                                      ->where('updated_at >=', date('Y-m-d H:i:s', strtotime('-30 days')))
                                      ->orderBy('updated_at', 'DESC')
                                      ->limit($limit)
                                      ->findAll();
        foreach ($assignedCourses as $course) {
            if (!empty($course['instructor_id'])) {
                $teacher = $userModel->find($course['instructor_id']);
                if ($teacher) {
                    $scheduleInfo = '';
                    if (!empty($course['day']) && !empty($course['time'])) {
                        $scheduleInfo = " ({$course['day']} at {$course['time']}";
                        if (!empty($course['room'])) {
                            $scheduleInfo .= ", Room {$course['room']}";
                        }
                        $scheduleInfo .= ')';
                    }
                    
                    $activities[] = [
                        'name' => $teacher['name'],
                        'role' => 'Teacher',
                        'action' => 'Assigned to',
                        'target' => "Course: \"{$course['title']}\"{$scheduleInfo}",
                        'created_at' => $course['updated_at'] ?? $course['created_at'] ?? date('Y-m-d H:i:s')
                    ];
                }
            }
        }

        // Get recent approved enrollments (last 30 days)
        $recentEnrollments = $enrollmentModel->where('status', 'approved')
                                             ->where('enrollment_date >=', date('Y-m-d H:i:s', strtotime('-30 days')))
                                             ->orderBy('enrollment_date', 'DESC')
                                             ->limit($limit)
                                             ->findAll();
        foreach ($recentEnrollments as $enrollment) {
            $student = $userModel->find($enrollment['user_id']);
            $course = $courseModel->find($enrollment['course_id']);
            if ($student && $course) {
                $activities[] = [
                    'name' => $student['name'],
                    'role' => 'Student',
                    'action' => 'Enrolled in',
                    'target' => "Course: \"{$course['title']}\"",
                    'created_at' => $enrollment['enrollment_date'] ?? date('Y-m-d H:i:s')
                ];
            }
        }

        // Get recent user registrations (last 30 days)
        $recentUsers = $userModel->where('created_at >=', date('Y-m-d H:i:s', strtotime('-30 days')))
                                 ->orderBy('created_at', 'DESC')
                                 ->limit($limit)
                                 ->findAll();
        foreach ($recentUsers as $user) {
            $activities[] = [
                'name' => $user['name'],
                'role' => ucfirst($user['role']),
                'action' => 'Registered',
                'target' => 'Account',
                'created_at' => $user['created_at'] ?? date('Y-m-d H:i:s')
            ];
        }

        // Sort all activities by created_at (most recent first)
        usort($activities, function($a, $b) {
            $timeA = strtotime($a['created_at']);
            $timeB = strtotime($b['created_at']);
            return $timeB - $timeA; // Descending order
        });

        // Return only the most recent activities
        return array_slice($activities, 0, $limit);
    }
}
