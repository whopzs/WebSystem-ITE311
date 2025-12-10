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
                        <button type="button" class="btn btn-sm me-2 create-schedule-btn" style="background-color: white; color: maroon; border: 1px solid white;" data-bs-toggle="modal" data-bs-target="#scheduleModal" data-action="create">
                            + Create Schedule
                        </button>
                        <button type="button" class="btn btn-sm me-2" style="background-color: white; color: maroon; border: 1px solid white;" data-bs-toggle="modal" data-bs-target="#assignTeacherModal">
                            + Assign Teacher
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
                                <th>Teacher</th>
                                <th>Schedule</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($courses)): ?>
                                <?php 
                                $userModel = new \App\Models\UserModel();
                                foreach ($courses as $course): 
                                    $teacher = null;
                                    if (!empty($course['instructor_id'])) {
                                        $teacher = $userModel->find($course['instructor_id']);
                                    }
                                ?>
                                    <tr>
                                        <td><?= esc($course['title']) ?></td>
                                        <td><?= esc(substr($course['description'], 0, 100)) ?><?= strlen($course['description']) > 100 ? '...' : '' ?></td>
                                        <td>
                                            <?php if ($teacher): ?>
                                                <?= esc($teacher['name']) ?>
                                            <?php else: ?>
                                                <span class="text-muted">Not assigned</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php if ($course['day'] && $course['time']): ?>
                                                <?= esc($course['day']) ?> - <?= esc($course['time']) ?>
                                                <?php if ($course['room']): ?>
                                                    (<?= esc($course['room']) ?>)
                                                <?php endif; ?>
                                            <?php else: ?>
                                                <span class="text-muted">Not scheduled</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php if ($course['day'] && $course['time']): ?>
                                                <button type="button" class="btn btn-sm edit-schedule-btn" style="background-color: maroon; color: white; border: 1px solid maroon;" data-bs-toggle="modal" data-bs-target="#scheduleModal" 
                                                    data-course-id="<?= $course['id'] ?>" 
                                                    data-course-title="<?= esc($course['title']) ?>" 
                                                    data-course-day="<?= esc($course['day']) ?>"
                                                    data-course-time="<?= esc($course['time']) ?>"
                                                    data-course-room="<?= esc($course['room'] ?? '') ?>">
                                                    Edit
                                                </button>
                                            <?php else: ?>
                                                <span class="text-muted">-</span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="5" class="text-center text-muted">No courses found</td>
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
                    <input type="hidden" id="scheduleCourseId" name="course_id">
                    <div class="mb-3">
                        <label for="scheduleCourseSelect" class="form-label">Select Course</label>
                        <select class="form-control" id="scheduleCourseSelect" required>
                            <option value="">Select Course</option>
                            <?php if (!empty($courses)): ?>
                                <?php foreach ($courses as $course): ?>
                                    <?php if (isset($course['id']) && isset($course['title'])): ?>
                                        <option value="<?= esc($course['id']) ?>"><?= esc($course['title']) ?></option>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="scheduleDay" class="form-label">Day of Week</label>
                            <select class="form-control" id="scheduleDay" required>
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
                            <select class="form-control" id="scheduleTime" required>
                                <option value="">Select Time</option>
                                <optgroup label="2 Hour Slots">
                                    <option value="7-9 AM">7-9 AM</option>
                                    <option value="9-11 AM">9-11 AM</option>
                                    <option value="11 AM-1 PM">11 AM-1 PM</option>
                                    <option value="12-2 PM">12-2 PM</option>
                                    <option value="2-4 PM">2-4 PM</option>
                                    <option value="4-6 PM">4-6 PM</option>
                                </optgroup>
                                <optgroup label="3 Hour Slots">
                                    <option value="7-10 AM">7-10 AM</option>
                                    <option value="10 AM-1 PM">10 AM-1 PM</option>
                                    <option value="12-3 PM">12-3 PM</option>
                                    <option value="3-6 PM">3-6 PM</option>
                                </optgroup>
                            </select>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="scheduleRoom" class="form-label">Room</label>
                        <input type="text" class="form-control" id="scheduleRoom" placeholder="e.g., Room 101" required>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn text-white" id="saveScheduleBtn" onclick="saveScheduleNow(event)" style="background-color: maroon;">Save Schedule</button>
            </div>
            <script>
            function saveScheduleNow(e) {
                e.preventDefault();
                e.stopPropagation();
                
                var courseId = document.getElementById('scheduleCourseId').value || document.getElementById('scheduleCourseSelect').value;
                var day = document.getElementById('scheduleDay').value;
                var time = document.getElementById('scheduleTime').value;
                var room = document.getElementById('scheduleRoom').value;
                
                if (!courseId) { alert('Please select a course'); return false; }
                if (!day) { alert('Please select a day'); return false; }
                if (!time) { alert('Please select a time'); return false; }
                if (!room || room.trim() === '') { alert('Please enter a room'); return false; }
                
                var csrfToken = document.querySelector('meta[name="X-CSRF-TOKEN"]')?.getAttribute('content') || '';
                var btn = document.getElementById('saveScheduleBtn');
                var originalText = btn.innerHTML;
                btn.disabled = true;
                btn.innerHTML = 'Saving...';
                
                var xhr = new XMLHttpRequest();
                xhr.open('POST', '<?= base_url('course/saveSchedule') ?>', true);
                xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
                xhr.setRequestHeader('X-CSRF-TOKEN', csrfToken);
                xhr.onreadystatechange = function() {
                    if (xhr.readyState === 4) {
                        btn.disabled = false;
                        btn.innerHTML = originalText;
                        if (xhr.status === 200) {
                            try {
                                var response = JSON.parse(xhr.responseText);
                                if (response.success) {
                                    alert('Schedule saved successfully!');
                                    var modalEl = document.getElementById('scheduleModal');
                                    var modal = bootstrap.Modal.getInstance(modalEl);
                                    if (modal) modal.hide();
                                    location.reload();
                                } else {
                                    alert('Error: ' + (response.message || 'Failed to save'));
                                }
                            } catch(e) {
                                alert('Error parsing response');
                            }
                        } else {
                            alert('Error: Server returned status ' + xhr.status);
                        }
                    }
                };
                xhr.send('course_id=' + encodeURIComponent(courseId) + 
                         '&day=' + encodeURIComponent(day) + 
                         '&time=' + encodeURIComponent(time) + 
                         '&room=' + encodeURIComponent(room));
                return false;
            }
            </script>
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
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn text-white" id="createCourseBtn" onclick="createCourseNow(event)" style="background-color: maroon;">Create Course</button>
            </div>
            <script>
            function createCourseNow(e) {
                e.preventDefault();
                e.stopPropagation();
                
                var title = document.getElementById('courseTitle').value;
                var courseNumber = document.getElementById('courseNumber').value;
                var description = document.getElementById('courseDescription').value;
                var semester = document.getElementById('semester').value;
                var term = document.getElementById('term').value;
                var academicYear = document.getElementById('academicYear').value;
                
                if (!title || title.trim() === '') { alert('Please enter a course title'); return false; }
                if (!description || description.trim() === '') { alert('Please enter a description'); return false; }
                if (!semester) { alert('Please select a semester'); return false; }
                if (!term) { alert('Please select a term'); return false; }
                if (!academicYear) { alert('Please select an academic year'); return false; }
                
                var csrfToken = document.querySelector('meta[name="X-CSRF-TOKEN"]')?.getAttribute('content') || '';
                var btn = document.getElementById('createCourseBtn');
                var originalText = btn.innerHTML;
                btn.disabled = true;
                btn.innerHTML = 'Creating...';
                
                var xhr = new XMLHttpRequest();
                xhr.open('POST', '<?= base_url('course/create') ?>', true);
                xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
                xhr.setRequestHeader('X-CSRF-TOKEN', csrfToken);
                xhr.onreadystatechange = function() {
                    if (xhr.readyState === 4) {
                        btn.disabled = false;
                        btn.innerHTML = originalText;
                        if (xhr.status === 200) {
                            try {
                                var response = JSON.parse(xhr.responseText);
                                if (response.success) {
                                    alert('Course created successfully!');
                                    var modalEl = document.getElementById('createCourseModal');
                                    var modal = bootstrap.Modal.getInstance(modalEl);
                                    if (modal) modal.hide();
                                    location.reload();
                                } else {
                                    alert('Error: ' + (response.message || 'Failed to create course'));
                                }
                            } catch(e) {
                                alert('Error parsing response');
                            }
                        } else {
                            try {
                                var errorResponse = JSON.parse(xhr.responseText);
                                alert('Error: ' + (errorResponse.message || 'Server error'));
                            } catch(e) {
                                alert('Error: Server returned status ' + xhr.status);
                            }
                        }
                    }
                };
                xhr.send('title=' + encodeURIComponent(title) + 
                         '&course_number=' + encodeURIComponent(courseNumber) + 
                         '&description=' + encodeURIComponent(description) + 
                         '&semester=' + encodeURIComponent(semester) + 
                         '&term=' + encodeURIComponent(term) + 
                         '&academic_year=' + encodeURIComponent(academicYear));
                return false;
            }
            </script>
        </div>
    </div>
