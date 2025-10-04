<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\BaseController;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Temp;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;


class AuthController extends BaseController
{

    public function checkPhone(Request $request)
    {
        $request->validate(['phone' => 'required|string|min:9|max:19']);
        $phone = $request->phone;

        try {
            $exists = User::where('phone', $phone)->exists();
            $newTemp = Temp::updateOrCreate(
                [ 'phone' => $phone] ,
                [
                    'phone' => $phone ,
                    'tempState' => $exists ? 'login' : 'register'
                ]
            );
            $data = [
                'phone'=>$phone,
                'exists' => $exists,
                'newTemp' => $newTemp,
                'tempUUID' => $newTemp->uuid
            ];

            if ($exists) {
                return $this->BaseResponse(true , "Phone  exists" , $data );
            } else {
                return $this->BaseResponse(false , "Phone  not exists" , $data ,   450 );
            }
        } catch (\Exception $e) {
            return $this->BaseResponse(false , "server error Exception !!" , ['serverError' =>  $e->getMessage()] , 500 );

        }



    }









    public function register(Request $request)
    {
        $request->validate([
            'password'      => 'required|string|max:64',
            'user_name'     => 'required|string|max:64',
            'tempUUID'      => 'required|string|max:64|exists:temps,uuid',
        ]);


        try {
            $tempRecord = Temp::where('uuid', "=" , $request->tempUUID)->first();

            if(User::where('phone', $tempRecord->phone)->exists()) {
                return $this->BaseResponse(false , "user already exist in users table");
            }


            $tempRecord->password =  Hash::make($request->password);
            $tempRecord->user_name = $request->user_name;
            // generate OTP and send it throw SMS
            $tempRecord->OTP = "1234";


            $data = [
                'tempRecord'=> $tempRecord, // its should delete in production mode
                'request' => $request->all(),
            ];

            if($tempRecord->save()) {
                return $this->BaseResponse(true , "temp update and otp send successfully" , $data );

            } else {
                return $this->BaseResponse(false , "fail db" , $data  );
            }


        } catch (\Exception $e) {
            return $this->BaseResponse(false , "server error Exception register !!" , ['serverError' =>  $e->getMessage()] , 500 );

        }



    }

    public function login(Request $request)
    {
        $req = $request->validate([
            'password'      => 'required|string|max:64',
            'tempUUID'      => 'required|string|max:64|exists:temps,uuid',
        ]);
        try {
            $temp = Temp::where('uuid',  $req['tempUUID'])->first();
            $user = User::where('phone' , $temp->phone)->first();
            if (!$user || !Hash::check($req['password'], $user->password)) {
                return $this->BaseResponse(false , "password not correct or user not exist !!" , [] , 450  );
            }
            // generate OTP and send it throw SMS
            $temp->OTP = "1234";
            $temp->save();
            $data = [
                'request'=> $req,
                'temp' => $temp ,
                'user' => $user,
                'user->password' => $user->password,
                'inputPassword' => $req['password']
            ];

            return $this->BaseResponse(true , "password is correct and OTP sended successfully !" , $data );

        } catch(\Exception $e) {
            return $this->BaseResponse(false , "server error Exception register !!" , ['serverError' =>  $e->getMessage()] , 500 );

        }

    }





    public function checkOtp(Request $request)
    {
        $req = $request->validate([
            'otp'      => 'required|string|size:4',
            'tempUUID' => 'required|string|max:64|exists:temps,uuid',
        ]);


        $temp = Temp::where("uuid" , $req["tempUUID"])
        ->where('OTP' , $req['otp'])
        ->first();
        if(!$temp) {
            return $this->BaseResponse(false , "otp dos not  match !!" , [] , statusCode: 450 );
        }

        $user = $temp['tempState'] === 'register'
        ? User::create([
                'name' => $temp['user_name'],
                'password' => $temp['password'],
                'phone' => $temp['phone']
            ])
        : User::where('phone' , $temp['phone'])->first();




        if($user) {
            $user->tokens()->delete();
            $token = $user->createToken(
                'api-souq-marin-7334',
                [], // abilities, empty = full access
                Carbon::now()->addDay() // expire in 1 day
            )->plainTextToken;
            $temp->delete();

            // session()->regenerate();
            // $request->session()->regenerate(); // 🔑 important

            // $token = $user->createToken('api-token')->plainTextToken;

            $data = [
                'request'   => $req,
                'temp'      => $temp,
                'user'     => $user,
                'token'     => $token,

            ];
            return $this->BaseResponse(true , "otp its match and user is created or login  " , $data );

        } else {
            return $this->BaseResponse(false , "cant create or found user !! " , [] , 500 );

        }
    }

    public function logout(Request $request)
    {
        // $request->user()->currentAccessToken()->delete();

        return response()->json(['message' => 'Logged out']);
    }
    public function me(Request $request)
    {
        return $this->BaseResponse(true , "this me function and you allowed to enter in   "  );


    }
    public function profile(Request $request)
    {
        return $this->BaseResponse(true , "this me function and you allowed to enter in   "  );


    }
    public function checkAuth(Request $request)
    {


        $user = auth()->user();


        $data = [
            'request' => $request->all(),
            'user' => $user,

        ];

        if ($user) {
            return $this->BaseResponse(true , "check auth allowed" , $data  );

        } else {
            return $this->BaseResponse(false , "check auth not allowed " , $data ,  401 );

        }


    }
}
