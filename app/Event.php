<?php namespace App;

use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model as Eloquent;

class Event extends Eloquent {


	public static function boot()
	{
		parent::boot();

		static::creating(function($event)
		{
			$event->created_by = Auth::user()->id;
			$event->updated_by =  Auth::user() ? Auth::user()->id : NULL;	
		});

		static::updating(function($event)
		{
			$event->updated_by = Auth::user() ? Auth::user()->id : NULL;			
		});
	}

    /**
     * The storage format of the model's date columns.
     *
     * @var string
     */
    //protected $dateFormat = 'Y-m-d\\TH:i';

	/**
	 * @var Array
	 *
	 **/
	protected $fillable = [
        'name', 'slug', 'short',
        'description',
        'visibility_id',
        'event_status_id',
        'event_type_id',
        'is_benefit', 'promoter_id', 'venue_id',
        'presale_price','door_price',
        'ticket_link','primary_link',
        'series_id',
        'soundcheck_at','door_at', 'start_at', 'end_at','cancelled_at',
        'min_age', 'created_by'
	];


	protected $dates = ['soundcheck_at', 'door_at', 'start_at', 'end_at','cancelled_at'];

	// building filter
	public function scopeFilter($query, QueryFilter $filters)
	{
		return $filters->apply($query);
	}

	/**
	 * Set the soundcheck_at attribute
	 *
	 * @param $date
	 */
	public function setSoundcheckAtAttribute($date)
	{
		if (!empty($date)) {
			$this->attributes['soundcheck_at'] = Carbon::parse($date);
		} else {
			$this->attributes['soundcheck_at'] = NULL;
		}
	}

	/**
	 * Set the start_at attribute
	 *
	 * @param $date
	 */
	public function setStartAtAttribute($date)
	{
		if (!empty($date)) {
			$this->attributes['start_at'] = Carbon::parse($date);
		} else {
			$this->attributes['start_at'] = NULL;
		}
	}

	/**
	 * Set the end_at attribute
	 *
	 * @param $date
	 */
	public function setEndAtAttribute($date)
	{
		if (!empty($date)) {
			$this->attributes['end_at'] = Carbon::parse($date);
		} else {
			$this->attributes['end_at'] = NULL;
		}
	}

    /**
     * Set the cancelled at attribute
     *
     * @param $date
     */
    public function setCancelledAtAttribute($date)
    {
        if (!empty($date)) {
            $this->attributes['cancelled_at'] = Carbon::parse($date);
        } else {
            $this->attributes['cancelled_at'] = NULL;
        }
    }

	/**
	 * Set the door_at attribute
	 *
	 * @param $date
	 */
	public function setDoorAtAttribute($date)
	{
		if (!empty($date)) {
			$this->attributes['door_at'] = Carbon::parse($date);
		} else {
			$this->attributes['door_at'] = NULL;
		}
	}


    public function scopeToday($query)
    {
        return $query->whereDate('start_at', '=', Carbon::today()->toDateString())
            ->orderBy('start_at', 'asc');
    }

	public function scopeFuture($query)
	{
		return $query->where('start_at','>=', Carbon::today()->startOfDay())
						->orderBy('start_at', 'asc');
	}

	public function scopePast($query)
	{
		return $query->where('start_at','<', Carbon::today()->startOfDay())
						->orderBy('start_at', 'desc');
	}


	/**
	 * Returns visible events
	 *
	 */
	public function scopeVisible($query, $user)
	{
		$public = Visibility::where('name','=','Public')->first();
		
 		$query->where('visibility_id','=', $public ? $public->id : NULL )->orWhere('created_by','=',($user ? $user->id : NULL));
	}

	/**
	 * Returns visible events
	 *
	 */
	public function scopeStarting($query, $date)
	{
		$cdate_yesterday = Carbon::parse($date)->subDay(1);
		$cdate_tomorrow = Carbon::parse($date)->addDay(1);

		$query->where('start_at','>', $cdate_yesterday->toDateString().' 23:59:59')
					->where('start_at','<',$cdate_tomorrow->toDateString().' 00:00:00')
                    ->where(function($query) {
                        return $query->where('visibility_id', '=', 3)
                            ->orWhere('created_by', '=', Auth::user() ? Auth::user()->id : NULL);
                    })
					->orderBy('start_at','ASC')
                    ->with('visibility');
	}

    /**
     * Get all of the events comments
     */
    public function comments()
    {
        return $this->morphMany('App\Comment', 'commentable')->orderBy('created_at', 'DESC');
    }

