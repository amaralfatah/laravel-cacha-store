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

        // Camera button - Barcode scanner
        document.getElementById('btn-camera')?.addEventListener('click', function() {
            openBarcodeScannerModal();
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
     * Open the barcode scanner modal and initialize the camera
     */
    function openBarcodeScannerModal() {
        // Create the modal if it doesn't exist
        if (!document.getElementById('barcodeScannerModal')) {
            createBarcodeScannerModal();
        }

        // Initialize and show modal
        const modal = new bootstrap.Modal(document.getElementById('barcodeScannerModal'));
        modal.show();

        // Initialize scanner after modal is shown
        document.getElementById('barcodeScannerModal').addEventListener('shown.bs.modal', function() {
            startBarcodeScanner();
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

                        <video id="scanner-preview" class="w-100" style="display:none; border: 2px solid #919b9c; min-height: 300px;"></video>

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

        // Add the modal to the page
        document.body.insertAdjacentHTML('beforeend', modalHtml);

        // Add event listeners to the modal buttons
        document.getElementById('btn-scan-manual').addEventListener('click', function() {
            const modal = bootstrap.Modal.getInstance(document.getElementById('barcodeScannerModal'));
            if (modal) {
                modal.hide();
            }

            setTimeout(() => {
                document.getElementById('pos_barcode').focus();
            }, 300);
        });

        document.getElementById('btn-switch-camera').addEventListener('click', switchCamera);
        document.getElementById('btn-toggle-flash').addEventListener('click', toggleFlash);

        // Handle cleanup when modal is closed
        document.getElementById('barcodeScannerModal').addEventListener('hidden.bs.modal', function() {
            stopBarcodeScanner();
        });
    }

    /**
     * Start the barcode scanner
     */
    async function startBarcodeScanner() {
        try {
            // Add the Quagga script if it doesn't exist
            if (typeof Quagga === 'undefined') {
                await loadQuaggaScript();
            }

            // Get available cameras
            const cameras = await getAvailableCameras();
            updateCameraSelection(cameras);

            // Start the camera
            startCamera();
        } catch (error) {
            console.error('Error starting barcode scanner:', error);
            showScannerError(
                'Gagal memulai scanner barcode. Pastikan kamera diizinkan dan browser Anda mendukung akses kamera.'
                );
        }
    }

    /**
     * Load the Quagga.js script
     */
    function loadQuaggaScript() {
        return new Promise((resolve, reject) => {
            const script = document.createElement('script');
            script.src = 'https://cdnjs.cloudflare.com/ajax/libs/quagga/0.12.1/quagga.min.js';
            // Remove integrity attribute to avoid blocking
            script.onload = resolve;
            script.onerror = reject;
            document.head.appendChild(script);
        });
    }

    /**
     * Get list of available cameras
     */
    async function getAvailableCameras() {
        if (!navigator.mediaDevices || !navigator.mediaDevices.enumerateDevices) {
            return [];
        }

        try {
            // Request camera permission first
            await navigator.mediaDevices.getUserMedia({
                video: true
            });

            // Get device list
            const devices = await navigator.mediaDevices.enumerateDevices();
            return devices.filter(device => device.kind === 'videoinput');
        } catch (error) {
            console.error('Error accessing cameras:', error);
            throw error;
        }
    }

    /**
     * Update the camera selection dropdown
     */
    function updateCameraSelection(cameras) {
        const cameraSelect = document.getElementById('camera-select');
        if (!cameraSelect) return;

        // Clear existing options
        cameraSelect.innerHTML = '<option value="">Pilih Kamera</option>';

        if (cameras.length > 0) {
            // Add camera options
            cameras.forEach((camera, index) => {
                const option = document.createElement('option');
                option.value = camera.deviceId;
                option.text = camera.label || `Kamera ${index + 1}`;
                cameraSelect.appendChild(option);
            });

            // Show the dropdown
            cameraSelect.style.display = 'block';

            // Show camera switcher button if more than one camera
            if (cameras.length > 1) {
                document.getElementById('btn-switch-camera').style.display = 'inline-block';
            }

            // Select back camera by default if available
            const backCamera = cameras.find(camera =>
                camera.label &&
                (camera.label.toLowerCase().includes('back') ||
                    camera.label.toLowerCase().includes('rear') ||
                    camera.label.toLowerCase().includes('belakang'))
            );

            if (backCamera) {
                cameraSelect.value = backCamera.deviceId;
            }
        }

        // Add change event to camera select
        cameraSelect.addEventListener('change', function() {
            startCamera(this.value);
        });
    }

    /**
     * Global variables for scanner state
     */
    let currentStream = null;
    let scannerRunning = false;
    let lastDetectedCode = '';
    let lastDetectionTime = 0;
    let detectionCount = 0;

    /**
     * Start camera with selected device
     */
    function startCamera(deviceId) {
        // Stop any existing scanner
        stopBarcodeScanner();

        // Reset detection variables
        lastDetectedCode = '';
        detectionCount = 0;
        lastDetectionTime = 0;

        // Show loading and hide other elements
        document.getElementById('scanner-loading').style.display = 'block';
        document.getElementById('scanner-preview').style.display = 'none';
        document.getElementById('scanner-error').style.display = 'none';
        document.getElementById('scanner-success').style.display = 'none';

        // Camera constraints
        const constraints = {
            video: {
                width: {
                    min: 640,
                    ideal: 1280,
                    max: 1920
                },
                height: {
                    min: 480,
                    ideal: 720,
                    max: 1080
                },
                facingMode: "environment"
            }
        };

        // If specific device requested, use it
        if (deviceId) {
            constraints.video.deviceId = {
                exact: deviceId
            };
        }

        // Initialize Quagga
        Quagga.init({
            inputStream: {
                name: "Live",
                type: "LiveStream",
                target: document.getElementById('scanner-preview'),
                constraints: constraints,
            },
            locator: {
                patchSize: "medium",
                halfSample: true
            },
            numOfWorkers: 2,
            decoder: {
                readers: [
                    "ean_reader",
                    "ean_8_reader",
                    "code_128_reader",
                    "code_39_reader",
                    "upc_reader",
                    "upc_e_reader",
                    "i2of5_reader"
                ],
                multiple: false
            },
            locate: true
        }, function(err) {
            if (err) {
                console.error('Quagga initialization error:', err);
                showScannerError('Gagal memulai kamera. ' + err.message);
                return;
            }

            // Hide loading and show preview
            document.getElementById('scanner-loading').style.display = 'none';
            document.getElementById('scanner-preview').style.display = 'block';

            // Start scanning
            Quagga.start();
            scannerRunning = true;

            // Store stream reference for flash control
            const videoElement = document.getElementById('scanner-preview');
            if (videoElement && videoElement.srcObject) {
                currentStream = videoElement.srcObject;

                // Check if flash is available
                setTimeout(checkFlashAvailability, 500);
            }
        });

        // Handle barcode detection
        Quagga.onDetected((result) => {
            const code = result.codeResult.code;

            // Verify detection (multiple reads of the same code)
            if (code === lastDetectedCode) {
                detectionCount++;
            } else {
                lastDetectedCode = code;
                detectionCount = 1;
            }

            const now = new Date().getTime();

            // Require at least 2 detections of the same code within a timeframe
            if (detectionCount >= 2 && (now - lastDetectionTime > 1000)) {
                lastDetectionTime = now;

                // Show success message
                document.getElementById('detected-barcode').textContent = code;
                document.getElementById('scanner-success').style.display = 'block';

                // Process the barcode after a short delay
                setTimeout(() => {
                    processBarcodeResult(code);
                }, 800);
            }
        });
    }

    /**
     * Process the detected barcode
     */
    function processBarcodeResult(code) {
        // Set barcode in input field
        const barcodeInput = document.getElementById('pos_barcode');
        if (barcodeInput) {
            barcodeInput.value = code;

            // Trigger Enter key to process barcode
            const enterEvent = new KeyboardEvent('keypress', {
                key: 'Enter',
                code: 'Enter',
                keyCode: 13,
                which: 13,
                bubbles: true
            });
            barcodeInput.dispatchEvent(enterEvent);
        }

        // Close the modal
        const modal = bootstrap.Modal.getInstance(document.getElementById('barcodeScannerModal'));
        if (modal) {
            modal.hide();
        }
    }

    /**
     * Stop the barcode scanner
     */
    function stopBarcodeScanner() {
        if (scannerRunning) {
            Quagga.stop();
            scannerRunning = false;
        }

        // Stop the camera stream
        if (currentStream) {
            currentStream.getTracks().forEach(track => track.stop());
            currentStream = null;
        }
    }

    /**
     * Show error message in scanner
     */
    function showScannerError(message) {
        document.getElementById('scanner-loading').style.display = 'none';
        document.getElementById('scanner-preview').style.display = 'none';

        const errorElement = document.getElementById('scanner-error');
        const errorMessageElement = document.getElementById('scanner-error-message');

        if (errorElement && errorMessageElement) {
            errorMessageElement.textContent = message;
            errorElement.style.display = 'block';
        }
    }

    /**
     * Switch to next camera
     */
    function switchCamera() {
        const cameraSelect = document.getElementById('camera-select');
        if (!cameraSelect || cameraSelect.options.length <= 2) return;

        // Find current index and move to next
        let currentIndex = cameraSelect.selectedIndex;
        let nextIndex = (currentIndex + 1) % cameraSelect.options.length;

        // Skip the first option (placeholder)
        if (nextIndex === 0) nextIndex = 1;

        // Select the next camera
        cameraSelect.selectedIndex = nextIndex;

        // Start the selected camera
        startCamera(cameraSelect.value);
    }

    /**
     * Check if flash is available and show button if supported
     */
    function checkFlashAvailability() {
        if (!currentStream) return;

        const videoTrack = currentStream.getVideoTracks()[0];
        if (!videoTrack || !videoTrack.getCapabilities) return;

        const capabilities = videoTrack.getCapabilities();

        // Show flash button if torch is supported
        const flashButton = document.getElementById('btn-toggle-flash');
        if (flashButton) {
            if (capabilities.torch) {
                flashButton.style.display = 'inline-block';
            } else {
                flashButton.style.display = 'none';
            }
        }
    }

    /**
     * Toggle camera flash/torch
     */
    async function toggleFlash() {
        if (!currentStream) return;

        try {
            const videoTrack = currentStream.getVideoTracks()[0];
            if (!videoTrack || !videoTrack.getCapabilities) return;

            const capabilities = videoTrack.getCapabilities();
            if (!capabilities.torch) return;

            // Get current torch state
            const settings = videoTrack.getSettings();
            const currentTorch = settings.torch || false;

            // Toggle torch state
            await videoTrack.applyConstraints({
                advanced: [{
                    torch: !currentTorch
                }]
            });

            // Update button style
            const flashButton = document.getElementById('btn-toggle-flash');
            if (flashButton) {
                if (!currentTorch) {
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
</script>
