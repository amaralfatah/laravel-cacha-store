<!-- resources/views/components/rating-stars.blade.php -->
@props(['total' => 0])

<div class="rating-stars">
    <i class="fas fa-star"></i>
    <i class="fas fa-star"></i>
    <i class="fas fa-star"></i>
    <i class="fas fa-star"></i>
    <i class="fas fa-star-half-alt"></i>
    @if($total > 0)
        <span class="ms-1 small">({{ $total }})</span>
    @endif
</div>
