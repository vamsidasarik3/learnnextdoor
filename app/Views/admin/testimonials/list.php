<?= $this->extend('admin/layout/default') ?>
<?= $this->section('content') ?>

<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0 fw-900 border-left border-4 border-primary pl-3">Testimonials Management</h1>
            </div>
            <div class="col-sm-6 text-right">
                <button class="btn btn-primary rounded-pill px-4 fw-bold shadow-sm" data-toggle="modal" data-target="#testimonialModal">
                    <i class="fas fa-plus mr-1"></i> Add Testimonial
                </button>
            </div>
        </div>
    </div>
</div>

<section class="content">
    <div class="container-fluid">
        
        <div class="row">
            <!-- HOME PAGE TESTIMONIALS -->
            <div class="col-lg-6">
                <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4">
                    <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                        <h3 class="card-title fw-bold text-primary"><i class="fas fa-home mr-2"></i> Home Page (Max 3)</h3>
                        <span class="badge badge-primary rounded-pill px-3"><?= count($homeTestimonials) ?> / 3</span>
                    </div>
                    <div class="card-body p-0">
                        <ul class="list-group list-group-flush sortable-testimonials" data-page="home">
                            <?php foreach ($homeTestimonials as $t): ?>
                                <li class="list-group-item p-4 border-bottom" data-id="<?= $t->id ?>">
                                    <div class="d-flex align-items-start">
                                        <div class="mr-3 move-handle" style="cursor: move;"><i class="fas fa-grip-vertical text-muted"></i></div>
                                        <img src="<?= base_url($t->user_image ?: 'assets/images/default-avatar.png') ?>" class="rounded-circle mr-3 border" width="50" height="50">
                                        <div class="flex-grow-1">
                                            <div class="d-flex justify-content-between align-items-start">
                                                <div>
                                                    <h6 class="fw-bold mb-0"><?= esc($t->user_name) ?></h6>
                                                    <div class="text-warning small mb-2">
                                                        <?php for($i=1;$i<=5;$i++): ?>
                                                            <i class="fas fa-star<?= $i<=$t->rating?'':'-o' ?>"></i>
                                                        <?php endfor; ?>
                                                    </div>
                                                </div>
                                                <div class="text-right">
                                                    <span class="badge badge-<?= $t->status=='active'?'success':'secondary' ?> rounded-pill mb-2"><?= strtoupper($t->status) ?></span>
                                                    <div>
                                                        <a href="javascript:void(0)" class="btn btn-xs btn-outline-info mr-1" onclick='editTestimonial(<?= json_encode($t) ?>)'><i class="fas fa-edit"></i></a>
                                                        <a href="<?= url("admin/testimonials/delete/{$t->id}") ?>" class="btn btn-xs btn-outline-danger" onclick="return confirm('Delete this testimonial?')"><i class="fas fa-trash"></i></a>
                                                    </div>
                                                </div>
                                            </div>
                                            <p class="small text-muted mb-0 italic">"<?= esc($t->feedback) ?>"</p>
                                        </div>
                                    </div>
                                </li>
                            <?php endforeach; ?>
                            <?php if (empty($homeTestimonials)): ?>
                                <li class="list-group-item text-center py-5 text-muted small">No testimonials added for home page.</li>
                            <?php endif; ?>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- ABOUT PAGE TESTIMONIALS -->
            <div class="col-lg-6">
                <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                    <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                        <h3 class="card-title fw-bold text-info"><i class="fas fa-info-circle mr-2"></i> About Page (Max 7)</h3>
                        <span class="badge badge-info rounded-pill px-3"><?= count($aboutTestimonials) ?> / 7</span>
                    </div>
                    <div class="card-body p-0">
                        <ul class="list-group list-group-flush sortable-testimonials" data-page="about">
                            <?php foreach ($aboutTestimonials as $t): ?>
                                <li class="list-group-item p-4 border-bottom" data-id="<?= $t->id ?>">
                                    <div class="d-flex align-items-start">
                                        <div class="mr-3 move-handle" style="cursor: move;"><i class="fas fa-grip-vertical text-muted"></i></div>
                                        <img src="<?= base_url($t->user_image ?: 'assets/images/default-avatar.png') ?>" class="rounded-circle mr-3 border" width="50" height="50">
                                        <div class="flex-grow-1">
                                            <div class="d-flex justify-content-between align-items-start">
                                                <div>
                                                    <h6 class="fw-bold mb-0"><?= esc($t->user_name) ?></h6>
                                                    <div class="text-warning small mb-2">
                                                        <?php for($i=1;$i<=5;$i++): ?>
                                                            <i class="fas fa-star<?= $i<=$t->rating?'':'-o' ?>"></i>
                                                        <?php endfor; ?>
                                                    </div>
                                                </div>
                                                <div class="text-right">
                                                    <span class="badge badge-<?= $t->status=='active'?'success':'secondary' ?> rounded-pill mb-2"><?= strtoupper($t->status) ?></span>
                                                    <div>
                                                        <a href="javascript:void(0)" class="btn btn-xs btn-outline-info mr-1" onclick='editTestimonial(<?= json_encode($t) ?>)'><i class="fas fa-edit"></i></a>
                                                        <a href="<?= url("admin/testimonials/delete/{$t->id}") ?>" class="btn btn-xs btn-outline-danger" onclick="return confirm('Delete this testimonial?')"><i class="fas fa-trash"></i></a>
                                                    </div>
                                                </div>
                                            </div>
                                            <p class="small text-muted mb-0 italic">"<?= esc($t->feedback) ?>"</p>
                                        </div>
                                    </div>
                                </li>
                            <?php endforeach; ?>
                            <?php if (empty($aboutTestimonials)): ?>
                                <li class="list-group-item text-center py-5 text-muted small">No testimonials added for about page.</li>
                            <?php endif; ?>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Modal -->