</div>

<!-- Assign Teacher Modal -->
<div class="modal fade" id="assignTeacherModal" tabindex="-1" aria-labelledby="assignTeacherModalLabel">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header" style="background-color: maroon; color: white;">
                <h5 class="modal-title" id="assignTeacherModalLabel">Assign Teacher to Course</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="assignTeacherAlert" class="alert" style="display: none;"></div>
                <form id="assignTeacherForm">
                    <div class="mb-3">
                        <label for="assignCourseSelect" class="form-label">Select Course</label>
                        <select class="form-control" id="assignCourseSelect" name="course_id" required>
                            <option value="">Select Course</option>
                            <?php foreach ($courses as $course): ?>
                                <option value="<?= esc($course['id']) ?>"><?= esc($course['title']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="assignTeacherSelect" class="form-label">Select Teacher</label>
                        <select class="form-control" id="assignTeacherSelect" name="teacher_id" required>
                            <option value="">Select Teacher</option>
                            <?php if (!empty($teachers)): ?>
                                <?php foreach ($teachers as $teacher): ?>
                                    <?php if (isset($teacher['id']) && isset($teacher['name']) && isset($teacher['email'])): ?>
                                        <option value="<?= esc($teacher['id']) ?>"><?= esc($teacher['name']) ?> - (<?= esc($teacher['email']) ?>)</option>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="assignDay" class="form-label">Day of Week</label>
                            <select class="form-control" id="assignDay" name="day" required>
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
                            <label for="assignTime" class="form-label">Time</label>
                            <select class="form-control" id="assignTime" name="time" required>
                                <option value="">Select Time</option>
                                <optgroup label="2 Hour Slots">
                                    <option value="7-9 AM">7-9 AM</option>
                                    <option value="9-11 AM">9-11 AM</option>
                                    <option value="11 AM-1 PM">11 AM-1 PM</option>
                                    <option value="12-2 PM">12-2 PM</option>
                                    <option value="2-4 PM">2-4 PM</option>
                                    <option value="4-6 PM">4-6 PM</option>
                                </optgroup>
                                <optgroup label="3 Hour Slots">
                                    <option value="7-10 AM">7-10 AM</option>
                                    <option value="10 AM-1 PM">10 AM-1 PM</option>
                                    <option value="12-3 PM">12-3 PM</option>
                                    <option value="3-6 PM">3-6 PM</option>
                                </optgroup>
                            </select>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="assignRoom" class="form-label">Room</label>
                        <input type="text" class="form-control" id="assignRoom" name="room" placeholder="e.g., Room 101" required>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn text-white" id="assignTeacherBtn" onclick="assignTeacherNow(event)" style="background-color: maroon;">Assign Teacher</button>
            </div>
            <script>
            function assignTeacherNow(e) {
                e.preventDefault();
                e.stopPropagation();
                
                var courseId = document.getElementById('assignCourseSelect').value;
                var teacherId = document.getElementById('assignTeacherSelect').value;
                var day = document.getElementById('assignDay').value;
                var time = document.getElementById('assignTime').value;
                var room = document.getElementById('assignRoom').value;
                
                if (!courseId) { alert('Please select a course'); return false; }
                if (!teacherId) { alert('Please select a teacher'); return false; }
                if (!day) { alert('Please select a day'); return false; }
                if (!time) { alert('Please select a time'); return false; }
                if (!room || room.trim() === '') { alert('Please enter a room'); return false; }
                
                var csrfToken = document.querySelector('meta[name="X-CSRF-TOKEN"]')?.getAttribute('content') || '';
                var btn = document.getElementById('assignTeacherBtn');
                var originalText = btn.innerHTML;
                btn.disabled = true;
                btn.innerHTML = 'Assigning...';
                
                var xhr = new XMLHttpRequest();
                xhr.open('POST', '<?= base_url('course/assignTeacher') ?>', true);
                xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
                xhr.setRequestHeader('X-CSRF-TOKEN', csrfToken);
                xhr.onreadystatechange = function() {
                    if (xhr.readyState === 4) {
                        btn.disabled = false;
                        btn.innerHTML = originalText;
                        if (xhr.status === 200) {
                            try {
                                var response = JSON.parse(xhr.responseText);
                                if (response.success) {
                                    alert('Teacher assigned successfully!');
                                    var modalEl = document.getElementById('assignTeacherModal');
                                    var modal = bootstrap.Modal.getInstance(modalEl);
                                    if (modal) modal.hide();
                                    location.reload();
                                } else {
                                    alert('Error: ' + (response.message || 'Failed to assign teacher'));
                                }
                            } catch(e) {
                                alert('Error parsing response');
                            }
                        } else {
                            try {
                                var errorResponse = JSON.parse(xhr.responseText);
                                alert('Error: ' + (errorResponse.message || 'Server error'));
                            } catch(e) {
                                alert('Error: Server returned status ' + xhr.status);
                            }
                        }
                    }
                };
                xhr.send('course_id=' + encodeURIComponent(courseId) + 
                         '&teacher_id=' + encodeURIComponent(teacherId) + 
                         '&day=' + encodeURIComponent(day) + 
                         '&time=' + encodeURIComponent(time) + 
                         '&room=' + encodeURIComponent(room));
                return false;
            }
            </script>
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
                <div class="d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-maroon">Assign Courses</h6>
                    <button type="button" class="btn btn-sm" style="background-color: maroon; color: white; border: 1px solid maroon;" data-bs-toggle="modal" data-bs-target="#addMaterialsModal">
                        <i class="bi bi-plus-circle me-1"></i>Add Materials
                    </button>
                </div>
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
                                <td>
                                    <?= $course['students'] ?>
                                    <?php if (!empty($course['pending_count']) && $course['pending_count'] > 0): ?>
                                        <span class="badge ms-2" style="background-color: maroon; color: white;" title="Pending enrollments">
                                            <?= $course['pending_count'] ?> pending
                                        </span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <span class="badge" style="background-color: #800000; color: white;">
                                        <?= esc(ucfirst($course['status'])) ?>
                                    </span>
                                </td>
                                <td>
                                    <button type="button" class="btn btn-sm view-course-btn" style="background-color: maroon; color: white; border: 1px solid maroon;" 
                                        data-bs-toggle="modal" data-bs-target="#viewCourseModal"
                                        data-course-id="<?= esc($course['id']) ?>"
                                        data-course-title="<?= esc($course['title']) ?>"
                                        data-course-description="<?= esc($course['description'] ?? '') ?>"
                                        data-course-day="<?= esc($course['day'] ?? '') ?>"
                                        data-course-time="<?= esc($course['time'] ?? '') ?>"
                                        data-course-room="<?= esc($course['room'] ?? '') ?>"
                                        data-course-students="<?= esc($course['students']) ?>"
                                        data-course-status="<?= esc($course['status']) ?>">
                                        View
                                    </button>
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

<!-- Add Materials Modal -->
<div class="modal fade" id="addMaterialsModal" tabindex="-1" aria-labelledby="addMaterialsModalLabel">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header" style="background-color: maroon; color: white;">
                <h5 class="modal-title" id="addMaterialsModalLabel">Add Materials</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="addMaterialsForm">
                    <div class="mb-3">
                        <label for="materialsCourseSelect" class="form-label">Select Course</label>
                        <select class="form-control" id="materialsCourseSelect" required>
                            <option value="">Select Course</option>
                            <?php if (!empty($teacherCourses)): ?>
                                <?php foreach ($teacherCourses as $course): ?>
                                    <option value="<?= esc($course['id']) ?>"><?= esc($course['title']) ?></option>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn text-white" id="goToMaterialsBtn" style="background-color: maroon;">Continue</button>
            </div>
            <script>
            document.getElementById('goToMaterialsBtn').addEventListener('click', function() {
                var courseId = document.getElementById('materialsCourseSelect').value;
                if (!courseId) {
                    alert('Please select a course');
                    return;
                }
                window.location.href = '<?= base_url('admin/course/') ?>' + courseId + '/upload';
            });
            </script>
        </div>
    </div>
</div>

<!-- View Course Modal -->
<div class="modal fade" id="viewCourseModal" tabindex="-1" aria-labelledby="viewCourseModalLabel">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header" style="background-color: maroon; color: white;">
                <h5 class="modal-title" id="viewCourseModalLabel">Course Details</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row mb-3">
                    <div class="col-md-12">
                        <h5 class="text-maroon" id="viewCourseTitle"></h5>
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-12">
                        <label class="form-label fw-bold">Description:</label>
                        <p id="viewCourseDescription" class="text-muted"></p>
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Schedule:</label>
                        <p id="viewCourseSchedule" class="text-muted">-</p>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Room:</label>
                        <p id="viewCourseRoom" class="text-muted">-</p>
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Number of Students:</label>
                        <p id="viewCourseStudents" class="text-muted">-</p>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Status:</label>
                        <p id="viewCourseStatus"><span class="badge" style="background-color: #800000; color: white;">-</span></p>
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-12">
                        <label class="form-label fw-bold">Pending Enrollments:</label>
                        <div id="pendingEnrollmentsList" class="mt-2">
                            <p class="text-muted">Loading...</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<script>
// View Course Modal Handler
document.addEventListener('DOMContentLoaded', function() {
    var viewCourseModal = document.getElementById('viewCourseModal');
    if (viewCourseModal) {
        viewCourseModal.addEventListener('show.bs.modal', function (event) {
            var button = event.relatedTarget;
            var courseId = button.getAttribute('data-course-id');
            var courseTitle = button.getAttribute('data-course-title');
            var courseDescription = button.getAttribute('data-course-description');
            var courseDay = button.getAttribute('data-course-day');
            var courseTime = button.getAttribute('data-course-time');
            var courseRoom = button.getAttribute('data-course-room');
            var courseStudents = button.getAttribute('data-course-students');
            var courseStatus = button.getAttribute('data-course-status');
            
            // Update modal title
            document.getElementById('viewCourseTitle').textContent = courseTitle || '-';
            
            // Update description
            document.getElementById('viewCourseDescription').textContent = courseDescription || 'No description available';
            
            // Update schedule
            var scheduleText = '-';
            if (courseDay && courseTime) {
                scheduleText = courseDay + ' - ' + courseTime;
            }
            document.getElementById('viewCourseSchedule').textContent = scheduleText;
            
            // Update room
            document.getElementById('viewCourseRoom').textContent = courseRoom || '-';
            
            // Update students
            document.getElementById('viewCourseStudents').textContent = courseStudents || '0';
            
            // Update status
            var statusBadge = document.getElementById('viewCourseStatus').querySelector('.badge');
            if (statusBadge) {
                statusBadge.textContent = courseStatus ? courseStatus.charAt(0).toUpperCase() + courseStatus.slice(1) : '-';
            }
            
            // Load pending enrollments
            if (courseId) {
                loadPendingEnrollments(courseId);
            }
        });
    }
});

function loadPendingEnrollments(courseId) {
    var pendingList = document.getElementById('pendingEnrollmentsList');
    pendingList.innerHTML = '<p class="text-muted">Loading...</p>';
    
    $.ajax({
        url: '<?= base_url('course/getPendingEnrollments') ?>',
        type: 'GET',
        data: { course_id: courseId },
        dataType: 'json',
        success: function(response) {
            if (response.success && response.enrollments && response.enrollments.length > 0) {
                var html = '<div class="list-group">';
                response.enrollments.forEach(function(enrollment) {
                    html += '<div class="list-group-item d-flex justify-content-between align-items-center">';
                    html += '<div>';
                    html += '<strong>' + enrollment.name + '</strong><br>';
                    html += '<small class="text-muted">' + enrollment.email + '</small><br>';
                    html += '<small class="text-muted">Requested: ' + new Date(enrollment.enrollment_date).toLocaleDateString() + '</small>';
                    html += '</div>';
                    html += '<div>';
                    html += '<button class="btn btn-sm btn-success me-2 approve-btn" data-enrollment-id="' + enrollment.id + '">Approve</button>';
                    html += '<button class="btn btn-sm btn-danger reject-btn" data-enrollment-id="' + enrollment.id + '">Reject</button>';
                    html += '</div>';
                    html += '</div>';
                });
                html += '</div>';
                pendingList.innerHTML = html;
                
                // Attach event handlers
                $('.approve-btn').on('click', function() {
                    var enrollmentId = $(this).data('enrollment-id');
                    approveEnrollment(enrollmentId, courseId);
                });
                
                $('.reject-btn').on('click', function() {
                    var enrollmentId = $(this).data('enrollment-id');
                    rejectEnrollment(enrollmentId, courseId);
                });
            } else {
                pendingList.innerHTML = '<p class="text-muted">No pending enrollments</p>';
            }
        },
        error: function() {
            pendingList.innerHTML = '<p class="text-danger">Error loading pending enrollments</p>';
        }
    });
}

function approveEnrollment(enrollmentId, courseId) {
    $.ajax({
        url: '<?= base_url('course/approveEnrollment') ?>',
        type: 'POST',
        data: { enrollment_id: enrollmentId },
        headers: {
            'X-CSRF-TOKEN': '<?= csrf_hash() ?>'
        },
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                showAlert('success', response.message);
                loadPendingEnrollments(courseId);
                // Reload the page to update student counts
                setTimeout(function() {
                    location.reload();
                }, 1000);
            } else {
                showAlert('danger', response.message || 'Failed to approve enrollment');
            }
        },
        error: function() {
            showAlert('danger', 'Error approving enrollment');
        }
    });
}

