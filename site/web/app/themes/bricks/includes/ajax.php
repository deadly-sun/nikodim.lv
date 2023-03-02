<?php
namespace Bricks;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Ajax {

	public function __construct() {
		add_action( 'wp_ajax_bricks_download_image', [ $this, 'download_image' ] );
		add_action( 'wp_ajax_bricks_get_image_metadata', [ $this, 'get_image_metadata' ] );
		add_action( 'wp_ajax_bricks_get_image_from_custom_field', [ $this, 'get_image_from_custom_field' ] );

		add_action( 'wp_ajax_bricks_get_dynamic_data_preview_content', [ $this, 'get_dynamic_data_preview_content' ] );

		add_action( 'wp_ajax_bricks_get_posts', [ $this, 'get_posts' ] );
		add_action( 'wp_ajax_bricks_get_terms_options', [ $this, 'get_terms_options' ] );
		add_action( 'wp_ajax_bricks_get_users', [ $this, 'get_users' ] );

		add_action( 'wp_ajax_bricks_get_sidebar', [ $this, 'get_sidebar' ] );
		add_action( 'wp_ajax_bricks_render_data', [ $this, 'render_data' ] );

		add_action( 'wp_ajax_bricks_publish_post', [ $this, 'publish_post' ] );
		add_action( 'wp_ajax_bricks_save_post', [ $this, 'save_post' ] );
		add_action( 'wp_ajax_bricks_create_autosave', [ $this, 'create_autosave' ] );
		add_action( 'wp_ajax_bricks_get_builder_url', [ $this, 'get_builder_url' ] );

		add_action( 'wp_ajax_bricks_save_global_element', [ $this, 'save_global_element' ] );
		add_action( 'wp_ajax_bricks_save_color_palette', [ $this, 'save_color_palette' ] );
		add_action( 'wp_ajax_bricks_save_panel_width', [ $this, 'save_panel_width' ] );
		add_action( 'wp_ajax_bricks_save_builder_scale_off', [ $this, 'save_builder_scale_off' ] );
		add_action( 'wp_ajax_bricks_save_builder_width_locked', [ $this, 'save_builder_width_locked' ] );

		add_action( 'wp_ajax_bricks_render_element', [ $this, 'render_element' ] );
		add_action( 'wp_ajax_bricks_get_html_from_content', [ $this, 'get_html_from_content' ] );

		add_action( 'wp_ajax_bricks_get_pages', [ $this, 'get_pages' ] );
		add_action( 'wp_ajax_bricks_create_new_page', [ $this, 'create_new_page' ] );

		add_action( 'wp_ajax_bricks_get_my_templates_data', [ $this, 'get_my_templates_data' ] );

		// Get fresh remote template in builder via PopupTemplates
		add_action( 'wp_ajax_bricks_get_remote_templates_data', [ $this, 'get_remote_templates_data' ] );

		add_action( 'wp_ajax_bricks_get_current_user_id', [ $this, 'get_current_user_id' ] );
	}

	/**
	 * Decode stringified JSON data
	 *
	 * @since 1.0
	 */
	public static function decode( $data, $run_wp_slash = true ) {
		$data = stripslashes( $data );
		$data = json_decode( $data, true );
		$data = $run_wp_slash ? wp_slash( $data ) : $data; // Make sure we keep the good slashes on update_post_meta

		return $data;
	}

	/**
	 * Verify nonce used in AJAX call
	 *
	 * @since 1.5.4
	 *
	 * @return void
	 */
	public static function verify_nonce() {
		if ( ! check_ajax_referer( 'bricks-nonce', 'nonce', false ) ) {
			wp_send_json_error( 'verify_nonce: "bricks-nonce" is invalid.' );
		}
	}

	/**
	 * Verify request: nonce and user access
	 *
	 * Check for builder in order to not trigger on wp_auth_check
	 *
	 * @since 1.0
	 */
	public static function verify_request() {
		self::verify_nonce();

		// Verfiy user access (NOTE: get_the_ID() returns 0 in AJAX call)
		$post_id = ! empty( $_POST['postId'] ) ? $_POST['postId'] : get_the_ID();

		if ( ! Capabilities::current_user_can_use_builder( $post_id ) ) {
			wp_send_json_error( 'verify_request: User can not use builder (' . get_current_user_id() . ')' );
		}
	}

	/**
	 * Save color palette
	 *
	 * @since 1.0
	 */
	public function save_color_palette() {
		self::verify_request();

		if ( isset( $_POST['colorPalette'] ) ) {
			$color_palette_updated = update_option( BRICKS_DB_COLOR_PALETTE, stripslashes_deep( $_POST['colorPalette'] ) );
			wp_send_json_success( $color_palette_updated );
		} else {
			wp_send_json_error( [ 'message' => esc_html__( 'New color could not be saved.', 'bricks' ) ] );
		}
	}

