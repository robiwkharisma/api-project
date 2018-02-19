<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;

use App\Helpers\ApplicationVersion;
use App\Helpers\ErrorReporter;
use Exception;

class ApiBaseController extends Controller
{
    const CODE_0    = 0;    // Success
    const CODE_1000 = 1000; // Invalid item ID
    const CODE_9000 = 9000; // Validation error
    const CODE_9001 = 9001; // Error unknown
    const CODE_9100 = 9100; // Bad syntax
    const CODE_9101 = 9101; // Unauthorized
    const CODE_9111 = 9111; // Forbidden
    const CODE_9999 = 9999; // Server Maintenance

    public $code      = 0;
    public $data      = null;
    public $message   = '';
    public $error     = 0;
    public $httpError = 500;

    public $exception;

    private function buildResponse()
    {
        $re = [
            'code'    => $this->code,
            'message' => empty($this->message) ? $this->buildResponseMessage() : $this->message,
            'data'    => $this->data,
        ];

        if (\App::environment(['local', 'dev', 'stage', 'testing']))
        {
            $appVersionControl = new ApplicationVersion;

            $re['version'] = [
                'api' => $appVersionControl->getApplicationVersion('api'),
                'web' => $appVersionControl->getApplicationVersion('web'),
            ];
        }

        return $re;
    }

    private function buildResponseMessage()
    {
        $this->message = trans('error_api.'.$this->code);

        return $this->message;
    }

    private function buildHttpErrorCode()
    {
        switch ($this->code) {
            // case 0:
            // case 1000:
            // case 9000:
            //     $this->httpError = 200;
            //     break;
            // case 9001:
            // case 9100:
            //     $this->httpError = 400;
            //     break;
            // case 9101:
            //     $this->httpError = 401;
            //     break;
            // case 9111:
            //     $this->httpError = 403;
            //     break;
            case 9403:
                $this->httpError = 403;
                break;
            default:
                $this->httpError = 200;
                break;
        }
        
        return $this->httpError;
    }

    public function response()
    {
        // Log when error occured
        if ($this->error && $this->exception instanceof Exception)
        {
            ErrorReporter::notifyException($this->exception);
        }

        return response()->json($this->buildResponse(),
            $this->buildHttpErrorCode());
    }

    public function responseForError($exception){

        $this->exception = $exception;
        $this->error = TRUE;
        $this->message = $exception->getMessage();

        if ($exception->getCode() == '1000') {
            $message = explode('|', $exception->getMessage());
            $this->message = $message[0];
            $this->data = [$message[1] => $message[2]];
        } elseif ($exception->getCode() == '9403') {
            $message = explode('|', $exception->getMessage());
            $this->message = $message[0];
        }
    }
}
