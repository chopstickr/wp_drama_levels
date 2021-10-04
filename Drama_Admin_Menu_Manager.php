<?php

require_once 'Drama_Manager.php';

class Drama_Admin_Menu_Manager {

	protected $name;
	protected $value;
	protected $defaultValue;

	public function __construct(string $name, string $defaultValue, array $values) {
		$this->name = $name;
		$this->defaultValue = $defaultValue;

		$this->value = strval(get_option($this->name, $this->defaultValue));
		$this->acceptableValues = $values;

		// menus
		add_action('admin_menu', [$this, 'init_menu']);

		// settings page
		add_action('admin_init', [$this, 'init_settings']);
	}

	/**
	 * Sets up the WP settings that controll the current drama provider
	 *
	 * @return void
	 */
	public function init_settings(): void {

		register_setting('drama-setting-admin', $this->name, [$this, 'sanitize']);
		add_settings_section('default','', function(){print _('Your posts and comments will be analyzed by the selected 3rd party drama provider.  If the provider is unavailable, no drama score will be recorded.', 'drama-text-domain');}, 'drama-admin');
		add_settings_field($this->name, 'Default Drama Provider', [$this, 'value_callback'], 'drama-admin', 'default');
	}

	/**
	 * Calls WP hooks to setup admin menus
	 *
	 * @return void
	 */
	public function init_menu(): void {
		// intro page
		add_menu_page('Drama Levels', 'Drama Levels', 'manage_options', 'custompage', [$this, 'intro_page'], 'dashicons-smiley');
		// settings menu for controlling current drama provider
		add_options_page('Drama Level', 'Drama Level', 'manage_options', 'drama-setting-admin', [$this, 'create_admin_page']);
	}
	
	/**
	 * Shows intro/readme page for background on the plugin
	 *
	 * @return void
	 */
	public function intro_page(): void {
		$imagePath = esc_attr(plugin_dir_url(__FILE__) . 'images/');
		?>
		<div class="wrap">
			<h1>Drama Levels Plugin</h1>
			<p>
				This WordPress plugin demonstrates the requirements for the Senior PHP Developer role.  The purpose of this plugin is to “calculate” the percentage of drama/strife in comments and posts based on the tone of the content.  The plugin can support multiple tone analyzers controlled by a custom WordPress setting.  The drama level is saved as custom meta tags for the comments/posts.<br><br>
				All the tone analyzers are silly fictional classes to demonstrate the functionality of the framework.  My inspiration for this plugin is base on the general negativity I see in the WordPress blogs I frequent (ie HackADay)<br><br>
				To view admin setting page <a href="<?php esc_attr(get_admin_url()) ?>options-general.php?page=drama-setting-admin">click here</a>
			</p>
			<h2>Features</h2>
			<ul style="padding: revert; list-style: revert;">
				<li>Admin Post grid shows the drama level per post and is sortable.</li>
				<li>The average comment drama level is shown per Post.</li>
				<img src="<?php echo $imagePath; ?>2021-09-01_00h14_40.png">
				<li>Admin comment grid shows drama level per comment and is sortable.</li>
				<img src="<?php echo $imagePath; ?>2021-09-01_00h16_29.png">
				<li>The drama provider framework is designed to easily add new providers.  A new provider adaptor should implement the IDrama_Provider interface and be placed in the provider folder.  It will automatically be selectable as the default provider via the setting interface.</li>
				<li>If the selected drama provider is unavailable for any reason, the system will fall back to the disabled provider.</li>
				<img src="<?php echo $imagePath; ?>2021-09-01_00h17_51.png">
			</ul>
			
		</div>
		<?php

	}

	/**
	 * Options page callback
	 *
	 * @return void
	 */	
	public function create_admin_page(): void {
		?>
		<div class="wrap">
			<h1>Drama Level Settings</h1>
			<form method="post" action="options.php">
			<?php
				// This prints out all hidden setting fields
				settings_fields('drama-setting-admin');
				do_settings_sections('drama-admin');
				submit_button();
			?>
			</form>
		</div>
		<?php
	}
	
	/**
	 * Clean the user's input
	 *
	 * @param mix $input form input will be force cast to string
	 *
	 * @return string
	 */
	public function sanitize($input): string {
		// simple array search to sanitize the user's input
		$input = strval($input);
		return in_array($input, $this->acceptableValues) ? $input : $this->defaultValue;
	}
	
	/**
	 *
	 * Generate dropdown/select input with available providers
	 *
	 * @return void
	 */
	public function value_callback(): void {
		// 
		$selectElement = '<select name="' . esc_attr($this->name) . '">';

		foreach($this->acceptableValues as $acceptableValue)
		{
			$selectElement .= "\n<option " . selected($acceptableValue, $this->value, false) . ' value="' . esc_attr($acceptableValue) . '" >' . esc_html($acceptableValue) . '</option>';
		}

		echo $selectElement . "\n</select>";
	}
}