	/**
	 * Save panel width
	 *
	 * @since 1.0
	 */
	public function save_panel_width() {
		self::verify_request();

		// Min. panel width check to fix disappearing panel issue
		$panel_width = isset( $_POST['panelWidth'] ) ? intval( $_POST['panelWidth'] ) : 0;

		if ( $panel_width >= 100 ) {
			$panel_width_updated = update_option( BRICKS_DB_PANEL_WIDTH, $panel_width );
			wp_send_json_success(
				[
					'panel_width_updated' => $panel_width_updated,
					'panel_width'         => $panel_width,
				]
			);
		} else {
			wp_send_json_error(
				[
					'message'     => esc_html__( 'Panel width could not be saved.', 'bricks' ),
					'panel_width' => $panel_width,
				]
			);
		}
	}

	/**
	 * Save builder state 'off' (enabled by default)
	 *
	 * @since 1.3.2
	 */
	public function save_builder_scale_off() {
		$scale_off = $_POST['off'] == 'true';
		$user_id   = get_current_user_id();

		if ( $scale_off ) {
			update_user_meta( $user_id, BRICKS_DB_BUILDER_SCALE_OFF, true );
		} else {
			delete_user_meta( $user_id, BRICKS_DB_BUILDER_SCALE_OFF );
		}

		wp_send_json_success(
			[
				'scale_off' => $scale_off,
				'user_id'   => $user_id,
			]
		);
	}

	/**
	 * Save builder width locked state (disabled by default)
	 *
	 * Only apply for bas breakpoint. Allows users on smaller screen not having to set a custom width on every page load.
	 *
	 * @since 1.3.2
	 */
	public function save_builder_width_locked() {
		$preview_width = intval( $_POST['width'] );
		$user_id       = get_current_user_id();

		if ( $preview_width ) {
			update_user_meta( $user_id, BRICKS_DB_BUILDER_WIDTH_LOCKED, $preview_width );
		} else {
			delete_user_meta( $user_id, BRICKS_DB_BUILDER_WIDTH_LOCKED );
		}

		wp_send_json_success(
			[
				'preview_width' => $preview_width,
				'user_id'       => $user_id,
			]
		);
	}

	/**
	 * Get pages
	 *
	 * @since 1.0
	 */
	public function get_pages() {
		self::verify_request();

		$query_args = [
			'posts_per_page' => -1,
			'post_status'    => 'any',
			'post_type'      => ! empty( $_GET['postType'] ) ? $_GET['postType'] : 'page',
			'fields'         => 'ids'
		];

		// NOTE: Undocumented
		$query_args = apply_filters( 'bricks/ajax/get_pages_args', $query_args );

		$page_ids = get_posts( $query_args );

		$pages = [];

		foreach ( $page_ids as $page_id ) {
			$page_data = [
				'id'      => $page_id,
				'title'   => wp_kses_post( get_the_title( $page_id ) ),
				'status'  => get_post_status( $page_id ),
				'slug'    => get_post_field( 'post_name', $page_id ),
				'editUrl' => Helpers::get_builder_edit_link( $page_id ),
			];

			if ( has_post_thumbnail( $page_id ) ) {
				$image_size         = isset( $_GET['imageSize'] ) ? $_GET['imageSize'] : 'large';
				$page_data['image'] = get_the_post_thumbnail_url( $page_id, $image_size );
			}

			$pages[] = $page_data;
		}

		wp_send_json_success( $pages );
	}

	/**
	 * Create new page
	 *
	 * @since 1.0
	 */
	public function create_new_page() {
		self::verify_request();

		$new_page_id = wp_insert_post(
			[
				'post_title' => isset( $_POST['title'] ) ? $_POST['title'] : esc_html__( '(no title)', 'bricks' ),
				'post_type'  => isset( $_POST['postType'] ) ? $_POST['postType'] : 'page',
			]
		);

		wp_send_json_success( $new_page_id );
	}

