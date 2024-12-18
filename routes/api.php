<?php
use App\Models\Entity;
use App\Models\Series;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;

Route::group(['middleware' => ['auth:sanctum']], function () {
    Route::get('tokens/validate', function () {
        return ['data' => 'token is valid'];
    });
});

Route::middleware('auth:sanctum')->get('tokens/test', function (Request $request) {
    return ['data' => 'token test'];
});


Route::middleware('auth.basic')->name('api.')->group(function () {

    Route::post('/tokens/create', function (Request $request) {
        $token = $request->user()->createToken($request->token_name, ['event:check']);
     
        return ['token' => $token->plainTextToken];
    });

    
    Route::get('events/{event}/embeds', ['as' => 'events.embeds', 'uses' => 'Api\EventsController@embeds']);
    Route::get('events/reset', ['as' => 'events.reset', 'uses' => 'Api\EventsController@reset']);


    Route::get('events/reset', ['as' => 'events.reset', 'uses' => 'Api\EventsController@reset']);
    Route::get('events/rpp-reset', ['as' => 'events.rppReset', 'uses' => 'Api\EventsController@rppReset']);
    Route::resource('events', 'Api\EventsController');

    Route::get('entities/reset', ['as' => 'entities.reset', 'uses' => 'Api\EntitiesController@reset']);
    Route::get('entities/rpp-reset', ['as' => 'entities.rppReset', 'uses' => 'Api\EntitiesController@rppReset']);
    Route::resource('entities', 'Api\EntitiesController');

    Route::match(['get', 'post'], 'entity-types/filter', ['as' => 'entityType.filter', 'uses' => 'Api\EntityTypesController@filter']);
    Route::get('entity-types/reset', ['as' => 'entity-types.reset', 'uses' => 'Api\EntityTypesController@reset']);
    Route::get('entity-types/rpp-reset', ['as' => 'entity-types.rppReset', 'uses' => 'Api\EntityTypesController@rppReset']);
    Route::resource('entity-types', 'Api\EntityTypesController');

    Route::match(['get', 'post'], 'entity-statuses/filter', ['as' => 'entityStatus.filter', 'uses' => 'Api\EntityStatusesController@filter']);
    Route::get('entity-statuses/reset', ['as' => 'entity-statuses.reset', 'uses' => 'Api\EntityStatusesController@reset']);
    Route::get('entity-statuses/rpp-reset', ['as' => 'entity-statuses.rppReset', 'uses' => 'Api\EntityStatusesController@rppReset']);
    Route::resource('entity-statuses', 'Api\EntityStatusesController');

    Route::match(['get', 'post'], 'event-types/filter', ['as' => 'eventType.filter', 'uses' => 'Api\EventTypesController@filter']);
    Route::get('event-types/reset', ['as' => 'event-types.reset', 'uses' => 'Api\EventTypesController@reset']);
    Route::get('event-types/rpp-reset', ['as' => 'event-types.rppReset', 'uses' => 'Api\EventTypesController@rppReset']);
    Route::resource('event-types', 'Api\EventTypesController');

    Route::get('series/reset', ['as' => 'series.reset', 'uses' => 'Api\SeriesController@reset']);
    Route::get('series/rpp-reset', ['as' => 'series.rppReset', 'uses' => 'Api\SeriesController@rppReset']);
    Route::resource('series', 'Api\SeriesController');

    Route::match(['get', 'post'], 'tags/filter', ['as' => 'tags.filter', 'uses' => 'Api\TagsController@filter']);
    Route::get('tags/reset', ['as' => 'tags.reset', 'uses' => 'Api\TagsController@reset']);
    Route::get('tags/rpp-reset', ['as' => 'tags.rppReset', 'uses' => 'Api\TagsController@rppReset']);
    Route::resource('tags', 'Api\TagsController');

    Route::match(['get', 'post'], 'links/filter', ['as' => 'links.filter', 'uses' => 'Api\LinksController@filter']);
    Route::get('links/reset', ['as' => 'links.reset', 'uses' => 'Api\LinksController@reset']);
    Route::get('links/rpp-reset', ['as' => 'links.rppReset', 'uses' => 'Api\LinksController@rppReset']);
    Route::resource('links', 'Api\LinksController');

    Route::match(['get', 'post'], 'locations/filter', ['as' => 'locations.filter', 'uses' => 'Api\LocationsController@filter']);
    Route::get('locations/reset', ['as' => 'locations.reset', 'uses' => 'Api\LocationsController@reset']);
    Route::get('locations/rpp-reset', ['as' => 'locations.rppReset', 'uses' => 'Api\LocationsController@rppReset']);
    Route::resource('locations', 'Api\LocationsController');

    Route::match(['get', 'post'], 'users/filter', ['as' => 'users.filter', 'uses' => 'Api\UsersController@filter']);
    Route::get('users/reset', ['as' => 'users.reset', 'uses' => 'Api\UsersController@reset']);
    Route::get('users/rpp-reset', ['as' => 'users.rppReset', 'uses' => 'Api\UsersController@rppReset']);
    Route::resource('users', 'Api\UsersController');

});

// routes protected by the shield middleware
Route::middleware('shield')->name('shield.')->group(function () {
});

// calendar routes - these are used by the web app for dynamic loading
Route::get('calendar-events', 'EventsController@calendarEventsApi')->name('calendarEvents.api');
Route::get('tag-calendar-events', 'EventsController@tagCalendarEventsApi')->name('tagCalendarEvents.api');