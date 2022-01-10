<?php

/**
 * WP_Webhooks_Pro_SQL Class
 *
 * This class contains all of the available SQL functions
 *
 * @since 1.6.3
 */

/**
 * The SQL class of the plugin.
 *
 * @since 1.6.3
 * @package WPWHPRO
 * @author Ironikus <info@ironikus.com>
 */
class WP_Webhooks_Pro_SQL{

	/**
	 * Run certain queries using dbbdelta
	 *
	 * @param string $sql
	 * @return bool - true for success
	 */
	public function run_dbdelta($sql){
		global $wpdb;

		$sql = $this->replace_tags($sql);

		if(empty($sql))
			return false;

		if(!function_exists('dbDelta'))
			require_once(ABSPATH . 'wp-admin/includes/upgrade.php');

		dbDelta($sql);
		$success = empty($wpdb->last_error);

		return $success;
	}

	/**
	 * Run certain SQL Queries
	 *
	 * @param string $sql
	 * @param string $type
	 * @return void
	 */
	public function run( $sql, $type = OBJECT, $args = array() ){
		global $wpdb;

		$sql = $this->replace_tags($sql);

		if(empty($sql))
			return false;

		$result = $wpdb->get_results($sql, $type);

		if( isset( $args['return_id'] ) && $args['return_id'] ){
			if( isset( $wpdb->insert_id ) ){
				$result = $wpdb->insert_id;
			}
		}

		return $result;
	}

	/**
	 * Prepare certain SQL Queries
	 *
	 * @param string $sql
	 * @param string $type
	 * @since 4.3.3
	 * @return void
	 */
	public function prepare( $sql, $values = array() ){
		global $wpdb;

		$sql = $this->replace_tags($sql);

		if( empty( $sql ) ){
			return false;
		}

		$sql = $wpdb->query( $wpdb->prepare( $sql, $values ) );

		return $sql;
	}

	/**
	 * Replace generic tags with values
	 *
	 * @param $string - string to fill
	 * @return mixed - filles string
	 */
	public function replace_tags($string){

		if(!is_string($string) || empty($string))
			return false;

		global $wpdb;

		$charset_collate = $wpdb->get_charset_collate();
		$prefix = $wpdb->base_prefix;
		$posts = $wpdb->posts;
		$postmeta = $wpdb->postmeta;

		$in = array(
			'{charset_collate}',
			'{prefix}',
			'{posts}',
			'{postmeta}',
		);

		$out = array(
			$charset_collate,
			$prefix,
			$posts,
			$postmeta,
		);

		return str_replace($in, $out, $string);

	}

	/**
	 * Checks if a table exists or not
	 *
	 * @param $table_name - the table name
	 * @return bool - true if the table exists
	 */
	public function table_exists($table_name){
		global $wpdb;

		$return = false;
		$prefix = $wpdb->base_prefix;
		$table_name = esc_sql($table_name);

		if(substr($table_name, 0, strlen($prefix)) != $prefix){
			$table_name = $prefix . $table_name;
		}

		$query = $wpdb->prepare( 'SHOW TABLES LIKE %s', $table_name );

		if( $wpdb->get_var( $query ) == $table_name ){
			$return = true;
		}

		return $return;
	}

	/**
	 * Checks if one or multiple column exists or not
	 *
	 * @param $table_name - the table name
	 * @param $column_name - the column name
	 * @return bool - true if the column exists
	 */
	public function column_exists( $table_name, $column_name ){
		global $wpdb;

		$return = false;
		$prefix = $wpdb->base_prefix;
		$table_name = esc_sql($table_name);
		$column_name = esc_sql($column_name);

		if(substr($table_name, 0, strlen($prefix)) != $prefix){
			$table_name = $prefix . $table_name;
		}

		$query = $wpdb->prepare( 'SHOW COLUMNS FROM %1$s LIKE \'%2$s\';', $table_name, $column_name );

		if( $wpdb->get_var( $query ) == $column_name ){
			$return = true;
		}

		return $return;
	}

}