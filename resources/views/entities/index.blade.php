@extends('app')

@section('title','Entities')

@section('content')

	<h4>Entities
		@include('entities.crumbs')
	</h4>

	<P>
		<a href="{!! URL::route('entities.create') !!}" class="btn btn-primary">Add an entity</a>
		<a href="{!! URL::route('entities.role', ['role' => 'artist']) !!}" class="btn btn-info">Show artists</a>
		<a href="{!! URL::route('entities.role', ['role' => 'band']) !!}" class="btn btn-info">Show bands</a>
		<a href="{!! URL::route('entities.role', ['role' => 'dj']) !!}" class="btn btn-info">Show DJs</a>
		<a href="{!! URL::route('entities.role', ['role' => 'producer']) !!}" class="btn btn-info">Show producers</a>
		<a href="{!! URL::route('entities.role', ['role' => 'promoter']) !!}" class="btn btn-info">Show promoters</a>
		<a href="{!! URL::route('entities.role', ['role' => 'shop']) !!}" class="btn btn-info">Show shops</a>
		<a href="{!! URL::route('entities.role', ['role' => 'venue']) !!}" class="btn btn-info">Show venues</a>
	</P>

	<!-- NAV / FILTER -->
	<div class="row" class="tab-content filters-content">

		<div id="filters-container" class="col-lg-12">

			<a href="#" id="filters" class="btn btn-primary">Filters <span id="filters-toggle" class="glyphicon @if (!$hasFilter) glyphicon-chevron-down @else glyphicon-chevron-up @endif"></span></a>
			{!! Form::open(['route' => ['entities.filter'], 'method' => 'GET']) !!}

			<div id="filter-list" @if (!$hasFilter)style="display: none"@endif >

			<!-- BEGIN: FILTERS -->
			<div class="form-group col-sm-2">

			{!! Form::label('filter_name','Filter By Name') !!}

			{!! Form::text('filter_name', (isset($filters['filter_name']) ? $filters['filter_name'] : NULL), ['class' =>'form-control']) !!}
			</div>

			<div class="form-group col-sm-2">

			{!! Form::label('filter_role','Filter By Role',  array('width' => '100%')) !!}<br>
			<?php $roles = [''=>'&nbsp;'] + App\Role::orderBy('name', 'ASC')->pluck('name', 'name')->all();?>
			{!! Form::select('filter_role', $roles, (isset($filters['filter_role']) ? $filters['filter_role'] : NULL), ['data-style' => '100%', 'data-width' => '100%', 'class' =>'form-control select2', 'data-placeholder' => 'Select a role']) !!}
			</div>

			<div class="form-group col-sm-2">
			{!! Form::label('filter_tag','Filter By Tag',  array('width' => '100%')) !!}
			<?php $tags =  [''=>'&nbsp;'] + App\Tag::orderBy('name','ASC')->pluck('name', 'name')->all();?>
			{!! Form::select('filter_tag', $tags, (isset($filters['filter_tag']) ? $filters['filter_tag'] : NULL), ['class' =>'form-control select2','data-width' => '100%', 'data-placeholder' => 'Select a tag']) !!}
			</div>

			<div class="form-group col-sm-1">
				{!! Form::label('filter_rpp','RPP') !!}
				<?php $rpp_options =  [''=>'&nbsp;', 5 => 5, 10 => 10, 25 => 25, 100 => 100, 1000 => 1000];?>
				{!! Form::select('filter_rpp', $rpp_options, (isset($rpp) ? $rpp : NULL), ['data-width' => '100%','class' =>'auto-submit form-control']) !!}
			</div>

			<div class="col-sm-2">
				<div class="btn-group col-sm-1">
				{!! Form::submit('Filter',  ['class' =>'btn btn-primary btn-sm btn-tb', 'id' => 'primary-filter-submit']) !!}

				{!! Form::close() !!}

				{!! Form::open(['route' => ['entities.reset'], 'method' => 'GET']) !!}

					{!! Form::submit('Reset',  ['class' =>'btn btn-primary btn-sm btn-tb', 'id' => 'primary-filter-reset']) !!}

				{!! Form::close() !!}
				</div>
			</div>
			</div>

		</div>
		<!-- END: FILTERS -->
	</div>

	<br style="clear: left;"/>

    <div class="row">
        <div class='col-md-12 col-lg-6'>

        @if (!$entities->isEmpty())
            {!! $entities->appends(['sort_by' => $sortBy,
                                    'rpp' => $rpp
            ])->render() !!}
        @endif

		@include('entities.list', ['entities' => $entities])

        </div>
    </div>
@stop

@section('footer')
	<script>
        $(document).ready(function() {
            $('#filters').click(function () {
                $('#filter-list').toggle();
                if ($('#filters-toggle').hasClass('glyphicon-chevron-down'))
                {
                    $('#filters-toggle').removeClass('glyphicon-chevron-down');
                    $('#filters-toggle').addClass('glyphicon-chevron-up');
                } else {
                    $('#filters-toggle').removeClass('glyphicon-chevron-up');
                    $('#filters-toggle').addClass('glyphicon-chevron-down');
                }
            });
        });
	</script>
@endsection