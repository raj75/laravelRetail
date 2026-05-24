{{-- Camera barcode scanner modal (include once per page) --}}
<div class="modal fade" id="barcodeScannerModal" tabindex="-1" aria-labelledby="barcodeScannerLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="barcodeScannerLabel"><i class="bi bi-upc-scan"></i> Scan Barcode</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center">
                <p class="small text-muted mb-2" id="barcodeScannerStatus">Allow camera access when prompted.</p>
                <div id="barcode-qr-reader" style="width:100%;max-width:360px;margin:0 auto;"></div>
                <p class="small text-muted mt-2 mb-0">USB scanners: focus this page and scan — no camera needed.</p>
            </div>
        </div>
    </div>
</div>
<script src="{{ asset('js/barcode-scanner.js') }}"></script>
