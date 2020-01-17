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

class HolesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view("holes.index");
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view("holes.create");
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
            "holenum" => "required",
            "par" => "required",
            "par.*" => "required|integer",
            "course" => "required",
            "course.*" => "required",
        ]);

        $data = json_decode($request->input("course"));
        $newcourse = new Course;
        $newcourse->name = $data->name;
        $newcourse->numholes = $data->numholes;
        $newcourse->address = $data->address;
        $newcourse->city = $data->city;
        $newcourse->zipcode = $data->zipcode;
        $newcourse->description = $data->description;
        $newcourse->lat = $data->lat;
        $newcourse->lng = $data->lng;
        $newcourse->save();

        $count = count($request->input('holenum'));
        for ($i=0; $i<$count; $i++){
        $hole = new Hole;
        $hole->courses_id = $newcourse->id;
        $hole->holenum = $request->input("holenum")[$i];
        $hole->par = $request->input("par")[$i];
        $hole->length = $request->input("length")[$i];
        $hole->save(); 
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
        // $article = Article::find($id);
        $course = Course::findOrFail($id);
        $courseholes = Course::find($id)->holes;
	    return view('holes.show')->with('course', $course)->with('courseholes', $courseholes);
    }
    
    /**
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
