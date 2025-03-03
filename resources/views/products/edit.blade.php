@extends('layouts.app')

@section('content')
    <x-section-header title="Edit Product: {{ $product->name }}"/>

    <form action="{{ route('products.update', $product) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        @include('products.partials.product-form')
    </form>
@endsection
