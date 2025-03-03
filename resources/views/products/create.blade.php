@extends('layouts.app')

@section('content')
    <x-section-header title="Buat Product Baru"/>

    <form action="{{ route('products.store') }}" method="POST" enctype="multipart/form-data">
        @csrf

        @include('products.partials.product-form')
    </form>
@endsection
