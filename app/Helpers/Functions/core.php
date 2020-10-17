<?php

/**
 * Get the login field
 *
 * @param $value
 * @return string
 */
function getLoginField($value)
{
	return (filter_var($value, FILTER_VALIDATE_EMAIL)) ? 'email' : 'username';
}
