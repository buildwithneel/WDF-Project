{{--
    Partial: _seat_tile.blade.php
    Renders a single seat tile with the correct status colour.
    Included by seats/show.blade.php for each seat in the loop.
--}}

@php
    // Determine effective status (treat expired holds as available)
    $effectiveStatus = $seat->status;
    if ($seat->status === 'held' && $seat->held_until < now()) {
        $effectiveStatus = 'available';
    }
@endphp

<div class="seat seat-{{ $effectiveStatus }} seat-tile"
     @if($effectiveStatus === 'available')
         data-seat-id="{{ $seat->id }}"
         data-seat-no="{{ $seat->seat_no }}"
         title="Seat {{ $seat->seat_no }} — Click to select"
     @elseif($effectiveStatus === 'held')
         title="Seat {{ $seat->seat_no }} — Held (expires soon)"
     @else
         title="Seat {{ $seat->seat_no }} — Already booked"
     @endif
     style="width:56px; height:60px;">

    <span class="text-xs font-bold">{{ $seat->seat_no }}</span>

    @if($effectiveStatus === 'available')
        <span style="font-size:0.6rem; opacity:0.85;">FREE</span>
    @elseif($effectiveStatus === 'held')
        <span style="font-size:0.6rem; opacity:0.85;">HELD</span>
    @else
        <span style="font-size:0.6rem; opacity:0.85;">SOLD</span>
    @endif
</div>
