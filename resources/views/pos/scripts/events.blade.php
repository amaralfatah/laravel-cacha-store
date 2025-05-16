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
                        <div id="scanner-loading" class="text-center p-3">
                            <div class="spinner-border text-primary" role="status">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                            <p class="mt-2">Memuat kamera...</p>
                        </div>

                        <div id="scanner-error" class="alert alert-danger text-center" style="display:none;">
                            <i class='bx bx-error-circle me-1'></i>
                            <span id="scanner-error-message">Error message here</span>
                        </div>

                        <video id="scanner-preview" class="w-100" style="display:none; border: 2px solid #919b9c;"></video>

                        <div id="scanner-success" class="alert alert-success mt-2" style="display:none;">
                            <strong>Barcode terdeteksi:</strong> <span id="detected-barcode"></span>
                        </div>
                    </div>

                    <div class="d-flex justify-content-between mt-3">
                        <select id="camera-select" class="form-select" style="max-width: 250px; display:none;">
                            <option value="">Pilih Kamera</option>
                        </select>
                        <div>
                            <button id="btn-switch-camera" class="btn btn-outline-primary" style="display:none;">
                                <i class='bx bx-refresh me-1'></i> Ganti Kamera
                            </button>
                            <button id="btn-toggle-flash" class="btn btn-outline-warning" style="display:none;">
                                <i class='bx bx-bulb me-1'></i> Flash
                            </button>
                        </div>
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

        // Append modal to body
        document.body.insertAdjacentHTML('beforeend', modalHtml);
    }

    // Initialize barcode scanner using QuaggaJS
    let scannerIsRunning = false;
    let scannerModal = null;
    let currentStream = null;
    let availableCameras = [];
    let currentCameraIndex = 0;
    let lastDetectedCode = '';
    let detectionCount = 0;
    let lastDetectionTime = 0;

    // Function to initialize barcode scanner
    async function initBarcodeScanner() {
        try {
            // Check if Quagga is already loaded
            if (typeof Quagga === 'undefined') {
                // Load QuaggaJS library dynamically
                await loadQuaggaScript();
            }

            // Get available cameras
            availableCameras = await getAvailableCameras();

            // Populate camera selector
            const cameraSelect = document.getElementById('camera-select');
            if (cameraSelect) {
                cameraSelect.innerHTML = '<option value="">Pilih Kamera</option>';

                if (availableCameras.length > 0) {
                    availableCameras.forEach((camera, index) => {
                        const option = document.createElement('option');
                        option.value = index;
                        option.text = camera.label || `Kamera ${index + 1}`;
                        cameraSelect.appendChild(option);
                    });

                    cameraSelect.style.display = 'block';

                    // Show camera switcher button if more than one camera
                    if (availableCameras.length > 1) {
                        document.getElementById('btn-switch-camera').style.display = 'inline-block';
                    }
                }
            }

            // Start scanner with default or last selected camera
            startScanner();

        } catch (error) {
            console.error('Error initializing barcode scanner:', error);
            const errorElement = document.getElementById('scanner-error');
            const errorMessageElement = document.getElementById('scanner-error-message');

            if (errorElement && errorMessageElement) {
                errorMessageElement.textContent =
                    'Gagal mengakses kamera. Pastikan kamera diizinkan pada browser Anda.';
                errorElement.style.display = 'block';
                document.getElementById('scanner-loading').style.display = 'none';
            }
        }
    }

    // Load QuaggaJS script dynamically
    function loadQuaggaScript() {
        return new Promise((resolve, reject) => {
            const script = document.createElement('script');
            script.src = 'https://cdnjs.cloudflare.com/ajax/libs/quagga/0.12.1/quagga.min.js';
            script.integrity =
                'sha512-bCsBoYoW6zE0aja5xcIyoCDPfT27+dGCchLDbzJJWr0ulRYHKITnqQRfccUn4nARj/9n8FQvjc34+8MEJKfg8Q==';
            script.crossOrigin = 'anonymous';
            script.referrerPolicy = 'no-referrer';
            script.onload = resolve;
            script.onerror = reject;
            document.head.appendChild(script);
        });
    }

    // Get available cameras
    async function getAvailableCameras() {
        if (!navigator.mediaDevices || !navigator.mediaDevices.enumerateDevices) {
            console.log("enumerateDevices() not supported.");
            return [];
        }

        try {
            // Request permission to access camera first
            await navigator.mediaDevices.getUserMedia({
                video: true
            });

            // Then get the list of devices
            const devices = await navigator.mediaDevices.enumerateDevices();
            return devices.filter(device => device.kind === 'videoinput');
        } catch (error) {
            console.error('Error accessing media devices:', error);
            throw error;
        }
    }

    // Start barcode scanner
    function startScanner() {
        if (scannerIsRunning) {
            Quagga.stop();
            scannerIsRunning = false;
        }

        // Reset detection counters
        lastDetectedCode = '';
        detectionCount = 0;

        // Hide error message if shown
        document.getElementById('scanner-error').style.display = 'none';
        document.getElementById('scanner-success').style.display = 'none';

        // Show loading indicator
        document.getElementById('scanner-loading').style.display = 'block';
        document.getElementById('scanner-preview').style.display = 'none';

        // Get camera constraints
        const cameraId = availableCameras.length > 0 ?
            availableCameras[currentCameraIndex].deviceId : null;

        const constraints = {
            width: {
                min: 640
            },
            height: {
                min: 480
            },
            facingMode: "environment", // Prefer back camera
            deviceId: cameraId ? {
                exact: cameraId
            } : undefined
        };

        // Initialize Quagga
        Quagga.init({
            inputStream: {
                name: "Live",
                type: "LiveStream",
                target: document.getElementById('scanner-preview'),
                constraints: constraints,
            },
            decoder: {
                readers: [
                    "ean_reader",
                    "ean_8_reader",
                    "code_128_reader",
                    "code_39_reader",
                    "code_93_reader",
                    "upc_reader",
                    "upc_e_reader",
                    "i2of5_reader"
                ],
                multiple: false,
                debug: {
                    showCanvas: true,
                    showPatches: true,
                    showFoundPatches: true,
                    showSkeleton: true,
                    showLabels: true,
                    showPatchLabels: true,
                    showRemainingPatchLabels: true,
                    boxFromPatches: {
                        showTransformed: true,
                        showTransformedBox: true,
                        showBB: true
                    }
                }
            },
            locator: {
                patchSize: "medium",
                halfSample: true
            },
            locate: true
        }, function(err) {
            if (err) {
                console.error('Error starting Quagga:', err);
                document.getElementById('scanner-loading').style.display = 'none';
                const errorElement = document.getElementById('scanner-error');
                const errorMessageElement = document.getElementById('scanner-error-message');

                if (errorElement && errorMessageElement) {
                    errorMessageElement.textContent = 'Gagal memulai scanner barcode. ' + err.message;
                    errorElement.style.display = 'block';
                }
                return;
            }

            // Hide loading indicator and show video preview
            document.getElementById('scanner-loading').style.display = 'none';
            document.getElementById('scanner-preview').style.display = 'block';

            // Start Quagga
            Quagga.start();
            scannerIsRunning = true;

            // Store the current stream
            const videoElement = document.getElementById('scanner-preview');
            if (videoElement && videoElement.srcObject) {
                currentStream = videoElement.srcObject;
            }

            console.log("Barcode scanner started");
        });

        // Process detected barcodes
        Quagga.onDetected(function(result) {
            const code = result.codeResult.code;
            console.log("Barcode detected:", code);

            // Check if it's the same code as the last detection
            if (code === lastDetectedCode) {
                detectionCount++;
            } else {
                // Reset for new code
                lastDetectedCode = code;
                detectionCount = 1;
            }

            // Debounce and require multiple detections of the same code for confirmation
            const now = new Date().getTime();
            if ((detectionCount >= 2) && (now - lastDetectionTime > 1000)) {
                lastDetectionTime = now;

                // Show detected barcode
                document.getElementById('detected-barcode').textContent = code;
                document.getElementById('scanner-success').style.display = 'block';

                // Add a short delay before closing the modal
                setTimeout(() => {
                    // Set the barcode value in the input field
                    document.getElementById('pos_barcode').value = code;

                    // Trigger a keypress event (Enter) to process the barcode
                    const event = new KeyboardEvent('keypress', {
                        key: 'Enter',
                        code: 'Enter',
                        keyCode: 13,
                        which: 13,
                        bubbles: true
                    });
                    document.getElementById('pos_barcode').dispatchEvent(event);

                    // Close the modal
                    if (scannerModal) {
                        scannerModal.hide();
                    }
                }, 800);
            }
        });
    }

    // Switch to next camera
    function switchCamera() {
        if (availableCameras.length <= 1) return;

        // Move to next camera index
        currentCameraIndex = (currentCameraIndex + 1) % availableCameras.length;

        // Restart scanner with new camera
        startScanner();
    }

    // Toggle camera flash/torch (if available)
    async function toggleFlash() {
        if (!currentStream) return;

        try {
            const track = currentStream.getVideoTracks()[0];
            if (!track) return;

            const capabilities = track.getCapabilities();

            // Check if torch is supported
            if (!capabilities.torch) {
                console.log("Torch not supported");
                return;
            }

            const settings = track.getSettings();
            const newTorchState = !settings.torch;

            await track.applyConstraints({
                advanced: [{
                    torch: newTorchState
                }]
            });

            // Update button appearance based on torch state
            const flashButton = document.getElementById('btn-toggle-flash');
            if (flashButton) {
                if (newTorchState) {
                    flashButton.classList.remove('btn-outline-warning');
                    flashButton.classList.add('btn-warning');
                } else {
                    flashButton.classList.remove('btn-warning');
                    flashButton.classList.add('btn-outline-warning');
                }
            }

        } catch (error) {
            console.error('Error toggling flash:', error);
        }
    }

    // Switch to manual input
    function switchToManualInput() {
        if (scannerModal) {
            scannerModal.hide();
        }

        // Focus on the barcode input field after a short delay
        setTimeout(() => {
            const barcodeInput = document.getElementById('pos_barcode');
            if (barcodeInput) {
                barcodeInput.focus();
            }
        }, 300);
    }

    // Setup event listeners for barcode scanner
    function setupBarcodeScannerEvents() {
        document.getElementById('btn-camera').addEventListener('click', function() {
            // Create modal if not already created
            if (!document.getElementById('barcodeScannerModal')) {
                createBarcodeScannerModal();
            }

            // Initialize modal
            scannerModal = new bootstrap.Modal(document.getElementById('barcodeScannerModal'));

            // Show modal
            scannerModal.show();

            // Initialize scanner after modal is shown
            document.getElementById('barcodeScannerModal').addEventListener('shown.bs.modal', function() {
                initBarcodeScanner();
            });

            // Cleanup when modal is hidden
            document.getElementById('barcodeScannerModal').addEventListener('hidden.bs.modal', function() {
                if (scannerIsRunning) {
                    Quagga.stop();
                    scannerIsRunning = false;
                }

                // Stop camera stream
                if (currentStream) {
                    currentStream.getTracks().forEach(track => track.stop());
                    currentStream = null;
                }
            });

            // Camera select change event
            document.getElementById('camera-select').addEventListener('change', function() {
                currentCameraIndex = parseInt(this.value) || 0;
                startScanner();
            });

            // Button click events
            document.getElementById('btn-switch-camera').addEventListener('click', switchCamera);
            document.getElementById('btn-toggle-flash').addEventListener('click', toggleFlash);
            document.getElementById('btn-scan-manual').addEventListener('click', switchToManualInput);
        });
    }

    // Check for torch capability after stream is active
    function checkTorchCapability() {
        if (!currentStream) return;

        const track = currentStream.getVideoTracks()[0];
        if (!track) return;

        const capabilities = track.getCapabilities();

        // Show flash button if torch is supported
        if (capabilities.torch) {
            document.getElementById('btn-toggle-flash').style.display = 'inline-block';
        } else {
            document.getElementById('btn-toggle-flash').style.display = 'none';
        }
    }

    // Add barcode scanner initialization to event listeners setup
    function initializeBarcodeScannerFeature() {
        // Create script to load QuaggaJS if it doesn't exist
        if (!document.querySelector('script[src*="quagga.min.js"]')) {
            const script = document.createElement('script');
            script.src = 'https://cdnjs.cloudflare.com/ajax/libs/quagga/0.12.1/quagga.min.js';
            script.integrity =
                'sha512-bCsBoYoW6zE0aja5xcIyoCDPfT27+dGCchLDbzJJWr0ulRYHKITnqQRfccUn4nARj/9n8FQvjc34+8MEJKfg8Q==';
            script.crossOrigin = 'anonymous';
            script.referrerPolicy = 'no-referrer';
            document.head.appendChild(script);
        }

        // Add CSS for the scanner UI
        const style = document.createElement('style');
        style.textContent = `
        #scanner-preview {
            min-height: 300px;
            background-color: #000;
            position: relative;
        }
        .drawingBuffer {
            position: absolute;
            top: 0;
            left: 0;
        }
    `;
        document.head.appendChild(style);

        // Setup barcode scanner events when DOM is loaded
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', setupBarcodeScannerEvents);
        } else {
            setupBarcodeScannerEvents();
        }
    }

    // Initialize barcode scanner feature
    initializeBarcodeScannerFeature();
</script>
