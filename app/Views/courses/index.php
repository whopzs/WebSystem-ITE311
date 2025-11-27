<?= $this->extend('templates/header') ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <h1 class="h3 mb-0 text-maroon">
                    <i class="bi bi-journal-bookmark me-2"></i>
                    <?php if ($userRole === 'admin'): ?>Course Management
                    <?php elseif ($userRole === 'teacher'): ?>My Courses
                    <?php elseif ($userRole === 'student'): ?>Courses
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
<!-- Course Table -->
<div class="row">
    <div class="col-12">
        <div class="card shadow">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-maroon">All Courses</h6>
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
                                            <a href="#" class="btn btn-sm" style="background-color: maroon; color: white; border: 1px solid maroon;">View</a>
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

<!-- Teacher Dashboard-->
<?php elseif ($userRole === 'teacher'): ?>

<!-- My Courses -->
<div class="row">
    <div class="col-12">
        <div class="card shadow">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-maroon">Assign Courses</h6>
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
                                        <span class="badge" style="background-color: #800000; color: white;">
                                            <?= esc(ucfirst($course['status'])) ?>
                                        </span>
                                    </td>
                                    <td>
                                        <a href="<?= base_url('admin/course/' . $course['id'] . '/upload') ?>" class="btn btn-sm" style="background-color: maroon; color: white; border: 1px solid maroon;">View</a>
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

<!-- Student Dashboard-->
<?php elseif ($userRole === 'student'): ?>

    <?php $materialModel = new \App\Models\MaterialModel(); ?>
    
<div class="row mb-4">
    <div class="col-md-6">
        <form id="searchform" class="d-flex">
            <div class="input-group">
                <input type="text" id="searchInput" class="form-control"
                    placeholder="Search courses..." name="search_term">
                <button class="btn btn-outline-maroon" type="submit">
                    <i class="bi bi-search"></i> Search
                </button>
            </div>
        </form>
    </div>
</div>

<div class="row">
    
    <!-- Enrolled Courses -->
    <div class="col-lg-6" id="enrolled-courses">
        <div class="card shadow">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-white">Enrolled Courses</h6>
            </div>
            <div class="card-body" id="coursesContainer">
                <?php if (empty($enrolledCourses)): ?>
                    <p class="text-muted">You are not enrolled in any courses yet.</p>
                <?php else: ?>
                    <?php foreach ($enrolledCourses as $course): ?>
                        <div class="d-flex align-items-center mb-3 p-3 border rounded course-card" data-course-id="<?= esc($course['course_id']) ?>">
                            <div class="flex-shrink-0">
                                <i class="bi bi-book text-maroon fa-2x"></i>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <h6 class="mb-1"> <?= esc($course['title']) ?></h6>
                                <p class="mb-1 text-muted small"> <?= esc($course['description']) ?></p>
                                <small class="text-muted">Enrolled on: <?= date('M d, Y', strtotime($course['enrollment_date'])) ?></small>
                            </div>
                            <div class="flex-shrink-0">
                                <a href="#" class="btn btn-sm" style="background-color: maroon; color: white; border: 1px solid maroon;" onclick="showMaterials(<?= $course['course_id'] ?>); return false;">View</a>
                            </div>
                        </div>
                        <div class="materials-section" id="materials-<?= $course['course_id'] ?>" style="display: none;">
                            <?php
                            $materials = $materialModel->getMaterialsByCourse($course['course_id']);
                            if (!empty($materials)):
                            ?>
                                <div class="mb-4">
                                    <h6 class="text-maroon mb-3"> <?= esc($course['title']) ?> Materials</h6>
                                    <div class="row">
                                        <?php foreach ($materials as $material): ?>
                                            <div class="col-md-6 col-lg-4 mb-3">
                                                <div class="card h-100">
                                                    <div class="card-body d-flex flex-column">
                                                        <div class="d-flex align-items-center mb-2">
                                                            <i class="bi bi-file-earmark-text text-maroon me-2"></i>
                                                            <h6 class="card-title mb-0 small"> <?= esc($material['file_name']) ?></h6>
                                                        </div>
                                                        <small class="text-muted mb-3">Uploaded: <?= date('M d, Y', strtotime($material['created_at'])) ?></small>
                                                        <a href=" <?= base_url('materials/download/' . $material['id']) ?>" class="btn btn-outline-maroon btn-sm mt-auto">
                                                            <i class="bi bi-download me-1"></i>Download
                                                        </a>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                            <?php endif; ?>
                        </div>
                        <div class="no-materials-message" id="no-materials-<?= $course['course_id'] ?>" style="display: none;">
                            <div class="text-center text-muted">
                                <i class="bi bi-file-earmark-x fa-2x mb-3 text-muted"></i>
                                <p>No materials available for this course yet.</p>
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

<script>
function showMaterials(courseId) {
    var materialsDiv = $('#materials-' + courseId);
    var noMaterialsDiv = $('#no-materials-' + courseId);

    if (materialsDiv.children().length > 0) {
        if (materialsDiv.is(':visible')) {
            materialsDiv.hide();
        } else {
            materialsDiv.show();
        }
        noMaterialsDiv.hide();
    } else {
        if (noMaterialsDiv.is(':visible')) {
            noMaterialsDiv.hide();
        } else {
            noMaterialsDiv.show();
        }
        materialsDiv.hide();
    }
}

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
                        '<div class="flex-shrink-0"><a href="#" class="btn btn-sm" style="background-color: maroon; color: white; border: 1px solid maroon;" onclick="showMaterials(' + courseId + '); return false;">View</a></div>' +
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

    // Client-side filtering
    $('#searchInput').on('keyup', function() {
        var value = $(this).val().toLowerCase();
        $('.course-card').filter(function() {
            $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1);
        });
    });

    // Server-side search with AJAX
    $('#searchform').on('submit', function(e) {
        e.preventDefault();
        var searchTerm = $('#searchInput').val();

        $.get('<?= base_url('course/search') ?>', { search_term: searchTerm }, function(data) {
            $('#coursesContainer').empty();

            if (data.length > 0) {
                $.each(data, function(index, course) {
                    var courseHtml = `
                        <div class="d-flex align-items-center mb-3 p-3 border rounded course-card" data-course-id="${course.id}">
                            <div class="flex-shrink-0">
                                <i class="bi bi-book text-maroon fa-2x"></i>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <h6 class="mb-1">${course.title}</h6>
                                <p class="mb-1 text-muted small">${course.description}</p>
                            </div>
                            <div class="flex-shrink-0">
                                <a href="#" class="btn btn-sm" style="background-color: maroon; color: white; border: 1px solid maroon;" onclick="showMaterials(${course.id}); return false;">View</a>
                            </div>
                        </div>
                    `;
                    $('#coursesContainer').append(courseHtml);
                });
            } else {
                $('#coursesContainer').html('<div class="col-12"><div class="alert alert-info">No courses found matching your search.</div></div>');
            }
        });
    });
});
</script>

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

.btn-outline-maroon {
    color: #800000;
    border-color: #800000;
}
.btn-outline-maroon:hover {
    background-color: #800000;
    border-color: #800000;
    color: white;
}
</style>

<?php endif; ?>
</div>

<?= $this->endSection() ?>
