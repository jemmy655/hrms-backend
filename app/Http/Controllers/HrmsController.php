<?php

namespace App\Http\Controllers;

use App\Http\Requests;

class HrmsController extends Controller
{
    /**
     * @var int
     */
    protected $statusCode = 200;

    /**
     * @return mixed
     */
    public function getStatusCode()
    {
        return $this->statusCode;
    }

    /**
     * @param mixed $statusCode
     * @return $this
     */
    public function setStatusCode($statusCode)
    {
        $this->statusCode = $statusCode;

        return $this;
    }

    /**
     * @param $data
     * @param array $header
     * @return \Illuminate\Http\JsonResponse
     */
    public function respond($data, $header = []) {
        return response()->json($data, $this->getStatusCode(), $header);
    }

    /**
     * @param $err
     * @param string $message
     * @return \Illuminate\Http\JsonResponse
     */
    public function respondWithError($err, $message = "Error Occurred"){
        return $this->respond([
            'code' => $this->getStatusCode(),
            'message' => $message,
            'error' => $err
        ]);
    }

    /**
     * @param $data
     * @param string $message
     * @return \Illuminate\Http\JsonResponse
     */
    public function respondWithSuccess($data, $message = "Operation Successful"){
        return $this->respond([
            'code' => $this->getStatusCode(),
            'message' => $message,
            'data' => $data
        ]);
    }

}