	/**
	 * Builder: Render element HTML (pre-render & AJAX/REST API)
	 *
	 * @since 1.0
	 */
	public static function render_element( $element ) {
		// Check: AJAX, REST API or builder
		$is_ajax = false;

		// AJAX
		if ( bricks_is_ajax_call() && isset( $_POST ) ) {
			self::verify_request();

			$element = isset( $_POST['element'] ) ? $_POST['element'] : $_POST;
			$element = stripslashes_deep( $element );

			$loop_element = ! empty( $_POST['loopElement'] ) ? $_POST['loopElement'] : false;

			$is_ajax = true;
		}

		// REST API (Permissions checked in the API->render_element_permissions_check())
		elseif ( bricks_is_rest_call() ) {
			$loop_element = ! empty( $element['loopElement'] ) ? $element['loopElement'] : false;
			$element      = $element['element'];
		}

		/**
		 * Builder: Init Query to get the builder preview for the first loop item (e.g.: "Product Category Image" DD)
		 *
		 * @since 1.4
		 */
		if ( ! empty( $loop_element ) ) {
			$query = new Query( $loop_element );

			if ( ! empty( $query->count ) ) {
				$query->is_looping = true;

				// NOTE: Use array_shift because not all the results are sequential arrays (e.g. JetEngine)
				$query->loop_object = $query->object_type == 'post' ? $query->query_result->posts[0] : array_shift( $query->query_result );
			}
		}

		// Init element class (i.e. new Bricks\Element_Alert( $element ))
		$element_name       = ! empty( $element['name'] ) ? $element['name'] : '';
		$element_class_name = isset( Elements::$elements[ $element_name ]['class'] ) ? Elements::$elements[ $element_name ]['class'] : false;

		if ( class_exists( $element_class_name ) ) {
			$element['is_frontend'] = false;

			$element_instance = new $element_class_name( $element );
			$element_instance->load();

			// Init element: enqueue styles/scripts, render element
			ob_start();
			$element_instance->init();
			$response = ob_get_clean();
			$response = stripslashes( $response );
		}

		// Element doesn't exist
		else {
			$response = '<div class="bricks-element-placeholder no-php-class">' . sprintf( esc_html__( 'Element "%s" doesn\'t exist.', 'bricks' ), $element_name ) . '</div>';
		}

		if ( $is_ajax ) {
			// Template element: Add additional builder data (CSS & list of elements to run scripts (@since 1.5))
			if ( $element_name === 'template' ) {
				$template_id = ! empty( $element['settings']['template'] ) ? $element ['settings']['template'] : false;

				if ( $template_id ) {
					$additional_data = Element_Template::get_builder_call_additional_data( $template_id );

					$response = array_merge( [ 'html' => $response ], $additional_data );
				}
			}

			// Subsequent element render via AJAX call
			wp_send_json_success( $response );
		}

		// Initial element render via PHP or REST API
		else {
			return $response;
		}
	}

	/**
	 * Generate the HTML based on the builder content data (post Id or content)
	 *
	 * Used to feed Rank Math SEO analyses
	 *
	 * Note: This method doesn't generate styles
	 */
	public function get_html_from_content() {
		self::verify_request();

		$post_id = ! empty( $_POST['postId'] ) ? $_POST['postId'] : false;

		$data = ! empty( $_POST['content'] ) ? self::decode( $_POST['content'], false ) : get_post_meta( $post_id, BRICKS_DB_PAGE_CONTENT, true );

		if ( is_array( $data ) ) {
			$data = array_map( 'Bricks\Helpers::set_is_frontend_to_false', $data );
		}

		$html = ! empty( $data ) ? Frontend::render_data( $data ) : '';

		wp_send_json_success(
			[
				'html' => $html,
			]
		);
	}

	/**
	 * Add/remove global element
	 *
	 * @since 1.0
	 */
	public function save_global_element() {
		self::verify_request();

		$global_elements = Database::$global_data['elements'] ? Database::$global_data['elements'] : [];

		$element_to_save         = isset( $_POST['newGlobalElement'] ) ? stripslashes_deep( $_POST['newGlobalElement'] ) : false;
		$element_to_delete_index = isset( $_POST['index'] ) ? $_POST['index'] : false;

		// Save new global element to db
		if ( $element_to_save ) {
			if ( empty( $element_to_save['label'] ) ) {
				$element_to_save['label'] = ucwords( str_replace( '-', ' ', $element_to_save['name'] ) );
			}

			array_unshift( $global_elements, $element_to_save );
			update_option( BRICKS_DB_GLOBAL_ELEMENTS, $global_elements );
		}

		// Delete global element from db
		elseif ( is_numeric( $element_to_delete_index ) ) {
			if ( count( $global_elements ) === 1 ) {
				// Is last global element
				delete_option( BRICKS_DB_GLOBAL_ELEMENTS );
				$global_elements = [];
			} else {
				// Remove global element
				array_splice( $global_elements, $element_to_delete_index, 1 );
				update_option( BRICKS_DB_GLOBAL_ELEMENTS, $global_elements );
			}
		}

		// Update global elements (i.e. changing element label)
		elseif ( isset( $_POST['globalElements'] ) ) {
			update_option( BRICKS_DB_GLOBAL_ELEMENTS, $_POST['globalElements'] );
		}

		// Return updated global elements 'settings' array
		wp_send_json_success( $global_elements );
	}

	/**
	 * Get posts (for builder ControlSelect.vue according to passed postType)
	 *
	 * @since 1.0
	 */
	public static function get_posts() {
		if ( ! empty( $_GET['postType'] ) && $_GET['postType'] !== 'any' ) {
			$post_type = $_GET['postType'];
		} else {
			$post_types = get_post_types( [ 'public' => true ] );

			$post_type = array_keys( $post_types );
		}

		$query_args = [ 'post_type' => $post_type ];

		// Necessary to retrieve more than 2 posts initially
		if ( $post_type !== 'any' ) {
			$query_args['orderby'] = 'date';
		}

		if ( ! empty( $_GET['search'] ) ) {
			$query_args['s'] = stripslashes_deep( $_GET['search'] );
		}

		$posts = Helpers::get_posts_by_post_id( $query_args );

		// // If AJAX request contains "include" parameter, make sure some post_ids are included in the response
		if ( ! empty( $_GET['include'] ) ) {
			$include = (array) $_GET['include'];

			foreach ( $include as $post_id ) {
				if ( ! array_key_exists( $post_id, $posts ) ) {
					$posts[ $post_id ] = get_the_title( $post_id );
				}
			}
		}

		wp_send_json_success( $posts );
	}

