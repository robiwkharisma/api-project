<?php

namespace App\Helpers;

use Exception;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;

use Mail;
use Symfony\Component\Debug\Exception\FlattenException;
use Symfony\Component\Debug\ExceptionHandler as SymfonyExceptionHandler;
use Illuminate\Http\Request;
use App\Jobs\EmailErrorReporter;

/**
 * Class AuthRepositorySentinel
 * @package namespace App\Repositories\Auth\Service;
 */
class ErrorReporter
{
    public $exception;
    public $request;

    public function __construct($exception = null)
    {
        $this->exception = $exception;
        $this->request = new Request;
    }

    /**
     * To notify exception
     *
     * @param Exception $exception
     * @return void
     */
    public static function notifyException(Exception $exception)
    {
        $report = new ErrorReporter($exception);
        $header = \Request::header();
        $newRequest = new Request;
        $request = [
            'request'       => $newRequest->all(),
            'requestIP'     => \Request::ip(),
            'requestURI'    => \Request::url(),
            'requestJson'   => $newRequest->json()->all(),
        ];

        // TODO: use environment check
        if (env('ERROR_REPORT_OPTION') == TRUE && !\App::environment(['local', 'testing']))
        {
            \Log::info("Reporting error...");

            dispatch(new EmailErrorReporter($exception, $header, $request));
        }

        $report->logError();
    }

    /**
     * Log error to laravel.log
     *
     * @return void
     */
    public function logError()
    {
        \Log::error([
            'request' => $this->request->toArray(),
            'requestJson' => json_encode($this->request->json()->all()),
            'exception' => [
                'file' => $this->exception->getFile(),
                'line' => $this->exception->getLine(),
                'message' => $this->exception->getMessage(),
            ],
        ]);
    }
}