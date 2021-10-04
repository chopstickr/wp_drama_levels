<?php
require_once 'Drama_Manager.php';
require_once 'Drama_Grid_Column_Adaptor_Trait.php';

class Drama_Post_Manager {
	
	// grants drama column in admin grid
	use Drama_Grid_Column_Adaptor_Trait;

	protected const COLUMN_NAME = 'post_drama';
	protected const AVERAGE_COMMENT_COLUMN_NAME = 'average_comment_drama';

	public static function init():void {
		
		// actions for adding drama level to posts after saving
		add_action('save_post', 'Drama_Post_Manager::add_drama_meta', 10, 2);

		// alter admin page post grid
		if(is_admin()) {
			// filters to add columns to admin post grid
			add_filter('manage_post_posts_columns', 'Drama_Post_Manager::add_average_comment_column');
			// show drama levels in grid
			add_action('manage_post_posts_custom_column', 'Drama_Post_Manager::show_average_comment_drama_level', 10, 2);
		}
		
		// add drama level column to admin post grid
		self::setup_column(Drama_Grid_Column_Adaptor_Trait::$COLUMN_TYPE_POSTS);
	}
	
	/**
	 * save_post hook handler.  Fires on post save.  Saves calculated drama level to post's meta value
	 *
	 * @param int $post_id post database id
	 * @param WP_Post $post post object
	 * @return void
	 */
	public static function add_drama_meta(int $post_id, WP_Post $post): void { 
		$drama = strval(Drama_Manager::get_drama_level($post->post_content));
		update_post_meta($post_id, Drama_Manager::DRAMA_META, $drama);
	}

	/**
	 * {@inheritdoc}
	 */
	public static function get_column_key(): string {
		return self::COLUMN_NAME;
	}
	/**
	 * {@inheritdoc}
	 */
	public static function get_column_name(): string {
		return __('Post<br>Drama Lvl', 'textdomain');
	}
	/**
	 * {@inheritdoc}
	 */
	public static function get_object_drama_level(int $object_id): string {
		return strval(get_post_meta($object_id, Drama_Manager::DRAMA_META, true));
	}

	/**
	 * Add the average comment post drama level custom field to the post grid
	 *
	 * @param array $columns current columns in the grid
	 *
	 * @return array
	 */
	public static function add_average_comment_column(array $columns): array {
		
		$columns = array_merge(
			$columns,
			// average drama for all comments
			[self::AVERAGE_COMMENT_COLUMN_NAME => __('Avg Comment<br>Drama Lvl', 'textdomain')]
		);
		
		return $columns;
	}

	/**
	 * Calculates/shows the average comment post drama level for the given post
	 *
	 * @param string $column_key current column being displayed
	 * @param int $post_id database post id
	 *
	 * @return void
	 */
	public static function show_average_comment_drama_level(string $column_key, int $post_id):void {
		global $wpdb;
		switch($column_key) {
			
			// average comment drama level
			case self::AVERAGE_COMMENT_COLUMN_NAME:
				/*...some basic interaction with the WordPress database. */
				echo $wpdb->get_var($wpdb->prepare
				("
					select 
						round(avg(coalesce(cm.meta_value, 0))) as `average`
					from
						{$wpdb->comments} c
					inner join
						{$wpdb->commentmeta} cm on cm.comment_ID = c.comment_ID
					where
						/* could filter by comment status */
						cm.meta_key = %s and
						c.comment_post_id = %d",
				[Drama_Manager::DRAMA_META, $post_id]));
				break;
		}
	}
}
