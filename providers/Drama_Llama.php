<?php

require_once __DIR__ . '/../IDrama_Provider.php';

/**
 *
 *	Provider is always available.  Reasoning for drama score is beyond comprehension but always
 *	consistent.
*/
class Drama_Llama implements IDrama_Provider
{
	/**
	 * {@inheritdoc}
	 */
	public function is_available(): bool
	{
		return true;
	}
	
	/**
	 * {@inheritdoc}
	 * Create weird drama level based off of md5 hash
	 */	
	public function calculate_drama(string $text): int
	{
		$text = strip_tags($text);
		// md5 will return 32 characters that are all hex.  
		$hash = md5($text);
		// pull two characters from the hash
		$hash1 = hexdec($hash[0] . $hash[16]);
		// get last 2 digits
		$result = $hash1 % 10;
		$hash1 = $hash1 / 10;
		$result += ($hash1 % 10) * 10 + 1;

		return $result;
	}
}