	/**
	 * Get users
	 *
	 * @since 1.2.2
	 *
	 * @return void
	 */
	public function get_users() {
		self::verify_request();

		$args = [
			'count_total' => false,
			'number'      => 50
		];

		if ( ! empty( $_GET['search'] ) ) {
			$args['search'] = stripslashes_deep( $_GET['search'] );
		}

		// Query users
		$users = Helpers::get_users_options( $args, true );

		if ( ! empty( $_GET['include'] ) ) {
			$include = (array) $_GET['include'];

			foreach ( $include as $user_id ) {
				if ( ! array_key_exists( $user_id, $users ) ) {
					$user = get_userdata( $user_id );
					if ( $user ) {
						$users[ $user_id ] = $user->display_name;
					}
				}
			}
		}

		wp_send_json_success( $users );
	}

	/**
	 * Get terms
	 *
	 * @since 1.0
	 */
	public function get_terms_options() {
		self::verify_request();

		$query      = ! empty( $_GET['query'] ) ? $_GET['query'] : null;
		$query_type = ! empty( $query['objectType'] ) ? $query['objectType'] : 'post';
		$post_types = ! empty( $query['post_type'] ) ? $query['post_type'] : ( $query_type == 'post' ? 'post' : null );
		$taxonomy   = ! empty( $query['taxonomy'] ) ? $query['taxonomy'] : null;

		$terms = [];

		if ( ! empty( $post_types ) ) {
			foreach ( (array) $post_types as $post_type ) {
				$type_terms = Helpers::get_terms_options( $taxonomy, $post_type );

				if ( ! empty( $type_terms ) ) {
					$terms = array_merge( $terms, $type_terms );
				}
			}
		} elseif ( ! empty( $taxonomy ) ) {
			$terms = Helpers::get_terms_options( $taxonomy );
		}

		wp_send_json_success( $terms );
	}

	/**
	 * Get sidebar
	 *
	 * @since 1.0
	 */
	public function get_sidebar() {
		self::verify_request();

		if ( is_active_sidebar( $_GET['sidebarId'] ) ) {
			ob_start();
			dynamic_sidebar( $_GET['sidebarId'] );
			$sidebar_output = ob_get_clean();
			wp_send_json_success( $sidebar_output );
		} else {
			wp_send_json_error( 'sidebar_doesnt_exist' );
		}
	}

	/**
	 * Render Bricks data for static header/content/footer and query loop preview HTML in builder
	 *
	 * @since 1.0
	 */
	public static function render_data() {
		if ( empty( $_POST['elements'] ) ) {
			return;
		}

		$elements = self::decode( $_POST['elements'], false );

		$elements = array_map( 'Bricks\Helpers::set_is_frontend_to_false', $elements );
		$area     = ! empty( $_POST['area'] ) ? $_POST['area'] : 'content';

		// Set Theme Styles (for correct preview of query loop nodes)
		Theme_Styles::load_set_styles( $_POST['postId'] );

		$html = Frontend::render_data( $elements, $area );

		wp_send_json_success( $html );
	}

	/**
	 * Don't check for chnage when creating revision as all that changed is the postmeta
	 *
	 * @since 1.7
	 */
	public function dont_check_for_revision_changes() {
		return false;
	}

