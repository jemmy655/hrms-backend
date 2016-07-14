<?php
/**
 * Created by PhpStorm.
 * User: oshomo.oforomeh
 * Date: 14/07/2016
 * Time: 6:33 PM
 */

namespace App\Traits;

use Exception;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\ModelNotFoundException;

trait ResponseHandlerTrait
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

    /**
     *
     * @param Request $request
     * @param Exception $e
     * @return \Illuminate\Http\JsonResponse
     */
    protected function getJsonResponseForException(Request $request, Exception $e)
    {
        switch(true) {
            case $this->isModelNotFoundException($e):
                $response = $this->modelNotFound();
                break;
            case $this->isQueryException($e):
                $response = $this->queryException();
                break;
            default:
                $response = $this->badRequest();
        }

        return $response;
    }

    /**
     *
     * @param string $message
     * @return \Illuminate\Http\JsonResponse
     * @internal param int $statusCode
     */
    protected function badRequest($message='Bad request')
    {
        return $this->setStatusCode(400)->respondWithError([$message], $message);
    }

    /**
     *
     * @param string $message
     * @return \Illuminate\Http\JsonResponse
     * @internal param int $statusCode
     */
    protected function modelNotFound($message='Record not found')
    {
        return $this->setStatusCode(404)->respondWithError(["The requested data cannot be found on the server."], $message);
    }

    /**
     *
     * @param string $message
     * @return \Illuminate\Http\JsonResponse
     * @internal param int $statusCode
     */
    protected function queryException($message='Internal server Error')
    {
        return $this->setStatusCode(404)->respondWithError(["There is an issue with our server. we are aware of this and fixing as we speak."], $message);
    }

    /**
     *
     * @param Exception $e
     * @return bool
     */
    protected function isModelNotFoundException(Exception $e)
    {
        return $e instanceof ModelNotFoundException;
    }

    /**
     *
     * @param Exception $e
     * @return bool
     */
    protected function isQueryException(Exception $e)
    {
        return $e instanceof QueryException;
    }

}