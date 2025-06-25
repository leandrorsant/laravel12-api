<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use App\Models\User;

class AuthController extends Controller
{
    //Registration API
    public function register(Request $request){ 
        //Validate
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required|unique:users,email',
            'password' => 'required|min:8|confirmed',
            'password_confirmation' => 'required|min:8'
        ]);

        if($validator->fails()){
            return response()->json([
                'status' => "error",
                'message' => $validator->errors()
            ], 400);
        }

        $data = $request->all();

        //Image upload
        $imagePath = null;
        if($request->hasFile('profile_picture')
            && $request->file('profile_picture')->isValid()){
                $file = $request->file('profile_picture');

                //Generate a unique filename
                $fileName = time() . '_' . $file->getClientOriginalName();

                //Move the file to the public storage directory
                $file->move(public_path('storage/profile'), $fileName);

                //Save the relative path to the database
                $imagePath = 'storage/profile/' . $fileName;
                $data['profile_picture'] = $imagePath;

        }

        User::create($data);

        return response()->json([
            'status' => "success",
            'message' => "New user created successfully"
        ], 201);
    }
    
    public function login(Request $request){
        $validator = Validator::make($request->all(), [
            'email' => 'required',
            'password' => 'required',
        ]);

        if($validator->fails()){
            return response()->json([
                'status' => "error",
                'message' => $validator->errors()
            ], 400);
        }

        if(Auth::attempt([
            'email' => $request->email,
            'password' => $request->password
        ])){
            $user = Auth::user();
            $response['token'] = $user->createToken('BlogApp')->plainTextToken;
            $response['email'] = $user->email;
            $response['name'] = $user->name;

            return response()->json([
                'status' => "success",
                'message' => "Logged in sucessfully",
                'data' => $response
            ], 200);
        } else {
             return response()->json([
                'status' => "error",
                'message' => "Invalid credentials"              
            ], status: 400);
        }
    }

    public function profile(Request $request)
    {
        $user = Auth::user();
        if(!$user){
            return response()->json([
                'status' => "error",
                'message' => "User not found"
            ], 404);
        }

        return response()->json([
            'status' => "success",
            'data' => $user
        ], 200);
    }

    public function logout(Request $request)
    {
        $user = Auth::user();
        if(!$user){
            return response()->json([
                'status' => "error",
                'message' => "User not found"
            ], 404);
        }

        $user->tokens()->delete();

        return response()->json([
            'status' => "success",
            'message' => "Logged out successfully"
        ], 200);
    }
}
