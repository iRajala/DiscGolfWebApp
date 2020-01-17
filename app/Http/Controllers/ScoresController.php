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
use Yajra\Datatables\DataTables;

class ScoresController extends Controller
{
        /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function userscores()
    {
            $courses = Course::whereHas(
            'rounds.scores', function ($query) {
                $query->where('scores.users_id', Auth::id());
            })
            ->get();

            //not in use but saved if needed
            /*$scores = Course::whereHas(
                'rounds.scores', function ($query) {
                    $query->where('scores.users_id', Auth::id());
                })
                ->with(['rounds' => function($q) { 
                    $q->join('scores','scores.rounds_id','rounds.id','rounds')
                    ->whereHas('scores', function($q2){
                        $q2->where('scores.users_id', Auth::id());
                        })
                            ->with(['scores' => function($q3) { 
                            $q3->where('scores.users_id', Auth::id())                   
                            ->select('scores.*')
                                ->with(['hole' => function($q4) { 
                                    $q4->join('scores','scores.holes_id','holes.id','holes')
                                    ->select('holes.*'); }]);
                    }])
                    ->select('rounds.*')
                    ->where('scores.users_id', Auth::id())
                    ->groupBy('rounds.id');
               }])   
               ->get();*/
         
                return view("scores.userscores")->with('courses', $courses);
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view("scores.index");
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        DB::transaction(function () use ($request) {

        $this->validate($request, [
            "holes_id" => "required",
            "holes_id.*" => "required|integer",
            "score" => "required",
            "score.*" => "required|integer",
            "courses_id" => "required",
        ]);

        $round = New Round;
        $round->courses_id = $request->input("courses_id");
        $round->save();
 
        $count = count($request->input('score'));
        for ($i=0; $i<$count; $i++){
        $score = new Score;
        $score->numstrokes = $request->input("score")[$i];
        $score->holes_id = $request->input("holes_id")[$i];
        $score->users_id = Auth::id();
        $score->rounds_id = $round->id;   
        $score->save(); 
        }  
        });
        return redirect()->route('home');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
            $scores = Course::whereHas(
                'rounds.scores', function ($query) {
                    $query->where('scores.users_id', Auth::id());
                })
                ->with(['rounds' => function($q) { 
                    $q->join('scores','scores.rounds_id','rounds.id','rounds')
                    ->whereHas('scores', function($q2){
                        $q2->where('scores.users_id', Auth::id());
                        })
                            ->with(['scores' => function($q3) { 
                            $q3->where('scores.users_id', Auth::id())                   
                            ->select('scores.*')
                                ->with(['hole' => function($q4) { 
                                    $q4->join('scores','scores.holes_id','holes.id','holes')
                                    ->select('holes.*'); }]);
                    }])
                    ->select('rounds.*')
                    ->where('scores.users_id', Auth::id())
                    ->groupBy('rounds.id');
               }])   
               ->get();

               //return json_decode($scores);
               return view("scores.show")->with('scores',$scores);
            
    }
    
    /*
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
