<?php

require_once 'Drama_Calculation_Factory.php';
require_once 'Drama_Admin_Menu_Manager.php';
require_once 'Drama_Post_Manager.php';
require_once 'Drama_Comments_Manager.php';
/**
 * Manages delegation setup of menus, hooks, filters.  Controls meta key and custom setting names needed 
 * to store drama provider and drama levels.
 * 
 */
class Drama_Manager
{
	// wordpress option to hold the selected drama provider
	public const DRAMA_OPTION = 'drama_provider';
	// wordpress post/comment meta to hold the drama level
	public const DRAMA_META = 'drama_level';

	public function __construct()
	{
		if(is_admin()) {
			new Drama_Admin_Menu_Manager(self::DRAMA_OPTION, Drama_Calculation_Factory::DEFAULT_DRAMA_PROVIDER, Drama_Calculation_Factory::get_drama_providers());
		}

		// everything is a static class as there is no reason for multiple instances, nor any local properties
		Drama_Post_Manager::init();
		Drama_Comments_Manager::init();
	}
	
	/**
	 * Primary method for interacting with drama providers.  This will return the drama level for the 
	 * passed text.
	 *
	 * @param string $text the text to be analyzed
	 *
	 * @return null|int
	 */
	public static function get_drama_level(string $text): ?int {
		return Drama_Calculation_Factory::get_current_drama_provider()->calculate_drama($text);
	}
}

