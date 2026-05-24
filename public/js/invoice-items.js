/**
 * Invoice line items: totals, search, barcode scan (sale / purchase).
 */
function initInvoiceItems(config) {
    let rowIndex = config.initialRowCount || 1;
    const searchUrl = config.searchUrl;
    const barcodeUrl = config.barcodeUrl;
    const invoiceType = config.invoiceType || 'sale';
    const isPurchase = ['purchase', 'purchase_order', 'debit_note'].includes(invoiceType);

    function itemRate(item) {
        return isPurchase ? item.purchase_price : item.sale_price;
    }

    function calcRow(row) {
        const qty = parseFloat(row.querySelector('.qty')?.value) || 0;
        const rate = parseFloat(row.querySelector('.rate')?.value) || 0;
        const disc = parseFloat(row.querySelector('.disc')?.value) || 0;
        const gst = parseFloat(row.querySelector('.gst')?.value) || 0;
        const taxable = (qty * rate) - disc;
        const isInter = document.querySelector('[name=is_inter_state]')?.checked;
        const tax = isInter ? taxable * gst / 100 : taxable * gst / 50 / 100 * 2;
        const amt = taxable + tax;
        row.querySelector('.line-amount').textContent = amt.toFixed(2);
        return amt;
    }

    function calcTotal() {
        let t = 0;
        document.querySelectorAll('.item-row').forEach((r) => { t += calcRow(r); });
        const invDisc = parseFloat(document.querySelector('[name=discount_amount]')?.value) || 0;
        const el = document.getElementById('grandTotal');
        if (el) el.textContent = '₹' + Math.max(0, t - invDisc).toFixed(2);
    }

    function fillRowFromItem(row, item) {
        row.querySelector('.item-id').value = item.id;
        row.querySelector('.item-search').value = item.name;
        row.querySelector('.rate').value = itemRate(item);
        row.querySelector('.gst').value = item.gst_rate;
        row.querySelector('.hsn').value = item.hsn_code || '';
        const qty = row.querySelector('.qty');
        if (!qty.value || qty.value === '0') qty.value = 1;
        calcTotal();
    }

    function createRow() {
        const tbody = document.getElementById('itemRows');
        const tr = document.createElement('tr');
        tr.className = 'item-row';
        tr.innerHTML = `
            <td>
                <input type="hidden" name="items[${rowIndex}][item_id]" class="item-id">
                <div class="input-group input-group-sm">
                    <input type="text" name="items[${rowIndex}][description]" class="form-control item-search" placeholder="Search or scan…" autocomplete="off">
                    <button type="button" class="btn btn-outline-secondary btn-scan-row" title="Scan"><i class="bi bi-upc-scan"></i></button>
                </div>
            </td>
            <td><input name="items[${rowIndex}][hsn_code]" class="form-control form-control-sm hsn"></td>
            <td><input name="items[${rowIndex}][quantity]" type="number" step="0.001" class="form-control form-control-sm qty" value="1"></td>
            <td><input name="items[${rowIndex}][rate]" type="number" step="0.01" class="form-control form-control-sm rate" value="0"></td>
            <td><input name="items[${rowIndex}][discount]" type="number" step="0.01" class="form-control form-control-sm disc" value="0"></td>
            <td><input name="items[${rowIndex}][gst_rate]" type="number" step="0.01" class="form-control form-control-sm gst" value="18"></td>
            <td class="line-amount align-middle">0.00</td>
            <td><button type="button" class="btn btn-sm btn-outline-danger remove-row">&times;</button></td>`;
        tbody.appendChild(tr);
        rowIndex++;
        bindRow(tr);
        return tr;
    }

    async function applyBarcode(code, targetRow) {
        try {
            const item = await LaravelRetailBarcode.lookupBarcode(barcodeUrl, code);
            let row = targetRow;
            if (!row) {
                row = createRow();
            }
            fillRowFromItem(row, item);
            showBarcodeToast('Added: ' + item.name, 'success');
            return row;
        } catch (err) {
            showBarcodeToast(err.message || 'Not found', 'danger');
            return null;
        }
    }

    function showBarcodeToast(msg, type) {
        const el = document.getElementById('barcodeToast');
        if (!el) {
            alert(msg);
            return;
        }
        el.className = 'alert alert-' + type + ' py-2 small mb-2';
        el.textContent = msg;
        el.classList.remove('d-none');
        setTimeout(() => el.classList.add('d-none'), 3500);
    }

    function bindRow(row) {
        row.querySelectorAll('input').forEach((inp) => inp.addEventListener('input', calcTotal));
        row.querySelector('.remove-row')?.addEventListener('click', () => { row.remove(); calcTotal(); });

        row.querySelector('.btn-scan-row')?.addEventListener('click', () => {
            LaravelRetailBarcode.openCamera((code) => applyBarcode(code, row));
        });

        const search = row.querySelector('.item-search');
        let timer;
        search?.addEventListener('input', function () {
            clearTimeout(timer);
            const q = this.value.trim();
            if (q.length < 2) return;
            timer = setTimeout(async () => {
                const res = await fetch(searchUrl + '?q=' + encodeURIComponent(q));
                const items = await res.json();
                if (!items.length) return;
                fillRowFromItem(row, items[0]);
            }, 300);
        });
    }

    document.getElementById('addRow')?.addEventListener('click', () => { createRow(); calcTotal(); });

    document.getElementById('btnScanBarcode')?.addEventListener('click', () => {
        LaravelRetailBarcode.openCamera((code) => applyBarcode(code, null));
    });

    document.querySelectorAll('.item-row').forEach((row) => {
        if (!row.querySelector('.btn-scan-row')) {
            const search = row.querySelector('.item-search');
            if (search && search.parentElement && !search.parentElement.classList.contains('input-group')) {
                const wrap = document.createElement('div');
                wrap.className = 'input-group input-group-sm';
                search.parentNode.insertBefore(wrap, search);
                wrap.appendChild(search);
                const btn = document.createElement('button');
                btn.type = 'button';
                btn.className = 'btn btn-outline-secondary btn-scan-row';
                btn.title = 'Scan';
                btn.innerHTML = '<i class="bi bi-upc-scan"></i>';
                wrap.appendChild(btn);
            }
        }
        bindRow(row);
    });

    document.querySelector('[name=is_inter_state]')?.addEventListener('change', calcTotal);
    document.querySelector('[name=discount_amount]')?.addEventListener('input', calcTotal);

    LaravelRetailBarcode.enableWedge((code) => applyBarcode(code, null));

    window.addEventListener('beforeunload', () => LaravelRetailBarcode.disableWedge());

    calcTotal();

    return { applyBarcode, calcTotal };
}
