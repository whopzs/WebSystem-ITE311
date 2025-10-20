<?php

namespace App\Controllers;

use App\Models\NotificationModel;

class Notifications extends BaseController
{
    /**
     * Get notifications for current user
     */
    public function get()
    {
        $session = service('session');
        $userId = $session->get('user_id');

        if (!$userId) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'User not logged in'
            ]);
        }

        $notificationModel = new NotificationModel();

        $unreadCount = $notificationModel->getUnreadCount($userId);
        $notifications = $notificationModel->getNotificationsForUser($userId);

        return $this->response->setJSON([
            'success' => true,
            'unread_count' => $unreadCount,
            'notifications' => $notifications
        ]);
    }

    /**
     * Mark notification as read
     */
    public function mark_as_read($id)
    {
        $session = service('session');
        $userId = $session->get('user_id');

        if (!$userId) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'User not logged in'
            ]);
        }

        $notificationModel = new NotificationModel();

        // Verify the notification belongs to the current user
        $notification = $notificationModel->where('id', $id)
                                         ->where('user_id', $userId)
                                         ->first();

        if (!$notification) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Notification not found'
            ]);
        }

        $result = $notificationModel->markAsRead($id);

        if ($result) {
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Notification marked as read'
            ]);
        } else {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Failed to mark notification as read'
            ]);
        }
    }
}
