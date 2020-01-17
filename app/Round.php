<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Round extends Model
{
    public function scores()
    {
        return $this->hasMany('App\Score', 'rounds_id');
    }

    public function course()
    {
        return $this->belongsTo('App\Course', 'courses_id');
    }


    
}
