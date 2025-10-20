<?php

namespace App\Models;

use CodeIgniter\Model;

class NotificationModel extends Model
{
    protected $table = 'notifications';
    protected $primaryKey = 'id';
    protected $allowedFields = ['user_id', 'message', 'is_read', 'created_at'];
    protected $useTimestamps = true;
    protected $createdField = 'created_at';

    /**
     * Get unread notification count for a user
     */
    public function getUnreadCount($userId)
    {
        return $this->where('user_id', $userId)
                    ->where('is_read', 0)
                    ->countAllResults();
    }

    /**
     * Get latest notifications for a user (limit 5)
     */
    public function getNotificationsForUser($userId)
    {
        return $this->where('user_id', $userId)
                    ->orderBy('created_at', 'DESC')
                    ->limit(5)
                    ->find();
    }

    /**
     * Mark a specific notification as read
     */
    public function markAsRead($notificationId)
    {
        return $this->update($notificationId, ['is_read' => 1]);
    }
}
