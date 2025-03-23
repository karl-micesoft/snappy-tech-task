<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PostcodeLocation extends Model
{
    protected $primaryKey = 'postcode';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;
}
