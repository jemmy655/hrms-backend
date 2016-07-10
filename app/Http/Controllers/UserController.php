<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use Validator;
use Illuminate\Support\Facades\Lang;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Facades\JWTAuth;

class UserController extends HrmsController
{
    /**
     * UserController constructor.
     */
    public function __construct()
    {
        $this->middleware('jwt.auth', ['except' => [
            'authenticate'
        ]]);
    }


    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
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
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
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
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function authenticate(Request $request)
    {
        $credentials = $request->only('email', 'password');

        $validator = Validator::make($credentials, [
            'email' => 'required|email',
            'password' => 'required|',
        ]);

        if($validator->fails()){

            return $this->setStatusCode(400)->respondWithError($validator->errors());

        } else {

            try {
                if (!$token = JWTAuth::attempt($credentials)) {
                    return $this->setStatusCode(401)->respondWithError([Lang::get('auth.failed')]);
                }
            } catch (JWTException $e) {
                return $this->setStatusCode(500)->respondWithError([Lang::get('auth.error_generating_token')]);
            }

            return $this->respondWithSuccess(compact('token'));
        }
    }
}