	/**
	 * Save post
	 *
	 * @since 1.0
	 */
	public function save_post() {
		self::verify_request();

		// Return: No post ID set
		if ( ! isset( $_POST['postId'] ) ) {
			wp_send_json_error( 'Error: No postId provided!' );
		}

		$post_id = $_POST['postId'];
		$post    = get_post( $post_id );

		// Update post at the very end (@since 1.6)
		$the_post = false;

		/**
		 * Save revision in database
		 */

		// Disabled WordPress content diff check
		add_filter( 'wp_save_post_revision_check_for_changes', [ $this, 'dont_check_for_revision_changes' ] );

		$revision_id = wp_save_post_revision( $post );

		// Delete autosave (@since 1.7)
		if ( $revision_id ) {
			$autosave = wp_get_post_autosave( $post_id );

			if ( $autosave ) {
				wp_delete_post_revision( $autosave );
			}
		}

		remove_filter( 'wp_save_post_revision_check_for_changes', [ $this, 'dont_check_for_revision_changes' ] );

		// Check user capabilities (@since 1.5.4)
		$has_full_access = Capabilities::current_user_has_full_access();

		/**
		 * Save color palettes
		 *
		 * @since 1.4
		 */
		if ( isset( $_POST['colorPalette'] ) && $has_full_access ) {
			$color_palette = self::decode( $_POST['colorPalette'], false );

			if ( is_array( $color_palette ) && count( $color_palette ) ) {
				update_option( BRICKS_DB_COLOR_PALETTE, $color_palette );
			} else {
				delete_option( BRICKS_DB_COLOR_PALETTE );
			}
		}

		/**
		 * Save global classes
		 *
		 * @since 1.4
		 */
		if ( isset( $_POST['globalClasses'] ) && $has_full_access ) {
			$global_classes = self::decode( $_POST['globalClasses'], false );

			Helpers::save_global_classes_in_db( $global_classes, "ajax_save_post_id_$post_id" );
		}

		/**
		 * Save global classes locked
		 *
		 * @since 1.4
		 */
		if ( isset( $_POST['globalClassesLocked'] ) && $has_full_access ) {
			$global_classes_locked = self::decode( $_POST['globalClassesLocked'], false );

			if ( is_array( $global_classes_locked ) && count( $global_classes_locked ) ) {
				update_option( BRICKS_DB_GLOBAL_CLASSES_LOCKED, $global_classes_locked, false );
			} else {
				delete_option( BRICKS_DB_GLOBAL_CLASSES_LOCKED );
			}
		}

		/**
		 * Save global elements
		 *
		 * @since 1.4
		 */
		if ( isset( $_POST['globalElements'] ) && $has_full_access ) {
			$global_elements = self::decode( $_POST['globalElements'], false );

			if ( is_array( $global_elements ) && count( $global_elements ) ) {
				update_option( BRICKS_DB_GLOBAL_ELEMENTS, $global_elements );
			} else {
				delete_option( BRICKS_DB_GLOBAL_ELEMENTS );
			}
		}

		/**
		 * Save pinned elements
		 *
		 * @since 1.4
		 */

		if ( isset( $_POST['pinnedElements'] ) && $has_full_access ) {
			$pinned_elements = self::decode( $_POST['pinnedElements'], false );

			if ( is_array( $pinned_elements ) && count( $pinned_elements ) ) {
				update_option( BRICKS_DB_PINNED_ELEMENTS, $pinned_elements );
			} else {
				delete_option( BRICKS_DB_PINNED_ELEMENTS );
			}
		}

		/**
		 * Save pseudo-classes
		 *
		 * @since 1.4
		 */
		if ( isset( $_POST['pseudoClasses'] ) && $has_full_access ) {
			$global_pseudo_classes = self::decode( $_POST['pseudoClasses'] );

			if ( is_array( $global_pseudo_classes ) && count( $global_pseudo_classes ) ) {
				update_option( BRICKS_DB_PSEUDO_CLASSES, $global_pseudo_classes );
			} else {
				delete_option( BRICKS_DB_PSEUDO_CLASSES );
			}
		}

		/**
		 * Save theme styles
		 *
		 * @since 1.4
		 */
		if ( isset( $_POST['themeStyles'] ) && $has_full_access ) {
			$theme_styles = self::decode( $_POST['themeStyles'], false );

			foreach ( $theme_styles as $theme_style_id => $theme_style ) {
				// Remove empty settings 'group'
				if ( isset( $theme_style['settings'] ) ) {
					foreach ( $theme_style['settings'] as $group_key => $group_settings ) {
						if ( ! $group_settings || ( is_array( $group_settings ) && ! count( $group_settings ) ) ) {
							unset( $theme_styles[ $theme_style_id ]['settings'][ $group_key ] );
						}
					}
				}
			}

			if ( is_array( $theme_styles ) && count( $theme_styles ) ) {
				update_option( BRICKS_DB_THEME_STYLES, $theme_styles );
			} else {
				delete_option( BRICKS_DB_THEME_STYLES );
			}
		}

		/**
		 * Save page data (post meta table)
		 */
		$header  = isset( $_POST['header'] ) ? self::decode( $_POST['header'] ) : [];
		$content = isset( $_POST['content'] ) ? self::decode( $_POST['content'] ) : [];
		$footer  = isset( $_POST['footer'] ) ? self::decode( $_POST['footer'] ) : [];

		/**
		 * Save page setting
		 *
		 * @since 1.4
		 */
		if ( isset( $_POST['pageSettings'] ) && $has_full_access ) {
			$page_settings = self::decode( $_POST['pageSettings'] );

			if ( is_array( $page_settings ) && count( $page_settings ) ) {
				if ( ! empty( $page_settings['postName'] ) || ! empty( $page_settings['postTitle'] ) ) {
					$the_post['ID'] = $post_id;
				}

				// Update post name (slug)
				if ( ! empty( $page_settings['postName'] ) ) {
					$the_post['post_name'] = trim( $page_settings['postName'] );

					unset( $page_settings['postName'] );
				}

				// Update post title
				if ( ! empty( $page_settings['postTitle'] ) ) {
					$the_post['post_title'] = trim( $page_settings['postTitle'] );

					unset( $page_settings['postTitle'] );
				}

				update_post_meta( $post_id, BRICKS_DB_PAGE_SETTINGS, $page_settings );
			} else {
				delete_post_meta( $post_id, BRICKS_DB_PAGE_SETTINGS );
			}
		}

		/**
		 * Bricks template
		 *
		 * @since 1.4
		 */
		if ( isset( $_POST['templateType'] ) ) {
			$template_type = $_POST['templateType'];

			update_post_meta( $post_id, BRICKS_DB_TEMPLATE_TYPE, $template_type );

			switch ( $template_type ) {
				// Header template
				case 'header':
					if ( isset( $_POST['header'] ) ) {
						// @since 1.5.4
						$header = Helpers::security_check_elements_before_save( $header, $post_id, 'header' );

						if ( is_array( $header ) && count( $header ) ) {
							// Save revision in post meta ('update_post_meta' can't process post type 'revision'
							if ( $revision_id ) {
								update_metadata( 'post', $revision_id, BRICKS_DB_PAGE_HEADER, $header );
							}

							update_post_meta( $post_id, BRICKS_DB_PAGE_HEADER, $header );
						} else {
							delete_post_meta( $post_id, BRICKS_DB_PAGE_HEADER );
						}
					}
					break;

				// Footer template
				case 'footer':
					if ( isset( $_POST['footer'] ) ) {
						// @since 1.5.4
						$footer = Helpers::security_check_elements_before_save( $footer, $post_id, 'footer' );

						if ( is_array( $footer ) && count( $footer ) ) {
							// Save revision in post meta ('update_post_meta' can't process post type 'revision'
							if ( $revision_id ) {
								update_metadata( 'post', $revision_id, BRICKS_DB_PAGE_FOOTER, $footer );
							}

							update_post_meta( $post_id, BRICKS_DB_PAGE_FOOTER, $footer );
						} else {
							delete_post_meta( $post_id, BRICKS_DB_PAGE_FOOTER );
						}
					}
					break;

				// Any other template type
				default:
					if ( isset( $_POST['content'] ) ) {
						// @since 1.5.4
						$content = Helpers::security_check_elements_before_save( $content, $post_id, 'content' );

						if ( is_array( $content ) && count( $content ) ) {
							// Save revision in post meta ('update_post_meta' can't process post type 'revision')
							if ( $revision_id ) {
								update_metadata( 'post', $revision_id, BRICKS_DB_PAGE_CONTENT, $content );
							}

							update_post_meta( $post_id, BRICKS_DB_PAGE_CONTENT, $content );
						} else {
							delete_post_meta( $post_id, BRICKS_DB_PAGE_CONTENT );
						}
					}
			}
		}

		/**
		 * Template settings
		 *
		 * @since 1.4
		 */
		if ( isset( $_POST['templateSettings'] ) && $has_full_access ) {
			$template_settings = self::decode( $_POST['templateSettings'], false );

			if ( is_array( $template_settings ) && count( $template_settings ) ) {
				// User saved template settings: Delete auto content notification
				unset( $template_settings['templatePreviewAutoContent'] );

				Helpers::set_template_settings( $post_id, $template_settings );
			} else {
				Helpers::delete_template_settings( $post_id );
			}
		}

		/**
		 * Content (not a Bricks template)
		 *
		 * @since 1.4
		 */
		if ( isset( $_POST['content'] ) && get_post_type( $post_id ) !== BRICKS_DB_TEMPLATE_SLUG ) {
			// @since 1.5.4
			$content = Helpers::security_check_elements_before_save( $content, $post_id, 'content' );

			if ( is_array( $content ) && count( $content ) ) {
				// Update empty or existing Gutenberg post_content (preserve Classic Editor data)
				$existing_post_content = $post->post_content;

				if ( Database::get_setting( 'bricks_to_wp' ) && ( ! $existing_post_content || has_blocks( get_post( $post_id ) ) ) ) {
					$new_post_content = Blocks::serialize_bricks_to_blocks( $content, $post_id );

					if ( $new_post_content ) {
						$the_post = (
							[
								'ID'           => $post_id,
								'post_content' => $new_post_content,
							]
						);
					}
				}

				// Save revision in post meta ('update_post_meta' can't process post type 'revision')
				if ( $revision_id ) {
					update_metadata( 'post', $revision_id, BRICKS_DB_PAGE_CONTENT, $content );
				}

				// Save content in post meta
				update_post_meta( $post_id, BRICKS_DB_PAGE_CONTENT, $content );
			} else {
				delete_post_meta( $post_id, BRICKS_DB_PAGE_CONTENT );
			}
		}

		// Set _bricks_editor_mode to 'bricks'
		update_post_meta( $post_id, BRICKS_DB_EDITOR_MODE, 'bricks' );

		/**
		 * STEP: Update post to (1) update post date & (2) re-generate CSS file via 'save_post' in files.php
		 *
		 * Check $wp_post_updated to ensure wp_update_post did not already ran above.
		 *
		 * @since 1.5.7
		 */
		$post_id = $the_post ? wp_update_post( $the_post ) : wp_update_post( $post );

		wp_send_json_success( $_POST );
	}

