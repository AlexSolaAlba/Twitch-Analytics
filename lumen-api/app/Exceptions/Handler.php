<?php

namespace App\Exceptions;

use Illuminate\Http\Request;
use Laravel\Lumen\Exceptions\Handler as ExceptionHandler;
use Throwable;

class Handler extends ExceptionHandler
{
    protected $dontReport = [];

    public function report(Throwable $error)
    {
        parent::report($error);
    }

    public function render($request, Throwable $error)
    {
        return parent::render($request, $error);
    }
}
