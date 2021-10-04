<?php
interface IDrama_Provider
{
	/**
	 * Returns if the provider's API is available/up.  
	 *	 
	 * @return bool
	 */
	public function is_available(): bool;
	
	/**
	 * Calculates the drama level for the pasted text using the provider's algorithm
	 *
	 * @param string $text text to process
	 * @param null|int null can be 
	 *
	 * @return null|int null can be returned if the provider cannot process the text.  This is to distinguish
	 * the lack of drama (zero) 
	 */
	public function calculate_drama(string $text): ?int;

}
