<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;
use Config;

class ErrorReportMailer extends Mailable
{
    use Queueable, SerializesModels;

    private $content;
    private $exception;
    private $exceptionMessage;
    private $request;
    private $requestJson;
    private $header;
    private $requestIP;
    private $requestURI;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($exception, $exceptionMessage, $request, $requestJson, $content, $header, $requestIP, $requestURI)
    {
        $this->request = $request;
        $this->requestJson = $requestJson;
        $this->exception = $exception;
        $this->exceptionMessage = $exceptionMessage;
        $this->content = $content;
        $this->header = $header;
        $this->requestIP = $requestIP;
        $this->requestURI = $requestURI;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('emails.error_report_simple')
                    // ->subject('Error report of '.Config::get('app.name').' ('.Config::get('app.env').') on '.request()->server('SERVER_ADDR'))
                    ->subject('('.Config::get('app.env').') '.Config::get('app.name').': '.$this->exceptionMessage.' on '.request()->server('SERVER_ADDR'))
                    // ->from(['address' => env('MAIL_USERNAME'), 'name' => 'System-Mailer'])
                    ->with('client_ip', request()->ip())
                    ->with('request', $this->request)
                    ->with('header', $this->header)
                    ->with('requestJson', $this->requestJson)
                    ->with('exception', $this->exception)
                    ->with('content', $this->content)
                    ->with('requestIP', $this->requestIP)
                    ->with('requestURI', $this->requestURI);
    }
}