	/**
	 * Create autosave
	 *
	 * @since 1.0
	 */
	public static function create_autosave() {
		self::verify_request();

		if (
			empty( $_POST['postId'] ) ||
			empty( $_POST['elements'] ) ||
			empty( $_POST['area'] )
		) {
			return;
		}

		$post_id   = intval( $_POST['postId'] );
		$post_type = get_post_type( $post_id );
		$area      = $_POST['area'];

		// 1/2: Create autosave
		$autosave_id = wp_create_post_autosave(
			[
				'post_ID'       => $post_id,
				'post_type'     => $post_type,
				'post_excerpt'  => '<!-- Built With Bricks -->', // Forces $autosave_is_different to 'true'
				'post_modified' => current_time( 'mysql' ),
			]
		);

		if ( is_wp_error( $autosave_id ) ) {
			wp_send_json_error( new \WP_Error( 'autosave_error', $autosave_id ) );
		}

		// 2/2: Save elements in db post meta with autosave post ID
		$elements = self::decode( $_POST['elements'] );

		if ( ! is_array( $elements ) ) {
			wp_send_json_error( new \WP_Error( 'element_error', 'bla bla ..' ) );
		}

		switch ( $area ) {
			case 'header':
				update_metadata( 'post', $autosave_id, BRICKS_DB_PAGE_HEADER, $elements );
				break;

			case 'content':
				update_metadata( 'post', $autosave_id, BRICKS_DB_PAGE_CONTENT, $elements );
				break;

			case 'footer':
				update_metadata( 'post', $autosave_id, BRICKS_DB_PAGE_FOOTER, $elements );
				break;
		}

		// STEP: Generate post CSS file on autosave
		if ( Database::get_setting( 'cssLoading' ) === 'file' ) {
			Assets_Files::generate_post_css_file( $post_id, $area, $elements );
		}

		wp_send_json_success( [ 'autosave_id' => $autosave_id ] );
	}

