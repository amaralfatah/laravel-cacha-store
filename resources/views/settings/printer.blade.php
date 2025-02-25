@extends('layouts.app')

@section('content')

    <x-section-header title="Pengaturan Printer" />

    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Pengaturan Printer</h5>
        </div>

        <div class="card-body">
            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <form action="{{ route('settings.printer.update') }}" method="POST">
                @csrf
                @method('PUT')

                <div class="mb-3">
                    <label class="form-label">Ukuran Kertas</label>
                    <div class="btn-group w-100" role="group">
                        <input type="radio" class="btn-check" name="paper_size" id="size57" value="57mm"
                            {{ old('paper_size', $setting->paper_size ?? '') == '57mm' ? 'checked' : '' }}>
                        <label class="btn btn-outline-primary" for="size57">
                            57mm
                            <small class="d-block">Printer Thermal Kecil</small>
                        </label>

                        <input type="radio" class="btn-check" name="paper_size" id="size78" value="78mm"
                            {{ old('paper_size', $setting->paper_size ?? '') == '78mm' ? 'checked' : '' }}>
                        <label class="btn btn-outline-primary" for="size78">
                            78mm
                            <small class="d-block">Printer Thermal Standard</small>
                        </label>

                        <input type="radio" class="btn-check" name="paper_size" id="custom" value="custom"
                            {{ !in_array(old('paper_size', $setting->paper_size ?? ''), ['57mm', '78mm']) ? 'checked' : '' }}>
                        <label class="btn btn-outline-primary" for="custom">
                            Kustom
                            <small class="d-block">Ukuran Lainnya</small>
                        </label>
                    </div>

                    <div id="customSizeInput" class="mt-2" style="{{ !in_array(old('paper_size', $setting->paper_size ?? ''), ['57mm', '78mm']) ? '' : 'display: none;' }}">
                        <div class="input-group">
                            <input type="text" class="form-control" name="custom_paper_size" id="customPaperSize"
                                   value="{{ !in_array(old('paper_size', $setting->paper_size ?? ''), ['57mm', '78mm']) ? old('paper_size', $setting->paper_size ?? '') : '' }}"
                                   placeholder="Contoh: 80mm">
                            <span class="input-group-text">mm</span>
                        </div>
                        <small class="text-muted">Masukkan ukuran lebar kertas dalam mm</small>
                    </div>

                    @error('paper_size')
                    <div class="text-danger mt-1">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label class="form-label">Nama Printer</label>
                    <input type="text" class="form-control" name="printer_name"
                           value="{{ old('printer_name', $setting->printer_name ?? '') }}"
                           placeholder="Contoh: EPSON TM-T82">
                    @error('printer_name')
                    <div class="text-danger">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" name="auto_print" value="1"
                               id="autoPrint" {{ old('auto_print', $setting->auto_print ?? true) ? 'checked' : '' }}>
                        <label class="form-check-label" for="autoPrint">
                            Print Otomatis
                            <small class="text-muted d-block">Struk akan langsung tercetak setelah transaksi</small>
                        </label>
                    </div>
                </div>

                <div class="mb-3">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-save"></i> Simpan Pengaturan
                    </button>
                    <button type="button" class="btn btn-outline-primary ms-2" onclick="printTest()">
                        <i class="bi bi-printer"></i> Test Print
                    </button>
                </div>
            </form>
        </div>
    </div>

    @push('scripts')
        <script>
            function printTest(storeId) {
                let url = '{{ route("settings.printer.test") }}';
                if (storeId) {
                    url += '?store_id=' + storeId;
                }
                const printWindow = window.open(url, '_blank', 'width=400,height=600');
            }

            // Script untuk menangani input ukuran kustom
            document.addEventListener('DOMContentLoaded', function() {
                const radioButtons = document.querySelectorAll('input[name="paper_size"]');
                const customSizeInput = document.getElementById('customSizeInput');
                const customPaperSizeInput = document.getElementById('customPaperSize');

                radioButtons.forEach(radio => {
                    radio.addEventListener('change', function() {
                        if (this.value === 'custom') {
                            customSizeInput.style.display = 'block';
                        } else {
                            customSizeInput.style.display = 'none';
                        }
                    });
                });

                // Form submission handler
                document.querySelector('form').addEventListener('submit', function(e) {
                    if (document.getElementById('custom').checked) {
                        e.preventDefault();
                        const customValue = customPaperSizeInput.value.trim();

                        // Validasi input kustom
                        if (!customValue) {
                            alert('Silakan masukkan ukuran kertas');
                            return;
                        }

                        // Pastikan format benar (angka + mm)
                        const numericValue = parseFloat(customValue.replace('mm', ''));
                        if (isNaN(numericValue)) {
                            alert('Format ukuran kertas tidak valid');
                            return;
                        }

                        // Set nilai radio ke input kustom dengan format yang benar
                        document.getElementById('custom').value = numericValue + 'mm';

                        // Lanjutkan submit form
                        this.submit();
                    }
                });
            });
        </script>
    @endpush
@endsection
