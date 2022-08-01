<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Hash;
use Validator;
use App\Models\User;
use DB;
use Auth;

class UserController extends Controller
{
    //
    public function register(Request $request){
        $validator = Validator::make($request->all(),[
            'email' => 'required|string|unique:users',
            'dob' => 'required|date',
            'gender' => 'required|string',
            'password' => 'required|min:6|confirmed',
        ]);

        if($validator->fails()){
            return response()->json([
            'success' => false,
            'message' => $validator->messages()->toArray()
            ], 500);
        }

        $gender = 'n';

        if($request->gender == "male" || $request->gender == "Male"){
            $gender = 'M';
        } else if($request->gender == "female" || $request->gender == "Female"){
            $gender = 'F';
        }

        $data = ['email'=>$request->email,'dob'=>$request->dob,'gender'=>$gender,'password'=>Hash::make($request->password),'category'=>'0'];

        $insertID = DB::table('users')->insertGetId($data);

        Log::info(date('Ymd'));
        Log::info(date('hisa'));

        if($insertID < 10){
            $userID = 'C00000'.$insertID.'.'.date('Ymd').'.'.date('his');
        }
        if($insertID > 9 && $insertID < 100){
            $userID = 'C0000'.$insertID.'.'.date('Ymd').'.'.date('his');
        }
        if($insertID > 99 && $insertID < 1000){
            $userID = 'C000'.$insertID.'.'.date('Ymd').'.'.date('his');
        }
        if($insertID > 999 && $insertID < 10000){
            $userID = 'C00'.$insertID.'.'.date('Ymd').'.'.date('his');
        }
        if($insertID > 9999 && $insertID < 100000){
            $userID = 'C0'.$insertID.'.'.date('Ymd').'.'.date('his');
        }
        if($insertID > 99999 && $insertID < 1000000){
            $userID = 'C'.$insertID.'.'.date('Ymd').'.'.date('his');
        }


        Log::info($userID);

        $data = ['user_id'=>$userID];

        DB::table('users')->where('id',$insertID)->update($data);

        $userData = DB::table('users')->where('id',$insertID)->get();

        return $userData;
    }

    // Add username
    public function add_username(Request $request){
        $validator = Validator::make($request->all(), [
            'username' => 'required|string|unique:users',
            'userid' => 'required',
        ]);

        if($validator->fails()){
            return response()->json([
            'success' => false,
            'message' => $validator->messages()->toArray()
            ], 500);
        }

        $data = ['username'=>$request->username];

        DB::table('users')->where('user_id',$request->userid)->update($data);

        return $request->all();

    }

    // Login
    public function login(Request $request){
        $validator = Validator::make($request->all(), [
            'username' => 'required|string',
            'password' => 'required|min:6'
        ]);

        if($validator->fails()){
            return response()->json([
            'success' => false,
            'message' => $validator->messages()->toArray()
            ], 500);
        }

        $credentials = $request->only(["username","password"]);

        $user = User::where('username',$credentials['username'])->first();

        if($user){
            if(!Auth::attempt($credentials)){
                $responseMessage = "Invalid username or password";
                return response()->json([
                        "success" => false,
                        "message" => $responseMessage,
                        "error" => $responseMessage
                    ], 422);
            }

            $accessToken = Auth::user()->createToken('authToken')->accessToken;
            $responseMessage = "Login Successful";

        // $request->session()->put('state', $state = Str::random(40));

        // $query = http_build_query([
        //     'client_id' => '3',
        //     'redirect_uri' => 'http://localhost:3000/user',
        //     'response_type' => 'code',
        //     'scope' => '',
        //     'state' => $state,
        // ]);

        //     return redirect('/oauth/authorize?'.$query);

            return $this->respondWithToken($accessToken,$responseMessage,auth()->user());
        } else {
            $responseMessage = "Sorry, this user does not exist";

            return response()->json([
                        "success" => false,
                        "message" => $responseMessage,
                        "error" => $responseMessage
                    ], 422);
        }
    }

    // Logout
    public function logout(){
        $user = Auth::guard("api")->user()->token();
        $user->revoke();
        $responseMessage = "successfully logged out";
        return response()->json([
            'success' => true,
            'message' => $responseMessage
        ], 200);
    }
}
