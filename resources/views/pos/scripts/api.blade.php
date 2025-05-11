<script>
    // ===============================================================
    // API Functions
    // ===============================================================

    /**
     * Get product by barcode
     */
    async function getProduct(barcode) {
        try {
            const response = await fetch(`{{ route('pos.get-product') }}?barcode=${barcode}`);
            const data = await response.json();

            if (!response.ok) {
                showErrorModal(data.message || 'Failed to retrieve product data');
                return;
            }

            if (!data.results || data.results.length === 0) {
                showErrorModal('Product not found');
                return;
            }

            const productResult = data.results[0];
            const product = productResult.product_data;

            productDetails[product.id] = product;
            addToCart(product);

        } catch (error) {
            console.error('Error:', error);
            showErrorModal('An error occurred while fetching product data');
        }
    }

    /**
     * Add product from search results
     */
    function addProductFromSearch(product) {
        const formattedProduct = {
            id: product.id,
            name: product.name,
            barcode: product.barcode,
            available_units: product.available_units,
            tax: product.tax,
            discount: product.discount,
            default_unit: product.default_unit
        };

        productDetails[product.id] = formattedProduct;
        addToCart(formattedProduct);
    }

    /**
     * Save transaction to server
     */
    async function saveTransaction() {
        if (cart.length === 0) {
            showErrorModal('Keranjang masih kosong!');
            return;
        }

        const paymentType = document.getElementById('pos_payment_type').value;

        // Prepare the transaction data
        const transactionData = {
            invoice_number: document.getElementById('pos_invoice_number').value,
            store_id: document.getElementById('pos_store_id').value,
            customer_id: document.getElementById('pos_customer_id').value,
            items: cart,
            payment_type: paymentType,
            total_amount: parseFloat(document.getElementById('pos_subtotal').value.replace(/[^0-9.-]+/g, "")),
            tax_amount: parseFloat(document.getElementById('pos_tax_amount').value.replace(/[^0-9.-]+/g, "")),
            discount_amount: parseFloat(document.getElementById('pos_discount_amount').value.replace(/[^0-9.-]+/g, "")),
            final_amount: parseFloat(document.getElementById('pos_final_amount').value.replace(/[^0-9.-]+/g, "")),
            pending_transaction_id: pendingTransactionId,
            status: 'success',
            cash_amount: parseFloat(document.getElementById('pos_cash_amount').value || 0)
        };

        try {
            const response = await fetch('{{ route('pos.store') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify(transactionData)
            });

            const result = await response.json();

            if (result.success) {
                showSuccessModal('Transaksi berhasil disimpan!', () => {
                    window.open(
                        `{{ url('pos/invoice') }}/${result.transaction_id}`,
                        'InvoiceWindow',
                        'width=400,height=600');
                    window.location.reload();
                });
            } else {
                showErrorModal(result.message);
            }
        } catch (error) {
            console.error('Error:', error);
            showErrorModal('Terjadi kesalahan saat menyimpan transaksi');
        }
    }

    /**
     * Save transaction as pending
     */
    async function saveAsPending() {
        if (cart.length === 0) {
            showErrorModal('Keranjang masih kosong!');
            return;
        }

        try {
            const data = {
                invoice_number: document.getElementById('pos_invoice_number').value,
                store_id: document.getElementById('pos_store_id').value,
                customer_id: document.getElementById('pos_customer_id').value,
                items: cart,
                payment_type: document.getElementById('pos_payment_type').value,
                total_amount: parseFloat(document.getElementById('pos_subtotal').value.replace(/[^0-9.-]+/g, "")),
                tax_amount: parseFloat(document.getElementById('pos_tax_amount').value.replace(/[^0-9.-]+/g, "")),
                discount_amount: parseFloat(document.getElementById('pos_discount_amount').value.replace(/[^0-9.-]+/g, "")),
                final_amount: parseFloat(document.getElementById('pos_final_amount').value.replace(/[^0-9.-]+/g, "")),
                pending_transaction_id: pendingTransactionId,
                status: 'pending',
                cash_amount: parseFloat(document.getElementById('pos_cash_amount').value || 0)
            };

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
                showSuccessModal('Transaksi Pending berhasil disimpan', () => {
                    window.location.href = '{{ route('pos.index') }}';
                });
            } else {
                showErrorModal(result.message || 'Terjadi kesalahan saat menyimpan transaksi');
            }
        } catch (error) {
            console.error('Error:', error);
            showErrorModal('Terjadi kesalahan saat menyimpan transaksi');
        }
    }

    /**
     * Show pending transactions in modal
     */
    async function showPendingTransactions() {
        try {
            const response = await fetch('{{ route('transactions.index') }}?' + new URLSearchParams({
                status: 'pending'
            }), {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });

            const result = await response.json();
            const tbody = document.querySelector('#pending-transactions-table tbody');
            tbody.innerHTML = '';

            if (result.data && result.data.length > 0) {
                result.data.forEach(transaction => {
                    const tr = document.createElement('tr');
                    tr.innerHTML = `
                    <td>${transaction.invoice_number}</td>
                    <td>${transaction.invoice_date_formatted}</td>
                    <td>${transaction.customer_name}</td>
                    <td>${transaction.final_amount_formatted}</td>
                    <td>
                        <a href="{{ url('transactions') }}/${transaction.id}/continue"
                           class="btn btn-primary btn-sm">
                            Lanjutkan
                        </a>
                    </td>
                `;
                    tbody.appendChild(tr);
                });
            } else {
                tbody.innerHTML = `
                <tr>
                    <td colspan="5" class="text-center">
                        Tidak ada transaksi pending
                    </td>
                </tr>
            `;
            }

            const modal = new bootstrap.Modal(document.getElementById('pendingTransactionsModal'));
            modal.show();
        } catch (error) {
            console.error('Error:', error);
            showErrorModal('Terjadi kesalahan saat mengambil data transaksi pending');
        }
    }
</script>
