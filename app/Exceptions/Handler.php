<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Symfony\Component\HttpKernel\Exception;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array
     */
    protected $dontFlash = [
        'password',
        'password_confirmation',
    ];

    /**
     * Report or log an exception.
     *
     * @param  \Throwable  $exception
     * @return void
     *
     * @throws \Exception
     */
    public function report(Throwable $exception)
    {
        parent::report($exception);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Throwable  $exception
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @throws \Throwable
     */
    public function render($request, Throwable $exception)
    {
         
        if ($exception instanceof \Symfony\Component\HttpKernel\Exception\NotFoundHttpException){

            // para bloquear requisições AJAX
            // if ($request->expectsJson()){
            //     return response()->json(['error' => 'Not Found URI'], $exception->getStatusCode());
            // }
         
            return response()->json(['error' => 'Not Found URI'], $exception->getStatusCode());
        }
        
        
        if ($exception instanceof \Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException)
            if ($request->expectsJson())
                return response()->json(['error' => 'Method_Not_Allowed'], $exception->getStatusCode());
        
        if ($exception instanceof Tymon\JWTAuth\Exceptions\TokenExpiredException) 
            return response()->json(['token_expired'], $exception->getStatusCode());
        
        if ($exception instanceof Tymon\JWTAuth\Exceptions\TokenInvalidException) 
            return response()->json(['token_invalid'], $exception->getStatusCode());
     
 
                  
        if ($exception instanceof HttpException) {
            $code = $exception->getStatusCode();
            $defaultMessage = \Symfony\Component\HttpFoundation\Response::$statusTexts[$code];
            $message = $exception->getMessage() == "" ? $defaultMessage : $exception->getMessage();
            return response()->json($message, $code);
        } else if ($exception instanceof ModelNotFoundException) {
            $model = strtolower(class_basename($exception->getModel()));
            return response()->json("Does not exist any instance of {$model} with the given id", Response::HTTP_NOT_FOUND);
        } else if ($exception instanceof AuthorizationException) {
            return response()->json($exception->getMessage(), Response::HTTP_FORBIDDEN);
        } else if ($exception instanceof TokenBlacklistedException) {
            return response()->json($exception->getMessage(), Response::HTTP_UNAUTHORIZED);
        } else if ($exception instanceof AuthenticationException) {
            return response()->json($e->getMessage(), Response::HTTP_UNAUTHORIZED);
        } else if ($exception instanceof ValidationException) {
            $errors = $exception->validator->errors()->getMessages();
            return response()->json($errors, Response::HTTP_UNPROCESSABLE_ENTITY);
        } else {
            if (config('app.debug'))
                return response()->json($exception->getMessage());
            else {
                return response()->json('Try later', Response::HTTP_INTERNAL_SERVER_ERROR);
            }
        }

        return parent::render($request, $exception);
    }
}
