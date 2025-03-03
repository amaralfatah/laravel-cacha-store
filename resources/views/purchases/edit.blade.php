{{-- resources/views/purchases/edit.blade.php --}}
@extends('layouts.app')

@section('content')
    <!-- Header with Back Button -->
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4>
            <i class='bx bx-edit me-1'></i> Edit Purchase Order #{{ $purchase->id }}
        </h4>
        <a href="{{ route('purchases.show', $purchase) }}" class="btn btn-outline-secondary btn-sm">
            <i class='bx bx-arrow-back me-1'></i> Back to Details
        </a>
    </div>

    <!-- Main Form -->
    <form action="{{ route('purchases.update', $purchase) }}" method="POST" id="purchaseForm">
        @csrf
        @method('PUT')
        @include('purchases.partials.form')
    </form>
@endsection
