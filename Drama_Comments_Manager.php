<?php
require_once 'Drama_Manager.php';
require_once 'Drama_Grid_Column_Adaptor_Trait.php';

class Drama_Comments_Manager
{
	// grants drama column in admin grid
	use Drama_Grid_Column_Adaptor_Trait;

	protected const COLUMN_NAME = 'comment_drama';
	public static function init():void {

		// save drama level on changes to comment
		add_action('comment_post', 'Drama_Comments_Manager::save_drama_on_post', 10, 3);
		add_action('edit_comment', 'Drama_Comments_Manager::save_drama_on_edit', 10, 2);

		// add drama level column to admin comment grid
		self::setup_column(Drama_Grid_Column_Adaptor_Trait::$COLUMN_TYPE_COMMENTS);
	}
	
	/**
	 * comment_post hook handler.  Fires on comment inital save.  Saves calculated drama level to a meta value
	 *
	 * @param int $comment_ID comment database id
	 * @param int|string $comment_approved 1 if the comment is approved, 0 if not, 'spam' if spam
	 * @param array $commentdata comment data in array form
	 * @return void
	 */
	public static function save_drama_on_post(int $comment_ID, $comment_approved, array $commentdata): void {
		$drama = strval(Drama_Manager::get_drama_level($commentdata['comment_content']));
		update_comment_meta($comment_ID, Drama_Manager::DRAMA_META, $drama);
	}

	/**
	 * edit_comment hook handler.  Fires on edits to the comment Saves calculated drama level to a meta value
	 *
	 * @param int $comment_ID comment database id	 
	 * @param array $commentdata comment data in array form
	 * @return void
	 */
	public static function save_drama_on_edit(int $comment_ID, array $data): void {
		$drama = strval(Drama_Manager::get_drama_level($data['comment_content']));
		update_comment_meta($comment_ID, Drama_Manager::DRAMA_META, $drama);
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
		return __('Drama Lvl', 'textdomain');
	}
	/**
	 * {@inheritdoc}
	 */
	public static function get_object_drama_level(int $object_id): string {
		return strval(get_comment_meta($object_id, Drama_Manager::DRAMA_META, true));
	}
}