<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model as Eloquent;

class EntityType extends Eloquent
{
    const SPACE = 1;

    const GROUP = 2;

    const INDIVIDUAL = 3;

    const INTEREST = 4;

    /**
     * @var Array
     *
     **/
    protected $fillable = [
        'name', 'slug', 'short'
    ];

    protected $dates = ['updated_at'];
}