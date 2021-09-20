<?php

/**
 * Plugin class.
 * @package PutBlocksAnywhere
 */

if ( ! class_exists( 'class-plugin' ) ) {
	/**
	 * WordPress plugin interface.
	 */
	class PMAB_Plugin {

		/** @var self Instance */
		private static $_instance;

		/**
		 * Returns instance of current calss
		 * @return self Instance
		 */
		public static function instance( $file = '' ) {
			if ( ! self::$_instance ) {
				self::$_instance = new self( $file );
			}

			return self::$_instance;
		}

		/**
		 * Absolute path to the main plugin file.
		 *
		 * @var string
		 */
		protected $file;

		/**
		 * Absolute path to the root directory of this plugin.
		 *
		 * @var string
		 */
		protected $dir;

		/**
		 * Setup the plugin.
		 *
		 * @param string $plugin_file_path Absolute path to the main plugin file.
		 */
		protected function __construct( string $plugin_file_path ) {
			$this->file = $plugin_file_path;
			$this->dir  = dirname( $plugin_file_path );
		}

		/**
		 * Return the absolute path to the plugin directory.
		 *
		 * @return string
		 */
		public function dir(): string {
			return $this->dir;
		}

		/**
		 * Return the absolute path to the plugin file.
		 *
		 * @return string
		 */
		public function file(): string {
			return $this->file;
		}

		/**
		 * Get the file path relative to the WordPress plugin directory.
		 *
		 * @param null $file_path Absolute path to any plugin file.
		 *
		 * @return string
		 */
		public function basename( $file_path = null ): string {
			if ( ! isset( $file_path ) ) {
				$file_path = $this->file();
			}

			return plugin_basename( $file_path );
		}

		/**
		 * Get the public URL to the asset file.
		 *
		 * @param string $path_relative Path relative to this plugin directory root.
		 *
		 * @return string The URL to the asset.
		 */
		public function asset_url( string $path_relative ): string {
			return plugins_url( $path_relative, $this->file() );
		}

		/**
		 * Is WP debug mode enabled.
		 *
		 * @return boolean
		 */
		public function is_debug(): bool {
			return ( defined( 'WP_DEBUG' ) && WP_DEBUG );
		}

		/**
		 * Is WP script debug mode enabled.
		 *
		 * @return boolean
		 */
		public function is_script_debug(): bool {
			return ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG );
		}

		/**
		 * Return the current version of the plugin.
		 *
		 * @return mixed
		 */
		public function version() {
			return $this->meta( 'Version' );
		}

		/**
		 * Sync the plugin version with the asset version.
		 *
		 * @return mixed
		 */
		public function asset_version() {
			if ( $this->is_debug() || $this->is_script_debug() ) {
				return time();
			}

			return $this->version();
		}

		/**
		 * Get plugin meta data.
		 *
		 * @param null $field Optional field key.
		 *
		 * @return string|array|null
		 */
		public function meta( $field = null ) {
			static $meta;

			if ( ! isset( $meta ) ) {
				$meta = get_file_data( $this->file, array() );
			}

			if ( isset( $field ) ) {
				return $meta[ $field ] ?? null;
			}

			return $meta;
		}
	}
}
