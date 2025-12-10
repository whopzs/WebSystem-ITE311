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
            <div class="card-header py-3" style="background-color: maroon; color: white;">
                <div class="d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-white">All Courses</h6>
                    <div>
                        <button type="button" class="btn btn-sm me-2" style="background-color: white; color: maroon; border: 1px solid white;" data-bs-toggle="modal" data-bs-target="#scheduleModal">
                            + Create Schedule
                        </button>
                        <button type="button" class="btn btn-sm" style="background-color: white; color: maroon; border: 1px solid white;" data-bs-toggle="modal" data-bs-target="#createCourseModal">
                            + Create Course
                        </button>
                    </div>
                </div>
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



<!-- Course Schedule Modal -->
<div class="modal fade" id="scheduleModal" tabindex="-1" aria-labelledby="scheduleModalLabel">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header" style="background-color: maroon; color: white;">
                <h5 class="modal-title" id="scheduleModalLabel">Manage Course Schedule</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="scheduleForm">
                    <div class="mb-3">
                        <label for="scheduleCourse" class="form-label">Select Teacher</label>
                        <select class="form-control" id="scheduleCourse" name="teacher_id" required>
                            <option value="">Select Teacher</option>
                            <?php foreach ($teachers as $teacher): ?>
                                <option value="<?= esc($teacher['id']) ?>"><?= esc($teacher['name']) ?> - (<?= esc($teacher['email']) ?>)</option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="scheduleDay" class="form-label">Day of Week</label>
                            <select class="form-control" id="scheduleDay" name="day" required>
                                <option value="">Select Day</option>
                                <option value="Monday">Monday</option>
                                <option value="Tuesday">Tuesday</option>
                                <option value="Wednesday">Wednesday</option>
                                <option value="Thursday">Thursday</option>
                                <option value="Friday">Friday</option>
                                <option value="Saturday">Saturday</option>
                                <option value="Sunday">Sunday</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="scheduleTime" class="form-label">Time</label>
                            <select class="form-control" id="scheduleTime" name="time" required>
                                <option value="">Select Time</option>
                                <optgroup label="2 Hour Slots">
                                    <option value="7-9 AM">7-9 AM</option>
                                    <option value="12-2 PM">12-2 PM</option>
                                </optgroup>
                                <optgroup label="3 Hour Slots">
                                    <option value="7-10 AM">7-10 AM</option>
                                    <option value="12-3 PM">12-3 PM</option>
                                </optgroup>
                            </select>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="scheduleRoom" class="form-label">Room</label>
                        <input type="text" class="form-control" id="scheduleRoom" name="room" placeholder="e.g., Room 101" required>
                    </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn text-white" id="addScheduleBtn" style="background-color: maroon;">Add to Schedule</button>
            </div>
        </div>
    </div>
</div>

