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

<!-- Course Table -->
<div class="row mt-4" id="course">
    <div class="col-12">
        <div class="card shadow">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-white">Course Management</h6>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th>Title</th>
                                <th>Description</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($courses)): ?>
                                <?php foreach ($courses as $course): ?>
                                    <tr>
                                        <td><?= esc($course['title']) ?></td>
                                        <td><?= esc(substr($course['description'], 0, 100)) ?><?= strlen($course['description']) > 100 ? '...' : '' ?></td>
                                        <td>
                                            <a href="#" class="btn btn-sm btn-primary">View</a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="3" class="text-center text-muted">No courses found</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
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

<!-- My Courses -->
<div class="row" id ="courses">
    <div class="col-12">
        <div class="card shadow">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-white">My Courses</h6>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th>Course Name</th>
                                <th>Students</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($teacherCourses as $course): ?>
                                <tr>
                                    <td><?= esc($course['title']) ?></td>
                                    <td><?= $course['students'] ?></td>
                                    <td>
                                        <span class="badge bg-<?= $course['status'] === 'active' ? 'success' : 'secondary' ?>">
                                            <?= esc(ucfirst($course['status'])) ?>
                                        </span>
                                    </td>
                                    <td>
                                        <a href="<?= base_url('admin/course/' . $course['id'] . '/upload') ?>" class="btn btn-sm btn-primary">View</a>
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

<!-- Recent Notifications -->
<div class="row mt-4" id="notifications">
    <div class="col-12">
        <div class="card shadow">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-white">Recent Notifications</h6>
            </div>
            <div class="card-body">
                <?php foreach ($notifications as $notification): ?>
                    <div class="d-flex align-items-center mb-3">
                        <div class="flex-shrink-0">
                            <i class="bi bi-<?= $notification['type'] === 'assignment' ? 'file-earmark-text' : ($notification['type'] === 'help' ? 'question-circle' : 'person-plus') ?> text-white"></i>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <div class="small text-gray-800"><?= esc($notification['message']) ?></div>
                            <div class="small text-muted"><?= esc($notification['time']) ?></div>
                        </div>
                    </div>
                <?php endforeach; ?>
                <div class="text-center mt-3">
                    <a href="#" class="btn btn-sm btn-outline-maroon">View All</a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Student Dashboard-->
<?php elseif ($userRole === 'student'): ?>
<!-- Quick Stats -->
<div class="row mb-4" id="course">
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-primary shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-maroon text-uppercase mb-1">
                            Enrolled Courses
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800" id="enrolled-count"><?= count($enrolledCourses) ?></div>
                    </div>
                    <div class="col-auto">
                        <i class="bi bi-book fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-info shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-maroon text-uppercase mb-1">
                            Available Courses
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800" id="available-count"><?= count($availableCourses) ?></div>
                    </div>
                    <div class="col-auto">
                        <i class="bi bi-book fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-success shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-maroon text-uppercase mb-1">
                            Average Grade
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                            <?= number_format(array_sum(array_column($recentGrades, 'grade')) / count($recentGrades), 1) ?>%
                        </div>
                    </div>
                    <div class="col-auto">
                        <i class="bi bi-trophy fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-warning shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-maroon text-uppercase mb-1">
                            Upcoming Deadlines
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800"><?= count($upcomingDeadlines) ?></div>
                    </div>
                    <div class="col-auto">
                        <i class="bi bi-clock fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Announcements - First thing students see after login -->
<div class="row mb-4" id="announcements">
    <div class="col-12">
        <div class="card shadow">
            <div class="card-header py-3 d-flex justify-content-between align-items-center">
                <h6 class="m-0 font-weight-bold text-white">
                    <i class="bi bi-megaphone me-2"></i>Announcements
                </h6>
            </div>
            <div class="card-body">
                <?php if (isset($announcements)): ?>
                    <?= view('announcements', ['announcements' => $announcements]) ?>
                <?php else: ?>
                    <div class="text-center text-muted">
                        <i class="bi bi-megaphone fa-2x mb-3 text-muted"></i>
                        <p>No announcements available at the moment.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<div class="row" id="courses">
    <!-- Enrolled Courses -->
    <div class="col-lg-6" id="enrolled-courses">
        <div class="card shadow">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-white">Enrolled Courses</h6>
            </div>
            <div class="card-body">
                <?php if (empty($enrolledCourses)): ?>
                    <p class="text-muted">You are not enrolled in any courses yet.</p>
                <?php else: ?>
                    <?php foreach ($enrolledCourses as $course): ?>
                        <div class="d-flex align-items-center mb-3 p-3 border rounded">
                            <div class="flex-shrink-0">
                                <i class="bi bi-book text-maroon fa-2x"></i>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <h6 class="mb-1"><?= esc($course['title']) ?></h6>
                                <p class="mb-1 text-muted small"><?= esc($course['description']) ?></p>
                                <small class="text-muted">Enrolled on: <?= date('M d, Y', strtotime($course['enrollment_date'])) ?></small>
                            </div>
                            <div class="flex-shrink-0">
                                <a href="#" class="btn btn-sm" style="background-color: maroon; color: white; border: 1px solid maroon;">View</a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Available Courses -->
    <div class="col-lg-6" id="available-courses">
        <div class="card shadow">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-white">Available Courses</h6>
            </div>
            <div class="card-body">
                <?php if (empty($availableCourses)): ?>
                    <p class="text-muted">No courses available for enrollment.</p>
                <?php else: ?>
                    <?php foreach ($availableCourses as $course): ?>
                        <div class="d-flex align-items-center mb-3 p-3 border rounded">
                            <div class="flex-shrink-0">
                                <i class="bi bi-book text-maroon fa-2x"></i>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <h6 class="mb-1"><?= esc($course['title']) ?></h6>
                                <p class="mb-1 text-muted small"><?= esc($course['description']) ?></p>
                            </div>
                            <div class="flex-shrink-0">
                               <button class="btn btn-sm enroll-btn" style="background-color: maroon; color: white; border: 1px solid maroon;" data-course-id="<?= $course['id'] ?>">Enroll</button>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Course Materials -->