    /**
     * The likes that belong to the event
     *
     * @ return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function likes()
    {
        return $this->belongsToMany('App\Like')->withTimestamps();
    }

	/**
	 * An event is owned by a user
	 *
	 * @ return \Illuminate\Database\Eloquent\Relations\BelongsTo
	 */
	public function user()
	{
		return $this->belongsTo('App\User','created_by');
	}

	/**
	 * An event is created by one user
	 *
	 * @ return \Illuminate\Database\Eloquent\Relations\BelongsTo
	 */
	public function creator()
	{
		return $this->belongsTo('App\User','created_by');
	}

	/**
	 * An event is created by one user
	 *
	 * @ param User $user
	 * 
	 * @ return boolean
	 */
	public function ownedBy(User $user)
	{
		return $this->created_by == $user->id;
	}

	/**
	 * An event has one venue
	 *
	 */
	public function venue()
	{
		return $this->hasOne('App\Entity','id','venue_id');
	}

	/**
	 * An event has one type
	 *
	 */
	public function eventType()
	{
		return $this->hasOne('App\EventType','id','event_type_id');
	}

	/**
	 * An event has one status
	 *
	 */
	public function eventStatus()
	{
		return $this->hasOne('App\EventStatus','id','event_status_id');
	}

    /**
     * Get all of the events photos
     */
    public function photos()
    {
		return $this->belongsToMany('App\Photo')->withTimestamps();
    }

    /**
     * An event can belong to many threads
     *
     * @ return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function threads()
    {
        return $this->hasMany('App\Thread');
    }


    /**
	 * An event has one visibility
	 *
	 */
	public function visibility()
	{
		return $this->hasOne('App\Visibility','id','visibility_id');
	}


	/**
	 * An event has one series
	 *
	 */
	public function series()
	{
		return $this->hasOne('App\Series','id','series_id');
	}
	/**
	 * The tags that belong to the event
	 *
	 * @ return \Illuminate\Database\Eloquent\Relations\BelongsToMany
	 */
	public function tags()
	{
		return $this->belongsToMany('App\Tag')->withTimestamps();
	}


	/**
	 * The entities that belong to the event
	 *
	 * @ return \Illuminate\Database\Eloquent\Relations\BelongsToMany
	 */
	public function entities()
	{
		return $this->belongsToMany('App\Entity')->withTimestamps();
	}

	/**
	 * A user can have many event responses
	 *
	 */
	public function eventResponses()
	{
		return $this->hasMany('App\EventResponse');
	}

	/**
	 * Get the count of users attending this event
	 *
	 */
	public function getAttendingCountAttribute()
	{
		$responses = $this->eventResponses()->get();
		$responses->filter(function($e)
		{
			return ($e->responseType->name == 'Attending');
		});

		return count($responses);
	}


	/**
	 * Get the length of the event in hours
	 *
	 * @ return decimal
	 */
	public function getLengthInHoursAttribute()
	{
	    if ($this->start_at) {
            return $this->start_at->diffInHours($this->end_time, false);
        };

	    return 0;

	}

	/**
	 * Get the end time of the event
	 *
	 * @ return 
	 */
	public function getEndTimeAttribute()
	{
		if (isset($this->end_at))
		{
			return $this->end_at;
		} else {
			return  $this->start_at->addDay()->startOfDay();
		};
	}


	/**
	 * Get a list of tag ids associated with the event
	 *
	 * @ return \Illuminate\Database\Eloquent\Relations\BelongsToMany
	 */
	public function getTagListAttribute()
	{
		return $this->tags->pluck('id')->all();
	}

	/**
	 * Get a list of entity ids associated with the event
	 *
	 * @ return \Illuminate\Database\Eloquent\Relations\BelongsToMany
	 */
	public function getEntityListAttribute()
	{
		return $this->entities->pluck('id')->all();
	}


	/**
	 * Return a collection of events with the passed tag
	 * 
	 * @return Collection $events
	 * 
	 **/
	public static function getByTag($tag)
	{
		// get a list of events that have the passed tag
		$events = self::whereHas('tags', function($q) use ($tag)
		{
			$q->where('name','=', ucfirst($tag));
		});

		return $events;
	}

	/**
	 * Return a collection of events with the passed venue
	 * 
	 * @return Collection $events
	 * 
	 **/
	public static function getByVenue($slug)
	{
		// get a list of events that have the passed tag
		$events = self::whereHas('venue', function($q) use ($slug)
		{
			$q->where('slug','=', $slug);
		});

		return $events;
	}

