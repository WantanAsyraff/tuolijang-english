<?php

declare(strict_types=1);


namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ClientInvoice extends Mailable
{
    use Queueable;
    use SerializesModels;

    /**
     * 最大错误次数.
     * @var int
     */
    public $maxExceptions = 3;

    protected $info;

    protected $files;

    /**
     * ClientInvoice constructor.
     */
    public function __construct($info, $files)
    {
        $this->info  = $info;
        $this->files = $files;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $data = $this->from(env('MAIL_FROM_ADDRESS'), env('MAIL_FROM_NAME'))
            ->view('mails.invoice')->subject('亲爱的' . $this->info['collect_name'] . ',请查收您的发票。')->with([
                'num' => $this->info['num'],
            ]);
        if (! $this->files) {
            return $data;
        }
        foreach ($this->files as $v) {
            $data = $data->attach($v['path'], [
                'as' => $v['name'],
            ]);
        }
        return $data;
    }
}