<div class="modal fade" id="testimonialModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-header bg-primary text-white border-0">
                <h5 class="modal-title fw-bold">Testimonial Details</h5>
                <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
            </div>
            <form action="<?= url('admin/testimonials/save') ?>" method="post" enctype="multipart/form-data">
                <?= csrf_field() ?>
                <input type="hidden" name="id" id="testiId">
                <div class="modal-body p-4">
                    <div class="row">
                        <div class="col-md-8">
                            <div class="form-group">
                                <label class="small fw-bold">User Name</label>
                                <input type="text" name="user_name" id="testiName" class="form-control" placeholder="e.g. John Doe" required>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="small fw-bold">Rating</label>
                                <select name="rating" id="testiRating" class="form-control">
                                    <option value="5">5 Stars</option>
                                    <option value="4">4 Stars</option>
                                    <option value="3">3 Stars</option>
                                    <option value="2">2 Stars</option>
                                    <option value="1">1 Star</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="small fw-bold">Feedback / Comment</label>
                        <textarea name="feedback" id="testiFeedback" class="form-control" rows="3" placeholder="What did they say?" required></textarea>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="small fw-bold">Assign to Page</label>
                                <select name="page" id="testiPage" class="form-control">
                                    <option value="home">Home Page</option>
                                    <option value="about">About Us</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="small fw-bold">Status</label>
                                <select name="status" id="testiStatus" class="form-control">
                                    <option value="active">Active</option>
                                    <option value="inactive">Inactive</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="form-group mb-0">
                        <label class="small fw-bold">User Photo (Optional)</label>
                        <input type="file" name="user_image" class="form-control-file">
                        <small class="text-muted">Best size: 200x200px (JPG/PNG)</small>
                    </div>
                </div>
                <div class="modal-footer border-0 p-4 pt-0">
                    <button type="button" class="btn btn-light rounded-pill px-4" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary rounded-pill px-4 fw-bold">Save Testimonial</button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
.fw-900 { font-weight: 900; }
.rounded-4 { border-radius: 1rem !important; }
.italic { font-style: italic; }
.list-group-item:hover { background-color: #f8f9fa; }
</style>

<?= $this->endSection() ?>

<?= $this->section('js') ?>
<script src="https://cdnjs.cloudflare.com/ajax/libs/Sortable/1.14.0/Sortable.min.js"></script>
<script>
$(function() {
    $('.sortable-testimonials').each(function() {
        new Sortable(this, {
            handle: '.move-handle',
            animation: 150,
            onEnd: function() {
                let order = [];
                $(this.el).find('li').each(function() {
                    order.push($(this).data('id'));
                });
                $.post('<?= url('admin/api/testimonials/reorder') ?>', {
                    order: order,
                    page: $(this.el).data('page'),
                    <?= csrf_token() ?>: '<?= csrf_hash() ?>'
                });
            }
        });
    });
});

function editTestimonial(t) {
    $('#testiId').val(t.id);
    $('#testiName').val(t.user_name);
    $('#testiRating').val(t.rating);
    $('#testiFeedback').val(t.feedback);
    $('#testiPage').val(t.page);
    $('#testiStatus').val(t.status);
    $('#testimonialModal').modal('show');
}
</script>
<?= $this->endSection() ?>
