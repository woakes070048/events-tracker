@extends('app')

@section('title', 'Keyword Tag Add')

@section('content')

<h1 class="display-crumbs text-primary">Add a New Keyword Tag</h1>

{!! Form::open(['route' => 'tags.store']) !!}

	@include('tags.form')

{!! Form::close() !!}

{!! link_to_route('tags.index', 'Return to list') !!}
@stop
