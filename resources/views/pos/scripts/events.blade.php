<script>
    // ===============================================================
    // Event Handlers
    // ===============================================================

    /**
     * Setup all event listeners
     */
    function setupEventListeners() {
        // Barcode scanner handling - replace the keypress event with input event
        const barcodeInput = document.getElementById('pos_barcode');

        // Add debounce to prevent multiple rapid-fire requests
        let barcodeTimer;
        const BARCODE_DELAY = 800; // Time in ms to wait after last character before submitting

        barcodeInput.addEventListener('input', function() {
            const barcode = this.value.trim();

            // Clear any pending timer
            clearTimeout(barcodeTimer);

            // Set a new timer for this input
            if (barcode.length >= 4) { // Only process if reasonable length for a barcode
                barcodeTimer = setTimeout(() => {
                    getProduct(barcode);
                    this.value = ''; // Clear the input after processing
                    // Refocus the input for the next scan
                    this.focus();
                }, BARCODE_DELAY);
            }
        });

        // If user presses Enter, process immediately
        barcodeInput.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                clearTimeout(barcodeTimer); // Cancel any pending timer

                const barcode = this.value.trim();
                if (barcode.length > 0) {
                    getProduct(barcode);
                    this.value = '';
                }
            }
        });

        // Clear timer if user clicks elsewhere or tabs out
        barcodeInput.addEventListener('blur', function() {
            clearTimeout(barcodeTimer);
        });

        // Rest of your event bindings remain the same
        document.getElementById('pos_search_product').addEventListener('input', handleProductSearch);
        document.getElementById('pos_search_product').addEventListener('keydown', function(e) {
            const productList = document.getElementById('pos_product_list');
            if (productList.style.display === 'block') {
                handleProductListNavigation(e, productList);
            }
        });

        // Start shopping button
        document.getElementById('btn-start-shopping')?.addEventListener('click', function() {
            document.getElementById('pos_search_product').focus();
        });

        // Transaction action buttons
        document.getElementById('btn-save')?.addEventListener('click', saveTransaction);
        document.getElementById('btn-pending')?.addEventListener('click', saveAsPending);
        document.getElementById('btn-show-pending')?.addEventListener('click', showPendingTransactions);
        document.getElementById('btn-clear-cart')?.addEventListener('click', clearCart);

        // Observe cart changes for UI updates
        const cartTableBody = document.querySelector('#cart-table tbody');
        if (cartTableBody) {
            const observer = new MutationObserver(updateEmptyCartMessage);
            observer.observe(cartTableBody, {
                childList: true
            });

            const rowObserver = new MutationObserver(enhanceTableRows);
            rowObserver.observe(cartTableBody, {
                childList: true
            });
        }

        // Pending transactions modal keyboard navigation
        document.getElementById('pendingTransactionsModal').addEventListener('shown.bs.modal',
            setupModalKeyboardNavigation);

        // Cash amount input for calculating change
        document.getElementById('pos_cash_amount').addEventListener('input', calculateChange);

        document.getElementById('btn-camera')?.addEventListener('click', function() {
            openBarcodeScanner();
        });
    }

    /**
     * Handle payment type change
     */
    function initializePaymentTypeHandling() {
        // Payment type change handler
        document.getElementById('pos_payment_type').addEventListener('change', function() {
            const isCash = this.value === 'cash';
            document.getElementById('pos_cash_amount_container').style.display = isCash ? 'block' : 'none';
            document.getElementById('pos_change_container').style.display = isCash ? 'block' : 'none';

            // Reset cash amount if not cash payment
            if (!isCash) {
                document.getElementById('pos_cash_amount').value = '';
                document.getElementById('pos_change').value = '';
            }
        });

        // Initialize display based on current payment type
        const event = new Event('change');
        document.getElementById('pos_payment_type').dispatchEvent(event);
    }

    /**
     * Handle product search input
     */
    async function handleProductSearch() {
        const productList = document.getElementById('pos_product_list');

        if (this.value.length >= 3) {
            try {
                const response = await fetch(`{{ route('pos.search-product') }}?search=${this.value}`);
                const result = await response.json();

                productList.innerHTML = '';

                if (result.success && result.data.length > 0) {
                    productList.style.display = 'block';

                    result.data.forEach(product => {
                        const defaultUnit = product.available_units.find(unit => unit.is_default === 1);
                        if (defaultUnit) {
                            const div = document.createElement('div');
                            div.className = 'product-item';
                            div.innerHTML = `
                            ${product.name}
                            <br>
                            <small class="text-muted">
                                ${product.barcode || 'No Barcode'} - Stock: ${defaultUnit.stock}
                                - ${formatCurrency(defaultUnit.selling_price)}
                            </small>
                        `;
                            div.onclick = () => {
                                addProductFromSearch(product);
                                productList.style.display = 'none';
                                this.value = '';
                            };
                            productList.appendChild(div);
                        }
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
    }

    /**
     * Handle keyboard navigation in product list
     */
    function handleProductListNavigation(e, productList) {
        const items = productList.querySelectorAll('.product-item');
        const activeItem = productList.querySelector('.product-item.active');
        let nextActive = null;

        switch (e.key) {
            case 'ArrowDown':
                e.preventDefault();
                if (!activeItem) {
                    nextActive = items[0];
                } else {
                    const currentIndex = Array.from(items).indexOf(activeItem);
                    nextActive = items[currentIndex + 1] || items[0];
                }
                break;

            case 'ArrowUp':
                e.preventDefault();
                if (!activeItem) {
                    nextActive = items[items.length - 1];
                } else {
                    const currentIndex = Array.from(items).indexOf(activeItem);
                    nextActive = items[currentIndex - 1] || items[items.length - 1];
                }
                break;

            case 'Enter':
                e.preventDefault();
                if (activeItem) {
                    activeItem.click();
                }
                break;
        }

        if (nextActive) {
            if (activeItem) activeItem.classList.remove('active');
            nextActive.classList.add('active');
            nextActive.scrollIntoView({
                block: 'nearest'
            });
        }
    }

    /**
     * BARU
     */

    /**
     * BARU
     */

    /**
     * BARU
     */

    /**
     * Open barcode scanner modal and initialize scanner
     */
    function openBarcodeScanner() {
        // Create modal if it doesn't exist
        if (!document.getElementById('barcodeScannerModal')) {
            createBarcodeScannerModal();
        }

        // Open the modal
        const modal = new bootstrap.Modal(document.getElementById('barcodeScannerModal'));
        modal.show();

        // Initialize the scanner after modal is shown
        document.getElementById('barcodeScannerModal').addEventListener('shown.bs.modal', function() {
            initBarcodeScanner();
        }, {
            once: true
        });
    }

    /**
     * Create the barcode scanner modal
     */
    function createBarcodeScannerModal() {
        const modalHtml = `
    <div class="modal fade" id="barcodeScannerModal" tabindex="-1" aria-labelledby="barcodeScannerModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content" style="border: 2px solid #919b9c; border-radius: 0; box-shadow: 3px 3px 5px rgba(0,0,0,0.3);">
                <div class="modal-header" style="background: linear-gradient(to bottom, #4f6acc 0%, #2a3c8e 100%); color: white; padding: 6px 10px; border-bottom: 1px solid #919b9c;">
                    <h5 class="modal-title" id="barcodeScannerModalLabel">
                        <i class='bx bx-scan me-1'></i> Scan Barcode
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" style="background-color: #ece9d8; padding: 10px;">
                    <div class="text-center mb-3">
                        <p>Arahkan kamera ke barcode produk</p>
                    </div>

                    <div class="scanner-wrapper">
                        <!-- Loading indicator -->
                        <div id="scanner-loading" class="text-center p-3">
                            <div class="spinner-border text-primary" role="status">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                            <p class="mt-2">Memuat kamera...</p>
                        </div>

                        <!-- Error message -->
                        <div id="scanner-error" class="alert alert-danger text-center" style="display:none;">
                            <i class='bx bx-error-circle me-1'></i>
                            <span id="scanner-error-message">Error message here</span>
                        </div>

                        <!-- Scanner container -->
                        <div id="scanner-container" style="display:none; border: 2px solid #919b9c; min-height: 300px;">
                            <div id="qr-reader" style="width: 100%;"></div>
                        </div>

                        <!-- Success message -->
                        <div id="scanner-success" class="alert alert-success mt-2" style="display:none;">
                            <strong>Barcode terdeteksi:</strong> <span id="detected-barcode"></span>
                        </div>
                    </div>

                    <!-- Camera controls -->
                    <div class="d-flex justify-content-between mt-3">
                        <select id="camera-select" class="form-select" style="max-width: 250px; display:none;">
                            <option value="">Pilih Kamera</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer" style="background-color: #ece9d8; border-top: 1px solid #919b9c; padding: 8px;">
                    <button type="button" id="btn-scan-cancel" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                        <i class='bx bx-x me-1'></i> Batal
                    </button>
                    <button type="button" id="btn-scan-manual" class="btn btn-primary">
                        <i class='bx bx-keyboard me-1'></i> Input Manual
                    </button>
                </div>
            </div>
        </div>
    </div>
    `;

        // Add modal to body
        document.body.insertAdjacentHTML('beforeend', modalHtml);

        // Add event listeners to buttons
        document.getElementById('btn-scan-manual').addEventListener('click', function() {
            const modal = bootstrap.Modal.getInstance(document.getElementById('barcodeScannerModal'));
            if (modal) {
                modal.hide();
            }

            setTimeout(() => {
                document.getElementById('pos_barcode').focus();
            }, 300);
        });

        // Camera select handler
        document.getElementById('camera-select').addEventListener('change', function() {
            if (window.barcodeScanner) {
                window.barcodeScanner.stop();
                initBarcodeScanner(this.value);
            }
        });

        // Cleanup when modal is closed
        document.getElementById('barcodeScannerModal').addEventListener('hidden.bs.modal', function() {
            if (window.barcodeScanner) {
                window.barcodeScanner.stop();
                window.barcodeScanner = null;
            }
        });
    }

    /**
     * Initialize the barcode scanner
     */
    async function initBarcodeScanner(cameraId) {
        try {
            // Show loading
            document.getElementById('scanner-loading').style.display = 'block';
            document.getElementById('scanner-container').style.display = 'none';
            document.getElementById('scanner-error').style.display = 'none';
            document.getElementById('scanner-success').style.display = 'none';

            // Load the HTML5-QRCode library if not already loaded
            if (typeof Html5Qrcode === 'undefined') {
                await loadHtml5QrcodeScript();
            }

            // Get camera list
            const cameras = await getCameraList();
            updateCameraDropdown(cameras, cameraId);

            // Get the selected camera ID
            let selectedCameraId = cameraId;
            if (!selectedCameraId && cameras.length > 0) {
                // Try to find back camera
                const backCamera = cameras.find(camera =>
                    camera.label.toLowerCase().includes('back') ||
                    camera.label.toLowerCase().includes('rear') ||
                    camera.label.toLowerCase().includes('belakang')
                );

                selectedCameraId = backCamera ? backCamera.id : cameras[0].id;
            }

            // Create and start scanner
            startScanner(selectedCameraId);
        } catch (error) {
            console.error('Error initializing barcode scanner:', error);
            showScannerError(
                'Gagal mengakses kamera. Pastikan kamera diizinkan dan browser Anda mendukung akses kamera. Error: ' +
                error.message);
        }
    }

    /**
     * Load the Html5-QRCode script
     */
    function loadHtml5QrcodeScript() {
        return new Promise((resolve, reject) => {
            const script = document.createElement('script');
            script.src = 'https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js';
            script.onload = resolve;
            script.onerror = reject;
            document.head.appendChild(script);
        });
    }

    /**
     * Get list of available cameras
     */
    async function getCameraList() {
        try {
            const devices = await Html5Qrcode.getCameras();
            return devices;
        } catch (error) {
            console.error('Error getting cameras:', error);
            return [];
        }
    }

    /**
     * Update camera selection dropdown
     */
    function updateCameraDropdown(cameras, selectedId) {
        const cameraSelect = document.getElementById('camera-select');
        if (!cameraSelect) return;

        cameraSelect.innerHTML = '<option value="">Pilih Kamera</option>';

        if (cameras.length > 0) {
            cameras.forEach(camera => {
                const option = document.createElement('option');
                option.value = camera.id;
                option.text = camera.label || `Kamera (${camera.id})`;
                option.selected = camera.id === selectedId;
                cameraSelect.appendChild(option);
            });

            cameraSelect.style.display = 'block';
        } else {
            cameraSelect.style.display = 'none';
        }
    }

    /**
     * Start the barcode scanner
     */
    function startScanner(cameraId) {
        // Scanner configuration
        const config = {
            fps: 10,
            qrbox: {
                width: 250,
                height: 250
            },
            aspectRatio: 1.0,
            formatsToSupport: [
                Html5QrcodeSupportedFormats.EAN_13,
                Html5QrcodeSupportedFormats.EAN_8,
                Html5QrcodeSupportedFormats.CODE_128,
                Html5QrcodeSupportedFormats.CODE_39,
                Html5QrcodeSupportedFormats.CODE_93,
                Html5QrcodeSupportedFormats.UPC_A,
                Html5QrcodeSupportedFormats.UPC_E,
                Html5QrcodeSupportedFormats.ITF
            ]
        };

        try {
            // Create scanner instance
            const html5QrCode = new Html5Qrcode("qr-reader");

            // Success callback
            const onScanSuccess = (decodedText) => {
                console.log(`Barcode detected: ${decodedText}`);

                // Show success message
                document.getElementById('detected-barcode').textContent = decodedText;
                document.getElementById('scanner-success').style.display = 'block';

                // Process barcode after a short delay
                setTimeout(() => {
                    // Set barcode in input and submit
                    document.getElementById('pos_barcode').value = decodedText;

                    // Trigger Enter keypress to process barcode
                    const event = new KeyboardEvent('keypress', {
                        key: 'Enter',
                        code: 'Enter',
                        keyCode: 13,
                        which: 13,
                        bubbles: true
                    });
                    document.getElementById('pos_barcode').dispatchEvent(event);

                    // Close the modal
                    const modal = bootstrap.Modal.getInstance(document.getElementById(
                        'barcodeScannerModal'));
                    if (modal) {
                        modal.hide();
                    }
                }, 800);
            };

            // Error callback - just log errors, don't show to user unless critical
            const onScanFailure = (error) => {
                // Only log, don't show to user for each frame
                // These are expected during normal scanning
                console.debug(`Scan error: ${error}`);
            };

            // Start the scanner
            html5QrCode.start(
                cameraId,
                config,
                onScanSuccess,
                onScanFailure
            ).then(() => {
                // Scanner started successfully
                console.log('Scanner started successfully');
                document.getElementById('scanner-loading').style.display = 'none';
                document.getElementById('scanner-container').style.display = 'block';

                // Store scanner reference
                window.barcodeScanner = html5QrCode;
            }).catch((err) => {
                // Handle start failure
                console.error('Scanner start error:', err);
                showScannerError(
                    'Gagal memulai kamera. Coba pilih kamera lain atau gunakan input manual. Error: ' + err);
            });
        } catch (error) {
            console.error('Scanner error:', error);
            showScannerError('Terjadi kesalahan saat memulai scanner. Error: ' + error.message);
        }
    }

    /**
     * Show error message in scanner
     */
    function showScannerError(message) {
        document.getElementById('scanner-loading').style.display = 'none';
        document.getElementById('scanner-container').style.display = 'none';

        const errorElement = document.getElementById('scanner-error');
        const errorMessageElement = document.getElementById('scanner-error-message');

        if (errorElement && errorMessageElement) {
            errorMessageElement.textContent = message;
            errorElement.style.display = 'block';
        }
    }
</script>
