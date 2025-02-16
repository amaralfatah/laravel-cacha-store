<script>
    console.log('POS script loaded');

    let cart = [];
    let productDetails = {};
    let pendingTransactionId = null;
    // Add this to the beginning of your POS page JavaScript, after cart initialization
    // Check if there's cart data from a pending transaction
    const cartData = @json(session('cart_data'));
    if (cartData) {
        pendingTransactionId = cartData.pending_transaction_id;

        // Set invoice number
        document.getElementById('invoice_number').value = cartData.invoice_number;

        // Set customer
        document.getElementById('customer_id').value = cartData.customer_id;

        // Set payment type and reference number
        document.getElementById('payment_type').value = cartData.payment_type;
        if (cartData.payment_type === 'transfer') {
            document.getElementById('reference_number').value = cartData.reference_number;
            document.getElementById('reference_number_container').style.display = 'block';
        }

        // Load cart items
        cart = cartData.items;
        cart.forEach(item => {
            productDetails[item.product_id] = {
                id: item.product_id,
                name: item.product_name,
                available_units: item.available_units
            };
        });

        // Update display
        updateCartTable();
        calculateTotals();
    }

    document.getElementById('barcode').addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            getProduct(this.value);
            this.value = '';
        }
    });

    document.getElementById('payment_type').addEventListener('change', function() {
        const refContainer = document.getElementById('reference_number_container');
        refContainer.style.display = this.value === 'transfer' ? 'block' : 'none';
    });

    async function getProduct(barcode) {
        try {
            const response = await fetch(`{{ route('pos.get-product') }}?barcode=${barcode}`);
            const data = await response.json();

            if (response.ok) {
                // Simpan detail produk untuk referensi
                productDetails[data.id] = data;
                addToCart(data);
            } else {
                alert(data.message);
            }
        } catch (error) {
            console.error('Error:', error);
            alert('Terjadi kesalahan saat mengambil data produk');
        }
    }

    function addToCart(product) {
        const defaultUnit = product.available_units.find(unit =>
            unit.unit_id === product.default_unit_id
        );

        if (!defaultUnit) {
            alert('Produk tidak memiliki unit default!');
            return;
        }

        const newItem = {
            product_id: product.id,
            product_name: product.name,
            unit_id: product.default_unit_id,
            unit_name: defaultUnit.unit_name,
            available_units: product.available_units,
            quantity: 1,
            unit_price: parseFloat(defaultUnit.selling_price),
            tax_rate: product.tax ? parseFloat(product.tax.rate) : 0,
            discount: product.discount ? calculateDiscount(product) : 0
        };

        showUnitSelectionModal(newItem, (selectedUnit) => {
            const selectedUnitId = parseInt(selectedUnit);
            const existingItemWithUnit = cart.find(item =>
                item.product_id === product.id &&
                item.unit_id === selectedUnitId
            );

            if (existingItemWithUnit) {
                existingItemWithUnit.quantity = parseFloat(existingItemWithUnit.quantity) + 1;
                calculateItemSubtotal(existingItemWithUnit);
            } else {
                newItem.unit_id = selectedUnitId;
                const unit = product.available_units.find(u => u.unit_id === selectedUnitId);
                newItem.unit_name = unit.unit_name;
                newItem.unit_price = parseFloat(unit.selling_price);
                calculateItemSubtotal(newItem);
                cart.push(newItem);
            }

            updateCartTable();
            calculateTotals();
        });
    }

    function showUnitSelectionModal(item, callback) {
        // Buat modal HTML
        const modalHtml = `
            <div class="modal fade" id="unitSelectionModal" tabindex="-1">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Pilih Unit untuk ${item.product_name}</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <div class="form-group">
                                <label>Unit</label>
                                <select class="form-select" id="unit_selection">
                                    ${item.available_units.map(unit => `
                                                            <option value="${unit.unit_id}" ${unit.unit_id === item.unit_id ? 'selected' : ''}>${unit.unit_name}</option>
                                                            `).join('')}
                                </select>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                            <button type="button" class="btn btn-primary" id="confirmUnitSelection">Pilih</button>
                        </div>
                    </div>
                </div>
            </div>
        `;

        // Hapus modal lama jika ada
        const existingModal = document.getElementById('unitSelectionModal');
        if (existingModal) {
            existingModal.remove();
        }

        // Tambahkan modal ke body
        document.body.insertAdjacentHTML('beforeend', modalHtml);

        // Initialize modal
        const modal = new bootstrap.Modal(document.getElementById('unitSelectionModal'));
        modal.show();

        // Handle konfirmasi
        document.getElementById('confirmUnitSelection').addEventListener('click', function() {
            const selectedUnit = document.getElementById('unit_selection').value;
            modal.hide();
            callback(selectedUnit);
        });

        // Cleanup setelah modal tertutup
        document.getElementById('unitSelectionModal').addEventListener('hidden.bs.modal', function() {
            this.remove();
        });
    }

    function getUnitPrice(product, quantity, unitId) {
        // 1. Temukan unit yang sesuai
        const productUnit = product.available_units.find(unit => unit.unit_id === unitId);
        if (!productUnit) return 0;

        // 2. Cek price tiers jika ada
        if (productUnit.prices && productUnit.prices.length > 0) {
            const applicableTier = productUnit.prices.find(price =>
                quantity >= parseFloat(price.min_quantity)
            );
            if (applicableTier) {
                return parseFloat(applicableTier.price);
            }
        }

        // 3. Gunakan selling_price dari unit
        return parseFloat(productUnit.selling_price);
    }

    function calculateDiscount(product) {
        if (!product.discount) return 0;

        const defaultUnit = product.available_units.find(unit =>
            unit.unit_id === product.default_unit_id
        );
        const basePrice = defaultUnit ? parseFloat(defaultUnit.selling_price) : 0;

        if (product.discount.type === 'percentage') {
            return basePrice * parseFloat(product.discount.value) / 100;
        }
        return parseFloat(product.discount.value);
    }


    function calculateItemSubtotal(item) {
        const quantity = parseFloat(item.quantity) || 0;
        const unitPrice = parseFloat(item.unit_price) || 0;
        const discount = parseFloat(item.discount) || 0;

        const baseSubtotal = quantity * unitPrice;
        item.subtotal = baseSubtotal - (discount * quantity);
        return item.subtotal;
    }

    function updateCartTable() {
        const tbody = document.querySelector('#cart-table tbody');
        tbody.innerHTML = '';

        cart.forEach((item, index) => {
            const tr = document.createElement('tr');
            const unit = item.available_units.find(u => u.unit_id === item.unit_id);

            // Hanya tampilkan conversion info jika conversion factor > 1
            const conversionInfo = parseFloat(unit.conversion_factor) > 1
                ? `(1 ${unit.unit_name} = ${unit.conversion_factor} ${unit.unit_name})`
                : '';

            tr.innerHTML = `
            <td>
                ${item.product_name}
                <br>
                <small class="text-muted">${conversionInfo}</small>
            </td>
            <td>
                <select class="form-select form-select-sm"
                        onchange="updateUnit(${index}, this.value)">
                    ${item.available_units.map(unit => `
                        <option value="${unit.unit_id}"
                            ${unit.unit_id === item.unit_id ? 'selected' : ''}>
                            ${unit.unit_name}
                        </option>
                    `).join('')}
                </select>
            </td>
            <td>
                <input type="number"
                       class="form-control form-control-sm"
                       value="${parseFloat(item.quantity)}"
                       step="1"
                       min="1"
                       onchange="updateQuantity(${index}, this.value)">
            </td>
            <td>${formatCurrency(item.unit_price)}</td>
            <td>${formatCurrency(item.discount)}</td>
            <td>${formatCurrency(item.subtotal)}</td>
            <td>
                <button class="btn btn-danger btn-sm" onclick="removeItem(${index})">
                    Hapus
                </button>
            </td>
        `;
            tbody.appendChild(tr);
        });
    }

    function updateQuantity(index, newQuantity) {
        const item = cart[index];
        const product = productDetails[item.product_id];

        // Ensure quantity is treated as a number
        item.quantity = parseFloat(newQuantity);
        item.unit_price = getUnitPrice(product, item.quantity, item.unit_id);

        calculateItemSubtotal(item);
        updateCartTable();
        calculateTotals();
    }

    function updateUnit(index, newUnitId) {
        const item = cart[index];
        const product = productDetails[item.product_id];
        const unit = product.available_units.find(u => u.unit_id === parseInt(newUnitId));

        item.unit_id = parseInt(newUnitId);
        item.unit_name = unit.unit_name;
        item.unit_price = getUnitPrice(product, item.quantity, item.unit_id);

        calculateItemSubtotal(item);
        updateCartTable();
        calculateTotals();
    }


    function removeItem(index) {
        cart.splice(index, 1);
        updateCartTable();
        calculateTotals();
    }

    function calculateTotals() {
        const subtotal = cart.reduce((sum, item) => {
            return sum + (parseFloat(item.subtotal) || 0);
        }, 0);

        const taxAmount = cart.reduce((sum, item) => {
            const itemTax = (parseFloat(item.subtotal) || 0) * (parseFloat(item.tax_rate) || 0) / 100;
            return sum + itemTax;
        }, 0);

        const discountAmount = cart.reduce((sum, item) => {
            return sum + ((parseFloat(item.discount) || 0) * (parseFloat(item.quantity) || 0));
        }, 0);

        const finalAmount = subtotal + taxAmount;

        document.getElementById('subtotal').value = formatCurrency(subtotal);
        document.getElementById('tax_amount').value = formatCurrency(taxAmount);
        document.getElementById('discount_amount').value = formatCurrency(discountAmount);
        document.getElementById('final_amount').value = formatCurrency(finalAmount);
    }

    function formatCurrency(amount) {
        return new Intl.NumberFormat('id-ID', {
            style: 'currency',
            currency: 'IDR'
        }).format(amount);
    }

    document.getElementById('btn-save').addEventListener('click', async function() {
        if (cart.length === 0) {
            alert('Keranjang masih kosong!');
            return;
        }

        const paymentType = document.getElementById('payment_type').value;
        const referenceNumber = document.getElementById('reference_number').value;

        if (paymentType === 'transfer' && !referenceNumber) {
            alert('Nomor referensi harus diisi untuk pembayaran transfer!');
            return;
        }

        const data = {
            invoice_number: document.getElementById('invoice_number').value,
            store_id: document.getElementById('store_id').value,
            customer_id: document.getElementById('customer_id').value,
            items: cart,
            payment_type: paymentType,
            reference_number: referenceNumber,
            total_amount: parseFloat(document.getElementById('subtotal').value.replace(/[^0-9.-]+/g, "")),
            tax_amount: parseFloat(document.getElementById('tax_amount').value.replace(/[^0-9.-]+/g, "")),
            discount_amount: parseFloat(document.getElementById('discount_amount').value.replace(/[^0-9.-]+/g, "")),
            final_amount: parseFloat(document.getElementById('final_amount').value.replace(/[^0-9.-]+/g, "")),
            pending_transaction_id: pendingTransactionId,
            status: 'success'
        };

        try {
            const response = await fetch('{{ route('pos.store') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify(data)
            });

            const result = await response.json();

            if (result.success) {
                alert('Transaksi berhasil disimpan!');
                window.open(`{{ url('pos/invoice') }}/${result.transaction_id}`, '_blank');
                window.location.reload();
            } else {
                alert(result.message);
            }
        } catch (error) {
            console.error('Error:', error);
            alert('Terjadi kesalahan saat menyimpan transaksi');
        }
    });

    // Tambahkan pencarian produk manual
    document.getElementById('search_product').addEventListener('input', async function() {
        const productList = document.getElementById('product_list');

        if (this.value.length >= 3) {
            try {
                const response = await fetch(`{{ route('pos.search-product') }}?search=${this.value}`);
                const products = await response.json();

                productList.innerHTML = '';

                if (products.length > 0) {
                    productList.style.display = 'block'; // Tampilkan list

                    products.forEach(product => {
                        const div = document.createElement('div');
                        div.className = 'product-item';
                        div.textContent = `${product.name} - ${product.barcode}`;
                        div.onclick = () => {
                            getProduct(product.barcode);
                            productList.style.display = 'none'; // Sembunyikan list
                            this.value = '';
                        };
                        productList.appendChild(div);
                    });
                } else {
                    productList.style.display = 'none';
                }
            } catch (error) {
                console.error('Error:', error);
                productList.style.display = 'none';
            }
        } else {
            productList.style.display = 'none';
        }
    });

    console.log('POS script loaded');
