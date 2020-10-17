<?php
namespace App\Observers;

use App\Models\User;
use App\Notifications\UserActivated;

class UserObserver
{
	/**
	 * Listen to the Entry updating event.
	 *
	 * @param  User $user
	 * @return void
	 */
	public function updating(User $user)
	{
		// Get the original object values
		$original = $user->getOriginal();

		// Post Email address or Phone was not verified
        if ($original['verified_email'] != 1) {
            if ($user->verified_email == 1) {
                $user->notify(new UserActivated($user));
            }
        }

	}

	/**
	 * Listen to the Entry deleting event.
	 *
	 * @param  User $user
	 * @return void
	 */
	public function deleting(User $user)
	{


	}

	/**
	 * Listen to the Entry saved event.
	 *
	 * @param  User $user
	 * @return void
	 */
	public function saved(User $user)
	{
		// Create a new email token if the user's email is marked as unverified
		if ($user->verified_email != 1) {
			if (empty($user->email_token)) {
				$user->email_token = md5(microtime() . mt_rand());
				$user->save();
			}
		}

	}
}
