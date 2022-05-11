<?php

namespace App\Http\Controllers;

use App\Traits\RestCallable;
use Illuminate\Http\Request;
use App\Http\Requests\LoginRequest;


class AuthController extends Controller
{
    use RestCallable;

    public $api_root;


    public function __construct(){
        $this->api_root = env('API_ROOT');
    }

    public function login(LoginRequest $request){
        
        $result = $this->makePostRequestWithoutToken('get-token/',[
            'username' => 'daud.csbt@gmail.com',
            'password' => '123456'
        ]);
        
        if($result['statusCode'] == 200 && $result['responseJson']['token']){
            $request->session()->put('token', $result['responseJson']['token']);
        }
        
        dump($result);

        //$result['statusCode'] == 400
        
    }

    public function logout(Request $request){
        $request->session()->forget('token');
        return 'logout';
    }
}
