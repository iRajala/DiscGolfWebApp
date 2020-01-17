<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Hole extends Model
{
    protected $fillable = ['courses_id','holenum','length','par'];

    public function course()
    {
        return $this->belongsTo('App\Course', 'courses_id');
    }

    public function scores()
    {
        return $this->hasMany('App\Score', 'holes_id');
    }
}
