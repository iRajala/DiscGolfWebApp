<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Course;
use App\Hole;
use App\Score;
use App\Round;
use App\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
            // get the last played round with course, holes and scores
           $lastscore = Round::whereHas(
            'scores', function ($query) {
                $query->where('scores.users_id', Auth::id());
            })
            ->with(['course','scores' => function($q) { 
                $q->where('scores.users_id', Auth::id())
                ->select('scores.*')
                ->with(['hole' => function($q4) { 
                    $q4->join('scores','scores.holes_id','holes.id','holes')
                    ->select('holes.*'); }]);
                }])  
           ->latest()
           ->first();  
        
        // get courses to display them on the map
        $courses = Course::All();
        return view('home')->with('courses',$courses)->with('lastscore',$lastscore);   
    }
}
