<?php
namespace App\Notifications;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Messages\NexmoMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;

class ResetPasswordNotification extends Notification implements ShouldQueue
{
	use Queueable;

	protected $user;
	protected $token;
	protected $field;

	public function __construct($user, $token, $field)
	{
		$this->user = $user;
		$this->token = $token;
		$this->field = $field;
	}

	public function via($notifiable)
	{
		return ['mail'];
	}

	public function toMail($notifiable)
	{
		$resetPwdUrl = config('app.url_api').'/password/reset/' . $this->token;

		return (new MailMessage)
			->subject(trans('mail.reset_password_title'))
			->line(trans('mail.reset_password_content_1'))
			->line(trans('mail.reset_password_content_2'))
			->action(trans('mail.reset_password_action'), $resetPwdUrl)
            ->line(trans('mail.reset_password_content_3'))
            ->salutation(trans('mail.Regards'))
            ->greeting(trans('mail.Hello!'));
	}

}
