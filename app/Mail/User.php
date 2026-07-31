<?php

declare(strict_types=1);


namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class User extends Mailable
{
    use Queueable;
    use SerializesModels;

    /**
     * 最大错误次数.
     * @var int
     */
    public $maxExceptions = 3;

    public function __construct(protected $title, protected $content) {}

    public function build()
    {
        return $this->from(env('MAIL_FROM_ADDRESS'), env('MAIL_FROM_NAME'))
            ->view('mails.user')
            ->subject($this->title)->with([
                'title'   => $this->title,
                'content' => $this->content,
            ]);
    }
}
