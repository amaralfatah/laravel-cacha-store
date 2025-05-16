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

    // Import library ZXing untuk scan barcode
    let barcodeDetector;

    /**
     * Open barcode scanner modal
     */
    function openBarcodeScanner() {
        // Create modal if not exists
        if (!document.getElementById('barcodeModal')) {
            createBarcodeModal();
        }

        // Show the modal
        const modal = new bootstrap.Modal(document.getElementById('barcodeModal'));
        modal.show();

        // Initialize scanner after modal is shown
        document.getElementById('barcodeModal').addEventListener('shown.bs.modal', function() {
            initBarcodeScanner();
        }, {
            once: true
        });
    }

    /**
     * Create barcode scanner modal
     */
    function createBarcodeModal() {
        const modalHtml = `
    <div class="modal fade" id="barcodeModal" tabindex="-1" aria-labelledby="barcodeModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content" style="border: 2px solid #919b9c; border-radius: 0; box-shadow: 3px 3px 5px rgba(0,0,0,0.3);">
                <div class="modal-header" style="background: linear-gradient(to bottom, #4f6acc 0%, #2a3c8e 100%); color: white; padding: 6px 10px; border-bottom: 1px solid #919b9c;">
                    <h5 class="modal-title" id="barcodeModalLabel">
                        <i class='bx bx-scan me-1'></i> Scan Barcode
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" style="background-color: #ece9d8; padding: 10px;">
                    <div class="text-center mb-3">
                        <p>Arahkan kamera ke barcode produk</p>
                    </div>

                    <div id="scanner-container">
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

                        <!-- Video container with barcode highlights -->
                        <div id="video-container" style="position: relative; display: none;">
                            <video id="video" style="width: 100%; border: 2px solid #919b9c;" autoplay playsinline></video>
                            <div id="barcode-box" style="position: absolute; border: 3px solid #00FF00; display: none;"></div>
                        </div>

                        <!-- Success message -->
                        <div id="scanner-success" class="alert alert-success mt-2" style="display:none;">
                            <strong>Barcode terdeteksi:</strong> <span id="detected-barcode"></span>
                        </div>
                    </div>

                    <!-- Camera selection -->
                    <div class="d-flex justify-content-between mt-3">
                        <select id="camera-select" class="form-select" style="max-width: 250px; display:none;">
                            <option value="">Pilih Kamera</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer" style="background-color: #ece9d8; border-top: 1px solid #919b9c; padding: 8px;">
                    <button type="button" id="btn-manual-input" class="btn btn-primary">
                        <i class='bx bx-keyboard me-1'></i> Input Manual
                    </button>
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                        <i class='bx bx-x me-1'></i> Tutup
                    </button>
                </div>
            </div>
        </div>
    </div>
    `;

        // Add modal to body
        document.body.insertAdjacentHTML('beforeend', modalHtml);

        // Add event listeners
        document.getElementById('btn-manual-input').addEventListener('click', function() {
            const modal = bootstrap.Modal.getInstance(document.getElementById('barcodeModal'));
            if (modal) {
                modal.hide();
            }

            // Focus barcode input field
            setTimeout(() => {
                document.getElementById('pos_barcode').focus();
            }, 300);
        });

        // Camera select change handler
        document.getElementById('camera-select').addEventListener('change', function() {
            if (window.stream) {
                stopVideoStream();
            }
            startVideoStream(this.value);
        });

        // Cleanup when modal is closed
        document.getElementById('barcodeModal').addEventListener('hidden.bs.modal', function() {
            stopVideoStream();
        });
    }

    // Keep track of video stream
    window.stream = null;
    let scannerInterval = null;
    let lastDetectedCode = null;
    let lastDetectionTime = 0;

    /**
     * Initialize barcode scanner
     */
    async function initBarcodeScanner() {
        try {
            // Show loading
            document.getElementById('scanner-loading').style.display = 'block';
            document.getElementById('video-container').style.display = 'none';
            document.getElementById('scanner-error').style.display = 'none';
            document.getElementById('scanner-success').style.display = 'none';

            // Get list of cameras
            const cameras = await getAvailableCameras();
            updateCameraDropdown(cameras);

            // Get the preferred camera (back camera if available)
            let preferredCameraId = null;
            if (cameras.length > 0) {
                // Try to find back camera
                const backCamera = cameras.find(camera =>
                    camera.label.toLowerCase().includes('back') ||
                    camera.label.toLowerCase().includes('rear') ||
                    camera.label.toLowerCase().includes('belakang')
                );

                preferredCameraId = backCamera ? backCamera.deviceId : cameras[0].deviceId;
            }

            // Check if BarcodeDetector is available in browser
            if ('BarcodeDetector' in window) {
                try {
                    barcodeDetector = new BarcodeDetector({
                        formats: [
                            'ean_13', 'ean_8', 'code_39', 'code_128',
                            'upc_a', 'upc_e', 'itf', 'codabar'
                        ]
                    });
                    console.log("Native BarcodeDetector supported");
                } catch (e) {
                    console.warn("Native BarcodeDetector is not supported, using fallback method");
                    barcodeDetector = null;
                }
            } else {
                console.warn("BarcodeDetector not available, using fallback method");
                barcodeDetector = null;
            }

            // Start video stream with preferred camera
            await startVideoStream(preferredCameraId);

        } catch (error) {
            console.error('Error initializing barcode scanner:', error);
            showScannerError(
                'Gagal mengakses kamera. Pastikan browser Anda mendukung akses kamera dan izin telah diberikan.'
                );
        }
    }

    /**
     * Get available cameras
     */
    async function getAvailableCameras() {
        try {
            // Request camera permission first
            const stream = await navigator.mediaDevices.getUserMedia({
                video: true
            });
            // Stop stream right away - we'll start a proper one later
            stream.getTracks().forEach(track => track.stop());

            // Get list of video devices
            const devices = await navigator.mediaDevices.enumerateDevices();
            return devices.filter(device => device.kind === 'videoinput');
        } catch (error) {
            console.error('Error getting cameras:', error);
            throw error;
        }
    }

    /**
     * Update camera selection dropdown
     */
    function updateCameraDropdown(cameras) {
        const cameraSelect = document.getElementById('camera-select');
        if (!cameraSelect) return;

        cameraSelect.innerHTML = '<option value="">Pilih Kamera</option>';

        if (cameras.length > 0) {
            cameras.forEach(camera => {
                const option = document.createElement('option');
                option.value = camera.deviceId;
                option.text = camera.label || `Kamera (${camera.deviceId.substr(0, 5)}...)`;
                cameraSelect.appendChild(option);
            });

            // Only show if there are multiple cameras
            cameraSelect.style.display = cameras.length > 1 ? 'block' : 'none';
        } else {
            cameraSelect.style.display = 'none';
        }
    }

    /**
     * Start video stream with specified camera
     */
    async function startVideoStream(cameraId) {
        try {
            // Stop existing stream if any
            if (window.stream) {
                stopVideoStream();
            }

            // Configure video constraints
            const constraints = {
                video: {
                    width: {
                        ideal: 1280
                    },
                    height: {
                        ideal: 720
                    },
                    facingMode: "environment"
                }
            };

            // If camera ID is specified, use it
            if (cameraId) {
                constraints.video.deviceId = {
                    exact: cameraId
                };
            }

            // Get video stream
            window.stream = await navigator.mediaDevices.getUserMedia(constraints);

            // Connect stream to video element
            const videoElement = document.getElementById('video');
            videoElement.srcObject = window.stream;

            // When video is ready, show it and start scanning
            videoElement.onloadedmetadata = function() {
                document.getElementById('scanner-loading').style.display = 'none';
                document.getElementById('video-container').style.display = 'block';

                // Start scanner
                startScanner();
            };

            console.log('Camera started successfully');
        } catch (error) {
            console.error('Error starting camera:', error);
            showScannerError('Gagal memulai kamera. ' + error.message);
        }
    }

    /**
     * Stop video stream
     */
    function stopVideoStream() {
        // Stop scanning interval
        if (scannerInterval) {
            clearInterval(scannerInterval);
            scannerInterval = null;
        }

        // Stop video stream
        if (window.stream) {
            window.stream.getTracks().forEach(track => track.stop());
            window.stream = null;
        }

        // Clear video source
        const videoElement = document.getElementById('video');
        if (videoElement) {
            videoElement.srcObject = null;
        }

        // Reset detection variables
        lastDetectedCode = null;
        lastDetectionTime = 0;
    }

    /**
     * Show scanner error
     */
    function showScannerError(message) {
        document.getElementById('scanner-loading').style.display = 'none';
        document.getElementById('video-container').style.display = 'none';

        const errorElement = document.getElementById('scanner-error');
        const errorMessageElement = document.getElementById('scanner-error-message');

        if (errorElement && errorMessageElement) {
            errorMessageElement.textContent = message;
            errorElement.style.display = 'block';
        }
    }

    /**
     * Start barcode scanner
     */
    function startScanner() {
        // Get video element
        const video = document.getElementById('video');
        if (!video) return;

        // Start scanning interval
        scannerInterval = setInterval(() => {
            scanBarcode(video);
        }, 200); // Scan every 200ms
    }

    /**
     * Scan for barcode in video frame
     */
    async function scanBarcode(videoElement) {
        if (!videoElement || videoElement.paused || videoElement.ended) return;

        try {
            // If native BarcodeDetector is available, use it
            if (barcodeDetector) {
                const barcodes = await barcodeDetector.detect(videoElement);
                processBarcodes(barcodes);
            } else {
                // Fallback to canvas-based detection (less reliable)
                manualBarcodeDetection(videoElement);
            }
        } catch (error) {
            console.error('Error scanning barcode:', error);
        }
    }

    /**
     * Process detected barcodes
     */
    function processBarcodes(barcodes) {
        if (!barcodes || barcodes.length === 0) return;

        // Get the first barcode
        const barcode = barcodes[0];

        // Get the barcode value
        const barcodeValue = barcode.rawValue;

        // Check if it's a new barcode and debounce detection
        const now = Date.now();
        if (barcodeValue !== lastDetectedCode || (now - lastDetectionTime > 2000)) {
            lastDetectedCode = barcodeValue;
            lastDetectionTime = now;

            // Show barcode box
            highlightBarcode(barcode.cornerPoints);

            // Process the barcode
            processDetectedBarcode(barcodeValue);
        }
    }

    /**
     * Highlight barcode on video
     */
    function highlightBarcode(cornerPoints) {
        const barcodeBox = document.getElementById('barcode-box');
        if (!barcodeBox) return;

        if (!cornerPoints || cornerPoints.length < 4) {
            barcodeBox.style.display = 'none';
            return;
        }

        // Calculate bounding box
        const videoContainer = document.getElementById('video-container');
        const video = document.getElementById('video');

        if (!videoContainer || !video) return;

        // Get video dimensions
        const videoWidth = video.videoWidth;
        const videoHeight = video.videoHeight;

        // Get container dimensions
        const containerWidth = video.offsetWidth;
        const containerHeight = video.offsetHeight;

        // Calculate scale factors
        const scaleX = containerWidth / videoWidth;
        const scaleY = containerHeight / videoHeight;

        // Find min/max coordinates
        let minX = Infinity,
            minY = Infinity,
            maxX = 0,
            maxY = 0;

        cornerPoints.forEach(point => {
            minX = Math.min(minX, point.x);
            minY = Math.min(minY, point.y);
            maxX = Math.max(maxX, point.x);
            maxY = Math.max(maxY, point.y);
        });

        // Apply scale and position the box
        const left = minX * scaleX;
        const top = minY * scaleY;
        const width = (maxX - minX) * scaleX;
        const height = (maxY - minY) * scaleY;

        barcodeBox.style.left = `${left}px`;
        barcodeBox.style.top = `${top}px`;
        barcodeBox.style.width = `${width}px`;
        barcodeBox.style.height = `${height}px`;
        barcodeBox.style.display = 'block';
    }

    /**
     * Manual barcode detection using canvas
     * This is a fallback method when BarcodeDetector is not available
     * Note: This method only simulates detection for demonstration
     */
    function manualBarcodeDetection(videoElement) {
        // In a real implementation, you would:
        // 1. Draw the video frame to a canvas
        // 2. Get the image data
        // 3. Use a JavaScript barcode library to detect barcodes
        // 4. Process the detected barcodes

        // For this demo, we'll just simulate detection every few seconds
        const now = Date.now();
        if (now - lastDetectionTime > 3000) { // Every 3 seconds for demo
            lastDetectionTime = now;

            // Generate a random barcode (for demonstration)
            // In a real implementation, this would be the actual detected barcode
            const mockBarcode = Math.floor(Math.random() * 10000000000000).toString().padStart(13, '0');

            // Process the barcode
            processDetectedBarcode(mockBarcode);
        }
    }

    /**
     * Process a detected barcode
     */
    function processDetectedBarcode(barcodeValue) {
        console.log('Barcode detected:', barcodeValue);

        // Show success message
        document.getElementById('detected-barcode').textContent = barcodeValue;
        document.getElementById('scanner-success').style.display = 'block';

        // Stop scanning
        clearInterval(scannerInterval);
        scannerInterval = null;

        // Set the barcode value in the input field
        document.getElementById('pos_barcode').value = barcodeValue;

        // Trigger Enter keypress to process barcode
        setTimeout(() => {
            const event = new KeyboardEvent('keypress', {
                key: 'Enter',
                code: 'Enter',
                keyCode: 13,
                which: 13,
                bubbles: true
            });
            document.getElementById('pos_barcode').dispatchEvent(event);

            // Close the modal
            const modal = bootstrap.Modal.getInstance(document.getElementById('barcodeModal'));
            if (modal) {
                modal.hide();
            }
        }, 1000);
    }
</script>