	/**
	 * Get bulider URL
	 *
	 * To reload builder with newly saved postName/postTitle (page settigns)
	 *
	 * @since 1.0
	 */
	public function get_builder_url() {
		self::verify_request();

		wp_send_json_success( [ 'url' => Helpers::get_builder_edit_link( $_POST['postId'] ) ] );
	}

	/**
	 * Publish post
	 *
	 * @since 1.0
	 */
	public function publish_post() {
		self::verify_request();

		if ( ! isset( $_POST['postId'] ) ) {
			wp_send_json_error( 'No postId provided.' );
		}

		$response = wp_update_post(
			[
				'ID'          => $_POST['postId'],
				'post_status' => 'publish',
			]
		);

		wp_send_json_success( $response );
	}

	/**
	 * Get image metadata
	 *
	 * @since 1.0
	 */
	public function get_image_metadata() {
		self::verify_request();

		if ( ! isset( $_POST['imageId'] ) ) {
			wp_send_json_error( 'no imageId provided' );
		}

		$get_attachment_metadata = wp_get_attachment_metadata( $_POST['imageId'] );

		// SVG returns empty metadata
		if ( ! $get_attachment_metadata ) {
			wp_send_json_success();
		}

		$response = [
			'filename' => isset( $get_attachment_metadata['original_image'] ) ? $get_attachment_metadata['original_image'] : '',
			'full'     => [
				'width'  => isset( $get_attachment_metadata['width'] ) ? $get_attachment_metadata['width'] : '',
				'height' => isset( $get_attachment_metadata['height'] ) ? $get_attachment_metadata['height'] : '',
			],
			'sizes'    => isset( $get_attachment_metadata['sizes'] ) ? $get_attachment_metadata['sizes'] : [],
			'src'      => wp_get_attachment_image_src( $_POST['imageId'], $_POST['imageSize'] ),
		];

		wp_send_json_success( $response );
	}

	/**
	 * Get Image Id from a custom field
	 *
	 * @since 1.0
	 */
	public function get_image_from_custom_field() {
		self::verify_request();

		if ( ! isset( $_POST['metaKey'] ) ) {
			wp_send_json_error( 'no custom field key provided' );
		}

		if ( ! isset( $_POST['postId'] ) ) {
			wp_send_json_error( 'Error: No postId provided!' );
		}

		$meta_key = sanitize_text_field( $_POST['metaKey'] );
		$post_id  = abs( $_POST['postId'] );
		$size     = empty( $_POST['size'] ) ? BRICKS_DEFAULT_IMAGE_SIZE : sanitize_text_field( $_POST['size'] );

		// Get images from custom field
		$images = Integrations\Dynamic_Data\Providers::render_tag( $meta_key, $post_id, 'image', [ 'size' => $size ] );

		if ( empty( $images ) ) {
			wp_send_json_error( 'Error: Image not found' );
		}

		if ( is_numeric( $images[0] ) ) {
			$get_attachment_metadata = wp_get_attachment_metadata( $images[0] );

			if ( empty( $get_attachment_metadata ) ) {
				wp_send_json_error( 'Error: Image not found' );
			}

			$output = [
				'filename' => isset( $get_attachment_metadata['original_image'] ) ? $get_attachment_metadata['original_image'] : '',
				'id'       => $images[0],
				'size'     => $size,
				'url'      => wp_get_attachment_image_url( $images[0], $size ),
			];
		}

		// Might be a Gravatar image
		else {
			$output = [
				'url' => $images[0]
			];
		}

		wp_send_json_success( $output );
	}

