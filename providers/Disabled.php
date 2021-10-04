<?php

require_once __DIR__ . '/../IDrama_Provider.php';

/**
 *
 *	Provider is always available but does not provide a drama calculation
 *
 */
class Disabled implements IDrama_Provider
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
	 */
	public function calculate_drama(string $text): ?int
	{
		return null;
	}
}