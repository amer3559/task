<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Traits\GeneralTrait;
use Illuminate\Http\Request;
use Validator;
use Auth;
class AuthController extends Controller
{

    use GeneralTrait;

    public function login(Request $request)
    {

        try {

            $rules = [
                "email" => "required",
                "password" => "required"

            ];

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                $code = $this->returnCodeAccordingToInput($validator);
                return $this->returnValidationError($code, $validator);
            }

            //login
            $credentials = $request -> only(['email','password']) ;

            // generate token
            $token =  Auth::guard('admin-api') -> attempt($credentials);

            if(!$token)
                return $this->returnError('E001','بيانات الدخول غير صحيحة');

            // get admin data
            $admin = Auth::guard('admin-api') -> user();

            // add token to admin data
            $admin -> api_token = $token;

            //return data
            return $this -> returnData('admin' , $admin);


        }catch (\Exception $ex){
            return $this->returnError($ex->getCode(), $ex->getMessage());
        }



}

}
