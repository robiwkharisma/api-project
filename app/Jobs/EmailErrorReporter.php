<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

use Symfony\Component\Debug\Exception\FlattenException;
use Symfony\Component\Debug\ExceptionHandler as SymfonyExceptionHandler;
use Illuminate\Http\Request;
use Exception;
use Mail;

class EmailErrorReporter implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $exception;
    public $request;
    public $html;
    public $exceptionMessage;
    public $header;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Exception $exception, $header, $request)
    {
        $this->exception        = $this->convertException($exception);
        $this->html             = $this->getHtml($exception);
        $this->exceptionMessage = $this->getMessageException($exception);
        $this->request          = $request;//new Request;
        $this->header           = $header;
    }

    /**
     * convert exception to array
     * 
     * @param  Exception $exception
     * @return mixed
     */
    public function convertException(Exception $exception)
    {
        $except = [];
        do {
            $except[] = $exception->getFile().": ". $exception->getLine()." | ".$exception->getMessage()." | (".$exception->getCode().") ".get_class($exception);
        } while ($exception = $exception->getPrevious());

        return $except;
    }

    /**
     * get content html
     * 
     * @param  Exception $exception
     * @return void
     */
    public function getHtml(Exception $exception)
    {
        $e = FlattenException::create($exception);

        $handler = new SymfonyExceptionHandler();

        $html = $handler->getHtml($e);

        return $html;
    }

    /**
     * get message exception
     * 
     * @param  Exception $exception
     * @return string
     */
    public function getMessageException(Exception $exception)
    {
        return $exception->getMessage();
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $reportTo = env('ERROR_REPORT_MAIL');

        if (!empty($reportTo))
        {
            Mail::to($reportTo)->queue(
                new \App\Mail\ErrorReportMailer(
                    $this->exception,
                    $this->exceptionMessage,
                    $this->request['request'],
                    json_encode($this->request['requestJson']),
                    $this->html,
                    json_encode($this->header, JSON_PRETTY_PRINT),
                    $this->request['requestIP'],
                    $this->request['requestURI']
                )
            );
        }
    }
}
