<?= $this->extend('frontend/layout/base') ?>
<?= $this->section('css') ?>
<style>
/* ── Detail Page ─────────────────────────────── */
.cnd-detail-hero{position:relative;background:#0d0d1a;min-height:340px;overflow:hidden;display:flex;align-items:flex-end;}
.cnd-detail-hero-bg{position:absolute;inset:0;width:100%;height:100%;object-fit:cover;opacity:.55;transition:opacity .4s;}
.cnd-detail-hero-gradient{position:absolute;inset:0;background:linear-gradient(to top,rgba(10,10,20,.95) 0%,rgba(10,10,20,.55) 55%,rgba(10,10,20,.15) 100%);}
.cnd-detail-hero-content{position:relative;z-index:2;padding:2.2rem 1.5rem 2rem;}
@media(min-width:992px){.cnd-detail-hero{min-height:420px;}.cnd-detail-hero-content{padding:3rem 3rem 2.5rem;}}
.cnd-detail-type-badge{display:inline-flex;align-items:center;gap:.35rem;background:var(--cnd-pink);color:#fff;border-radius:var(--cnd-radius-pill);font-size:.72rem;font-weight:700;padding:.28rem .85rem;letter-spacing:.04em;margin-bottom:.9rem;text-transform:uppercase;}
.cnd-detail-type-badge.workshop{background:linear-gradient(135deg,#f7971e,#ffd200);}
.cnd-detail-type-badge.course{background:linear-gradient(135deg,#7C4DFF,#c776ff);}
.cnd-detail-title{font-size:clamp(1.45rem,4vw,2.4rem);font-weight:900;color:#fff;line-height:1.2;letter-spacing:-.5px;margin-bottom:.7rem;}
.cnd-detail-meta-row{display:flex;flex-wrap:wrap;align-items:center;gap:.7rem 1rem;color:rgba(255,255,255,.82);font-size:.83rem;}
.cnd-detail-meta-row i{color:var(--cnd-gold);}
.cnd-detail-stars-inline{color:var(--cnd-gold);letter-spacing:.06em;font-size:.88rem;}
/* Breadcrumb */
.cnd-breadcrumb{font-size:.78rem;padding:.7rem 0;color:var(--cnd-muted);}
.cnd-breadcrumb a{color:var(--cnd-pink);text-decoration:none;}
.cnd-breadcrumb a:hover{text-decoration:underline;}
/* Layout */
.cnd-detail-layout{display:flex;gap:1.8rem;align-items:flex-start;}
.cnd-detail-main{flex:1;min-width:0;}
.cnd-detail-sidebar{width:300px;flex-shrink:0;position:sticky;top:calc(var(--cnd-navbar-h) + 1rem);}
@media(max-width:991.98px){.cnd-detail-layout{flex-direction:column;}.cnd-detail-sidebar{width:100%;position:static;}}
/* Thumbnail strip */
.cnd-thumb-strip{display:flex;gap:.6rem;overflow-x:auto;scrollbar-width:none;padding-bottom:.3rem;margin-bottom:1.5rem;}
.cnd-thumb-strip::-webkit-scrollbar{display:none;}
.cnd-thumb{width:80px;height:60px;object-fit:cover;border-radius:var(--cnd-radius-xs);cursor:pointer;opacity:.65;transition:opacity .2s,transform .2s;border:2.5px solid transparent;flex-shrink:0;}
.cnd-thumb.active,.cnd-thumb:hover{opacity:1;border-color:var(--cnd-pink);transform:scale(1.05);}
/* Tab nav */
.cnd-detail-tabs{border-bottom:2px solid var(--cnd-card-border);margin-bottom:1.5rem;display:flex;gap:0;overflow-x:auto;scrollbar-width:none;}
.cnd-detail-tabs::-webkit-scrollbar{display:none;}
.cnd-detail-tab{padding:.65rem 1.1rem;font-size:.84rem;font-weight:700;color:var(--cnd-muted);border:none;background:none;cursor:pointer;border-bottom:3px solid transparent;margin-bottom:-2px;white-space:nowrap;transition:color var(--cnd-transition),border-color var(--cnd-transition);}
.cnd-detail-tab.active,.cnd-detail-tab:hover{color:var(--cnd-pink);border-bottom-color:var(--cnd-pink);}
.cnd-detail-panel{display:none;}.cnd-detail-panel.active{display:block;}
/* Description */
.cnd-detail-desc{font-size:.93rem;line-height:1.75;color:var(--cnd-dark);}
/* Schedule grid */
.cnd-slot-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(160px,1fr));gap:.65rem;}
.cnd-slot-card{background:#fff;border:1.5px solid var(--cnd-card-border);border-radius:var(--cnd-radius-xs);padding:.7rem .9rem;transition:border-color var(--cnd-transition),box-shadow var(--cnd-transition);cursor:default;}
.cnd-slot-card:hover{border-color:var(--cnd-pink);box-shadow:0 2px 10px rgba(255, 104, 180,.15);}
.cnd-slot-date{font-size:.76rem;font-weight:700;color:var(--cnd-grad-start);text-transform:uppercase;letter-spacing:.06em;}
.cnd-slot-time{font-size:.88rem;font-weight:600;color:var(--cnd-dark);margin-top:.15rem;}
/* Reviews */
.cnd-rating-summary{display:flex;align-items:center;gap:2rem;padding:1.2rem 1.4rem;background:var(--cnd-light);border-radius:var(--cnd-radius-sm);margin-bottom:1.4rem;flex-wrap:wrap;}
.cnd-rating-big{font-size:3rem;font-weight:900;color:var(--cnd-dark);line-height:1;}
.cnd-rating-stars-lg{font-size:1.4rem;color:var(--cnd-gold);letter-spacing:.05em;}
.cnd-rating-count-sm{font-size:.8rem;color:var(--cnd-muted);}
.cnd-rating-bars{flex:1;min-width:180px;display:flex;flex-direction:column;gap:.35rem;}
.cnd-rating-bar-row{display:flex;align-items:center;gap:.6rem;font-size:.75rem;}
.cnd-rating-bar-track{flex:1;height:6px;background:var(--cnd-card-border);border-radius:3px;overflow:hidden;}
.cnd-rating-bar-fill{height:100%;background:var(--cnd-gold);border-radius:3px;}
.cnd-review-card{background:#fff;border:1px solid var(--cnd-card-border);border-radius:var(--cnd-radius-sm);padding:1rem 1.2rem;margin-bottom:.9rem;box-shadow:var(--cnd-card-shadow);}
.cnd-review-header{display:flex;align-items:center;justify-content:space-between;margin-bottom:.5rem;flex-wrap:wrap;gap:.4rem;}
.cnd-reviewer-name{font-weight:700;font-size:.88rem;color:var(--cnd-dark);}
.cnd-review-date{font-size:.74rem;color:var(--cnd-muted);}
.cnd-review-stars{color:var(--cnd-gold);font-size:.82rem;letter-spacing:.06em;}
.cnd-review-text{font-size:.84rem;color:var(--cnd-muted);line-height:1.65;}
/* Sidebar card */
.cnd-sidebar-card{background:#fff;border:1.5px solid var(--cnd-card-border);border-radius:var(--cnd-radius-sm);padding:1.4rem 1.3rem;box-shadow:var(--cnd-card-shadow);}
.cnd-sidebar-price{font-size:2rem;font-weight:900;color:var(--cnd-dark);line-height:1;}
.cnd-sidebar-price sup{font-size:1rem;font-weight:700;vertical-align:top;margin-top:.3rem;display:inline-block;}
.cnd-sidebar-price-label{font-size:.78rem;color:var(--cnd-muted);margin-top:.2rem;}
.cnd-sidebar-divider{height:1px;background:var(--cnd-card-border);margin:1rem 0;}
.cnd-sidebar-meta-item{display:flex;align-items:flex-start;gap:.55rem;font-size:.83rem;color:var(--cnd-dark);margin-bottom:.7rem;}
.cnd-sidebar-meta-item i{color:var(--cnd-pink);font-size:1rem;flex-shrink:0;margin-top:.05rem;}
.cnd-sidebar-meta-label{font-size:.72rem;color:var(--cnd-muted);display:block;}
.cnd-btn-book{display:block;width:100%;padding:.85rem;font-weight:800;font-size:1rem;background:var(--cnd-gradient);border:none;color:#fff;border-radius:var(--cnd-radius-pill);text-align:center;cursor:pointer;text-decoration:none;transition:opacity .2s,transform .2s;margin-bottom:.7rem;}
.cnd-btn-book:hover{opacity:.9;transform:translateY(-2px);color:#fff;}
.cnd-btn-trial{display:block;width:100%;padding:.7rem;font-weight:700;font-size:.88rem;background:transparent;border:2px solid var(--cnd-pink);color:var(--cnd-pink);border-radius:var(--cnd-radius-pill);text-align:center;cursor:pointer;text-decoration:none;transition:all .2s;}
.cnd-btn-trial:hover{background:var(--cnd-pink);color:#fff;}
/* Early bird */
.cnd-early-bird{background:linear-gradient(135deg,#fff8e1,#fff3cd);border:1.5px solid #ffd200;border-radius:var(--cnd-radius-xs);padding:.8rem 1rem;margin-bottom:1rem;font-size:.82rem;}
.cnd-early-bird-badge{font-size:.72rem;font-weight:800;background:#ffd200;color:#333;border-radius:var(--cnd-radius-pill);padding:.15rem .6rem;margin-right:.4rem;}
/* Provider box */
.cnd-provider-box{background:var(--cnd-light);border-radius:var(--cnd-radius-xs);padding:.85rem 1rem;margin-top:.8rem;font-size:.82rem;}
.cnd-provider-box strong{font-size:.85rem;color:var(--cnd-dark);}
/* Empty states */
.cnd-empty-state-sm{text-align:center;padding:2rem 1rem;color:var(--cnd-muted);}
.cnd-empty-icon-sm{font-size:2.2rem;display:block;opacity:.25;margin-bottom:.5rem;}
/* Share buttons group */
.cnd-share-group{display:flex;gap:.5rem;margin-top:1.2rem;justify-content:center;flex-wrap:wrap;}
.cnd-share-btn{display:inline-flex;align-items:center;justify-content:center;width:38px;height:38px;border-radius:50%;background:#fff;border:1.5px solid var(--cnd-card-border);color:var(--cnd-muted);transition:all .2s;font-size:1.1rem;}
.cnd-share-btn:hover{background:var(--cnd-light);color:var(--cnd-pink);border-color:var(--cnd-pink);transform:translateY(-2px);}
.cnd-share-btn.whatsapp:hover{color:#25D366;border-color:#25D366;}
/* Provider contact reveal */
.cnd-reveal-box{background:rgba(255, 104, 180,0.05);border:1px dashed var(--cnd-pink);border-radius:var(--cnd-radius-xs);padding:.8rem;text-align:center;margin-top:.8rem;}
.cnd-reveal-btn{background:none;border:none;color:var(--cnd-pink);font-weight:700;font-size:.82rem;text-decoration:underline;cursor:pointer;padding:0;}
.cnd-verified-badge{display:inline-flex;align-items:center;gap:.3rem;background:#d4f8e8;color:#1a7a4a;font-size:.68rem;font-weight:700;padding:.15rem .5rem;border-radius:var(--cnd-radius-pill);margin-top:.3rem;}
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<?php
$l         = $listing;
$coverImg  = listing_img_url($l['cover_image'] ?? '');
$rating    = (float)($l['avg_rating']  ?? 0);
$rCount    = (int)($l['review_count']  ?? 0);
$price     = (float)($l['price']       ?? 0);
$type      = $l['type']      ?? 'regular';
$typeLabel = ucfirst($type);
$typeIcon  = ['regular'=>'bi-calendar3','workshop'=>'bi-lightning-charge','course'=>'bi-journal-richtext'][$type] ?? 'bi-star';
?>

<!-- ══ HERO ════════════════════════════════════════════════ -->
<section class="cnd-detail-hero" aria-label="Listing cover">
  <img id="detailHeroBg" src="<?= $coverImg ?>" alt="" class="cnd-detail-hero-bg" loading="eager">
  <div class="cnd-detail-hero-gradient"></div>
  <div class="cnd-detail-hero-content container-fluid px-3 px-lg-5 w-100">
    <span class="cnd-detail-type-badge <?= esc($type) ?>">
      <i class="bi <?= $typeIcon ?>" aria-hidden="true"></i><?= $typeLabel ?>
    </span>
    <h1 class="cnd-detail-title"><?= esc($l['title']) ?></h1>
    <div class="cnd-detail-meta-row">
      <?php if ($rating > 0): ?>
      <span class="cnd-detail-stars-inline" aria-label="Rating <?= number_format($rating,1) ?> out of 5">
        <?php for($s=1;$s<=5;$s++) echo $s<=$rating?'★':'☆'; ?>
      </span>
      <span><?= number_format($rating,1) ?></span>
      <span class="opacity-75">(<?= number_format($rCount) ?> review<?= $rCount!==1?'s':'' ?>)</span>
      <?php endif; ?>
      <?php if (!empty($l['category_name'])): ?>
      <span><i class="bi bi-tag-fill" aria-hidden="true"></i> <?= esc($l['category_name']) ?><?= !empty($l['subcategory_names']) ? ' &rsaquo; ' . esc($l['subcategory_names']) : '' ?></span>
      <?php endif; ?>
      <?php if (!empty($l['locality']) || !empty($l['city'])): ?>
      <span><i class="bi bi-geo-alt-fill" aria-hidden="true"></i> <strong>Location:</strong> <?= esc(implode(', ', array_filter([$l['locality'] ?? '', $l['city'] ?? '']))) ?></span>
      <?php endif; ?>
      <?php if (!empty($l['formatted_address']) || !empty($l['address'])): ?>
      <span><i class="bi bi-pin-map-fill" aria-hidden="true"></i> <strong>Address:</strong> <?= esc(character_limiter($l['formatted_address'] ?: $l['address'], 100)) ?></span>
      <?php endif; ?>
      <?php if (!empty($l['distance_km'])): ?>
      <span><i class="bi bi-radar" aria-hidden="true"></i> <?= number_format($l['distance_km'],1) ?> km away</span>
      <?php endif; ?>
    </div>
  </div>
</section>

<!-- ══ BODY ════════════════════════════════════════════════ -->
<section class="py-4" style="background:var(--cnd-light);">
  <div class="container-fluid px-3 px-lg-5">

    <!-- Breadcrumb -->
    <nav class="cnd-breadcrumb" aria-label="breadcrumb">
      <a href="<?= base_url() ?>">Home</a> &rsaquo;
      <a href="<?= base_url('classes') ?>">Classes</a> &rsaquo;
      <?php if (!empty($l['category_name'])): ?>
        <a href="<?= base_url('classes?category=' . $l['category_id']) ?>"><?= esc($l['category_name']) ?></a> &rsaquo;
      <?php endif; ?>
      <?php if (!empty($l['subcategory_name'])): ?>
        <a href="<?= base_url('classes?category=' . $l['category_id'] . '&subcategory=' . $l['subcategory_id']) ?>"><?= esc($l['subcategory_name']) ?></a> &rsaquo;
      <?php endif; ?>
      <span><?= esc($l['title']) ?></span>
    </nav>

    <div class="cnd-detail-layout">

      <!-- ── MAIN ── -->
      <div class="cnd-detail-main">

        <!-- Thumbnail strip (if multiple images) -->
        <?php if (count($images) > 1): ?>
        <div class="cnd-thumb-strip mb-3" role="list" aria-label="Listing images">
          <?php foreach ($images as $idx => $img): ?>
          <img
            src="<?= listing_img_url($img['image_path'] ?? '') ?>"
            alt="Listing image <?= $idx+1 ?>"
            class="cnd-thumb<?= $idx===0?' active':'' ?>"
            role="listitem"
            loading="lazy"
            onclick="window.cndDetailSwap(this,'<?= listing_img_url($img['image_path'] ?? '') ?>')">
          <?php endforeach; ?>
        </div>
        <?php endif; ?>

        <!-- Tab navigation -->
        <div class="cnd-detail-tabs" role="tablist" aria-label="Listing details">
          <button class="cnd-detail-tab active" data-tab="about"    role="tab" aria-selected="true"  id="tab-about">Overview</button>
          <button class="cnd-detail-tab"         data-tab="reviews"  role="tab" aria-selected="false" id="tab-reviews">
            Reviews
            <?php if($rCount > 0): ?>
            <span class="badge rounded-pill ms-1" style="background:var(--cnd-pink);font-size:.65rem;"><?= $rCount ?></span>
            <?php endif; ?>
          </button>
        </div>

        <!-- ── ABOUT panel ── -->
        <div class="cnd-detail-panel active" id="panel-about" role="tabpanel" aria-labelledby="tab-about">
          <!-- Description -->
          <?php if (!empty($l['description'])): ?>
          <div class="cnd-detail-desc mb-4">
             <h5 class="fw-bold mb-3">Description</h5>
             <?= nl2br(esc($l['description'])) ?>
          </div>
          <?php endif; ?>

          <!-- Instructor Details -->
          <div class="mb-4 p-3 bg-white border rounded-4 shadow-sm">
             <h5 class="fw-bold mb-3 d-flex align-items-center">
                <i class="bi bi-person-workspace text-pink me-2"></i> Instructor Info
             </h5>
             <div class="d-flex align-items-center gap-3 mb-3">
                <div class="bg-soft-pink text-pink rounded-circle p-2" style="width: 50px; height: 50px; display: flex; align-items: center; justify-content: center;">
                   <i class="bi bi-person-fill fs-4"></i>
                </div>
                <div>
                   <div class="fw-bold fs-5 d-flex align-items-center gap-2">
                      <?= esc($l['instructor_name'] ?: $l['provider_name']) ?>
                      <?php if(($l['instructor_kyc_status'] ?? '') === 'verified' || !empty($l['provider_verified'])): ?>
                        <i class="bi bi-patch-check-fill text-success" title="Verified Instructor"></i>
                      <?php endif; ?>
                   </div>
                   <?php if(!empty($l['social_links']) || !empty($l['linkedin_url'])): ?>
                      <a href="<?= esc($l['social_links'] ?: $l['linkedin_url']) ?>" target="_blank" class="small text-pink text-decoration-none">
                         <i class="bi bi-link-45deg"></i> Social / Portfolio Link
                      </a>
                   <?php endif; ?>
                </div>
             </div>
             <?php if (!empty($l['experience'])): ?>
                <div class="small text-muted border-top pt-3">
                   <strong>Experience:</strong><br>
                   <?= nl2br(esc($l['experience'])) ?>
                </div>
             <?php endif; ?>
          </div>

          <!-- Batch / Schedule Details -->
          <div class="mb-4">
             <h5 class="fw-bold mb-3">Batch & Schedule Details</h5>
             <?php
             $listingType = $l['type'] ?? 'regular';
             $batches = $l['batches'] ?? [];
             if (empty($batches) && $listingType === 'regular') {
                $batches = [[
                  'name' => 'General Batch',
                  'from_time' => $l['class_time'],
                  'to_time' => $l['class_end_time'],
                  'price' => $price,
                  'batch_size' => $l['batch_size'],
                  'free_trial' => $l['free_trial']
                ]];
             }
             ?>

             <?php if ($listingType === 'regular' && !empty($batches)): ?>
                <div class="row g-3">
                   <?php foreach($batches as $idx => $batch): ?>
                   <div class="col-md-6">
                      <div class="card border-0 shadow-sm rounded-4 p-3 border-start border-4 border-pink">
                         <div class="d-flex justify-content-between align-items-start mb-2">
                            <h6 class="fw-bold mb-0"><?= esc($batch['name'] ?? 'Batch') ?></h6>
                            <span class="badge bg-pink text-white">₹<?= number_format($batch['price'] ?? 0) ?> / <?= ($batch['price_type'] ?? "monthly") === "quarterly" ? "Quarter" : "Month" ?></span>
                         </div>
                         <div class="small text-muted mb-2">
                            <i class="bi bi-calendar3 me-1"></i> <?= esc(is_array($batch['days'] ?? null) ? implode(', ', $batch['days']) : ($batch['days'] ?? 'Check schedule')) ?>
                         </div>
                         <div class="small text-dark fw-bold mb-2">
                            <i class="bi bi-clock me-1 text-pink"></i> 
                            <?= !empty($batch['from_time']) ? date('g:i A', strtotime($batch['from_time'])) : 'N/A' ?>
                            <?= !empty($batch['to_time']) ? ' - ' . date('g:i A', strtotime($batch['to_time'])) : '' ?>
                         </div>
                         <div class="d-flex justify-content-between align-items-center mt-2 pt-2 border-top">
                            <div class="small text-muted">
                               Size: <?= (int)($batch['batch_size'] ?? 0) ?> Students
                            </div>
                            <?php if(!empty($batch['free_trial'])): ?>
                               <span class="badge bg-success bg-opacity-10 text-success small">Free Trial Available</span>
                            <?php endif; ?>
                         </div>
                         <div class="mt-3 d-flex gap-2">
                            <button type="button" onclick="openBookingModal('regular', <?= $idx ?>)" class="btn btn-pink btn-sm flex-grow-1 rounded-pill fw-bold shadow-sm">Book Now</button>
                            <?php if(!empty($batch['free_trial'])): ?>
                               <button type="button" onclick="openBookingModal('trial', <?= $idx ?>)" class="btn btn-outline-success btn-sm flex-grow-1 rounded-pill fw-bold">Free Trial</button>
                            <?php endif; ?>
                         </div>
                      </div>
                   </div>
                   <?php endforeach; ?>
                </div>
             <?php elseif ($listingType === 'workshop'): ?>
                <!-- Workshop View -->
                <div class="bg-white p-3 border rounded-4">
                   <div class="fw-bold mb-2 text-warning"><i class="bi bi-star-fill me-1"></i> Workshop Sessions</div>
                   <?php foreach($slots as $slot): ?>
                      <div class="d-flex justify-content-between py-1 border-bottom last-border-0">
                         <span><?= date('D, d M Y', strtotime($slot['available_date'])) ?></span>
                         <span class="fw-bold text-dark"><?= date('g:i A', strtotime($slot['available_time'])) ?></span>
                      </div>
                   <?php endforeach; ?>
                </div>
             <?php elseif ($listingType === 'course'): ?>
                <!-- Course View -->
                <div class="card border-0 shadow-sm rounded-4 p-3">
                   <div class="row text-center g-2">
                      <div class="col-6 border-end">
                         <div class="small text-muted text-uppercase">Starts</div>
                         <div class="fw-bold"><?= date('d M Y', strtotime($l['start_date'])) ?></div>
                      </div>
                      <div class="col-6">
                         <div class="small text-muted text-uppercase">Ends</div>
                         <div class="fw-bold"><?= date('d M Y', strtotime($l['end_date'])) ?></div>
                      </div>
                   </div>
                   <div class="mt-3 text-center border-top pt-2">
                      <div class="small text-muted">Schedule</div>
                      <div class="fw-bold"><?= date('g:i A', strtotime($l['class_time'])) ?> - <?= date('g:i A', strtotime($l['class_end_time'])) ?></div>
                   </div>
                </div>
             <?php endif; ?>
          </div>

          <!-- Quick-info pills -->
          <div class="d-flex flex-wrap gap-2 mt-3">
            <?php if($l['free_trial']): ?>
            <span class="badge rounded-pill px-3 py-2" style="background:#d4f8e8;color:#1a7a4a;font-weight:700;">
              <i class="bi bi-gift-fill me-1"></i>Free Trial Available
            </span>
            <?php endif; ?>
            <?php if(!empty($l['batch_size'])): ?>
            <span class="badge rounded-pill px-3 py-2" style="background:#e0d4f7;color:#7C4DFF;font-weight:700;">
              <i class="bi bi-people-fill me-1"></i>Max <?= (int)$l['batch_size'] ?> Students per batch
            </span>
            <?php endif; ?>
          </div>
        </div>


        <!-- ── REVIEWS panel ── -->
        <div class="cnd-detail-panel" id="panel-reviews" role="tabpanel" aria-labelledby="tab-reviews">
          <?php
          // Build rating breakdown
          $breakdown = [5=>0,4=>0,3=>0,2=>0,1=>0];
          foreach ($reviews as $rv) {
            $r = (int)($rv['rating'] ?? 0);
            if(isset($breakdown[$r])) $breakdown[$r]++;
          }
          $total_rv = count($reviews);
          ?>
          <?php if ($is_enrolled && !$has_reviewed): ?>
             <div class="card border-0 shadow-sm rounded-4 mb-4" style="background:#f8f9ff; border: 1px solid #e0e4ff !important;">
                <div class="card-body p-4">
                   <h5 class="fw-bold mb-2">How was your class?</h5>
                   <?php if (($l['type'] ?? 'regular') === 'regular'): ?>
                      <p class="small text-muted mb-3">Share your experience with others! Marking as complete will allow you to post a review.</p>
                      <form id="reviewForm" class="mt-3">
                         <div class="mb-3">
                            <label class="form-label small fw-bold text-uppercase">Your Rating</label>
                            <div class="cnd-rating-input fs-3 text-pink cursor-pointer" id="starInput">
                               <i class="bi bi-star" data-value="1"></i>
                               <i class="bi bi-star" data-value="2"></i>
                               <i class="bi bi-star" data-value="3"></i>
                               <i class="bi bi-star" data-value="4"></i>
                               <i class="bi bi-star" data-value="5"></i>
                            </div>
                            <input type="hidden" name="rating" id="reviewRating" value="0">
                         </div>
                         <div class="mb-3">
                            <label class="form-label small fw-bold text-uppercase">Your Review</label>
                            <textarea name="review_text" class="form-control rounded-3 border-2" rows="3" placeholder="What did you like the most?"></textarea>
                         </div>
                         <button type="submit" class="btn btn-pink rounded-pill px-4 fw-bold">Post Review & Complete Class</button>
                      </form>
                   <?php else: ?>
                      <p class="small text-muted mb-3">To leave a review for Workshops or Courses, please head to your history page.</p>
                      <a href="<?= base_url('my-bookings') ?>" class="btn btn-pink rounded-pill px-4 fw-bold shadow-sm">
                         Click here to post review <i class="bi bi-arrow-right ms-1"></i>
                      </a>
                   <?php endif; ?>
                </div>
             </div>
          <?php elseif ($is_enrolled && $has_reviewed): ?>
             <div class="alert alert-soft-success rounded-4 border-0 mb-4 py-3">
                <i class="bi bi-patch-check-fill me-2"></i> You have already reviewed this class. Thank you!
             </div>
          <?php endif; ?>

          <?php if ($total_rv === 0): ?>
          <div class="cnd-empty-state-sm">
            <i class="bi bi-chat-square-text cnd-empty-icon-sm"></i>
            <p class="fw-600 mb-1">No reviews yet</p>
            <p class="small">Be the first to leave a review after your class!</p>
          </div>
          <?php else: ?>

          <!-- Rating summary -->
          <div class="cnd-rating-summary">
            <div class="text-center">
              <div class="cnd-rating-big"><?= number_format($rating,1) ?></div>
              <div class="cnd-rating-stars-lg">
                <?php for($s=1;$s<=5;$s++) echo $s<=$rating?'★':'☆'; ?>
              </div>
              <div class="cnd-rating-count-sm"><?= $total_rv ?> review<?= $total_rv!==1?'s':'' ?></div>
            </div>
            <div class="cnd-rating-bars">
              <?php foreach ([5,4,3,2,1] as $star): ?>
              <div class="cnd-rating-bar-row">
                <span style="min-width:14px;"><?= $star ?></span>
                <i class="bi bi-star-fill" style="color:var(--cnd-gold);font-size:.7rem;" aria-hidden="true"></i>
                <div class="cnd-rating-bar-track">
                  <div class="cnd-rating-bar-fill" style="width:<?= $total_rv>0?round($breakdown[$star]/$total_rv*100):0 ?>%;"></div>
                </div>
                <span style="min-width:22px;"><?= $breakdown[$star] ?></span>
              </div>
              <?php endforeach; ?>
            </div>
          </div>

          <!-- Individual reviews -->
          <?php foreach ($reviews as $rv): ?>
          <article class="cnd-review-card">
            <div class="cnd-review-header">
              <span class="cnd-reviewer-name">
                <?php
                $name = $rv['reviewer_name'] ?? 'Anonymous';
                echo esc($name[0] . str_repeat('*', max(0, mb_strlen($name)-2)) . substr($name,-1));
                ?>
              </span>
              <span class="cnd-review-date"><?= date('d M Y', strtotime($rv['created_at'])) ?></span>
            </div>
            <div class="cnd-review-stars" aria-label="Rating: <?= (int)$rv['rating'] ?> out of 5">
              <?php for($s=1;$s<=5;$s++) echo $s<=(int)$rv['rating']?'★':'☆'; ?>
            </div>
            <?php if (!empty($rv['review_text'])): ?>
            <p class="cnd-review-text mt-1 mb-0"><?= esc($rv['review_text']) ?></p>
            <?php endif; ?>
          </article>
          <?php endforeach; ?>

          <?php endif; ?>
        </div><!-- /reviews panel -->

      </div><!-- /.cnd-detail-main -->

      <!-- ── SIDEBAR ── -->
      <aside class="cnd-detail-sidebar" aria-label="Booking information">
        <div class="cnd-sidebar-card">

          <!-- Price -->
          <div class="mb-2">
            <?php if ($price > 0): ?>
            <div class="cnd-sidebar-price">
              <sup>₹</sup><?= number_format($price) ?>
            </div>
            <div class="cnd-sidebar-price-label">
              <?php
              $pb = is_array($l['price_breakdown'] ?? null) ? $l['price_breakdown'] : [];
              if (!empty($pb['sessions']) && !empty($pb['per_session'])): ?>
                <?= (int)$pb['sessions'] ?> sessions &middot; ₹<?= number_format($pb['per_session']) ?>/session
              <?php elseif ($type === 'regular'): ?>
                <?= ($l['price_type'] ?? 'monthly') === 'quarterly' ? 'Quarterly' : 'Monthly' ?> class fee
              <?php else: ?>
                per course / workshop fee
              <?php endif; ?>
            </div>
            <?php else: ?>
            <div class="cnd-sidebar-price" style="color:#2ECC71;">Free</div>
            <div class="cnd-sidebar-price-label">No fees for this class</div>
            <?php endif; ?>
          </div>

          <!-- Early bird -->
          <?php
          $now  = date('Y-m-d');
          $ebDate  = $l['early_bird_date']  ?? null;
          $ebPrice = $l['early_bird_price'] ?? null;
          $ebSlots = (int)($l['early_bird_slots'] ?? 0);
          if ($ebDate && $ebPrice && $now <= $ebDate): ?>
          <div class="cnd-early-bird">
            <span class="cnd-early-bird-badge">Early Bird</span>
            <strong>₹<?= number_format($ebPrice) ?></strong>
            — offer ends <?= date('d M', strtotime($ebDate)) ?>
            <?php if ($ebSlots > 0): ?>· only <?= $ebSlots ?> slots<?php endif; ?>
          </div>
          <?php endif; ?>

          <div class="cnd-sidebar-divider"></div>

          <!-- Meta -->
          <div class="mb-3">
            <?php if (!empty($l['category_name'])): ?>
            <div class="cnd-sidebar-meta-item">
              <i class="bi bi-tag-fill" aria-hidden="true"></i>
              <div>
                <span class="cnd-sidebar-meta-label">Category</span>
                <?= esc($l['category_name']) ?>
              </div>
            </div>
            <?php endif; ?>
            <div class="cnd-sidebar-meta-item">
              <i class="bi bi-collection-fill" aria-hidden="true"></i>
              <div>
                <span class="cnd-sidebar-meta-label">Type</span>
                <?= esc(ucfirst($type)) ?>
              </div>
            </div>
            <?php if ($l['free_trial']): ?>
            <div class="cnd-sidebar-meta-item">
              <i class="bi bi-gift-fill" aria-hidden="true"></i>
              <div>
                <span class="cnd-sidebar-meta-label">Trial</span>
                Free trial available
              </div>
            </div>
            <?php endif; ?>
            <?php if (!empty($l['batch_size']) || !empty($l['total_students'])): ?>
            <div class="cnd-sidebar-meta-item">
              <i class="bi bi-people-fill" aria-hidden="true"></i>
              <div>
                <span class="cnd-sidebar-meta-label">Capacity</span>
                <?php if (!empty($l['batch_size'])): ?>
                  <?= number_format((int)$l['total_students']) ?> / <?= number_format((int)$l['batch_size']) ?> enrolled
                <?php else: ?>
                  <?= number_format($l['total_students']) ?> enrolled
                <?php endif; ?>
              </div>
            </div>
            <?php endif; ?>

            <?php
            // ── Type-specific schedule summary in sidebar ────────────
            $sType = $l['type'] ?? 'regular';
            $sDate = $l['start_date'] ?? null;
            $eDate = $l['end_date']   ?? null;
            $cTime = $l['class_time'] ?? null;
            $ceTime = $l['class_end_time'] ?? null;
            ?>
            <?php if ($sType === 'regular' && ($sDate || $cTime)): ?>
            <div class="cnd-sidebar-meta-item">
              <i class="bi bi-calendar-event" aria-hidden="true" style="color:#3F3590;"></i>
              <div>
                <span class="cnd-sidebar-meta-label">Class Schedule</span>
                <?= $sDate ? date('d M Y', strtotime($sDate)) : '' ?>
                <?php if ($cTime): ?>
                   <br>
                   <small class="fw-bold">
                      <?= date('g:i A', strtotime($cTime)) ?>
                      <?= $ceTime ? ' - ' . date('g:i A', strtotime($ceTime)) : '' ?>
                   </small>
                <?php endif; ?>
              </div>
            </div>
            <?php elseif ($sType === 'course' && ($sDate || $eDate)): ?>
            <div class="cnd-sidebar-meta-item">
              <i class="bi bi-calendar-range" aria-hidden="true" style="color:#7C4DFF;"></i>
              <div>
                <span class="cnd-sidebar-meta-label">Course Dates</span>
                <?= $sDate ? date('d M Y', strtotime($sDate)) : '?' ?>
                <?= $eDate ? ' → ' . date('d M Y', strtotime($eDate)) : '' ?>
                <?php if ($cTime): ?>
                   <br>
                   <small class="fw-bold text-muted">
                      Daily: <?= date('g:i A', strtotime($cTime)) ?>
                      <?= $ceTime ? ' - ' . date('g:i A', strtotime($ceTime)) : '' ?>
                   </small>
                <?php endif; ?>
              </div>
            </div>
            <?php elseif ($sType === 'workshop' && !empty($slots)): ?>
            <div class="cnd-sidebar-meta-item">
              <i class="bi bi-calendar3" aria-hidden="true" style="color:#f7971e;"></i>
              <div>
                <span class="cnd-sidebar-meta-label">Workshop Sessions</span>
                <?= count($slots) ?> session<?= count($slots) !== 1 ? 's' : '' ?> available
              </div>
            </div>
            <?php endif; ?>

            <?php if (!empty($l['locality']) || !empty($l['city'])): ?>
            <div class="cnd-sidebar-meta-item">
              <i class="bi bi-geo-alt-fill" aria-hidden="true"></i>
              <div>
                <span class="cnd-sidebar-meta-label">Location</span>
                <?= esc(implode(', ', array_filter([$l['locality'] ?? '', $l['city'] ?? '']))) ?>
              </div>
            </div>
            <?php endif; ?>

            <?php if (!empty($l['formatted_address']) || !empty($l['address'])): ?>
            <div class="cnd-sidebar-meta-item">
              <i class="bi bi-pin-map-fill" aria-hidden="true"></i>
              <div>
                <span class="cnd-sidebar-meta-label">Address</span>
                <?= esc($l['formatted_address'] ?: $l['address']) ?>
              </div>
            </div>
            <?php endif; ?>
          </div>

          <!-- CTAs -->
          <a href="<?= base_url('book/' . (int)$l['id']) ?>" class="cnd-btn-book" id="bookNowBtn"
             aria-label="Book <?= esc($l['title']) ?>">
            <i class="bi bi-calendar-check-fill me-2" aria-hidden="true"></i>Book Now
          </a>
          <?php if ($l['free_trial']): ?>
          <a href="<?= base_url('book/' . (int)$l['id']) ?>?trial=1" class="cnd-btn-trial" id="trialBtn"
             aria-label="Book a free trial for <?= esc($l['title']) ?>">
            <i class="bi bi-gift me-1" aria-hidden="true"></i>Book Free Trial
          </a>
          <?php endif; ?>

          <!-- Provider -->
          <?php if (!empty($l['provider_name'])): ?>
          <!-- Location Map -->
          <div class="cnd-provider-box mt-3">
             <h6 class="fw-bold mb-3 small text-uppercase letter-spacing-sm">Class Location</h6>
             <?php if (logged('id')): ?>
                <?php if (!empty($l['latitude']) && !empty($l['longitude'])): ?>
                   <div class="rounded-3 overflow-hidden" style="height: 200px; position:relative;">
                      <iframe 
                        width="100%" 
                        height="200" 
                        frameborder="0" 
                        style="border:0" 
                        src="https://www.google.com/maps/embed/v1/place?key=<?= env('GOOGLE_MAP_API_KEY') ?>&q=<?= $l['latitude'] ?>,<?= $l['longitude'] ?>" 
                        allowfullscreen>
                      </iframe>
                   </div>
                   <div class="mt-2 small text-muted">
                      <i class="bi bi-geo-alt-fill text-pink"></i> <?= esc($l['address'] ?: ($l['locality'] . ', ' . $l['city'])) ?>
                   </div>
                <?php else: ?>
                   <div class="bg-light p-3 rounded-3 text-center small text-muted">
                      <i class="bi bi-map fs-2 d-block mb-1"></i>
                      Map coordinates not available.
                   </div>
                <?php endif; ?>
             <?php else: ?>
                <div class="bg-soft-pink text-pink p-4 rounded-3 text-center">
                   <i class="bi bi-lock-fill fs-2 d-block mb-2"></i>
                   <p class="small fw-bold mb-3">Login to see the exact location</p>
                   <a href="<?= base_url('login') ?>" class="btn btn-pink btn-sm rounded-pill px-4">Login Now</a>
                </div>
             <?php endif; ?>
          </div>
          <?php endif; ?>

        </div><!-- /.cnd-sidebar-card -->

        <!-- Share buttons -->
        <div class="cnd-share-group">
          <button class="cnd-share-btn whatsapp" id="shareWa" title="Share on WhatsApp">
            <i class="bi bi-whatsapp"></i>
          </button>
          <button class="cnd-share-btn" id="shareNative" title="Other sharing options">
            <i class="bi bi-share"></i>
          </button>
          <button class="cnd-share-btn" id="shareCopy" title="Copy Link">
            <i class="bi bi-link-45deg"></i>
          </button>
          <a href="<?= base_url('classes') ?>" class="cnd-share-btn" title="Browse Classes">
            <i class="bi bi-grid"></i>
          </a>
        </div>
      </aside>

    </div><!-- /.cnd-detail-layout -->
  </div>
</section>

<!-- ── Booking data for JS ──────────────────────────── -->
<script id="listingDetailData" type="application/json">
<?= json_encode([
  'id'          => (int)($l['id'] ?? 0),
  'title'       => $l['title'] ?? '',
  'type'        => $type,
  'avg_rating'  => $rating,
  'review_count'=> $rCount,
  'price'       => $price,
  'free_trial'  => (bool)($l['free_trial'] ?? false),
  'lat'         => $l['latitude']  ?? null,
  'lng'         => $l['longitude'] ?? null,
  'csrf_token'  => csrf_hash(),
  'csrf_name'   => csrf_token(),
  'rp_key'      => env('RAZORPAY_KEY', ''),
  'base_url'    => rtrim(base_url(), '/'),
  'user_phone'  => session()->get('cnd_user')['phone'] ?? '',
], JSON_HEX_TAG|JSON_HEX_AMP) ?>
</script>

<!-- ══ EMAIL VERIFICATION MODAL ════════════════════════════════ -->
<div class="modal fade" id="verifyEmailModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
      <div class="modal-header bg-light border-0 py-3">
        <h5 class="modal-title fw-bold">Unlock Instructor Contact</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body p-4">
        
        <!-- Step 1: Enter Email -->
        <div id="vStep1">
          <div class="text-center mb-4">
             <div class="bg-soft-pink text-pink rounded-circle mx-auto d-flex align-items-center justify-content-center mb-3" style="width: 70px; height: 70px;">
                <i class="bi bi-person-lines-fill fs-2"></i>
             </div>
             <p class="text-muted px-3">To see the instructor's phone number, please verify your email address.</p>
          </div>
          
          <div class="mb-4">
            <label class="form-label small fw-bold text-uppercase letter-spacing-sm">Your Email Address</label>
            <input type="email" id="vEmail" class="form-control form-control-lg rounded-3 border-2" placeholder="e.g. parent@example.com">
          </div>
          <button id="vSendBtn" class="btn btn-pink w-100 py-3 rounded-pill fw-bold shadow-sm" style="background:var(--cnd-pink); border:none; color:#fff;">
            <span id="vSendSpinner" class="spinner-border spinner-border-sm d-none me-2"></span>
            Send Verification Code
          </button>
        </div>

        <!-- Step 2: Enter OTP -->
        <div id="vStep2" class="d-none text-center">
          <div class="mb-4">
             <div class="bg-success bg-opacity-10 text-success rounded-circle mx-auto d-flex align-items-center justify-content-center mb-3" style="width: 70px; height: 70px;">
                <i class="bi bi-envelope-open fs-2"></i>
             </div>
             <h6 class="fw-bold mb-1">Check your inbox</h6>
             <p class="text-muted small">We've sent a 6-digit code to <br><span id="vDisplayEmail" class="fw-bold text-dark"></span></p>
          </div>
          
          <div class="mb-4">
            <input type="text" id="vOtp" class="form-control form-control-lg text-center fw-bold fs-3 rounded-3" maxlength="6" placeholder="------" style="letter-spacing: .5rem;">
          </div>

          <button id="vVerifyBtn" class="btn btn-pink w-100 py-3 rounded-pill fw-bold mb-3 shadow-sm" style="background:var(--cnd-pink); border:none; color:#fff;">
             <span id="vVerifySpinner" class="spinner-border spinner-border-sm d-none me-2"></span>
             Verify & Unlock
          </button>
          
          <div class="d-flex justify-content-between px-2">
            <button class="btn btn-link btn-sm text-pink text-decoration-none px-0" id="vResendBtn" style="color:var(--cnd-pink);">Resend Code</button>
            <button class="btn btn-link btn-sm text-muted text-decoration-none px-0" id="vBackBtn">Change Email</button>
          </div>
        </div>

      </div>
    </div>
  </div>
</div>

<style>
.bg-soft-pink { background: rgba(255, 104, 180, 0.08); }
.text-pink { color: var(--cnd-pink); }
</style>

<!-- ══ BOOKING MODAL ══════════════════════════════════════════ -->
<div class="modal fade" id="bookingModal" tabindex="-1"
     aria-labelledby="bookingModalLabel" aria-modal="true" role="dialog">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content" style="border-radius:1.1rem;overflow:hidden;">

      <!-- Header -->
      <div class="modal-header" style="background:var(--cnd-gradient);border:none;padding:1.2rem 1.5rem;">
        <h2 class="modal-title fs-5 fw-800 text-white m-0" id="bookingModalLabel">
          <i class="bi bi-calendar-check-fill me-2" aria-hidden="true"></i>
          <span id="bookingModalHeading">Book Your Spot</span>
        </h2>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>

      <div class="d-flex" style="background:#f8f4ff;padding:.65rem 1.5rem;gap:1.5rem;align-items:center;">
        <?php foreach ([1=>'Student Info',2=>'Confirmation'] as $sn=>$sl): ?>
        <div class="d-flex align-items-center gap-2 cnd-step-item" data-step="<?= $sn ?>">
          <div class="cnd-step-dot"><?= $sn ?></div>
          <span class="cnd-step-label d-none d-sm-inline"><?= $sl ?></span>
        </div>
        <?php if($sn < 2): ?><div style="flex:1;height:2px;background:#e0d4f7;border-radius:1px;"></div><?php endif; ?>
        <?php endforeach; ?>
      </div>

      <div class="modal-body p-4">

        <!-- Alert area -->
        <div id="bookingAlert" class="alert d-none mb-3" role="alert" aria-live="polite"></div>

        <!-- ── Step 1: Student Info ── -->
        <div id="bStep1">
          <p class="text-muted small mb-3">Enter the student's details and your phone for OTP verification.</p>
          <form id="bookingForm1" novalidate>
            <div class="mb-3">
              <label for="bStudentName" class="form-label fw-600 small">Student Name <span class="text-danger">*</span></label>
              <input type="text" class="form-control" id="bStudentName" name="student_name"
                     placeholder="e.g. Arya Sharma" required minlength="2" maxlength="150"
                     autocomplete="name">
            </div>
            <div class="row g-3 mb-3">
              <div class="col-6">
                <label for="bStudentAge" class="form-label fw-600 small">Age</label>
                <input type="number" class="form-control" id="bStudentAge" name="student_age"
                       placeholder="8" min="1" max="18" autocomplete="off">
              </div>
              <div class="col-6">
                <label for="bBookingType" class="form-label fw-600 small">Booking Type</label>
                <select class="form-select" id="bBookingType" name="booking_type">
                  <option value="regular">Regular</option>
                  <?php if(!empty($l['free_trial'])): ?>
                  <option value="trial">Free Trial</option>
                  <?php endif; ?>
                </select>
              </div>
            </div>

            <?php if ($listingType === 'regular' && !empty($batches)): ?>
            <div class="mb-3">
              <label for="bBatch" class="form-label fw-600 small">Choose Batch <span class="text-danger">*</span></label>
              <select class="form-select border-2" id="bBatch" name="batch_index" required>
                <?php foreach($batches as $idx => $batch): ?>
                <option value="<?= $idx ?>">
                  <?= esc($batch['name'] ?? 'Batch '.($idx+1)) ?> — <?= !empty($batch['from_time']) ? date('g:i A', strtotime($batch['from_time'])) : '' ?> (₹<?= number_format($batch['price'] ?? 0) ?> / <?= ($batch['price_type'] ?? "monthly") === "quarterly" ? "Quarter" : "Month" ?>)
                </option>
                <?php endforeach; ?>
              </select>
            </div>
            <?php endif; ?>
            <?php if (!empty($slots)): ?>
            <div class="mb-3">
              <label for="bSlot" class="form-label fw-600 small">Preferred Session</label>
              <select class="form-select" id="bSlot" name="slot">
                <option value="">— Any / Contact provider —</option>
                <?php foreach(array_slice($slots,0,10) as $slot): ?>
                <option value="<?= esc($slot['available_date'].'|'.$slot['available_time']) ?>">
                  <?= date('D, d M', strtotime($slot['available_date'])) ?> at <?= date('g:i A', strtotime($slot['available_time'])) ?>
                </option>
                <?php endforeach; ?>
              </select>
            </div>
            <?php endif; ?>
            <div class="mb-3">
              <label for="bPhone" class="form-label fw-600 small">Your Phone <span class="text-danger">*</span></label>
              <div class="input-group">
                <span class="input-group-text">+91</span>
                <input type="tel" class="form-control" id="bPhone" name="phone"
                       placeholder="9876543210" pattern="[6-9][0-9]{9}" maxlength="10"
                       required autocomplete="tel-national">
              </div>
              <div class="form-text">Mobile No will be used for session updates.</div>
            </div>
            <button type="submit" class="btn cnd-btn-book mt-1" id="bStep1Btn">
              <span id="bStep1BtnText">Book Now <i class="bi bi-arrow-right ms-1"></i></span>
              <span id="bStep1Spinner" class="spinner-border spinner-border-sm d-none" role="status"></span>
            </button>
          </form>
        </div>

        <!-- ── Step 2: Confirmation ── -->
        <div id="bStep2" class="d-none">
          <div class="text-center mb-3">
            <div style="font-size:3.5rem;">🎉</div>
            <h3 class="fw-800 mt-2" style="font-size:1.2rem;">Booking Confirmed!</h3>
          </div>
          <div id="bConfirmDetails" class="rounded p-3 mb-3" style="background:var(--cnd-light);font-size:.88rem;"></div>
          <p class="text-muted small text-center">📱 A WhatsApp confirmation has been sent to your phone.</p>
          <a href="<?= base_url('classes') ?>" class="btn btn-outline-secondary btn-sm w-100 mt-1">Browse more classes</a>
        </div>

      </div><!-- /.modal-body -->
    </div>
  </div>
</div><!-- /#bookingModal -->
<?= $this->section('js') ?>
<!-- Razorpay SDK -->
<script src="https://checkout.razorpay.com/v1/checkout.js"></script>
<style>
/* Booking modal step dots */
.cnd-step-dot{width:26px;height:26px;border-radius:50%;background:#e0d4f7;color:#7C4DFF;font-size:.75rem;font-weight:800;display:flex;align-items:center;justify-content:center;flex-shrink:0;transition:background .3s,color .3s;}
.cnd-step-item.active .cnd-step-dot{background:var(--cnd-pink);color:#fff;}
.cnd-step-item.done .cnd-step-dot{background:#2ECC71;color:#fff;}
.cnd-step-label{font-size:.73rem;font-weight:600;color:var(--cnd-muted);white-space:nowrap;}
.cnd-step-item.active .cnd-step-label{color:var(--cnd-pink);}
</style>
<script>
(function(){
  'use strict';

  /* ── Detail data from PHP ─────────────────────── */
  var D = JSON.parse(document.getElementById('listingDetailData').textContent);
  var BASE  = D.base_url + '/';
  var CSRF  = { name: D.csrf_name, token: D.csrf_token };

  /* ── Tab switcher ─────────────────────────────── */
  var tabs   = document.querySelectorAll('.cnd-detail-tab');
  var panels = document.querySelectorAll('.cnd-detail-panel');
  tabs.forEach(function(tab){
    tab.addEventListener('click', function(){
      var target = this.dataset.tab;
      tabs.forEach(function(t){ t.classList.remove('active'); t.setAttribute('aria-selected','false'); });
      panels.forEach(function(p){ p.classList.remove('active'); });
      this.classList.add('active'); this.setAttribute('aria-selected','true');
      var panel = document.getElementById('panel-' + target);
      if(panel) panel.classList.add('active');
    });
  });

  /* ── Hero image swap ───────────────────────────── */
  window.cndDetailSwap = function(thumbEl, src){
    var hero = document.getElementById('detailHeroBg');
    if(hero){ hero.style.opacity='0'; setTimeout(function(){ hero.src=src; hero.style.opacity=''; },200); }
    document.querySelectorAll('.cnd-thumb').forEach(function(t){ t.classList.remove('active'); });
    thumbEl.classList.add('active');
  };

  /* ── Booking modal helpers ─────────────────────── */
  var bModal = null;
  var phone  = '';

  window.openBookingModal = function(type, batchIdx) {
    document.getElementById('bBookingType').value = type || 'regular';
    if (D.user_phone) {
      document.getElementById('bPhone').value = D.user_phone;
    }
    // Pre-select batch if index provided
    if (batchIdx !== undefined && batchIdx !== null) {
      var batchSelect = document.getElementById('bBatch');
      if (batchSelect) {
        batchSelect.value = batchIdx;
      }
    }
    setStep(1);
    showAlert('', '');
    if(!bModal) bModal = new bootstrap.Modal(document.getElementById('bookingModal'));
    bModal.show();
  }

  document.getElementById('bookNowBtn')?.addEventListener('click', function(e){ e.preventDefault(); openBookingModal('regular'); });
  document.getElementById('trialBtn')?.addEventListener('click',   function(e){ e.preventDefault(); openBookingModal('trial');   });

  /* ── Step indicator ────────────────────────────── */
  function setStep(n) {
    ['bStep1','bStep2'].forEach(function(id,i){
      document.getElementById(id).classList.toggle('d-none', i+1 !== n);
    });
    document.querySelectorAll('.cnd-step-item').forEach(function(el){
      var s = parseInt(el.dataset.step,10);
      el.classList.toggle('active', s === n);
      el.classList.toggle('done',   s < n);
    });
    var headings = ['Book Your Spot','Booking Confirmed!'];
    document.getElementById('bookingModalHeading').textContent = headings[n-1];
  }

  /* ── Alert helper ──────────────────────────────── */
  function showAlert(msg, type) {
    var el = document.getElementById('bookingAlert');
    if(!msg){ el.classList.add('d-none'); return; }
    el.className = 'alert alert-' + (type||'danger') + ' mb-3';
    el.textContent = msg;
    el.classList.remove('d-none');
  }

  /* ── setLoading helper ─────────────────────────── */
  function setLoading(btnId, spinnerId, loading) {
    var btn = document.getElementById(btnId);
    var sp  = document.getElementById(spinnerId);
    if(!btn||!sp) return;
    btn.disabled = loading;
    sp.classList.toggle('d-none', !loading);
    document.getElementById(btnId+'Text')?.classList.toggle('d-none', loading);
  }

  /* ── POST helper ───────────────────────────────── */
  function postJson(url, body, cb) {
    body[CSRF.name] = CSRF.token;
    fetch(BASE + url, {
      method: 'POST',
      headers: { 'Content-Type': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
      body: JSON.stringify(body),
    })
    .then(function(r){ return r.json(); })
    .then(cb)
    .catch(function(){ cb({ success:false, message:'Network error. Please try again.' }); });
  }

  /* ═══════════════════════════════════════════════
     STEP 1 — Student info + OTP send
  ═══════════════════════════════════════════════ */
  document.getElementById('bookingForm1').addEventListener('submit', function(e){
    e.preventDefault();
    showAlert('','');
    var name  = document.getElementById('bStudentName').value.trim();
    var age   = document.getElementById('bStudentAge').value.trim();
    var btype = document.getElementById('bBookingType').value;
    var ph    = document.getElementById('bPhone').value.trim();
    var slot  = document.getElementById('bSlot')?.value || '';
    if(!name){ showAlert('Please enter the student name.'); return; }
    if(!/^[6-9][0-9]{9}$/.test(ph)){ showAlert('Enter a valid 10-digit Indian mobile number.'); return; }

    var parts = slot ? slot.split('|') : [];
    var batchInput = document.getElementById('bBatch');
    var batchIdx = batchInput ? batchInput.value : null;

    var body = {
      listing_id:   D.id,
      booking_type: btype,
      student_name: name,
      student_age:  age || null,
      phone:        ph,
      class_date:   parts[0] || null,
      class_time:   parts[1] || null,
      batch_index:  batchIdx
    };

    setLoading('bStep1Btn','bStep1Spinner',true);
    postJson('booking/init', body, function(res){
      setLoading('bStep1Btn','bStep1Spinner',false);
      if(!res.success){
        if(res.auth_required){
          if(bModal) bModal.hide();
          window.location.href = (res.login_url || (BASE + 'login'));
          return;
        }
        var errs = res.errors ? Object.values(res.errors).join(' ') : (res.message||'Error');
        showAlert(errs); return;
      }

      phone = ph;

      // Handle direct confirmation or payment if OTP skipped
      if (res.otp_skipped) {
        if (!res.paid) {
            if(res.redirect_url) {
              window.location.href = res.redirect_url;
            } else {
              renderConfirmation(res.booking);
              setStep(2); 
              schedulePushPrompt(phone);
            }
        } else {
           handleRazorpay(res);
        }
        return;
      }

      document.getElementById('bPhoneDisplay').textContent = '+91 ' + ph.replace(/(\d{2})(\d{4})(\d{4})/,'$1** ****');
      // Dev mode — show OTP hint
      var hint = document.getElementById('bDevOtpHint');
      if(res.dev_otp){
        hint.textContent = '🛠 Dev mode OTP: ' + res.dev_otp;
        hint.classList.remove('d-none');
        document.getElementById('bOtp').value = res.dev_otp;
      } else { hint.classList.add('d-none'); }
      setStep(2);
    });
  });

  function handleRazorpay(res) {
      showAlert('Opening payment secure gateway…','info');
      var options = {
        key:          D.rp_key || res.rp_key,
        amount:       res.amount,
        currency:     res.currency || 'INR',
        order_id:     res.order_id,
        name:         'Class Next Door',
        description:  res.description || 'Class Booking',
        image:        BASE + 'assets/frontend/img/logo.png',
        prefill:      res.prefill || {},
        theme:        { color: '#FF68B4' },
        handler: function(payment){
          showAlert('Payment successful! Verifying with server…','info');
          postJson('booking/confirm-payment', {
            razorpay_payment_id: payment.razorpay_payment_id,
            razorpay_order_id:   payment.razorpay_order_id,
            razorpay_signature:  payment.razorpay_signature,
          }, function(r){
            if(!r.success){ showAlert(r.message||'Payment confirmation failed.'); return; }
            // If the server provides a redirect_url (it should), use it.
            if(r.redirect_url) {
              window.location.href = r.redirect_url;
            } else {
              // Fallback to modal if no redirect provided
              showAlert('', ''); 
              renderConfirmation(r.booking);
              setStep(2);
              schedulePushPrompt(phone);
            }
          });
        },
        modal: { ondismiss: function(){ showAlert('Payment cancelled. Your booking was not placed.','warning'); } },
      };
      var rzp = new Razorpay(options);
      rzp.open();
  }


  /* ── Render confirmation card ─────────────────── */
  function renderConfirmation(b) {
    if(!b) return;
    var ref  = '#' + String(b.id || 0).padStart(6, '0');
    var amt  = b.amount > 0 ? '₹' + Number(b.amount).toLocaleString('en-IN') : 'Free';
    
    // Schedule string based on type
    var schedule = 'TBD';
    var dtOptions = { day:'numeric', month:'short', year:'numeric' };
    
    if (b.listing_type === 'course' && b.class_date && b.end_date) {
      // Course: show From - To + Timing
      var d1 = new Date(b.class_date).toLocaleDateString('en-IN', dtOptions);
      var d2 = new Date(b.end_date).toLocaleDateString('en-IN', dtOptions);
      schedule = d1 + ' to ' + d2;
      if (b.class_time) schedule += ' &bull; ' + b.class_time.slice(0,5);
    } 
    else if (b.listing_type === 'workshop' && b.class_date) {
      // Workshop: show Date + Timing
      schedule = new Date(b.class_date).toLocaleDateString('en-IN', dtOptions);
      if (b.class_time) schedule += ' &bull; ' + b.class_time.slice(0,5);
    }
    else if (b.class_date) {
      // Regular: show specific date + Timing
      schedule = new Date(b.class_date).toLocaleDateString('en-IN', { weekday:'short', day:'numeric', month:'short' });
      if (b.class_time) schedule += ' &bull; ' + b.class_time.slice(0,5);
    }

    document.getElementById('bConfirmDetails').innerHTML =
      '<div class="mb-2 text-dark"><strong>' + (b.listing_title || D.title) + '</strong></div>' +
      '<div class="mb-1"><i class="bi bi-person-fill me-2 text-pink"></i>' + (b.student_name || '') + '</div>' +
      '<div class="mb-1"><i class="bi bi-calendar-check-fill me-2 text-pink"></i>' + schedule + '</div>' +
      (b.address ? '<div class="mb-1"><i class="bi bi-geo-alt-fill me-2 text-pink"></i>' + b.address + '</div>' : '') +
      '<div class="mb-1 font-weight-bold text-success"><i class="bi bi-credit-card-fill me-2 text-pink"></i>' + amt + '</div>' +
      '<div class="mt-2 pt-2 border-top text-muted small">Booking Reference: ' + ref + '</div>';
  }

  /* ── Sharing Logic ────────────────────────────── */
  
  document.getElementById('shareWa')?.addEventListener('click', function(){
    const text = encodeURIComponent('Check out this class: ' + D.title + '\n' + window.location.href);
    window.open('https://wa.me/?text=' + text, '_blank');
  });

  document.getElementById('shareNative')?.addEventListener('click', function(){
    if (navigator.share) {
      navigator.share({ title: D.title, url: window.location.href }).catch(() => {});
    } else {
      copyToClipboard(window.location.href);
    }
  });

  document.getElementById('shareCopy')?.addEventListener('click', function(){
    copyToClipboard(window.location.href);
  });

  function copyToClipboard(text) {
    navigator.clipboard.writeText(text).then(function() {
      alert('Link copied to clipboard!');
    }, function() {
      alert('Failed to copy link.');
    });
  }

  /* ── Review Submission ──────────────────────────── */
  var starInput = document.getElementById('starInput');
  if (starInput) {
    var stars = starInput.querySelectorAll('i');
    stars.forEach(function(star){
      star.addEventListener('click', function(){
        var val = parseInt(this.dataset.value);
        document.getElementById('reviewRating').value = val;
        stars.forEach(function(s, i){
          if (i < val) {
            s.classList.replace('bi-star', 'bi-star-fill');
          } else {
            s.classList.replace('bi-star-fill', 'bi-star');
          }
        });
      });
      star.addEventListener('mouseover', function(){
        var val = parseInt(this.dataset.value);
        stars.forEach(function(s, i){
           if(i < val) s.style.color = 'var(--cnd-pink)';
           else s.style.color = '';
        });
      });
      star.addEventListener('mouseout', function(){
        var currentVal = parseInt(document.getElementById('reviewRating').value);
        stars.forEach(function(s, i){
           if(i >= currentVal) s.style.color = '';
        });
      });
    });
  }

  document.getElementById('reviewForm')?.addEventListener('submit', function(e){
    e.preventDefault();
    var rating = document.getElementById('reviewRating').value;
    if (rating == 0) { alert('Please select a rating'); return; }
    
    var formData = new FormData(this);
    formData.append('listing_id', D.id);
    var body = {};
    formData.forEach((value, key) => body[key] = value);

    var btn = this.querySelector('button[type="submit"]');
    var originalText = btn.textContent;
    btn.disabled = true;
    btn.textContent = 'Posting...';

    postJson('submit-review', body, function(res){
      if(res.success){
        window.location.reload();
      } else {
        alert(res.message);
        btn.disabled = false;
        btn.textContent = originalText;
      }
    });
  });

  /* ── Email Verification for Contact Reveal ────── */
  
  var vModalEl = document.getElementById('verifyEmailModal');
  var vModal = vModalEl ? new bootstrap.Modal(vModalEl) : null;
  
  document.getElementById('revealContactBtn')?.addEventListener('click', function(){
    setVStep(1);
    vModal.show();
  });

  function setVStep(n){
    document.getElementById('vStep1').classList.toggle('d-none', n !== 1);
    document.getElementById('vStep2').classList.toggle('d-none', n !== 2);
  }

  document.getElementById('vBackBtn')?.addEventListener('click', function(){ setVStep(1); });

  document.getElementById('vSendBtn')?.addEventListener('click', function(){
    var email = document.getElementById('vEmail').value.trim();
    if(!email || !/^\S+@\S+\.\S+$/.test(email)){ alert('Please enter a valid email.'); return; }

    setLoading('vSendBtn', 'vSendSpinner', true);
    
    postJson('api/email/send-otp', { email: email }, function(res){
      setLoading('vSendBtn', 'vSendSpinner', false);
      if(res.success){
        document.getElementById('vDisplayEmail').textContent = email;
        setVStep(2);
      } else { alert(res.message || 'Error sending email. Please try again.'); }
    });
  });

  document.getElementById('vVerifyBtn')?.addEventListener('click', function(){
    var otp   = document.getElementById('vOtp').value.trim();
    var email = document.getElementById('vEmail').value.trim();
    if(!otp || otp.length !== 6){ alert('Enter the 6-digit code sent to your email.'); return; }

    setLoading('vVerifyBtn', 'vVerifySpinner', true);

    postJson('api/email/verify-otp', { email: email, otp: otp }, function(res){
      setLoading('vVerifyBtn', 'vVerifySpinner', false);
      if(res.success){
        vModal.hide();
        location.reload(); // Reload to show contact info
      } else { alert(res.message || 'Invalid or expired code.'); }
    });
  });

  document.getElementById('vResendBtn')?.addEventListener('click', function(){
    var email = document.getElementById('vEmail').value.trim();
    if(!email) return;
    showAlert('Resending...', 'info');
    postJson('api/email/send-otp', { email: email }, function(res){
       if(res.success) alert('Code resent!');
    });
  });

  /* ── Push notification prompt after booking ───── */
  function schedulePushPrompt(ph) {
    // Delay 2 s so the confirmation card is visible first
    setTimeout(function(){
      if(typeof window.cndRequestPush === 'function') {
        window.cndRequestPush(ph);
      }
    }, 2000);
  }

})();
</script>
<?= $this->endSection() ?>

<?= $this->endSection() ?>
