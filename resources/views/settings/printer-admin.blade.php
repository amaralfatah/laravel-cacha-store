@extends('layouts.app')

@section('content')

    <x-section-header title="Pengaturan Printer Semua Toko" />

    <div class="card">

        <div class="card-body">
            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead>
                    <tr>
                        <th>Kode Toko</th>
                        <th>Nama Toko</th>
                        <th>Ukuran Kertas</th>
                        <th>Nama Printer</th>
                        <th>Auto Print</th>
                        <th>Aksi</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($settings as $setting)
                        <tr>
                            <td>{{ $setting->store->code }}</td>
                            <td>{{ $setting->store->name }}</td>
                            <td>{{ $setting->paper_size }}</td>
                            <td>{{ $setting->printer_name }}</td>
                            <td>
                                <span class="badge {{ $setting->auto_print ? 'bg-success' : 'bg-secondary' }}">
                                    {{ $setting->auto_print ? 'Ya' : 'Tidak' }}
                                </span>
                            </td>
                            <td>
                                <button type="button"
                                        class="btn btn-sm btn-outline-primary"
                                        onclick="printTest({{ $setting->store_id }})">
                                    <i class="bi bi-printer"></i> Test Print
                                </button>
                                <button type="button"
                                        class="btn btn-sm btn-primary"
                                        data-bs-toggle="modal"
                                        data-bs-target="#editModal{{ $setting->store_id }}">
                                    <i class="bi bi-pencil"></i> Edit
                                </button>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <!-- Modal Edit untuk setiap toko -->
    @foreach($settings as $setting)
        <div class="modal fade" id="editModal{{ $setting->store_id }}" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <form action="{{ route('settings.printer.update') }}" method="POST">
                        @csrf
                        @method('PUT')
                        <input type="hidden" name="store_id" value="{{ $setting->store_id }}">

                        <div class="modal-header">
                            <h5 class="modal-title">Edit Pengaturan Printer - {{ $setting->store->name }}</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>

                        <div class="modal-body">
                            <div class="mb-3">
                                <label class="form-label">Ukuran Kertas</label>
                                <div class="btn-group w-100" role="group">
                                    <input type="radio" class="btn-check" name="paper_size"
                                           id="size57{{ $setting->store_id }}" value="57mm"
                                        {{ $setting->paper_size == '57mm' ? 'checked' : '' }}>
                                    <label class="btn btn-outline-primary" for="size57{{ $setting->store_id }}">
                                        57mm
                                        <small class="d-block">Printer Thermal Kecil</small>
                                    </label>

                                    <input type="radio" class="btn-check" name="paper_size"
                                           id="size80{{ $setting->store_id }}" value="80mm"
                                        {{ $setting->paper_size == '80mm' ? 'checked' : '' }}>
                                    <label class="btn btn-outline-primary" for="size80{{ $setting->store_id }}">
                                        80mm
                                        <small class="d-block">Printer Thermal Standard</small>
                                    </label>

                                    <input type="radio" class="btn-check" name="paper_size"
                                           id="custom{{ $setting->store_id }}" value="custom"
                                        {{ !in_array($setting->paper_size, ['57mm', '80mm']) ? 'checked' : '' }}>
                                    <label class="btn btn-outline-primary" for="custom{{ $setting->store_id }}">
                                        Kustom
                                        <small class="d-block">Ukuran Lainnya</small>
                                    </label>
                                </div>

                                <div id="customSizeInput{{ $setting->store_id }}" class="mt-2"
                                     style="{{ !in_array($setting->paper_size, ['57mm', '80mm']) ? '' : 'display: none;' }}">
                                    <div class="input-group">
                                        <input type="text" class="form-control"
                                               name="custom_paper_size"
                                               id="customPaperSize{{ $setting->store_id }}"
                                               value="{{ !in_array($setting->paper_size, ['57mm', '80mm']) ? $setting->paper_size : '' }}"
                                               placeholder="Contoh: 76mm">
                                        <span class="input-group-text">mm</span>
                                    </div>
                                    <small class="text-muted">Masukkan ukuran lebar kertas dalam mm</small>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Nama Printer</label>
                                <input type="text" class="form-control" name="printer_name"
                                       value="{{ $setting->printer_name }}"
                                       placeholder="Contoh: EPSON TM-T82">
                            </div>

                            <div class="mb-3">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" name="auto_print"
                                           value="1" id="autoPrint{{ $setting->store_id }}"
                                        {{ $setting->auto_print ? 'checked' : '' }}>
                                    <label class="form-check-label" for="autoPrint{{ $setting->store_id }}">
                                        Print Otomatis
                                        <small class="text-muted d-block">Struk akan langsung tercetak setelah transaksi</small>
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                            <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endforeach
@endsection

@push('scripts')
    <script>
        function printTest(storeId) {
            let url = '{{ route("settings.printer.test") }}';
            if (storeId) {
                url += '?store_id=' + storeId;
            }
            const printWindow = window.open(url, '_blank', 'width=400,height=600');
        }

        // Menangani input kustom untuk setiap modal
        document.addEventListener('DOMContentLoaded', function() {
            @foreach($settings as $setting)
            const radioButtons{{ $setting->store_id }} = document.querySelectorAll('input[name="paper_size"][id$="{{ $setting->store_id }}"]');
            const customSizeInput{{ $setting->store_id }} = document.getElementById('customSizeInput{{ $setting->store_id }}');
            const customPaperSizeInput{{ $setting->store_id }} = document.getElementById('customPaperSize{{ $setting->store_id }}');

            radioButtons{{ $setting->store_id }}.forEach(radio => {
                radio.addEventListener('change', function() {
                    if (this.value === 'custom') {
                        customSizeInput{{ $setting->store_id }}.style.display = 'block';
                    } else {
                        customSizeInput{{ $setting->store_id }}.style.display = 'none';
                    }
                });
            });

            // Form submission handler
            document.querySelector('#editModal{{ $setting->store_id }} form').addEventListener('submit', function(e) {
                if (document.getElementById('custom{{ $setting->store_id }}').checked) {
                    e.preventDefault();
                    const customValue = customPaperSizeInput{{ $setting->store_id }}.value.trim();

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
                    document.getElementById('custom{{ $setting->store_id }}').value = numericValue + 'mm';

                    // Lanjutkan submit form
                    this.submit();
                }
            });
            @endforeach
        });
    </script>
@endpush