<!-- Create Course Modal -->
<div class="modal fade" id="createCourseModal" tabindex="-1" aria-labelledby="createCourseModalLabel">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header" style="background-color: maroon; color: white;">
                <h5 class="modal-title" id="createCourseModalLabel">Create New Course</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="createCourseForm">
                    <div class="row">
                        <div class="col-md-8 mb-3">
                            <label for="courseTitle" class="form-label">Course Title</label>
                            <input type="text" class="form-control" id="courseTitle" name="title" required>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="courseNumber" class="form-label">Course Number</label>
                            <input type="text" class="form-control" id="courseNumber" name="course_number" readonly>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="courseDescription" class="form-label">Description</label>
                        <textarea class="form-control" id="courseDescription" name="description" rows="3" required></textarea>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="semester" class="form-label">Semester</label>
                            <select class="form-control" id="semester" name="semester" required>
                                <option value="">Select Semester</option>
                                <option value="1st Semester">1st Semester</option>
                                <option value="2nd Semester">2nd Semester</option>
                                <option value="Summer">Summer</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="term" class="form-label">Term</label>
                            <select class="form-control" id="term" name="term" required>
                                <option value="">Select Term</option>
                                <option value="Prelim">Prelim</option>
                                <option value="Midterm">Midterm</option>
                                <option value="Final">Final</option>
                            </select>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="academicYear" class="form-label">Academic Year</label>
                            <select class="form-control" id="academicYear" name="academic_year" required>
                                <option value="">Select Academic Year</option>
                                <option value="2025-2026">2025-2026</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="assignedTeacher" class="form-label">Assign Teacher</label>
                            <select class="form-control" id="assignedTeacher" name="assigned_teacher" required>
                                <option value="">Select Teacher</option>
                                <?php foreach ($teachers as $teacher): ?>
                                    <option value="<?= esc($teacher['id']) ?>"><?= esc($teacher['name']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn text-white" id="createCourseBtn" style="background-color: maroon;">Create Course</button>
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
    var originalEnrolledHtml = $('#coursesContainer').html();
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

                    // Add materials section
                    if (response.materials && response.materials.length > 0) {
                        var materialsHtml = `
                            <div class="materials-section mb-4" id="materials-${courseId}" style="display: none;">
                                <div class="mb-4">
                                    <h6 class="text-maroon mb-3">${courseTitle} Materials</h6>
                                    <div class="row">
                        `;
                        $.each(response.materials, function(index2, material) {
                            materialsHtml += `
                                        <div class="col-md-6 col-lg-4 mb-3">
                                            <div class="card h-100">
                                                <div class="card-body d-flex flex-column">
                                                    <div class="d-flex align-items-center mb-2">
                                                        <i class="bi bi-file-earmark-text text-maroon me-2"></i>
                                                        <h6 class="card-title mb-0 small">${material.file_name}</h6>
                                                    </div>
                                                    <small class="text-muted mb-3">Uploaded: ${new Date(material.created_at).toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' })}</small>
                                                    <a href="<?= base_url('materials/download/') ?>${material.id}" class="btn btn-outline-maroon btn-sm mt-auto">
                                                        <i class="bi bi-download me-1"></i>Download
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                            `;
                        });
                        materialsHtml += `
                                    </div>
                                </div>
                            </div>
                        `;
                        $('#enrolled-courses .card-body').append(materialsHtml);
                    } else {
                        var noMaterialsHtml = `
                            <div class="no-materials-message" id="no-materials-${courseId}" style="display: none;">
                                <div class="text-center text-muted">
                                    <i class="bi bi-file-earmark-x fa-2x mb-3 text-muted"></i>
                                    <p>No materials available for this course yet.</p>
                                </div>
                            </div>
                        `;
                        $('#enrolled-courses .card-body').append(noMaterialsHtml);
                    }

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
        var searchTerm = $('#searchInput').val().trim();

        if (searchTerm === '') {
            $('#coursesContainer').html(originalEnrolledHtml);
            return;
        }

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
                                <small class="text-muted">Enrolled on: ${new Date(course.enrollment_date).toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' })}</small>
                            </div>
                            <div class="flex-shrink-0">
                                <a href="#" class="btn btn-sm" style="background-color: maroon; color: white; border: 1px solid maroon;" onclick="showMaterials(${course.id}); return false;">View</a>
                            </div>
                        </div>
                    `;
                    $('#coursesContainer').append(courseHtml);

                    // Add materials section
                    if (course.materials && course.materials.length > 0) {
                        var materialsHtml = `
                            <div class="materials-section mb-4" id="materials-${course.id}" style="display: none;">
                                <div class="mb-4">
                                    <h6 class="text-maroon mb-3">${course.title} Materials</h6>
                                    <div class="row">
                        `;
                        $.each(course.materials, function(index2, material) {
                            materialsHtml += `
                                        <div class="col-md-6 col-lg-4 mb-3">
                                            <div class="card h-100">
                                                <div class="card-body d-flex flex-column">
                                                    <div class="d-flex align-items-center mb-2">
                                                        <i class="bi bi-file-earmark-text text-maroon me-2"></i>
                                                        <h6 class="card-title mb-0 small">${material.file_name}</h6>
                                                    </div>
                                                    <small class="text-muted mb-3">Uploaded: ${new Date(material.created_at).toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' })}</small>
                                                    <a href="<?= base_url('materials/download/') ?>${material.id}" class="btn btn-outline-maroon btn-sm mt-auto">
                                                        <i class="bi bi-download me-1"></i>Download
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                            `;
                        });
                        materialsHtml += `
                                    </div>
                                </div>
                            </div>
                        `;
                        $('#coursesContainer').append(materialsHtml);
                    } else {
                        var noMaterialsHtml = `
                            <div class="no-materials-message" id="no-materials-${course.id}" style="display: none;">
                                <div class="text-center text-muted">
                                    <i class="bi bi-file-earmark-x fa-2x mb-3 text-muted"></i>
                                    <p>No materials available for this course yet.</p>
                                </div>
                            </div>
                        `;
                        $('#coursesContainer').append(noMaterialsHtml);
                    }
                });
            } else {
                $('#coursesContainer').html('<div class="col-12"><div class="alert alert-info">No courses found matching your search.</div></div>');
            }
        });
    });

    // Create Course Modal
    $('#courseTitle').on('input', function() {
        var title = $(this).val().toUpperCase().replace(/[^A-Z\s]/g, '').trim();
        var words = title.split(' ').filter(word => word.length > 0);
        var acronym = '';
        if (words.length >= 3) {
            acronym = words.slice(0, 3).map(word => word.charAt(0)).join('') + Math.floor(Math.random() * 10);
        } else if (words.length === 2) {
            acronym = words[0].charAt(0) + words[1].charAt(0) + Math.floor(Math.random() * 100);
        } else if (words.length === 1 && words[0].length >= 2) {
            acronym = words[0].substring(0, 3).toUpperCase();
        } else {
            acronym = 'CN' + Math.floor(Math.random() * 1000);
        }
        $('#courseNumber').val(acronym);
    });

    $('#createCourseBtn').on('click', function(e) {
        e.preventDefault();
        var formData = new FormData(document.getElementById('createCourseForm'));
        $.ajax({
            url: '<?= base_url('course/create') ?>',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            headers: {
                'X-CSRF-TOKEN': '<?= csrf_hash() ?>'
            },
            success: function(response) {
                if (response.success) {
                    showAlert('success', response.message || 'Course created successfully');
                    $('#createCourseModal').modal('hide');
                    $('#createCourseForm')[0].reset();
                    location.reload();
                } else {
                    showAlert('danger', response.message || 'Error creating course');
                }
            },
            error: function(xhr) {
                showAlert('danger', 'Error creating course');
                console.error(xhr.responseText);
            }
        });
    });

    // Add Schedule
    $('#addScheduleBtn').on('click', function(e) {
        e.preventDefault();
        var formData = new FormData(document.getElementById('scheduleForm'));
        $.ajax({
            url: '<?= base_url('course/saveSchedule') ?>',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            headers: {
                'X-CSRF-TOKEN': '<?= csrf_hash() ?>'
            },
            success: function(response) {
                if (response.success) {
                    showAlert('success', response.message || 'Schedule added successfully');
                    $('#scheduleModal').modal('hide');
                    $('#scheduleForm')[0].reset();
                    // Optionally refresh the page or update the UI
                } else {
                    showAlert('danger', response.message || 'Error adding schedule');
                }
            },
            error: function(xhr) {
                showAlert('danger', 'Error adding schedule');
                console.error(xhr.responseText);
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
