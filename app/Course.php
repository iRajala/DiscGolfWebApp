<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Course extends Model
{
    public function holes()
    {
        return $this->hasMany('App\Hole', 'courses_id');
    }

    public function rounds()
    {
        return $this->hasMany('App\Round', 'courses_id');
    }

}
