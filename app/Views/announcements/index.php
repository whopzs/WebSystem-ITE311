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
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-white">Announcements</h6>

                <?php if (session()->get('userRole') === 'teacher' || session()->get('userRole') === 'admin'): ?>
                    <a href="<?= base_url('announcements/create') ?>" 
                    class="btn btn-sm d-flex align-items-center" 
                    style="background-color: maroon; color: white; border: 1px solid maroon;">
                    <i class="bi bi-plus-circle me-1"></i>Create Announcement
                  </a>
                      <?php endif; ?>
                </div>
                <div class="card-body">
                    <?php if (empty($announcements)): ?>
                        <div class="text-center text-muted">
                            <i class="bi bi-megaphone fa-2x mb-3 text-muted"></i>
                            <p>
                                <?php if (session()->get('userRole') === 'student'): ?>
                                    No announcements available for at the moment.
                                <?php elseif (session()->get('userRole') === 'teacher'): ?>
                                    No announcements available at the moment.
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
                                        <?php if (isset($announcement['course_title'])): ?>
                                            <small class="text-muted">(<?= esc($announcement['course_title']) ?>)</small>
                                        <?php endif; ?>
                                    </h6>
                                    <div class="text-muted small mb-2">
                                        Author: <strong><?= esc($announcement['author_name'] ?? 'Unknown Author') ?></strong><br>
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
