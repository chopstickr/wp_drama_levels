<?php


/**
 * Plugin Name:       Drama Levels
 * Plugin URI:        
 * Description:       Will calculate the amount of drama in posts and comments based on the tone.  Add more drama tone analyzer providers in the providers folder.
 * Version:           1.0
 * Author:            John-David Bain
 * Author URI:        
 * Text Domain:       drama-text-domain
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Domain Path:       
 * GitHub Plugin URI: 
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

require_once 'Drama_Manager.php';

$dramaManager = new Drama_Manager();


