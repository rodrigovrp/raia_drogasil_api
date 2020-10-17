<?php
namespace App\Notifications;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Messages\NexmoMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;

class UserActivated extends Notification implements ShouldQueue
{
	use Queueable;

	protected $user;

	public function __construct(User $user)
	{
		$this->user = $user;
	}

	public function via($notifiable)
	{
		return ['mail'];
	}

	public function toMail($notifiable)
	{
		return (new MailMessage)
			->subject(trans('mail.user_activated_title', ['appName' => config('app.name'), 'userName' => $this->user->name]))
			->greeting(trans('mail.user_activated_content_1', ['appName' => config('app.name'), 'userName' => $this->user->name]))
			->line(trans('mail.user_activated_content_2'))
			->line(trans('mail.user_activated_content_3', ['appName' => config('app.name')]));
	}

}
