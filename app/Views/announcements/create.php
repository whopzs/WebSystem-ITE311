<?= $this->extend('templates/header') ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <h1 class="h3 mb-0 text-maroon">
                    <i class="bi bi-megaphone me-2"></i>Create Announcement
                </h1>
            </div>
        </div>
    </div>

    <!-- Create Announcement Form -->
    <div class="row">
        <div class="col-12">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-white">New Announcement</h6>
                </div>
                <div class="card-body">
                    <?php if (session()->getFlashdata('success')): ?>
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <i class="bi bi-check-circle me-2"></i>
                            <strong>Success!</strong> <?= session()->getFlashdata('success') ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    <?php endif; ?>

                    <?php if (session()->getFlashdata('error')): ?>
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="bi bi-exclamation-triangle me-2"></i>
                            <strong>Error!</strong> <?= session()->getFlashdata('error') ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    <?php endif; ?>

                    <form method="post" action="<?= base_url('announcements/create') ?>">
                        <?= csrf_field() ?>

                        <div class="mb-3">
                            <label for="title" class="form-label">Title</label>
                            <input type="text" class="form-control" id="title" name="title" value="<?= old('title') ?>" required>
                            <?php if (isset($validation) && $validation->hasError('title')): ?>
                                <div class="text-danger">
                                    <?= $validation->getError('title') ?>
                                </div>
                            <?php endif; ?>
                        </div>

                        <div class="mb-3">
                            <label for="course_id" class="form-label">Course</label>
                            <?php if (empty($courses)): ?>
                                <div class="alert alert-warning">
                                    You don't have any courses assigned to you. Please contact an administrator to assign courses.
                                </div>
                            <?php else: ?>
                                <select class="form-select" id="course_id" name="course_id" required>
                                    <option value="">Select a course</option>
                                    <?php foreach ($courses as $course): ?>
                                        <option value="<?= $course['id'] ?>" <?= old('course_id') == $course['id'] ? 'selected' : '' ?>>
                                            <?= esc($course['title']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <?php if (isset($validation) && $validation->hasError('course_id')): ?>
                                    <div class="text-danger">
                                        <?= $validation->getError('course_id') ?>
                                    </div>
                                <?php endif; ?>
                            <?php endif; ?>
                        </div>

                        <div class="mb-3">
                            <label for="content" class="form-label">Content</label>
                            <textarea class="form-control" id="content" name="content" rows="5" required><?= old('content') ?></textarea>
                            <?php if (isset($validation) && $validation->hasError('content')): ?>
                                <div class="text-danger">
                                    <?= $validation->getError('content') ?>
                                </div>
                            <?php endif; ?>
                        </div>

                        <button type="submit" class="btn btn-primary">Create Announcement</button>
                        <a href="<?= base_url('dashboard') ?>" class="btn btn-secondary">Cancel</a>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.text-maroon {
    color: maroon !important;
}
.card-header {
    background-color: maroon;
    color: white;
}
.btn-primary {
    background-color: maroon;
    border-color: maroon;
}
.btn-primary:hover {
    background-color: #660000;
    border-color: #660000;
}
.form-control:focus {
    border-color: maroon;
    box-shadow: 0 0 0 0.2rem rgba(128, 0, 0, 0.25);
}
.form-select:focus {
    border-color: maroon;
    box-shadow: 0 0 0 0.2rem rgba(128, 0, 0, 0.25);
}
.alert-warning {
    background-color: #f8d7da;
    border-color: #f5c6cb;
    color: #721c24;
}
</style>
<?= $this->endSection() ?>