function rejectEnrollment(enrollmentId, courseId) {
    if (!confirm('Are you sure you want to reject this enrollment request?')) {
        return;
    }
    
    $.ajax({
        url: '<?= base_url('course/rejectEnrollment') ?>',
        type: 'POST',
        data: { enrollment_id: enrollmentId },
        headers: {
            'X-CSRF-TOKEN': '<?= csrf_hash() ?>'
        },
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                showAlert('success', response.message);
                loadPendingEnrollments(courseId);
            } else {
                showAlert('danger', response.message || 'Failed to reject enrollment');
            }
        },
        error: function() {
            showAlert('danger', 'Error rejecting enrollment');
        }
    });
}

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
</script>

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
    
    <!-- Pending Enrollments -->
    <?php if (!empty($pendingEnrollments)): ?>
    <div class="col-12 mb-4">
        <div class="card shadow">
            <div class="card-header py-3" style="background-color: maroon;">
                <h6 class="m-0 font-weight-bold text-white">Pending Enrollment Requests</h6>
            </div>
            <div class="card-body">
                <?php foreach ($pendingEnrollments as $course): ?>
                    <div class="d-flex align-items-center mb-3 p-3 border rounded" style="border-color: maroon !important;">
                        <div class="flex-shrink-0">
                            <i class="bi bi-clock-history fa-2x" style="color: maroon;"></i>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="mb-1"><?= esc($course['title']) ?></h6>
                            <p class="mb-1 text-muted small"><?= esc($course['description']) ?></p>
                            <small style="color: maroon;"><i class="bi bi-hourglass-split"></i> Waiting for teacher approval</small><br>
                            <small class="text-muted">Requested on: <?= date('M d, Y', strtotime($course['enrollment_date'])) ?></small>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
    <?php endif; ?>
    
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

                    // Move course card
                    var courseCard = button.closest('.d-flex');
                    var courseTitle = courseCard.find('h6').text();
                    var courseDesc = courseCard.find('p').text();
                    courseCard.remove();

                    // Check if enrollment is pending or approved
                    if (response.status === 'pending') {
                        // Add to pending enrollments section
                        var pendingSection = $('.col-12.mb-4').first();
                        if (pendingSection.length === 0 || !pendingSection.find('.card-header').text().includes('Pending')) {
                            // Create pending section if it doesn't exist
                            var pendingHtml = `
                                <div class="col-12 mb-4">
                                    <div class="card shadow">
                                        <div class="card-header py-3" style="background-color: maroon;">
                                            <h6 class="m-0 font-weight-bold text-white">Pending Enrollment Requests</h6>
                                        </div>
                                        <div class="card-body" id="pendingEnrollmentsContainer">
                                        </div>
                                    </div>
                                </div>
                            `;
                            $('#enrolled-courses').before(pendingHtml);
                        }
                        
                        var pendingHtml = '<div class="d-flex align-items-center mb-3 p-3 border rounded" style="border-color: maroon !important;">' +
                            '<div class="flex-shrink-0"><i class="bi bi-clock-history fa-2x" style="color: maroon;"></i></div>' +
                            '<div class="flex-grow-1 ms-3">' +
                            '<h6 class="mb-1">' + courseTitle + '</h6>' +
                            '<p class="mb-1 text-muted small">' + courseDesc + '</p>' +
                            '<small style="color: maroon;"><i class="bi bi-hourglass-split"></i> Waiting for teacher approval</small><br>' +
                            '<small class="text-muted">Requested on: ' + new Date().toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' }) + '</small>' +
                            '</div></div>';
                        
                        $('#pendingEnrollmentsContainer').append(pendingHtml);
                    } else {
                        // Add to enrolled courses (approved)
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

                        // Add materials section if available
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

    // ========== SCHEDULE SAVE LOGIC ==========
    
    // When modal opens, populate fields if editing
    $('#scheduleModal').on('shown.bs.modal', function (event) {
        var button = event.relatedTarget;
        if (!button) {
            // Creating - clear fields
            resetScheduleModal();
            return;
        }
        
        var action = button.getAttribute('data-action');
        var courseId = button.getAttribute('data-course-id');
        
        // Check if this is a create action (from Create Schedule button)
        if (action === 'create' || !courseId) {
            // Creating - clear fields
            resetScheduleModal();
            return;
        }
        
        // Editing - read data attributes directly from HTML
        var courseDay = button.getAttribute('data-course-day') || '';
        var courseTime = button.getAttribute('data-course-time') || '';
        var courseRoom = button.getAttribute('data-course-room') || '';
        
        console.log('Editing schedule - Modal opened with data:', {courseId, courseDay, courseTime, courseRoom});
        
        // Set hidden course ID
        var courseIdEl = document.getElementById('scheduleCourseId');
        if (courseIdEl) courseIdEl.value = courseId;
        
        // Set course select and disable it
        var courseSelect = document.getElementById('scheduleCourseSelect');
        if (courseSelect) {
            courseSelect.value = courseId;
            courseSelect.disabled = true;
        }
        
        // Set day
        var daySelect = document.getElementById('scheduleDay');
        if (daySelect && courseDay) {
            daySelect.value = courseDay;
            console.log('Set day to:', courseDay, 'Current value:', daySelect.value);
        }
        
        // Set time
        var timeSelect = document.getElementById('scheduleTime');
        if (timeSelect && courseTime) {
            timeSelect.value = courseTime;
            console.log('Set time to:', courseTime, 'Current value:', timeSelect.value);
        }
        
        // Set room
        var roomInput = document.getElementById('scheduleRoom');
        if (roomInput) {
            roomInput.value = courseRoom || '';
            console.log('Set room to:', courseRoom);
        }
        
        // Update modal title
        var modalLabel = document.getElementById('scheduleModalLabel');
        if (modalLabel) modalLabel.textContent = 'Edit Course Schedule';
    });
    
    // Helper function to reset modal for creating new schedule
    function resetScheduleModal() {
        $('#scheduleCourseId').val('');
        $('#scheduleCourseSelect').val('').prop('disabled', false);
        $('#scheduleDay').val('');
        $('#scheduleTime').val('');
        $('#scheduleRoom').val('');
        $('#scheduleModalLabel').text('Create Course Schedule');
    }

    // Reset modal when closed (only if not editing)
    $('#scheduleModal').on('hidden.bs.modal', function () {
        // Clear all fields
        $('#scheduleCourseId').val('');
        $('#scheduleCourseSelect').val('').prop('disabled', false);
        $('#scheduleDay').val('');
        $('#scheduleTime').val('');
        $('#scheduleRoom').val('');
        $('#scheduleModalLabel').text('Create Course Schedule');
    });
    

    // Update hidden course_id when dropdown changes
    $('#scheduleCourseSelect').on('change', function() {
        $('#scheduleCourseId').val($(this).val());
    });

    // Save Schedule function
    function saveSchedule() {
        console.log('saveSchedule function called'); // Debug
        
        // Get values
        var courseId = $('#scheduleCourseId').val() || $('#scheduleCourseSelect').val();
        var day = $('#scheduleDay').val();
        var time = $('#scheduleTime').val();
        var room = $('#scheduleRoom').val();
        
        console.log('Form values:', {courseId, day, time, room}); // Debug
        
        // Validate
        if (!courseId) {
            alert('Please select a course');
            return false;
        }
        if (!day) {
            alert('Please select a day');
            return false;
        }
        if (!time) {
            alert('Please select a time');
            return false;
        }
        if (!room || room.trim() === '') {
            alert('Please enter a room');
            return false;
        }
        
        // Get CSRF token
        var csrfToken = $('meta[name="X-CSRF-TOKEN"]').attr('content') || '<?= csrf_hash() ?>';
        
        // Prepare data
        var data = {
            course_id: courseId,
            day: day,
            time: time,
            room: room
        };
        
        // Disable button
        var btn = $('#saveScheduleBtn');
        var originalText = btn.html();
        btn.prop('disabled', true).html('Saving...');
        
        // Send AJAX request
        console.log('Sending AJAX request...', data); // Debug
        $.ajax({
            url: '<?= base_url('course/saveSchedule') ?>',
            type: 'POST',
            data: data,
            headers: {
                'X-CSRF-TOKEN': csrfToken
            },
            dataType: 'json',
            success: function(response) {
                console.log('AJAX Success:', response); // Debug
                btn.prop('disabled', false).html(originalText);
                
                if (response && response.success) {
                    alert('Schedule saved successfully!');
                    $('#scheduleModal').modal('hide');
                    location.reload();
                } else {
                    alert('Error: ' + (response && response.message ? response.message : 'Failed to save schedule'));
                }
            },
            error: function(xhr, status, error) {
                console.error('AJAX Error:', xhr, status, error); // Debug
                btn.prop('disabled', false).html(originalText);
                
                var errorMsg = 'Error saving schedule';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMsg = xhr.responseJSON.message;
                } else if (xhr.responseText) {
                    try {
                        var parsed = JSON.parse(xhr.responseText);
                        if (parsed.message) {
                            errorMsg = parsed.message;
                        }
                    } catch(e) {
                        errorMsg = 'Server error occurred';
                    }
                }
                alert('Error: ' + errorMsg);
            }
        });
        
        return false;
    }
    
    // Make function globally accessible for inline onclick
    window.saveScheduleHandler = function() {
        console.log('saveScheduleHandler called from inline onclick');
        return saveSchedule();
    };
    
    // Attach handler using event delegation (works even if button is in modal)
    $(document).on('click', '#saveScheduleBtn', function(e) {
        e.preventDefault();
        e.stopPropagation();
        console.log('Save Schedule button clicked (delegation)!');
        return saveSchedule();
    });
    
    // Also attach directly as backup
    $('#saveScheduleBtn').on('click', function(e) {
        e.preventDefault();
        e.stopPropagation();
        console.log('Save Schedule button clicked (direct)!');
        return saveSchedule();
    });
    
    // Test if button exists after modal is shown and attach pure JS handler
    $('#scheduleModal').on('shown.bs.modal', function() {
        console.log('Modal shown, button exists:', $('#saveScheduleBtn').length > 0);
        
        // Attach pure JavaScript handler as backup
        var saveBtn = document.getElementById('saveScheduleBtn');
        if (saveBtn && !saveBtn.hasAttribute('data-handler-attached')) {
            saveBtn.setAttribute('data-handler-attached', 'true');
            saveBtn.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                console.log('Pure JS handler fired!');
                
                var courseId = document.getElementById('scheduleCourseId').value || document.getElementById('scheduleCourseSelect').value;
                var day = document.getElementById('scheduleDay').value;
                var time = document.getElementById('scheduleTime').value;
                var room = document.getElementById('scheduleRoom').value;
                
                console.log('Values:', {courseId, day, time, room});
                
                if (!courseId) { alert('Please select a course'); return false; }
                if (!day) { alert('Please select a day'); return false; }
                if (!time) { alert('Please select a time'); return false; }
                if (!room || room.trim() === '') { alert('Please enter a room'); return false; }
                
                var csrfToken = document.querySelector('meta[name="X-CSRF-TOKEN"]')?.getAttribute('content') || '<?= csrf_hash() ?>';
                
                var btn = this;
                var originalText = btn.innerHTML;
                btn.disabled = true;
                btn.innerHTML = 'Saving...';
                
                var formData = new URLSearchParams();
                formData.append('course_id', courseId);
                formData.append('day', day);
                formData.append('time', time);
                formData.append('room', room);
                
                fetch('<?= base_url('course/saveSchedule') ?>', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                        'X-CSRF-TOKEN': csrfToken
                    },
                    body: formData.toString()
                })
                .then(response => {
                    console.log('Response status:', response.status);
                    return response.json();
                })
                .then(data => {
                    console.log('Response data:', data);
                    btn.disabled = false;
                    btn.innerHTML = originalText;
                    if (data && data.success) {
                        alert('Schedule saved successfully!');
                        var modalElement = document.getElementById('scheduleModal');
                        var modal = bootstrap.Modal.getInstance(modalElement);
                        if (modal) {
                            modal.hide();
                        }
                        setTimeout(function() {
                            location.reload();
                        }, 500);
                    } else {
                        alert('Error: ' + (data && data.message ? data.message : 'Failed to save schedule'));
                    }
                })
                .catch(error => {
                    console.error('Fetch error:', error);
                    btn.disabled = false;
                    btn.innerHTML = originalText;
                    alert('Error: ' + error.message);
                });
                
                return false;
            });
        }
    });

    // Reset assign teacher modal when shown
    $('#assignTeacherModal').on('show.bs.modal', function (event) {
        $('#assignTeacherAlert').hide();
        $('#assignTeacherForm')[0].reset();
    });

    // Assign Teacher
    $('#assignTeacherBtn').on('click', function(e) {
        e.preventDefault();
        var formData = new FormData(document.getElementById('assignTeacherForm'));
        var alertDiv = $('#assignTeacherAlert');
        
        $.ajax({
            url: '<?= base_url('course/assignTeacher') ?>',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            headers: {
                'X-CSRF-TOKEN': '<?= csrf_hash() ?>'
            },
            success: function(response) {
                if (response.success) {
                    showAlert('success', response.message || 'Teacher assigned successfully');
                    $('#assignTeacherModal').modal('hide');
                    $('#assignTeacherForm')[0].reset();
                    location.reload();
                } else {
                    alertDiv.removeClass('alert-success').addClass('alert-danger');
                    alertDiv.html('<i class="bi bi-exclamation-triangle me-2"></i>' + (response.message || 'Error assigning teacher'));
                    alertDiv.show();
                }
            },
            error: function(xhr) {
                var errorMessage = 'Error assigning teacher';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMessage = xhr.responseJSON.message;
                }
                alertDiv.removeClass('alert-success').addClass('alert-danger');
                alertDiv.html('<i class="bi bi-exclamation-triangle me-2"></i>' + errorMessage);
                alertDiv.show();
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
