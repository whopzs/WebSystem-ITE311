<?= $this->extend('templates/header') ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <h1 class="h3 mb-0 text-maroon">
                    <i class="bi bi-megaphone me-2"></i>Announcements
                </h1>
            </div>
        </div>
    </div>

    <!-- Announcements List -->
    <div class="row">
        <div class="col-12">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-white">Announcements</h6>
                </div>
                <div class="card-body">
                    <?php if (empty($announcements)): ?>
                        <div class="text-center text-muted">
                            <i class="bi bi-megaphone fa-2x mb-3 text-muted"></i>
                            <p>
                                <?php if (session()->get('userRole') === 'student'): ?>
                                    No announcements available for your enrolled courses at the moment.
                                <?php elseif (session()->get('userRole') === 'teacher'): ?>
                                    No announcements available for your courses at the moment.
                                <?php else: ?>
                                    No announcements available at the moment.
                                <?php endif; ?>
                            </p>
                        </div>
                    <?php else: ?>
                        <?php foreach ($announcements as $announcement): ?>
                            <div class="d-flex align-items-start mb-3 p-3 border rounded bg-light">
                                <div class="flex-shrink-0 me-3">
                                    <i class="bi bi-megaphone text-maroon fa-lg"></i>
                                </div>
                                <div class="flex-grow-1">
                                    <h6 class="mb-1 text-maroon fw-bold">
                                        <?= esc($announcement['title']) ?>
                                        <?php if (isset($announcement['course_title']) && $announcement['course_title'] !== 'General'): ?>
                                            <small class="text-muted">(<?= esc($announcement['course_title']) ?>)</small>
                                        <?php endif; ?>
                                    </h6>
                                    <div class="text-muted small mb-2">
                                        Posted on: <?= date('M d, Y, g:i a', strtotime($announcement['created_at'])) ?>
                                    </div>
                                    <div class="announcement-content">
                                        <?= esc($announcement['content']) ?>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
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
</style>
<?= $this->endSection() ?>
