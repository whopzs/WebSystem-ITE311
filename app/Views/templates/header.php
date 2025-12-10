<?php
// Get current route (like 'login', 'register', 'dashboard', etc.)
$currentRoute = service('router')->getMatchedRoute()[0] ?? '';
$session = session();
$userRole = $session->get('userRole'); // Role saved in session

// Sidebar menus per role
$menus = [
    'admin' => [
        ['route' => 'dashboard', 'icon' => 'bi-speedometer2', 'label' => 'Dashboard'],
        ['route' => 'announcements/create', 'icon' => 'bi-megaphone', 'label' => 'Announcement'],
        ['route' => 'users', 'icon' => 'bi-people', 'label' => 'Users'],
        ['route' => 'course', 'icon' => 'bi-journal-bookmark', 'label' => 'Courses'],
        ['route' => 'settings', 'icon' => 'bi-gear', 'label' => 'Settings'],
    ],
    'teacher' => [
        ['route' => 'dashboard', 'icon' => 'bi-speedometer2', 'label' => 'Dashboard'],
        ['route' => 'announcements', 'icon' => 'bi-megaphone', 'label' => 'Announcements'],
        ['route' => 'course', 'icon' => 'bi-journal-bookmark', 'label' => 'Courses'],
        ['route' => 'teacher/assignment', 'icon' => 'bi-pencil-square', 'label' => 'Assignments'],
        ['route' => 'teacher/grades', 'icon' => 'bi-mortarboard', 'label' => 'Grades'],
        ['route' => 'teacher/settings', 'icon' => 'bi-gear', 'label' => 'Settings'],
    ],
    'student' => [
        ['route' => 'dashboard', 'icon' => 'bi-speedometer2', 'label' => 'Dashboard'],
        ['route' => 'announcements', 'icon' => 'bi-megaphone', 'label' => 'Announcements'],
        ['route' => 'course', 'icon' => 'bi-journal-bookmark', 'label' => 'Courses'],
        ['route' => 'student/assignment', 'icon' => 'bi-pencil-square', 'label' => 'Assignments'],
        ['route' => 'student/settings', 'icon' => 'bi-gear', 'label' => 'Settings'],
    ]
];

