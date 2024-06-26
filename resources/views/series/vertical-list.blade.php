@if (count($series) > 0)
<ul class='vertical-list'>

    @php $type = NULL @endphp

	@foreach ($series as $s)
		@if ($type !== $s->occurrence_type_id)
			<li>
				<h2>{{ $s->occurrenceType->name }}</h2>
                <?php $type = $s->occurrence_type_id; ?>
			</li>
		@endif
		@include('series.single', ['series' => $s])
	@endforeach
</ul>
@else
<div><small>No series listed today.</small></div>
@endif

