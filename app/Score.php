<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Score extends Model
{

    protected $fillable = ['holes_id','numstrokes','users_id','rounds_id'];

    public function user()
    {
        return $this->belongsTo('App\User', 'users_id');
    }

    public function round()
    {
        return $this->belongsTo('App\Round', 'rounds_id');
    }

    public function hole()
    {
        return $this->belongsTo('App\Hole', 'holes_id');
    }
}
