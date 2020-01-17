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
use DataTables;

class CoursesController extends Controller
{   
    public function __construct()
    {
        $this->middleware('admin', ['only' => ['edit','destroy','update']]);
    }

    // returns all rounds->scores->hole played by logged in player on a course selected from a selectbox
    public function usercourses(Request $request)
    {  
        $id = $request->id;
  
        $course = Course::find($id);


        $rounds = Round::whereHas(
            'scores', function ($query) {
                $query->where('scores.users_id', Auth::id());
            })
            ->with(['scores' => function($q) { 
                $q->where('scores.users_id', Auth::id())
                ->select('scores.*')
                ->with(['hole' => function($q4) { 
                    $q4->join('scores','scores.holes_id','holes.id','holes')
                    ->select('holes.*'); }]);
                }])  
           ->where('rounds.courses_id',$course->id)
           ->orderBy('rounds.created_at','desc') 
           ->get();

           return $rounds;
    }

    //list courses with datatables
    public function filterCourses()
    { 
    $courses = Course::all();
        
    return DataTables::of($courses)
    ->addColumn('name', function ($course) 
    {
        return '<span style="display: none;">'.$course->name.'</span><a href="/courses/'.$course->id.'">'.$course->name.'</a>';
    })
    ->rawColumns(['name'])
    ->make(true);
    }

    
    public function listCourses()
    { 
        $courses = Course::all();    
     
        // list courses for guests
        if (Auth::guest()){
            return DataTables::of($courses)
            ->addColumn('name', function ($course) 
            {
                return '<span style="display: none;">'.$course->name.'</span><a href="/holes/'.$course->id.'">'.$course->name.'</a>';
                
            })
            ->rawColumns(['name'])
            ->make(true); 
            }
        // list courses for admins with different links
        else if (auth()->user()->isAdmin == 1){
            return DataTables::of($courses)
            ->addColumn('name', function ($course) 
            {
                return '<span style="display: none;">'.$course->name.'</span><a href="/courses/'.$course->id.'/edit">'.$course->name.'</a>';
            })
            ->rawColumns(['name'])
            ->make(true);
            }
        // list courses for the rest as in logged in users basically
        else {
            return DataTables::of($courses)
            ->addColumn('name', function ($course) 
            {
                return '<span style="display: none;">'.$course->name.'</span><a href="/holes/'.$course->id.'">'.$course->name.'</a>';
                    
            })
            ->rawColumns(['name'])
            ->make(true); 
            }
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $courses = Course::all();
        return view("courses.index", compact("courses"));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view("courses.create");
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            "name" => "required",
            "numholes" => "required|integer",
            "lat" => "required",
            "lng" => "required"

        ]);
        
        $course = new Course;
        $course->name = $request->input("name");
        $course->numholes = $request->input("numholes");
        $course->address = $request->input("address");
        $course->city = $request->input("city");
        $course->zipcode = $request->input("zipcode");
        $course->lat = $request->input("lat");
        $course->lng = $request->input("lng");
        $course->description = $request->input("description");

        return view("holes.create", compact("course"));   
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $course = Course::findOrFail($id);
        $courseholes = Course::find($id)->holes;
        return view('courses.show')->with('course', $course)->with('courseholes', $courseholes);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $course = Course::findOrFail($id);
        $courseholes = Course::find($id)->holes;
        return view('courses.edit')->with('course', $course)->with('courseholes', $courseholes);
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
        DB::transaction(function () use ($request) {
        $this->validate($request, [
            "name" => "required",
            "numholes" => "required|integer",
            "lat" => "required",
            "lng" => "required"

        ]);
        
        $course = Course::findOrFail($id);
        $course->name = request("name");
        $course->numholes = request("numholes");
        $course->address = request("address");
        $course->city = request("city");
        $course->zipcode = request("zipcode");
        $course->lat = request("lat");
        $course->lng = request("lng");
        $course->description = request("description");
        $course->save();
        });
        return redirect()->route('home'); 
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $course = Course::findOrFail($id);
        $course->load(['rounds', 'holes', 'rounds.scores'])->get();
        
        DB::transaction(function () use ($course) {
        $course->delete();
        });
        return redirect()->route('home');  
    }
}
