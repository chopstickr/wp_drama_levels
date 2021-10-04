<?php
require_once 'Drama_Manager.php';

/**
 *	Adaptor for adding the drama level column to common admin grids.
 */
trait Drama_Grid_Column_Adaptor_Trait {

	// php doesn't support constants in traits
	public static $COLUMN_TYPE_POSTS = 1;
	public static $COLUMN_TYPE_COMMENTS = 2;

	/**
	 * Returns the columns html name/id
	 *
	 * @return string
	 */
	abstract public static function get_column_key(): string;
	
	/**
	 * Returns the text shown in the GUI
	 *
	 * @return string
	 */
	abstract public static function get_column_name(): string;
	
	/**
	 * Pulls the drama level from the database object
	 *
	 * @param int $object_id the database id of the object
	 *
	 * @return null|int
	 */
	abstract public static function get_object_drama_level(int $object_id): ?int;
	
	/**
	 * Wrapper to initalize filters/actions for adding the drama field to different admin
	 * grids.  Deals with slight differences in grid's behavior.
	 *
	 * @param int $gridType type of admin grid to add the drama column to
	 *
	 * @return void
	 */
	public static function setup_column(int $gridType):void {
		
		if(is_admin()){
			// map column behavior to interface specific hooks
			$columnTypeMapper = [
				Drama_Grid_Column_Adaptor_Trait::$COLUMN_TYPE_POSTS => [
					'addColumnFilter'         => 'manage_post_posts_columns',
					'addSortableColumnFilter' => 'manage_edit-post_sortable_columns',
					'sortColumnAction'        => 'pre_get_posts',
					'getColumnValueAction'    => 'manage_post_posts_custom_column',
				],
				Drama_Grid_Column_Adaptor_Trait::$COLUMN_TYPE_COMMENTS => [
					'addColumnFilter'         => 'manage_edit-comments_columns',
					'addSortableColumnFilter' => 'manage_edit-comments_sortable_columns',
					'sortColumnAction'        => 'pre_get_comments',
					'getColumnValueAction'    => 'manage_comments_custom_column',
				],
			];
		
			if(isset($columnTypeMapper[$gridType])) {

				// filters to add columns to admin post grid
				add_filter($columnTypeMapper[$gridType]['addColumnFilter'], __CLASS__ . '::add_column');
				// allow for sorting on drama level
				add_filter($columnTypeMapper[$gridType]['addSortableColumnFilter'], __CLASS__ . '::sortable_drama_column');
				// perform sort logic
				add_action($columnTypeMapper[$gridType]['sortColumnAction'], __CLASS__ . '::sort_drama_column');
				// show drama levels in grid
				add_action($columnTypeMapper[$gridType]['getColumnValueAction'], __CLASS__ . '::show_drama_level', 10, 2);
			}
		}
	}

	/**
	 * Handler for adding column to grid's existing columns
	 *
	 * @param array $columns current columns in the grid
	 *
	 * @return array
	 */
	public static function add_column(array $columns): array {
		
		$columns = array_merge(
			$columns, 
			// drama per object
			[Drama_Manager::DRAMA_META => self::get_column_name()],
		);
		
		return $columns;
	}

	/**
	 * Handler for setting the drama column as sortable
	 *
	 * @param array $columns current columns in the grid
	 *
	 * @return array
	 */
	public static function sortable_drama_column (array $columns): array {
		$columns[Drama_Manager::DRAMA_META] = Drama_Manager::DRAMA_META;
		return $columns;
	}

	/**
	 * Handler for sorting the drama column before the query is ran
	 *
	 * @param WP_Query|WP_Comment_Query $query query object used by admin views
	 *
	 * @return void
	 */
	public static function sort_drama_column ($query): void {
	 
		if (!is_admin()) {
			return;
		}
		
		switch(true)
		{
			// handle the post page
			case $query instanceof WP_Query:
				$orderby = $query->get('orderby');
				if($query->get('orderby') == Drama_Manager::DRAMA_META) {				
					$query->set('meta_key', Drama_Manager::DRAMA_META);
					$query->set('orderby', 'meta_value_num');
				}
				break;
			
			// the comment page uses a completely different query class?
			case $query instanceof WP_Comment_Query:
				$orderby = $query->query_vars['orderby'];
				if($query->query_vars['orderby'] == Drama_Manager::DRAMA_META) {
					$query->query_vars['meta_key'] = Drama_Manager::DRAMA_META;
					$query->query_vars['orderby'] = 'meta_value_num';
				}
				break;

			default:
				$orderby = null;
				break;
		}
	}
	
	/**
	 * Handler/wrapper for displaying the drama level from the grid's object
	 *
	 * @param string $column_key name of the column
	 * @param int $object_id the database id of the object
	 *
	 * @return void
	 */
	public static function show_drama_level(string $column_key, int $object_id):void {
		if($column_key == Drama_Manager::DRAMA_META) {
			echo self::get_object_drama_level($object_id);
		}
	}

}

