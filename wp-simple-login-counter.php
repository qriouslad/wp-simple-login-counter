<?php
/*
Plugin Name: WP Simple Login Counter
Plugin URI:  https://github.com/qriouslad/wp-simple-login-counter
Description: Simple user login counter plugin for WordPress
Version:     1.0
Author:      Bowo
Author URI:  https://bowo.io
Text Domain: wpscf
Domain Path: /languages
License:     GPL2
 
WP Simple Login Counter is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 2 of the License, or
any later version.
 
WP Simple Login Counter is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
GNU General Public License for more details.
 
You should have received a copy of the GNU General Public License
along with {Plugin Name}. If not, see {License URI}.
*/

namespace Qriouslad\WordPressPlugin;

class Login_Counter {

	public function init() {

			add_action( 'wp_login', array( $this, 'count_user_login' ), 10, 2 );

			add_filter( 'manage_users_columns', array( $this, 'add_stats_column' ) );

			add_action( 'manage_users_custom_column', array( $this, 'fill_stats_column' ), 10, 3 );
	}

	/**
	 * Save user login count to database
	 *
	 * @param string $user_login username
	 * @param object $user WP_User object
	 */
	public function count_user_login( $user_login, $user ) {

		if ( !empty( get_user_meta( $user->ID, 'sp_login_count', true) ) ) {

			$login_count = get_user_meta( $user->ID, 'sp_login_count', true );
			update_user_meta( $user->ID, 'sp_login_count', ( (int) $login_count + 1 ) );

		} else {

			update_user_meta( $user->ID, 'sp_login_count', 1 );

		}

	}


	/**
	 * Add login stats column to the users listing page
	 *
	 * @param string $columns
	 *
	 * @return mixed
	 */
	public function add_stats_column( $columns ) {
		$columns['login_stat'] = __( 'Login Count' );
		return $columns;
	}


	/**
	 * Fill the stat column with values
	 *
	 * @param string $empty
	 * @param string $column_name
	 * @param int $user_id
	 *
	 * @return string|void
	 */
	public function fill_stats_column( $empty, $column_name, $user_id ) {

		if ( 'login_stat' == $column_name ) {

			if ( get_user_meta( $user_id, 'sp_login_count', true ) !== '' ) {
					$login_count = get_user_meta( $user_id, 'sp_login_count', true );
					return '<strong>' . $login_count . '</strong>';
			} else {
				return __( 'No record found.' );
			}

		}

		return $empty;

	}


	/**
	 * Singleton class instance
	 * @return Login_Counter
	 */
	public static function get_instance() {
		static $instance;
		if ( ! isset( $instance ) ) {
			$instance = new self();
			$instance->init();
		}

		return $instance;
	}

}

Login_Counter::get_instance();