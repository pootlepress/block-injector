<?php

/**
 * Router class.
 *
 * @package BlockInjector
 */

if (!class_exists('PMAB_Router')) {
    /**
     * Plugin Router.
     */
    class PMAB_Router
    {
        private $post_type = 'block_injector';

        /**
         * Plugin interface.
         *
         * @var PMAB_Plugin
         */
        protected $plugin;

	    /**
	     * Setup the plugin instance.
	     *
	     * @param PMAB_Plugin $plugin Instance of the plugin abstraction.
	     */
        public function __construct( PMAB_Plugin $plugin )
        {
            $this->plugin = $plugin;
        }

        /**
         * Hook into WP.
         *
         * @return void
         */
        public function init()
        {
            add_action('enqueue_block_editor_assets', array($this, 'enqueue_editor_assets'));
            add_action('init', array($this, 'register_post_type'));
            add_action('add_meta_boxes', array($this, 'add_meta_box'));
            add_action('save_post', array($this, 'save_post'));

            pmab_push_to_specific_content();
        }

        /**
         * Load js assets on editor.
         *
         * @return void
         */
        public function enqueue_editor_assets()
        {
            wp_enqueue_script(
                'put-me-anywhere-block-js',
                $this->plugin->asset_url('js/src/editor.js'),
                array(
                    'lodash',
                    'react',
                    'wp-block-editor',
                ),
                $this->plugin->asset_version()
            );
        }
        /**
         * Register meta box container.
         * @return void
         */
        public function register_post_type()
        {
            register_post_type(
                $this->post_type,
                array(
                    'labels'              => array(
                        'name'               => __('Block Injector', 'pmab'),
                        'singular_name'      => __('Block Injector', 'pmab'),
                        'add_new'            => __('Add New', 'pmab'),
                        'add_new_item'       => __('Add New Block Injector', 'pmab'),
                        'edit_item'          => __('Edit Block Injector', 'pmab'),
                        'new_item'           => __('New Block Injector', 'pmab'),
                        'all_items'          => __('Block Injector', 'pmab'),
                        'view_item'          => __('View Block Injector', 'pmab'),
                        'search_items'       => __('Search Block Injector', 'pmab'),
                        'not_found'          => __('Nothing found', 'pmab'),
                        'not_found_in_trash' => __('Nothing found in Trash', 'pmab'),
                        'parent_item_colon'  => '',
                    ),

                    'public'              => true,
                    'show_ui'             => true,
                    'show_in_menu'        => true,
                    'publicly_queryable'  => false,
                    'can_export'          => true,
                    'query_var'           => true,
                    'has_archive'         => false,
                    'hierarchical'        => false,
                    'show_in_rest'        => true,
                    'exclude_from_search' => true,

                    'supports'            => array(
                        'title',
                        'editor',
                    ),

                    'capabilities'        => array(
                        'edit_post'              => 'edit_pages',
                        'read_post'              => 'edit_pages',
                        'delete_post'            => 'edit_pages',
                        'edit_posts'             => 'edit_pages',
                        'edit_others_posts'      => 'edit_pages',
                        'publish_posts'          => 'edit_pages',
                        'read_private_posts'     => 'edit_pages',
                        'read'                   => 'edit_pages',
                        'delete_posts'           => 'edit_pages',
                        'delete_private_posts'   => 'edit_pages',
                        'delete_published_posts' => 'edit_pages',
                        'delete_others_posts'    => 'edit_pages',
                        'edit_private_posts'     => 'edit_pages',
                        'edit_published_posts'   => 'edit_pages',
                    ),
                )
            );
        }

        /**
         * Adds the meta box container.
         *
         * @return void
         */
        public function add_meta_box()
        {
            // Limit meta box to certain post types.
            add_meta_box(
                'some_meta_box_name',
                __('Location and Position', 'pmab'),
                array($this, 'render_meta_box_content'),
                $this->post_type,
                'side',
                'high'
            );
        }

        /**
         * Save the meta box container.
         * @param mixed $post_id The post object.
         *
         * @return void
         */
        public function save_post($post_id)
        {
            if (isset($_POST['_pmab_meta_number_of_blocks'], $_POST['_pmab_meta_type'], $_POST['pmab_plugin_field']) && wp_verify_nonce($_POST['pmab_plugin_field'], 'pmab_plugin_nonce')) {
                update_post_meta($post_id, '_pmab_meta_number_of_blocks', sanitize_text_field($_POST['_pmab_meta_number_of_blocks']));
                update_post_meta($post_id, '_pmab_meta_specific_post', sanitize_text_field($_POST['_pmab_meta_specific_post']));
                update_post_meta($post_id, '_pmab_meta_specific_post_exclude', sanitize_text_field($_POST['_pmab_meta_specific_post_exclude']));
                update_post_meta($post_id, '_pmab_meta_tags', sanitize_text_field($_POST['_pmab_meta_tags']));
                update_post_meta($post_id, '_pmab_meta_category', $_POST['_pmab_meta_category']);
                update_post_meta($post_id, '_pmab_meta_type', sanitize_text_field($_POST['_pmab_meta_type']));
                update_post_meta($post_id, '_pmab_meta_type2', sanitize_text_field($_POST['_pmab_meta_type2']));
                update_post_meta($post_id, '_pmab_meta_tag_n_fix', sanitize_text_field(isset($_POST['_pmab_meta_tag_n_fix']) ? $_POST['_pmab_meta_tag_n_fix'] : 'p_after'));
                update_post_meta($post_id, '_pmab_meta_expiredate', $_POST['_pmab_meta_expiredate']);
                update_post_meta($post_id, '_pmab_meta_startdate', $_POST['_pmab_meta_startdate']);
            }
        }


        /**
         * Render Meta Box content.
         *
         * @param WP_Post $post The post object.
         */
        public function render_meta_box_content($post)
        {

            // Add an nonce field so we can check for it later.
            wp_nonce_field('pmab_plugin_nonce', 'pmab_plugin_field');

            // Use get_post_meta to retrieve an existing value from the database.
            $_pmab_meta_number_of_blocks = get_post_meta($post->ID, '_pmab_meta_number_of_blocks', true);
            $_pmab_meta_specific_post    = get_post_meta($post->ID, '_pmab_meta_specific_post', true);
            $_pmab_meta_specific_post_exclude   = get_post_meta($post->ID, '_pmab_meta_specific_post_exclude', true);
            $_pmab_meta_tags    		 = get_post_meta($post->ID, '_pmab_meta_tags', true);
            $_pmab_meta_type             = get_post_meta($post->ID, '_pmab_meta_type', true);
            $_pmab_meta_type2            = get_post_meta($post->ID, '_pmab_meta_type2', true);
            $_pmab_meta_tag_n_fix        = get_post_meta($post->ID, '_pmab_meta_tag_n_fix', true);
            $_pmab_meta_expiredate       = get_post_meta($post->ID, '_pmab_meta_expiredate', true);
            $_pmab_meta_startdate        = get_post_meta($post->ID, '_pmab_meta_startdate', true);
            $_pmab_meta_category         = get_post_meta($post->ID, '_pmab_meta_category', true);


            // Display the form, using the current value & Template .


            $this->view_template(
                plugin_dir_path(__FILE__) . 'View.php',
                [
                    '_pmab_meta_type' => $_pmab_meta_type,
                    '_pmab_meta_type2' => $_pmab_meta_type2,
                    '_pmab_meta_number_of_blocks' => $_pmab_meta_number_of_blocks,
                    '_pmab_meta_specific_post' => $_pmab_meta_specific_post,
                    '_pmab_meta_specific_post_exclude' => $_pmab_meta_specific_post_exclude,
                    '_pmab_meta_tags' => $_pmab_meta_tags,
                    '_pmab_meta_tag_n_fix' => $_pmab_meta_tag_n_fix,
                    '_pmab_meta_expiredate' => $_pmab_meta_expiredate,
                    '_pmab_meta_startdate'  => $_pmab_meta_startdate,
                    '_pmab_meta_category'  => $_pmab_meta_category
                ]
            );
        }

        /*
         * Meta Preview Template.
         *
         * @param:string $file_path Real file path.
         * @param:array $variables Pass data into variable.
         * @param:boolean $print.
         * @return mixed
         */
        public function view_template($file_path, $variables = array(), $print = true)
        {
            $output = null;
            if (file_exists($file_path)) {
                // Extract the variables to a local namespace.
                $data = $variables;

                // Start output buffering.
                ob_start();

                // Include the template file.
                include $file_path;

                // End buffering and return its contents
                $output = ob_get_clean();
            }
            if ($print) {
                echo $output;
            }
            return $output;
        }
    }
}