	/**
	 * Return a collection of events with the passed event type
	 * 
	 * @return Collection $events
	 * 
	 **/
	public static function getByType($slug)
	{
		// get a list of events that have the passed tag
		$events = self::whereHas('eventType', function($q) use ($slug)
		{
			$q->where('name','=', $slug);
		});

		return $events;
	}

	/**
	 * Return a collection of events with the passed series
	 * 
	 * @return Collection $events
	 * 
	 **/
	public static function getBySeries($slug)
	{
		// get a list of events that have the passed tag
		$events = self::whereHas('series', function($q) use ($slug)
		{
			$q->where('name','=', $slug);
		});

		return $events;
	}

	/**
	 * Return a collection of events with the passed entity
	 * 
	 * @return Collection $events
	 * 
	 **/
	public static function getByEntity($slug)
	{
		// get a list of events that have the passed entity
		$events = self::whereHas('entities', function($q) use ($slug)
		{
			$q->where('slug','=', $slug);
		});

		return $events;
	}

	/**
	 * Returns the response status for the passed user
	 *
     * @param User $user
	 * @return Collection $eventResponse
	 * 
	 **/
	public function getEventResponse($user)
	{
		return EventResponse::where('event_id','=', $this->id)
		->where('user_id','=',$user->id)->first();

	}


    /**
     * Returns the review from the passed use if there is one
     *
     * @param User $user
     * @return Collection $eventReview
     *
     **/
    public function getEventReview($user)
    {
        return EventReview::where('event_id','=', $this->id)
            ->where('user_id','=',$user->id)->first();

    }

    /**
     * Returns a count of reviews
     *
     * @return integer
     *
     **/
    public function getCountReviewsAttribute()
    {
        return EventReview::where('event_id','=', $this->id)
            ->count();

    }

    /**
     * Returns a count of confirmed attendees
     *
     * @return integer
     *
     **/
    public function getCountAttendedAttribute()
    {
        return EventReview::where('event_id','=', $this->id)
            ->where('attended', '=' , 1)
            ->count();

    }


    /**
     * Returns an average of reviews
     *
     * @return float
     *
     **/
    public function getAvgRatingAttribute()
    {
        return EventReview::where('event_id','=', $this->id)
            ->avg('rating');

    }

	/**
	 * Return a collection of events that begin on the passed date
	 * 
	 * @return Collection $events
	 * 
	 **/
	public static function getByStartAt($date)
	{
		// get a list of events that start on the passed date

		$cdate = Carbon::parse($date);
		$cdate_yesterday = Carbon::parse($date)->subDay(1);
		$cdate_tomorrow = Carbon::parse($date)->addDay(1);

		$events = Event::where('start_at','>', $cdate_yesterday->toDateString())
					->where('start_at','<',$cdate_tomorrow->toDateString())
                    ->with('visibility')
					->orderBy('start_at', 'ASC')
					->orderBy('name', 'ASC');

		return $events;
	}

	public function addPhoto(Photo $photo)
	{
		return $this->photos()->attach($photo->id);;
	}


	/**
	 * Return the flyer for this event
	 * 
	 * @return Photo $photo
	 * 
	 **/
	public function getFlyer()
	{
		// get a list of events that start on the passed date
		$flyer = $this->photos()->first();

		return $flyer;
	}

	/**
	 * Return the primary photo for this event
	 * 
	 * @return Photo $photo
	 * 
	 **/
	public function getPrimaryPhoto()
	{
		// gets the first photo related to this event
		$primary = $this->photos()->where('photos.is_primary','=','1')->first();

		return $primary;
	}

    /**
     * Create the slug from the name if none was passed
     */
    public function setSlugAttribute($value) {

        // grab the title and slugify it
        if ($value === '')
        {
        	$this->attributes['slug'] = str_slug($this->name);
        } else {
        	$this->attributes['slug'] = $value;
        }
    }

    public function getGoogleCalendarLink()
    {
        $action = 'TEMPLATE';
        $text = $this->name;
        $start = Carbon::parse($this->start_at)->format('Ymd\THis');
        $end = Carbon::parse($this->start_at)->format('Ymd\THis');
        $details = $this->description;
        $location = $this->venue ? $this->venue->name : 'Unknown';
        $sf = "true";

        $url = sprintf('https://www.google.com/calendar/render?action=%s&text=%s&dates=%s/%s&details=%s&location=%s&sf=%s&output=xml', $action, $text, $start, $end, $details, $location, $sf);

        return $url;
    }

}
