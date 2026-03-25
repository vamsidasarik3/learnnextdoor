<?= $this->extend('admin/layout/default') ?>
<?= $this->section('content') ?>

<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">Carousel Management</h1>
            </div>
            <div class="col-sm-6">
                <p class="float-sm-right text-muted">Select up to 5 featured listings per state.</p>
            </div>
        </div>
    </div>
</div>

<section class="content">
    <div class="container-fluid">
        
        <?php foreach ($carouselData as $state => $featured): ?>
        <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4">
            <div class="card-header border-0 bg-white py-3 d-flex align-items-center">
                <div class="d-flex align-items-center">
                    <div class="state-icon bg-primary text-white rounded-circle d-flex align-items-center justify-content-center mr-3" style="width: 35px; height: 35px; font-size: 14px;">
                        <i class="fas fa-map-marker-alt"></i>
                    </div>
                    <h3 class="card-title fw-900 text-dark"><?= esc($state) ?> Featured Carousel</h3>
                </div>
                <div class="ml-auto">
                    <button class="btn btn-primary rounded-pill px-4 fw-bold shadow-sm" onclick="showAddModal('<?= esc($state) ?>')">
                        <i class="fas fa-plus mr-2"></i> Add Listing
                    </button>
                </div>
            </div>
            <div class="card-body p-0">
                <?php if (empty($featured)): ?>
                    <div class="p-4 text-center text-muted bg-light">
                        <i class="fas fa-images fa-3x mb-3 opacity-25"></i>
                        <p class="mb-0">No listings featured for this state yet. The system will fill gaps using top-rated listings.</p>
                    </div>
                <?php else: ?>
                    <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light text-muted uppercase" style="font-size: 0.75rem; letter-spacing: 0.05em;">
                            <tr>
                                <th class="px-4 py-3 border-0" style="width: 80px">Order</th>
                                <th class="py-3 border-0">Class Title</th>
                                <th class="py-3 border-0">Pricing</th>
                                <th class="px-4 py-3 border-0 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="sortable-list" data-state="<?= esc($state) ?>">
                            <?php foreach ($featured as $item): ?>
                            <tr data-id="<?= $item->id ?>" class="bg-white">
                                <td class="px-4">
                                    <div class="d-flex align-items-center">
                                        <i class="fas fa-grip-vertical text-muted mr-3" style="cursor: move;"></i>
                                        <span class="fw-bold text-primary">#<?= $item->position + 1 ?></span>
                                    </div>
                                </td>
                                <td>
                                    <span class="fw-bold text-dark" style="font-size: 0.95rem;"><?= esc($item->title) ?></span>
                                </td>
                                <td>
                                    <span class="text-dark fw-900">₹<?= number_format($item->price, 2) ?></span>
                                </td>
                                <td class="px-4 text-right">
                                    <button class="btn btn-sm btn-outline-danger rounded-pill px-3 fw-bold" onclick="removeListing(<?= $item->id ?>)">
                                        <i class="fas fa-trash-alt mr-1"></i> Remove
                                    </button>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>
        <?php endforeach; ?>

    </div>
</section>

<!-- Add Listing Modal -->
<div class="modal fade" id="addModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Featured Listing for <span id="modalStateName"></span></h5>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="targetState">
                <div class="form-group">
                    <label>Select Active Listing (Max 5 total for this state)</label>
                    <select id="listingSelect" class="form-control select2" style="width: 100%;">
                        <option value="">-- Choose a Listing --</option>
                        <?php foreach($allActive as $listing): ?>
                            <option value="<?= $listing->id ?>"><?= esc($listing->title) ?> (By: <?= esc($listing->provider_id) ?>)</option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="alert alert-info small mt-3">
                    Only 'Active' listings are available for selection.
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" onclick="saveAdd()">Feature This Listing</button>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('js') ?>
<!-- Include jQuery UI for Sortable if not present, but AdminLTE usually has it or we can use a CDN -->
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.min.js"></script>
<script>
$(function() {
    // Make lists sortable
    $(".sortable-list").sortable({
        update: function(event, ui) {
            const state = $(this).data('state');
            const order = [];
            $(this).find('tr').each(function() {
                order.push($(this).data('id'));
            });
            reorder(order);
        }
    });
});

function showAddModal(state) {
    $('#targetState').val(state);
    $('#modalStateName').text(state);
    $('#addModal').modal('show');
}

function saveAdd() {
    const state = $('#targetState').val();
    const lId   = $('#listingSelect').val();
    if (!lId) return alert('Select a listing');

    $.post('<?= url('admin/api/carousel/add') ?>', {
        state: state,
        listing_id: lId
    }, function(res) {
        if (res.success) {
            location.reload();
        } else {
            alert(res.message);
        }
    });
}

function removeListing(id) {
    if (!confirm('Remove this listing from featured carousel?')) return;
    $.post('<?= url('admin/api/carousel/remove') ?>', { id: id }, function(res) {
        if (res.success) {
            location.reload();
        }
    });
}

function reorder(order) {
    $.post('<?= url('admin/api/carousel/reorder') ?>', { order: order }, function(res) {
        // Just reload for simplicity to reflect new positions
        if (res.success) {
            location.reload();
        }
    });
}
</script>
<style>
.sortable-list tr { cursor: move; }
.ui-sortable-placeholder { height: 50px; background: #f4f4f4; visibility: visible !important; }
</style>
<?= $this->endSection() ?>
