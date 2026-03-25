<?= $this->extend('frontend/layout/base') ?>

<?= $this->section('content') ?>

<section class="cnd-hero-mini bg-light py-5">
  <div class="container text-center py-4">
    <h1 class="display-5 fw-bold mb-2">My <span class="text-pink">Payouts</span></h1>
    <p class="text-muted lead">Track earnings and download payout receipts.</p>
  </div>
</section>

<section class="py-5">
  <div class="container">
    <div class="row justify-content-center">
      <div class="col-lg-10">
        
        <?php if (empty($payouts)): ?>
          <div class="text-center py-5">
            <div class="mb-3 text-muted" style="font-size: 3.5rem;"><i class="bi bi-wallet2"></i></div>
            <h5 class="fw-bold">No payout history yet.</h5>
            <p class="text-muted mb-4">Payouts are processed 24-48 hours after a class is completed.</p>
            <a href="<?= base_url('provider/dashboard') ?>" class="btn btn-pink rounded-pill px-5 py-2 fw-bold">Return to Dashboard</a>
          </div>
        <?php else: ?>
          <div class="table-responsive bg-white rounded-4 shadow-sm border p-3">
             <table class="table align-middle">
                <thead class="bg-light">
                   <tr>
                      <th class="border-0 small text-uppercase">Payout ID</th>
                      <th class="border-0 small text-uppercase">Date</th>
                      <th class="border-0 small text-uppercase">Reference</th>
                      <th class="border-0 small text-uppercase">Amount</th>
                      <th class="border-0 small text-uppercase">Status</th>
                      <th class="border-0 small text-uppercase text-end">Receipt</th>
                   </tr>
                </thead>
                <tbody>
                   <?php foreach($payouts as $px): ?>
                   <tr>
                      <td class="small fw-bold text-pink">#<?= esc(strtoupper(substr($px->transfer_id ?? $px->razorpay_id, -8))) ?></td>
                      <td class="small text-muted"><?= date('d M Y', strtotime($px->created_at)) ?></td>
                      <td class="small">Settlement #<?= $px->id ?></td>
                      <td class="fw-bold">₹<?= number_format($px->amount, 2) ?></td>
                      <td>
                        <span class="badge bg-success bg-opacity-10 text-success rounded-pill px-3 py-2 small">COMPLETED</span>
                      </td>
                      <td class="text-end">
                         <button class="btn btn-sm btn-outline-pink rounded-pill btn-receipt-download" 
                           data-id="<?= $px->id ?>" 
                           data-tid="<?= $px->transfer_id ?? $px->razorpay_id ?>" 
                           data-amount="<?= $px->amount ?>" 
                           data-date="<?= date('d M Y', strtotime($px->created_at)) ?>">
                            <i class="bi bi-download"></i> Receipt
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
  </div>
</section>

<style>
:root { --cnd-pink: #FF68B4; --cnd-pink-dark: #FF1493; }
.text-pink { color: var(--cnd-pink); }
.btn-pink { background: var(--cnd-pink); color: #fff; border: none; }
.btn-pink:hover { background: var(--cnd-pink-dark); color: #fff; transform: translateY(-1px); }
.btn-outline-pink { border: 2px solid var(--cnd-pink); color: var(--cnd-pink); font-weight: 600; }
.btn-outline-pink:hover { background: var(--cnd-pink); color: #fff; }
</style>

<?= $this->endSection() ?>

<?= $this->section('js') ?>
<script src="https://unpkg.com/pdf-lib@1.17.1/dist/pdf-lib.min.js"></script>
<script>
document.querySelectorAll('.btn-receipt-download').forEach(function(btn){
  btn.addEventListener('click', function(){
    generateReceipt(this.dataset);
  });
});

async function generateReceipt(data) {
  try {
    const { PDFDocument, rgb, StandardFonts } = PDFLib;
    const pdfDoc = await PDFDocument.create();
    const page   = pdfDoc.addPage([595.28, 841.89]);
    const { width, height } = page.getSize();
    const fontBold = await pdfDoc.embedFont(StandardFonts.HelveticaBold);
    const fontReg  = await pdfDoc.embedFont(StandardFonts.Helvetica);

    page.drawRectangle({ x: 0, y: height - 80, width: width, height: 80, color: rgb(0.1, 0.1, 0.5) });
    page.drawText('CLASS NEXT DOOR', { x: 40, y: height - 50, size: 24, font: fontBold, color: rgb(1, 1, 1) });
    page.drawText('PAYOUT ADVICE / RECEIPT', { x: width - 250, y: height - 50, size: 16, font: fontBold, color: rgb(1, 1, 1) });
    
    let y = height - 130;
    page.drawText('PAYOUT DETAILS', { x: 40, y: y, size: 12, font: fontBold }); y -= 30;
    
    const lines = [
      ['Payout ID:', data.tid],
      ['Settlement Date:', data.date],
      ['Payout Status:', 'TRANSFERRED / SUCCESS'],
      ['Provider Name:', '<?= esc($user->name) ?>'],
      ['Amount:', `INR ${parseFloat(data.amount).toLocaleString('en-IN')}`]
    ];

    lines.forEach(([l, v]) => {
      page.drawText(l, { x: 40, y: y, size: 11, font: fontBold });
      page.drawText(String(v), { x: 180, y: y, size: 11, font: fontReg });
      y -= 25;
    });

    y -= 40;
    page.drawText('Note: This payout is credited to your bank via UPI/IMPS.', { x: 40, y: y, size: 9, font: fontReg, color: rgb(0.5, 0.5, 0.5) });

    const pdfBytes = await pdfDoc.save();
    const blob = new Blob([pdfBytes], { type: 'application/pdf' });
    const link = document.createElement('a');
    link.href = URL.createObjectURL(blob);
    link.download = `Payout_Receipt_${data.id}.pdf`;
    link.click();
  } catch (err) { console.error(err); alert('Receipt generation failed.'); }
}
</script>
<?= $this->endSection() ?>
