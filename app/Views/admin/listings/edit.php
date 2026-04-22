<?= $this->extend('admin/layout/default') ?>

<?= $this->section('content') ?>

<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0 font-weight-bold">Edit & Moderate Listing</h1>
            </div>
            <div class="col-sm-6 text-sm-right">
                <a href="<?= url('admin/listings') ?>" class="btn btn-outline-secondary"><i class="fas fa-arrow-left mr-1"></i> Back to Queue</a>
            </div>
        </div>
    </div>
</div>

<section class="content">
    <div class="container-fluid">
        <div class="card shadow-sm border-0 rounded-4">
            <form id="adminEditForm" enctype="multipart/form-data">
                <input type="hidden" name="id" value="<?= $listing->id ?>">
                <div class="card-body">
                    <div class="row">
                        <!-- Basic Info -->
                        <div class="col-md-6">
                            <h5 class="mb-4 text-primary fw-bold"><i class="fas fa-info-circle mr-2"></i>Basic Information</h5>
                            <div class="form-group mb-3">
                                <label class="text-xs text-uppercase font-weight-bold opacity-50">Class Title / Institute Name</label>
                                <input type="text" name="institute_name" class="form-control form-control-lg" value="<?= esc($listing->title) ?>" required>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group mb-3">
                                        <label class="text-xs text-uppercase font-weight-bold opacity-50">Type</label>
                                        <select name="type" id="classType" class="form-control" required>
                                            <option value="regular" <?= $listing->type == 'regular' ? 'selected' : '' ?>>Regular Class</option>
                                            <option value="workshop" <?= $listing->type == 'workshop' ? 'selected' : '' ?>>Workshop</option>
                                            <option value="course" <?= $listing->type == 'course' ? 'selected' : '' ?>>Course</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group mb-3">
                                        <label class="text-xs text-uppercase font-weight-bold opacity-50">Category</label>
                                        <select name="category_id" id="categorySelect" class="form-control" required>
                                            <?php foreach($categories as $cid => $cname): ?>
                                                <option value="<?= $cid ?>" <?= $cid == $listing->category_id ? 'selected' : '' ?>><?= esc($cname) ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group mb-3">
                                <label class="text-xs text-uppercase font-weight-bold opacity-50">Subcategories</label>
                                <select name="subcategory_ids[]" id="subcategorySelect" class="form-control select2" multiple required>
                                    <?php foreach($subcategories as $sub): ?>
                                        <option value="<?= $sub->id ?>" <?= in_array($sub->id, explode(',', $listing->subcategory_ids)) ? 'selected' : '' ?>><?= esc($sub->name) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="form-group mb-3">
                                <label class="text-xs text-uppercase font-weight-bold opacity-50">Description</label>
                                <textarea name="description" class="form-control" rows="5" required><?= esc($listing->description) ?></textarea>
                            </div>
                        </div>

                        <!-- Location & Details -->
                        <div class="col-md-6 border-left">
                            <h5 class="mb-4 text-primary fw-bold"><i class="fas fa-map-marker-alt mr-2"></i>Location & Media</h5>
                            <div class="form-group mb-3">
                                <label class="text-xs text-uppercase font-weight-bold opacity-50">Manual Address</label>
                                <textarea name="manual_address" class="form-control" rows="2" required><?= esc($listing->manual_address ?: $listing->address) ?></textarea>
                            </div>
                            <div class="form-group mb-3">
                                <label class="text-xs text-uppercase font-weight-bold opacity-50">Formatted Address (Search Results)</label>
                                <input type="text" name="formatted_address" id="locationInput" class="form-control" value="<?= esc($listing->formatted_address ?: $listing->address) ?>" required>
                                <div class="row mt-2">
                                    <div class="col-6"><input type="text" name="latitude" id="lat" class="form-control form-control-sm" value="<?= $listing->latitude ?>" placeholder="Lat" readonly></div>
                                    <div class="col-6"><input type="text" name="longitude" id="lng" class="form-control form-control-sm" value="<?= $listing->longitude ?>" placeholder="Lng" readonly></div>
                                </div>
                                <input type="hidden" name="city" id="city" value="<?= $listing->city ?>">
                                <input type="hidden" name="locality" id="locality" value="<?= $listing->locality ?>">
                                <input type="hidden" name="pincode" id="pincode" value="<?= $listing->pincode ?>">
                            </div>
                            
                            <hr>
                            <h6 class="fw-bold text-muted small text-uppercase">Admin Moderate Note</h6>
                            <div class="alert alert-info py-2 px-3 small rounded-3">
                                <i class="fas fa-lightbulb mr-1"></i> Saving changes will automatically mark this listing as <strong>APPROVED</strong> and set it live.
                            </div>
                        </div>
                    </div>

                    <!-- Batches / Specifics -->
                    <div id="dynamicSections" class="mt-4 pt-4 border-top">
                        <!-- To keep it simple for Admin, we show JSON representation or basic inputs -->
                        <div class="alert alert-secondary py-3 text-center">
                            <i class="fas fa-tools mr-2"></i> Direct schedule editing for Admin is coming soon. Currently, please use the Provider screen for complex batch changes.
                        </div>
                    </div>
                </div>
                <div class="card-footer bg-white py-4 border-top text-right">
                    <button type="submit" class="btn btn-primary btn-lg rounded-pill px-5 shadow-sm" id="saveBtn">
                        <i class="fas fa-check-circle mr-2"></i> Update & Approve Listing
                    </button>
                </div>
            </form>
        </div>
    </div>
</section>

<?= $this->endSection() ?>

<?= $this->section('js') ?>
<script src="https://maps.googleapis.com/maps/api/js?key=<?= env('GOOGLE_MAP_API_KEY') ?>&libraries=places"></script>
<script>
$(function() {
    $('.select2').select2({ width: '100%', placeholder: 'Select subcategories' });

    // Google Autocomplete
    const locationInput = document.getElementById('locationInput');
    const autocomplete = new google.maps.places.Autocomplete(locationInput, { types: ['geocode', 'establishment'], componentRestrictions: { country: 'IN' } });
    autocomplete.addListener('place_changed', function() {
        const place = autocomplete.getPlace();
        if (!place.geometry) return;
        $('#lat').val(place.geometry.location.lat());
        $('#lng').val(place.geometry.location.lng());
        place.address_components.forEach(c => {
            if (c.types.includes('locality')) $('#city').val(c.long_name);
            if (c.types.includes('sublocality')) $('#locality').val(c.long_name);
            if (c.types.includes('postal_code')) $('#pincode').val(c.long_name);
        });
    });

    // Form Submit
    $('#adminEditForm').on('submit', function(e) {
        e.preventDefault();
        const btn = $('#saveBtn');
        btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-2"></i> Saving...');

        $.post('<?= url('admin/api/listings/save') ?>', $(this).serialize(), function(res) {
            if(res.success) {
                toastr.success(res.message);
                setTimeout(() => window.location.href = '<?= url('admin/listings') ?>', 1000);
            } else {
                toastr.error(res.message);
                btn.prop('disabled', false).html('<i class="fas fa-check-circle mr-2"></i> Update & Approve Listing');
            }
        });
    });
});
</script>
<?= $this->endSection() ?>
