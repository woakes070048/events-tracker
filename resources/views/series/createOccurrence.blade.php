@extends('app')

@section('title','Event Series -  Create Occurrence')

@section('content')
	<script src="{{ asset('/js/facebook-sdk.js') }}"></script>
	<h1>{{ $series->name}} </h1> 
	<h2>Add Occurrence: {{ $event->name }}</h2>

	{!! Form::model($event, ['route' => ['events.store']]) !!}

		@include('events.form', ['action' => 'createOccurrence'])

	{!! Form::close() !!}

	<P><a href="{!! URL::route('series.index') !!}" class="btn btn-info">Return to list</a></P>

@stop

@section('scripts.footer')
	<script src="{{ asset('/js/facebook-event.js') }}"></script>
@stop