<div class="row mt-4" id="course-materials">
    <div class="col-12">
        <div class="card shadow">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-white">Course Materials</h6>
            </div>
            <div class="card-body">
                <?php
                $materialModel = new \App\Models\MaterialModel();
                $hasMaterials = false;
                foreach ($enrolledCourses as $course):
                    $materials = $materialModel->getMaterialsByCourse($course['course_id']);
                    if (!empty($materials)):
                        $hasMaterials = true;
                ?>
                    <div class="mb-4">
                        <h6 class="text-maroon mb-3"><?= esc($course['title']) ?> Materials</h6>
                        <div class="row">
                            <?php foreach ($materials as $material): ?>
                                <div class="col-md-6 col-lg-4 mb-3">
                                    <div class="card h-100">
                                        <div class="card-body d-flex flex-column">
                                            <div class="d-flex align-items-center mb-2">
                                                <i class="bi bi-file-earmark-text text-maroon me-2"></i>
                                                <h6 class="card-title mb-0 small"><?= esc($material['file_name']) ?></h6>
                                            </div>
                                            <small class="text-muted mb-3">Uploaded: <?= date('M d, Y', strtotime($material['created_at'])) ?></small>
                                            <a href="<?= base_url('materials/download/' . $material['id']) ?>" class="btn btn-outline-maroon btn-sm mt-auto">
                                                <i class="bi bi-download me-1"></i>Download
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php
                    endif;
                endforeach;

                if (!$hasMaterials):
                ?>
                    <p class="text-muted text-center">No materials available for your enrolled courses yet.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<div class="row">

</div>

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

<script>
$(document).ready(function() {
    $('.enroll-btn').on('click', function(e) {
        e.preventDefault();

        var courseId = $(this).data('course-id');
        var button = $(this);
        var originalText = button.text();

        // Disable button and change text
        button.prop('disabled', true).text('Enrolling...');

        $.ajax({
            url: '<?= base_url('course/enroll') ?>',
            type: 'POST',
            data: {
                course_id: courseId
            },
            headers: {
                'X-CSRF-TOKEN': '<?= csrf_hash() ?>'
            },
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    // Show success message
                    showAlert('success', response.message);

                    // Move course to enrolled list
                    var courseCard = button.closest('.d-flex');
                    var courseTitle = courseCard.find('h6').text();
                    var courseDesc = courseCard.find('p').text();

                    courseCard.remove();
                    
                    // Add to enrolled
                    var enrolledHtml = '<div class="d-flex align-items-center mb-3 p-3 border rounded">' +
                        '<div class="flex-shrink-0"><i class="bi bi-book text-maroon fa-2x"></i></div>' +
                        '<div class="flex-grow-1 ms-3">' +
                        '<h6 class="mb-1">' + courseTitle + '</h6>' +
                        '<p class="mb-1 text-muted small">' + courseDesc + '</p>' +
                        '<small class="text-muted">Enrolled on: ' + new Date().toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' }) + '</small>' +
                        '</div>' +
                        '<div class="flex-shrink-0"><a href="#" class="btn btn-sm btn-outline-maroon">View</a></div>' +
                        '</div>';

                    $('#enrolled-courses .card-body').append(enrolledHtml);

                    // Update counts
                    var enrolledCount = $('#enrolled-courses .card-body .d-flex').length;
                    var availableCount = $('#available-courses .card-body .d-flex').length;
                    $('#enrolled-count').text(enrolledCount);
                    $('#available-count').text(availableCount);

                } else {
                    button.prop('disabled', false).text(originalText);
                }
            },
            error: function(xhr, status, error) {
                console.error('Error:', error);
                console.error('Response:', xhr.responseText);
                button.prop('disabled', false).text(originalText);
            }
        });
    });

    function showAlert(type, message) {
        var alertHtml = '<div class="alert alert-' + type + ' alert-dismissible fade show position-fixed top-0 start-50 translate-middle-x" style="z-index: 1050;" role="alert">' +
            message +
            '<button type="button" class="btn-close" data-bs-dismiss="alert"></button>' +
            '</div>';
        $('body').append(alertHtml);
        setTimeout(function() {
            $('.alert').alert('close');
        }, 5000);
    }
});
</script>

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
