<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class StudentApiController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $students = Student::get();

        return response()->json([
            "status" => "success",
            "data" => $students
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //Validade
        $validator = Validator::make($request->all(),[
            "name" => "required|min:4",
            "email" => "required|unique:students,email",
            "gender" => "required|in:male,female,other"
        ]);

        if($validator->fails()){
            return response()->json([
                "status" => "error",
                "message" => $validator->errors()
            ],400);
        }

        $data = $request->all();

        //Store data into the database table
        Student::create($data);

        return response()->json([
            "status" => "success",
            "message" => "Student created successfully"
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $student = Student::find($id);
        
        //Check if student exists
        if(!$student){
            return response()->json([
                "status" => "error",
                "message" => "student not found"
            ], 404);
        }


        return response()->json([
            "status" => "success",
            "data" => $student
        ], 200);

    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $student = Student::find($id);
        
        //Check if student exists in the database
        if(!$student){
            return response()->json([
                "status" => "error",
                "message" => "student not found, cannot update"
            ], 404);
        }

        //Validade
        $validator = Validator::make($request->all(),[
            "name" => "required|min:4",
            "email" => "required|min:4|unique:students,email,".$id,
            "gender" => "required|in:male,female,other"
        ]);

        if($validator->fails()){
            return response()->json([
                "status" => "error",
                "message" => $validator->errors()
            ],400);
        }

        

        $student->update($request->all());

        return response()->json([
            "status" => "sucess",
            "message" => $request->all()
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $student = Student::find($id);

        if(!$student){
            return response()->json([
                "status" => "error",
                "message" => "Cannot delete student because the provided id doesn't match any records"
            ]);
        }

        Student::destroy($id);

        return response()->json([
                "status" => "success",
                "message" => "Student deleted successfully"
        ]);

    }
}
