<li class="card" style="clear: both;">
	@if ($primary = $user->getPrimaryPhoto())
	<div class="card-thumb" style="float: left; padding: 5px;">
			<img src="/{!! str_replace(' ','%20', $user->getPrimaryPhoto()->thumbnail) !!}" alt="{{ $user->name}}"  style="max-width: 100px; ">
	</div>
	@else
		<div class="card-thumb" style="float: left; padding: 5px;">
			<img src="/images/avatar-placeholder-generic.jpg"  style="max-width: 100px; ">
		</div>
	@endif

	{!! link_to_route('users.show', $user->name, [$user->id]) !!}

	@if ($signedIn && (Auth::user()->id === $user->id || Auth::user()->id === Config::get('app.superuser') || Auth::user()->hasGroup('super_admin') ))
	<a href="{!! route('users.edit', ['id' => $user->id]) !!}">
	<span class='glyphicon glyphicon-pencil'></span></a>
    {!! link_form_icon('glyphicon-trash text-warning', $user, 'DELETE', 'Delete the user', NULL, 'delete') !!}

			@can('grant_access')
				@if (!$user->isActive)
				<a href="{!! route('users.activate', ['id' => $user->id]) !!}" class="confirm">
					<span class='glyphicon glyphicon-ok-circle' title='Activate the user'></span></a>
				@endif
			@endcan
			@can('grant_access')
				@if ($user->isActive)
					<a href="{!! route('users.reminder', ['id' => $user->id]) !!}"  class="confirm">
						<span class='glyphicon glyphicon-pushpin' title='Send reminder'></span></a>
				@endif
			@endcan
			@can('impersonate_user')
				<a href="{!! route('user.impersonate', ['id' => $user->id]) !!}" title="Impersonate {{ $user->name }}"  class="confirm">
					<span class='glyphicon glyphicon-user'></span>
				</a>
			@endif
	@endif

	<ul class="list">
	@if ($events = $user->events->take(3))
	@foreach ($events as $event)
		<li>Events:
		<b>{{ $event->start_at->format('m.d.y')  }}</b> {!! link_to_route('events.show', $event->name, [$event->id], ['class' =>'butt']) !!} </li>
	@endforeach
	@endif

	</ul>
</li>
