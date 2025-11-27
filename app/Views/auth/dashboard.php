<?= $this->extend('templates/header') ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <h1 class="h3 mb-0 text-maroon">
                    <i class="bi bi-speedometer2 me-2"></i>
                    <?php if ($userRole === 'admin'): ?>Admin Dashboard
                    <?php elseif ($userRole === 'teacher'): ?>Teacher Dashboard
                    <?php elseif ($userRole === 'student'): ?>Student Dashboard
                    <?php endif; ?>
                </h1>
            </div>
        </div>
    </div>

<style>
    .text-maroon {
        color: maroon !important;
    }
</style>

<!-- Admin Dashboard-->
<?php if ($userRole === 'admin'): ?>
<!-- Statistics Cards -->
<div class="row mb-4">
    <div class="col-xl-6 col-md-6 mb-4">
        <div class="card border-left-primary shadow h-100 py-2">
            <div class="card-body">
                <!-- Total Users -->
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-maroon text-uppercase mb-1">
                            Total Users
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800"><?= $totalUsers ?></div>
                    </div>
                    <div class="col-auto">
                        <i class="bi bi-people-fill fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-6 col-md-6 mb-4">
        <div class="card border-left-success shadow h-100 py-2">
            <div class="card-body">
                <!-- Total Courses -->
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-maroon text-uppercase mb-1">
                            Total Courses
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800"><?= $courseCount ?></div>
                    </div>
                    <div class="col-auto">
                        <i class="bi bi-book fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>



<!-- Recent Activity -->
<div class="row mt-4" id="recentActivitySection">
    <div class="col-12">
        <div class="card shadow">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-white">Recent Activity</h6>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Role</th>
                                <th>Action</th>
                                <th>Target</th>
                                <th>Created</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($recentActivities as $activity): ?>
                                <tr>
                                    <td><?= esc($activity['name']) ?></td>
                                    <td>
                                        <span class="badge bg-<?= $activity['role'] === 'Teacher' ? 'info' : 'warning' ?>">
                                            <?= esc($activity['role']) ?>
                                        </span>
                                    </td>
                                    <td><?= esc($activity['action']) ?></td>
                                    <td><?= esc($activity['target']) ?></td>
                                    <td><?= date('M d, Y H:i', strtotime($activity['created_at'])) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Teacher Dashboard-->
<?php elseif ($userRole === 'teacher'): ?>
<!-- Quick Stats -->
<div class="row mb-4">
    <div class="col-xl-4 col-md-6 mb-4">
        <div class="card border-left-primary shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-maroon text-uppercase mb-1">
                            Active Courses
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800"><?= count($teacherCourses) ?></div>
                    </div>
                    <div class="col-auto">
                        <i class="bi bi-book fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-4 col-md-6 mb-4">
        <div class="card border-left-success shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-maroon text-uppercase mb-1">
                            Total Students
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                            <?= array_sum(array_column($teacherCourses, 'students')) ?>
                        </div>
                    </div>
                    <div class="col-auto">
                        <i class="bi bi-people fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-4 col-md-6 mb-4">
        <div class="card border-left-info shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-maroon text-uppercase mb-1">
                            New Notifications
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800"><?= count($notifications) ?></div>
                    </div>
                    <div class="col-auto">
                        <i class="bi bi-bell fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Recent Notifications -->
<div class="row mt-4" id="notifications">
    <div class="col-12">
        <div class="card shadow">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-white">Recent Notifications</h6>
            </div>
            <div class="card-body">
                <?php if (!empty($notifications)): ?>
                    <?php foreach ($notifications as $notification): ?>
                        <div class="d-flex align-items-center mb-3">
                            <div class="flex-shrink-0">
                                <i class="bi bi-bell text-white"></i>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <div class="small text-gray-800">
                                    <?= esc($notification['message']) ?>
                                    <?php if ($notification['is_read'] == 0): ?>
                                    <?php endif; ?>
                                </div>
                                <div class="small text-muted">
                                    <?= date('M d, Y H:i', strtotime($notification['created_at'])) ?>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                    <div class="text-center mt-3">
                        <a href="#" class="btn btn-sm btn-outline-maroon">View All</a>
                    </div>
                <?php else: ?>
                    <div class="text-center text-muted">
                        <i class="bi bi-bell-slash fa-2x mb-3 text-muted"></i>
                        <p>No notifications yet.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>


<!-- Student Dashboard-->
<?php elseif ($userRole === 'student'): ?>

<!-- Upcoming Deadlines -->
<div class="row mt-4" id ="deadlines">
    <div class="col-12">
        <div class="card shadow">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-white">Upcoming Deadlines</h6>
            </div>
            <div class="card-body">
                <?php foreach ($upcomingDeadlines as $deadline): ?>
                    <div class="d-flex align-items-center mb-3 p-3 border rounded">
                        <div class="flex-shrink-0">
                            <i class="bi bi-clock text-maroon fa-2x"></i>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="mb-1"><?= esc($deadline['assignment']) ?></h6>
                            <p class="mb-1 text-muted small">Course: <?= esc($deadline['course']) ?></p>
                            <small class="text-muted">Due: <?= date('M d, Y', strtotime($deadline['due_date'])) ?></small>
                        </div>
                        <div class="flex-shrink-0">
                            <span class="badge bg-<?= $deadline['status'] === 'pending' ? 'warning' : 'success' ?>">
                                <?= esc(ucfirst($deadline['status'])) ?>
                            </span>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</div>

<!-- Recent Grades -->
<div class="row mt-4" id="recent-grades">
    <div class="col-12">
        <div class="card shadow">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-white">Recent Grades</h6>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th>Course</th>
                                <th>Assignment</th>
                                <th>Grade</th>
                                <th>Date</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($recentGrades as $grade): ?>
                                <tr>
                                    <td><?= esc($grade['course']) ?></td>
                                    <td><?= esc($grade['assignment']) ?></td>
                                    <td>
                                        <span class="badge bg-<?= $grade['grade'] >= 90 ? 'success' : ($grade['grade'] >= 80 ? 'info' : ($grade['grade'] >= 70 ? 'warning' : 'danger')) ?>">
                                            <?= $grade['grade'] ?>%
                                        </span>
                                    </td>
                                    <td><?= date('M d, Y', strtotime($grade['date'])) ?></td>
                                    <td>
                                        <span class="badge bg-success">Graded</span>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>


<?php endif; ?>
</div>

<style>

.border-left-primary,
.border-left-success,
.border-left-info,
.border-left-warning {
    border-left: 0.25rem solid #800000 !important;
}

.text-xs {
    font-size: 0.7rem;
}

.text-gray-300,
.text-gray-800 {
    color: #800000 !important;
}

.card {
    border: 1px solid #800000;
    box-shadow: 0 2px 4px rgba(128, 0, 0, 0.1);
}
.card-header {
    background-color: #800000;
    color: white;
    border-bottom: 1px solid #800000;
}

.btn-primary {
    background-color: #800000;
    border-color: #800000;
    color: #fff !important;
}
.btn-primary:hover {
    background-color: #660000;
    border-color: #660000;
}

.badge.bg-danger,
.badge.bg-info,
.badge.bg-warning,
.badge.bg-success,
.badge.bg-secondary {
    background-color: #800000 !important;
}

.progress-bar {
    background-color: #800000;
}
</style>
<?= $this->endSection() ?>