	/**
	 * Download image to WordPress media libary (Unsplash)
	 *
	 * @since 1.0
	 */
	public function download_image() {
		self::verify_request();

		// http://www.codingduniya.com/2016/07/generate-featured-image-for-post-using.html
		$file_array = [];

		$tmp = download_url( $_POST['download_url'] );

		$file_array['tmp_name'] = $tmp;

		// Manually add file extension as Unsplash download URL doesn't provide file extension
		$file_array['name'] = $_POST['file_name'] . '.jpg';

		// Check for download errors
		if ( is_wp_error( $tmp ) ) {
			wp_send_json_error( $tmp );
		}

		$id = media_handle_sideload( $file_array, 0 );

		// If error storing permanently, unlink
		if ( is_wp_error( $id ) ) {
			@unlink( $file_array['tmp_name'] );
		}

		wp_send_json_success( $id );
	}

	/**
	 * Parse content through dynamic data logic
	 *
	 * @since 1.5.1
	 */
	public function get_dynamic_data_preview_content() {
		self::verify_request();

		if ( ! isset( $_POST['content'] ) ) {
			wp_send_json_error( 'Error: No content' );
		}

		if ( ! isset( $_POST['postId'] ) ) {
			wp_send_json_error( 'Error: No postId' );
		}

		// Use stripslashes to unescape img URLs, etc. (@since 1.7)
		$content = ! empty( $_POST['content'] ) ? $_POST['content'] : '';
		$context = ! empty( $_POST['context'] ) ? $_POST['context'] : 'text';

		if ( is_string( $content ) ) {
			$content = stripslashes( $content );
		}

		// Get content from custom field
		if ( is_array( $content ) ) {
			// Array format used to parse colors in the builder (@since 1.5.1)
			foreach ( $content as $key => $data ) {
				$content[ $key ]['value'] = bricks_render_dynamic_data( $data['value'], $_POST['postId'], $context );
			}
		} else {
			// Preview composed links e.g. "https://my-domain.com/?user={wp_user_id}" (@since 1.5.4)
			if ( $context == 'link' && ( strpos( $content, '{' ) !== 0 || substr_count( $content, '}' ) > 1 ) ) {
				$context = 'text';
			}

			$content = bricks_render_dynamic_data( $content, $_POST['postId'], $context );
		}

		if ( 'link' === $context ) {
			$content = esc_url( $content );
		}

		// When output a code field, extract the content
		elseif ( is_string( $content ) && strpos( $content, '<pre' ) === 0 ) {
			preg_match( '#<\s*?code\b[^>]*>(.*?)</code\b[^>]*>#s', $content, $matches );
			$content = isset( $matches[1] ) ? $matches[1] : $content;

			// esc_html to escape code tags
			$content = esc_html( $content );
		}

		/**
		 * Run additional checks for non-basic text elements like removing extra <p> tags, etc.
		 *
		 * ContentEditable.js provides the element name.
		 *
		 * @since 1.7
		 */
		$element_name = ! empty( $_POST['elementName'] ) ? $_POST['elementName'] : false;

		if ( $element_name && $element_name !== 'text-basic' ) {
			$content = Helpers::parse_editor_content( $content );
		}

		// NOTE: We are not escaping text content since it could contain formatting tags like <strong> (@since 1.5.1 - preview dynamic data)
		wp_send_json_success(
			[
				'content' => $content,
			]
		);
	}

	/**
	 * Get remote templates
	 *
	 * @since 1.0
	 */
	public function get_remote_templates_data() {
		self::verify_request();

		$look_in_db_first = isset( $_POST['db'] ) ? $_POST['db'] : false;

		$remote_templates = Templates::get_remote_templates_data( $look_in_db_first );

		wp_send_json_success( $remote_templates );
	}

	/**
	 * Builder: Get "My templates" from db
	 *
	 * @since 1.4
	 */
	public function get_my_templates_data() {
		wp_send_json_success( Templates::get_templates( [ 'post_status' => 'any' ] ) );
	}

	/**
	 * Get current user
	 *
	 * Verify logged in user when builder in loaded on the frontend.
	 *
	 * @since 1.5
	 */
	public function get_current_user_id() {
		self::verify_request();

		wp_send_json_success( [ 'user_id' => get_current_user_id() ] );
	}

}