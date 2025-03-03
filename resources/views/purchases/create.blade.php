{{-- resources/views/purchases/create.blade.php --}}
@extends('layouts.app')

@section('content')
    <x-section-header title="Input Data Pembelian">
        <x-slot:actions>
            <a href="{{ route('purchases.index') }}" class="btn btn-outline-secondary btn-sm">
                <i class='bx bx-arrow-back me-1'></i> Kembali
            </a>
        </x-slot:actions>
    </x-section-header>

    <!-- Main Form -->
    <form action="{{ route('purchases.store') }}" method="POST" id="purchaseForm">
        @csrf
        @include('purchases.partials.form')
    </form>
@endsection
