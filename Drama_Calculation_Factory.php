<?php

require_once 'IDrama_Provider.php';
require_once 'Drama_Manager.php';
require_once __DIR__ . '/providers/Disabled.php';

class Drama_Calculation_Factory {

	// default provider to use if option is not set or current provider's API is down
	public const DEFAULT_DRAMA_PROVIDER = 'Disabled';
	
	// local folder for provider integrations
	protected const PROVIDER_FOLDER = __DIR__ . '/providers/';
	
	/**
	 * Returns a string list of providers PHP files found in PROVIDER_FOLDER
	 *
	 * @return array
	 */
	public static function get_drama_providers(): array
	{
		static $providers = [];
		foreach(glob(self::PROVIDER_FOLDER . '/*.php') as $provider)
		{
			$providers[] = basename($provider, '.php');
		}

		return $providers;
	}
	
	/**
	 * Returns the current provider class based on the WP setting
	 *
	 * @return IDrama_Provider
	 */
	public static function get_current_drama_provider(): IDrama_Provider
	{
		return self::get_drama_provider(get_option(Drama_Manager::DRAMA_OPTION), self::DEFAULT_DRAMA_PROVIDER);
	}

	/**
	 * Returns a provider class
	 * 
	 * @param string $providerName provider PHP file/class name
	 *
	 * @return IDrama_Provider
	 */
	public static function get_drama_provider(string $providerName): IDrama_Provider
	{
		$a = self::DEFAULT_DRAMA_PROVIDER;
		$returnValue = new $a();

		if(in_array($providerName, self::get_drama_providers()))
		{
			require_once self::PROVIDER_FOLDER . '/' . $providerName . '.php';
			
			$provider = new $providerName();
			// use the provider if their API is up
			$returnValue = $provider->is_available() ? $provider : $returnValue;
		}
		return $returnValue;
	}
}