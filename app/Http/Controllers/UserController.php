<?php

namespace App\Http\Controllers;

use App\Transformers\UserTransformer;
use App\User;
use Illuminate\Http\Request;
use League\Fractal\Manager;
use League\Fractal\Pagination\IlluminatePaginatorAdapter;
use League\Fractal\Resource\Collection;
use League\Fractal\Resource\Item;
use Validator;
use Illuminate\Support\Facades\Lang;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Facades\JWTAuth;

/**
 * @property Manager fractal
 * @property int perPage
 */
class UserController extends HrmsController
{
    /**
     * @var UserTransformer
     */
    private $userTransformer;

    /**
     * UserController constructor.
     * @param UserTransformer $userTransformer
     * @param Manager $fractal
     */
    public function __construct(UserTransformer $userTransformer, Manager $fractal)
    {
        $this->userTransformer = $userTransformer;
        $this->fractal = $fractal;
        $this->perPage = 20;
        $this->middleware('jwt.auth', ['except' => ['authenticate']]);
        $this->middleware('permission:list-users', ['only' => ['index']]);
        $this->middleware('permission:create-users', ['only' => ['store']]);
        $this->middleware('permission:view-user', ['only' => ['show']]);
        $this->middleware('permission:update-users', ['only' => ['update']]);
    }

    /**
     * @param $type
     * @param $data
     * @param null $paginator
     * @return array
     */
    private function transform($type, $transformer, $data, $paginator=null) {

        if ($type == "Item"){
            $resource = new Item($data, $transformer);
        } elseif ($type == "Collection"){
            $resource = new Collection($data, $transformer);
            $resource->setPaginator(new IlluminatePaginatorAdapter($paginator));
        }

        return $this->fractal->createData(
            $resource
        )->toArray();

    }


    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $this->perPage = empty($request->all()) ? $this->perPage : $request->get('per-page');
        $paginator = User::paginate($this->perPage);

        $users = $paginator->getCollection();

        $paginator->appends(array_diff_key($request->all(), array_flip(['page'])));
        $data = $this->transform("Collection", $this->userTransformer, $users, $paginator);

        return $this->respondWithSuccess($data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|max:255',
            'email' => 'required|email|max:255|unique:users',
            'password' => 'required|min:6|confirmed',
        ]);

        if($validator->fails()){

            return $this->setStatusCode(400)->respondWithError($validator->errors());

        } else {

            $user = User::create([
                'name' => $request->get('name'),
                'email' => $request->get('email'),
                'password' => bcrypt($request->get('password')),
            ]);

            $data = $this->transform("Item", $this->userTransformer, $user);

            return $this->respondWithSuccess($data, "User created successfully.");
        }

    }

    /**
     * Display the specified resource.
     *
     * @param User $user
     * @return \Illuminate\Http\Response
     * @internal param App\User|User $users
     * @internal param int $id
     */
    public function show($user)
    {
        $user = User::findOrFail($user);
        if ( ! $user ) {
            return $this->setStatusCode(404)->respondWithError(['User does not Exist']);
        }

        $data = $this->transform("Item", $this->userTransformer, $user);

        return $this->respondWithSuccess($data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param User $user
     * @return \Illuminate\Http\Response
     * @internal param int $id
     */
    public function update(Request $request, $user)
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