</script>
<script>
    document.getElementById('btn-pending').addEventListener('click', async function() {
        if (cart.length === 0) {
            alert('Keranjang masih kosong!');
            return;
        }

        const data = {
            invoice_number: document.getElementById('invoice_number').value,
            store_id: document.getElementById('store_id').value,
            customer_id: document.getElementById('customer_id').value,
            items: cart,
            payment_type: paymentType,
            reference_number: referenceNumber,
            total_amount: parseFloat(document.getElementById('subtotal').value.replace(/[^0-9.-]+/g, "")),
            tax_amount: parseFloat(document.getElementById('tax_amount').value.replace(/[^0-9.-]+/g, "")),
            discount_amount: parseFloat(document.getElementById('discount_amount').value.replace(/[^0-9.-]+/g, "")),
            final_amount: parseFloat(document.getElementById('final_amount').value.replace(/[^0-9.-]+/g, "")),
            pending_transaction_id: pendingTransactionId,
            status: 'success'
        };

        try {
            const response = await fetch('{{ route('pos.store') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify(data)
            });

            const result = await response.json();

            if (result.success) {
                alert('Transaksi berhasil disimpan sebagai draft!');
                window.location.href = '{{ route('transactions.index') }}';
            } else {
                alert(result.message);
            }
        } catch (error) {
            console.error('Error:', error);
            alert('Terjadi kesalahan saat menyimpan transaksi');
        }
    });
</script>