// Sidebar render function
function renderSidebar($role, $menus, $currentRoute) {
    if (!isset($menus[$role])) return;

    echo '<div class="sidebar"><nav class="nav flex-column">';
    foreach ($menus[$role] as $menu) {
        $active = ($currentRoute === $menu['route']) ? 'active' : '';
        echo '<a class="nav-link '.$active.'" href="'.base_url($menu['route']).'">
                <i class="bi '.$menu['icon'].' me-2"></i>'.$menu['label'].'</a>';
    }
    echo '<hr class="text-white">
          <a class="nav-link" href="'.base_url('logout').'"><i class="bi bi-box-arrow-right me-2"></i>Logout</a>';
    echo '</nav></div>';
    echo "<script>document.body.classList.add('has-sidebar');</script>";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="X-CSRF-TOKEN" content="<?= csrf_hash() ?>" />
    <title>ITE311</title>
    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <style>
        html, 
        body { 
            height: 100%; 
            margin: 0;
            display: flex;
            flex-direction: column; 
        }
        body { 
            background-color: #fff;
            color: #333; 
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; 
        }
        .navbar { 
            background-color: #800000 !important; box-shadow: 0 2px 4px rgba(0,0,0,0.1); 
        }
        .navbar-brand,
        .nav-link { color: #fff !important; font-weight: 500; 
        } 
        .nav-link:hover { 
            color: #ffcccc !important; 
        }
        .sidebar { 
            width: 240px; 
            background-color: #800000; 
            color: #fff; 
            height: 100%; 
            position: fixed; 
            top: 56px; 
            left: 0; 
            overflow-y: auto; 
            z-index: 1000; 
        }
        .sidebar 
        .nav-link { 
            color: #fff; 
            padding: 10px 20px; 
            display: block; 
        }      
        .sidebar 
        .nav-link.active, 
        .sidebar 
        .nav-link:hover { 
            background-color: #a52a2a; 
            color: #fff; 
        }
        main { 
            flex: 1; 
            padding-top: 80px; 
            transition: margin-left 0.3s; 
        }
        .has-sidebar main { 
            margin-left: 240px; 
            width: calc(100% - 240px); 
            padding: 80px 20px 20px; 
        }
        .card { 
            width: 100%; 
            margin-bottom: 1rem; 
        }
    </style>
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg fixed-top">
        <div class="container-fluid">
            <a class="navbar-brand" href="<?= base_url('/') ?>">ITE311</a>

            <!-- Right-side Menu -->
            <ul class="navbar-nav ms-auto">
                <?php if ($session->get('isLoggedIn')): ?>
                    <!-- Notifications Dropdown -->
                    <li class="nav-item dropdown me-3">
                        <a class="nav-link dropdown-toggle position-relative" href="#" role="button" data-bs-toggle="dropdown">
                            <i class="bi bi-bell"></i>
                            <?php if (isset($notificationCount) && $notificationCount > 0): ?>
                                <span class="badge bg-danger position-absolute top-0 start-100 translate-middle rounded-pill">
                                    <?= $notificationCount ?>
                                </span>
                            <?php endif; ?>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end" id="notifications-dropdown">
                            <li><h6 class="dropdown-header">Notifications</h6></li>
                            <li><hr class="dropdown-divider"></li>
                            <li id="notifications-list">
                                <!-- Notifications will be loaded here via AJAX -->
                            </li>
                            <li id="no-notifications" style="display: none;">
                                <a class="dropdown-item text-muted">No notifications</a>
                            </li>
                        </ul>
                    </li>

                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                            <i class="bi bi-person-circle me-1"></i>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><a class="dropdown-item" href="#"><i class="bi bi-person me-2"></i>Profile</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="<?= base_url('logout') ?>"><i class="bi bi-box-arrow-right me-2"></i>Logout</a></li>
                        </ul>
                    </li>
                <?php else: ?>
                    <li class="nav-item"><a class="nav-link" href="<?= base_url('login') ?>">Login</a></li>
                <?php endif; ?>
            </ul>
        </div>
    </nav>

    <!-- Sidebar -->
    <?php if ($session->get('isLoggedIn')) renderSidebar($userRole, $menus, $currentRoute); ?>

    <!-- Dynamic Content -->
    <main class="container-fluid">
        <?= $this->renderSection('content') ?>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <?php if ($session->get('isLoggedIn')): ?>
    <script>
    $(document).ready(function() {
        // Load notifications on page load
        loadNotifications();

        // Refresh notifications every 10 seconds
        setInterval(loadNotifications, 10000);

        $('.nav-link:has(.bi-bell)').parent().on('shown.bs.dropdown', function() {
            loadNotifications();
        });
    });

    function loadNotifications() {
        $.get('<?= base_url('notifications') ?>')
            .done(function(response) {
                if (response.success) {
                    updateNotificationBadge(response.unread_count);
                    updateNotificationList(response.notifications);
                }
            })
            .fail(function() {
                console.error('Failed to load notifications');
            });
    }

    function updateNotificationBadge(count) {
        const badge = $('.nav-link .badge');
        if (count > 0) {
            if (badge.length) {
                badge.text(count);
            } else {
                $('.nav-link:has(.bi-bell)').append('<span class="badge bg-danger position-absolute top-0 start-100 translate-middle rounded-pill">' + count + '</span>');
            }
        } else {
            badge.remove();
        }
    }

    function updateNotificationList(notifications) {
        const list = $('#notifications-list');
        const noNotifications = $('#no-notifications');

        list.empty();

        if (notifications.length === 0) {
            noNotifications.show();
            return;
        }
        noNotifications.hide();
        notifications.forEach(function(notification) {
            const notificationItem = `
                <div class="notification-item d-flex justify-content-between align-items-start mb-3 p-3 border-bottom border-light">
                    <div class="flex-grow-1 me-4" style="min-width: 0;">
                        <div class="notification-message text-dark fw-semibold mb-2" style="font-size: 0.9rem; line-height: 1.4; word-wrap: break-word; overflow-wrap: break-word;">
                            ${notification.message}
                        </div>
                        <div class="notification-time text-muted d-flex align-items-center" style="font-size: 0.75rem;">
                            <i class="bi bi-clock me-2"></i>${formatDate(notification.created_at)}
                        </div>
                    </div>
                    <div class="flex-shrink-0 ms-3">
                        <button class="btn btn-sm mark-read-btn d-flex align-items-center" onclick="markAsRead(${notification.id})"
                                style="background-color: #800000; border-color: #800000; color: white; font-size: 0.8rem; padding: 0.5rem 1rem; border-radius: 0.375rem; transition: all 0.2s ease; white-space: nowrap;">
                            <i class="bi bi-check me-2"></i>mark as read
                        </button>
                    </div>
                </div>
            `;
            list.append(notificationItem);
        });
    }

    function markAsRead(notificationId) {
        // Try to get CSRF token from the page
        var csrfToken = '<?= csrf_hash() ?>';

        if (!csrfToken) {
            console.error('CSRF token not found');
            return;
        }
        var notificationItem = $('button[onclick="markAsRead(' + notificationId + ')"]').closest('.notification-item');
        var originalContent = notificationItem.html();

        // Show loading state
        notificationItem.html('<div class="text-center text-muted"><small>Marking as read...</small></div>');

        $.ajax({
            url: '<?= base_url('notifications/mark_read') ?>/' + notificationId,
            type: 'POST',
            data: {
                csrf_token_name: csrfToken
            },
            headers: {
                'X-CSRF-TOKEN': csrfToken,
                'X-Requested-With': 'XMLHttpRequest'
            },
            success: function(response) {
                if (response.success) {
                    // Remove the notification item from the UI
                    notificationItem.fadeOut(300, function() {
                        $(this).remove();
                        var remainingNotifications = $('#notifications-list .notification-item').length;
                        if (remainingNotifications === 0) {
                            $('#no-notifications').show();
                        }
                        // Update badge count
                        var currentBadge = $('.nav-link .badge');
                        if (currentBadge.length) {
                            var currentCount = parseInt(currentBadge.text());
                            if (currentCount > 1) {
                                currentBadge.text(currentCount - 1);
                            } else {
                                currentBadge.remove();
                            }
                        }
                    });
                } else {
                    // Restore original content on error
                    notificationItem.html(originalContent);
                    console.error('Server response:', response.message);
                }
            },
            error: function(xhr, status, error) {
                // Restore original content on error
                notificationItem.html(originalContent);
                console.error('Failed to mark notification as read:', error);
                console.error('Status:', status);
                console.error('Response:', xhr.responseText);
                console.error('CSRF Token used:', csrfToken);
                console.error('Response headers:', xhr.getAllResponseHeaders());
            }
        });
    }

    function formatDate(dateString) {
        const date = new Date(dateString);
        const now = new Date();
        const diff = now - date;

        const minutes = Math.floor(diff / 60000);
        const hours = Math.floor(diff / 3600000);
        const days = Math.floor(diff / 86400000);

        if (minutes < 1) return 'Just now';
        if (minutes < 60) return minutes + ' minutes ago';
        if (hours < 24) return hours + ' hours ago';
        return days + ' days ago';
    }
    </script>
    <?php endif; ?>
</body>
</html>
