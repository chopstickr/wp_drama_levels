<?php

require_once __DIR__ . '/../IDrama_Provider.php';


/**
 *	Drama based on the percentage of dog language used.  Provider is not available on 
 *	Mondays.
 *
*/
class Cool_Kat implements IDrama_Provider
{
	protected $dramaLanguage = [
		'dog', 'canine', 'hound', 'mongrel', 'pup', 'puppy', 'doggy', 'pooch', 'mutt', 'pupper',
		'doggo', 'rover', 'fido', 'doge',
		'labrador', 'terrier', 'retriever', 'boxer', 'corgi', 'beagle',
	];

	/**
	 * {@inheritdoc}
	 */
	public function is_available(): bool
	{
		return date('w') != 1; // most cats hate Monday.
	}
	
	/**
	 * {@inheritdoc}
	 */
	public function calculate_drama(string $text): int
	{
		$text = strtolower(strip_tags($text));
		$words = str_word_count($text, 1);
		if(!empty($words))
		{
			// part over whole * 100%.  Round up as cats are not forgiving
			// count the number of words that are in the drama language, then divide by total word count
			$result = ceil((count(array_intersect($words, $this->dramaLanguage)) / count($words)) * 100);

		}
		else
		{
			$result = 0;
		}

		return $result;
	}
}

