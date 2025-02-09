<style>
    .position-relative {
        position: relative;
    }

    .product-list {
        position: absolute;
        top: 100%;
        /* Posisi tepat di bawah input */
        left: 0;
        right: 0;
        background: white;
        border: 1px solid #ddd;
        border-radius: 4px;
        max-height: 250px;
        /* Batasi tinggi maksimal */
        overflow-y: auto;
        /* Scroll vertikal jika konten melebihi max-height */
        z-index: 1050;
        /* Pastikan muncul di atas konten lain */
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        display: none;
        /* Hidden by default */
    }

    .product-item {
        padding: 8px 12px;
        cursor: pointer;
        border-bottom: 1px solid #eee;
    }

    .product-item:last-child {
        border-bottom: none;
    }

    .product-item:hover {
        background-color: #f8f9fa;
    }

    /* Styling untuk scrollbar */
    .product-list::-webkit-scrollbar {
        width: 8px;
    }

    .product-list::-webkit-scrollbar-track {
        background: #f1f1f1;
    }

    .product-list::-webkit-scrollbar-thumb {
        background: #888;
        border-radius: 4px;
    }

    .product-list::-webkit-scrollbar-thumb:hover {
        background: #555;
    }
</style>
