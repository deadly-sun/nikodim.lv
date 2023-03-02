<?php
namespace Bricks;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Conditions {
	/**
	 * Return all controls (builder)
	 *
	 * @return array
	 */
	public static function get_controls_data() {
		// Return: Prevent querying database outside of builder for condition controls (@since 1.5.7)
		if ( ! bricks_is_builder() ) {
			return;
		}

		// OPTIONS
		$math_options = [
			'==' => '==',
			'!=' => '!=',
			'>=' => '>=',
			'<=' => '<=',
			'>'  => '>',
			'<'  => '<',
		];

		$is_not_options = [
			'==' => esc_html__( 'is', 'bricks' ),
			'!=' => esc_html__( 'is not', 'bricks' ),
		];

		// post_author: 'id' => 'display_name' of all users with 'edit_posts' capability
		$authors = get_users(
			[
				'fields'       => [ 'ID', 'display_name' ],
				'orderby'      => 'display_name',
				'capabilities' => [ 'edit_posts' ],
			]
		);

		$author_options = [];

		foreach ( $authors as $author ) {
			$author_options[ $author->ID ] = $author->display_name;
		}

		// Return condition controls
		return [
			// POST
			'postGroupTitle'   => [
				'label' => esc_html__( 'Post', 'bricks' ),
			],

			'post_id'          => [
				'label'   => esc_html__( 'Post ID', 'bricks' ),
				'compare' => [
					'type'        => 'select',
					'options'     => $math_options,
					'placeholder' => '==',
				],
				'value'   => [
					'type' => 'text',
				],
			],

			'post_title'       => [
				'label'   => esc_html__( 'Post title', 'bricks' ),
				'compare' => [
					'type'        => 'select',
					'options'     => [
						'=='           => esc_html__( 'is', 'bricks' ),
						'!='           => esc_html__( 'is not', 'bricks' ),
						'contains'     => esc_html__( 'contains', 'bricks' ),
						'contains_not' => esc_html__( 'does not contain', 'bricks' ),
					],
					'placeholder' => esc_html__( 'is', 'bricks' ),
				],
				'value'   => [
					'type' => 'text',
				],
			],

			'post_parent'      => [
				'label'   => esc_html__( 'Post parent', 'bricks' ),
				'compare' => [
					'type'        => 'select',
					'options'     => $math_options,
					'placeholder' => '==',
				],
				'value'   => [
					'type'        => 'text',
					'placeholder' => 0,
				],
			],

			'post_status'      => [
				'label'   => esc_html__( 'Post status', 'bricks' ),
				'compare' => [
					'type'        => 'select',
					'options'     => $is_not_options,
					'placeholder' => esc_html__( 'is', 'bricks' ),
				],
				'value'   => [
					'type'        => 'select',
					'options'     => get_post_statuses(),
					'multiple'    => true,
					'placeholder' => esc_html__( 'Select', 'bricks' ),
				],
			],

			'post_author'      => [
				'label'   => esc_html__( 'Post author', 'bricks' ),
				'compare' => [
					'type'    => 'select',
					'options' => $is_not_options,
				],
				'value'   => [
					'type'    => 'select',
					'options' => $author_options,
				],
			],

			'post_date'        => [
				'label'   => esc_html__( 'Post date', 'bricks' ),
				'compare' => [
					'type'        => 'select',
					'options'     => $math_options,
					'placeholder' => '==',
				],
				'value'   => [
					'type'       => 'datepicker',
					'enableTime' => false,
				],
			],

			// set OR not set
			'featured_image'   => [
				'label'   => esc_html__( 'Featured image', 'bricks' ),
				'compare' => [
					'type'        => 'select',
					'options'     => $is_not_options,
					'placeholder' => esc_html__( 'Select', 'bricks' ),
					// 'required' => ['key', '!=', 'featured_image'],
				],
				'value'   => [
					'type'        => 'select',
					'options'     => [
						'1' => esc_html__( 'set', 'bricks' ),
						'0' => esc_html__( 'not set', 'bricks' ),
					],
					'placeholder' => esc_html__( 'Select', 'bricks' ),
				],
			],

			// USER
			'userGroupTitle'   => [
				'label' => esc_html__( 'User', 'bricks' ),
			],

			'user_logged_in'   => [
				'label'   => esc_html__( 'User login', 'bricks' ),
				'compare' => [
					'type'        => 'select',
					'options'     => $is_not_options,
					'placeholder' => esc_html__( 'is', 'bricks' ),
				],
				'value'   => [
					'type'        => 'select',
					'options'     => [
						1 => esc_html__( 'Logged in', 'bricks' ),
						0 => esc_html__( 'Logged out', 'bricks' ),
					],
					'placeholder' => esc_html__( 'Select', 'bricks' ),
				],
			],

			'user_id'          => [
				'label'   => esc_html__( 'User ID', 'bricks' ),
				'compare' => [
					'type'        => 'select',
					'options'     => $math_options,
					'placeholder' => '==',
				],
				'value'   => [
					'type'        => 'text',
					'placeholder' => '',
				],
			],

			'user_registered'  => [
				'label'   => esc_html__( 'User registered', 'bricks' ),
				'compare' => [
					'type'        => 'select',
					'options'     => [
						'<' => esc_html__( 'after', 'bricks' ),
						'>' => esc_html__( 'before', 'bricks' ),
					],
					'placeholder' => esc_html__( 'Select', 'bricks' ),
				],
				'value'   => [
					'type'        => 'datepicker',
					'enableTime'  => false,
					'placeholder' => date( 'Y-m-d' ),
				],
			],

			'user_role'        => [
				'label'   => esc_html__( 'User role', 'bricks' ),
				'compare' => [
					'type'    => 'select',
					'options' => $is_not_options,
				],
				'value'   => [
					'type'        => 'select',
					'options'     => wp_roles()->get_names(),
					'multiple'    => true,
					'placeholder' => esc_html__( 'Select', 'bricks' ),
				],
			],

			// DATE
			'dateGroupTitle'   => [
				'label' => esc_html__( 'Date & time', 'bricks' ),
			],

			'weekday'          => [
				'label'   => esc_html__( 'Weekday', 'bricks' ),
				'compare' => [
					'type'        => 'select',
					'options'     => $math_options,
					'placeholder' => '==',
				],
				'value'   => [
					'type'        => 'select',
					'options'     => [
						1 => esc_html__( 'Monday', 'bricks' ),
						2 => esc_html__( 'Tuesday', 'bricks' ),
						3 => esc_html__( 'Wednesday', 'bricks' ),
						4 => esc_html__( 'Thursday', 'bricks' ),
						5 => esc_html__( 'Friday', 'bricks' ),
						6 => esc_html__( 'Saturday', 'bricks' ),
						7 => esc_html__( 'Sunday', 'bricks' ),
					],
					'placeholder' => esc_html__( 'Select', 'bricks' ),
				],
			],

			'date'             => [
				'label'   => esc_html__( 'Date', 'bricks' ),
				'compare' => [
					'type'        => 'select',
					'options'     => $math_options,
					'placeholder' => '==',
				],
				'value'   => [
					'type'        => 'datepicker',
					'enableTime'  => false,
					'placeholder' => date( 'Y-m-d' ),
				],
			],

			'time'             => [
				'label'   => esc_html__( 'Time', 'bricks' ),
				'compare' => [
					'type'        => 'select',
					'options'     => $math_options,
					'placeholder' => '==',
				],
				'value'   => [
					'type'        => 'text',
					'placeholder' => date( 'H:i' ),
				],
			],

			// OTHER
			'otherGroupTitle'  => [
				'label' => esc_html__( 'Other', 'bricks' ),
			],

			'dynamic_data'     => [
				'label'   => esc_html__( 'Dynamic data', 'bricks' ),
				'compare' => [
					'type'        => 'select',
					'options'     => array_merge(
						[
							'contains'     => esc_html__( 'contains', 'bricks' ),
							'contains_not' => esc_html__( 'does not contain', 'bricks' ),
						],
						$math_options
					),
					'placeholder' => '==',
				],
				'value'   => [
					'type' => 'text',
				],
			],

			'browser'          => [
				'label'   => esc_html__( 'Browser', 'bricks' ),
				'compare' => [
					'type'        => 'select',
					'options'     => $is_not_options,
					'placeholder' => esc_html__( 'is', 'bricks' ),
				],
				'value'   => [
					'type'        => 'select',
					'options'     => [
						'chrome'  => 'Chrome',
						'firefox' => 'Firefox',
						'safari'  => 'Safari',
						'edge'    => 'Edge',
						'opera'   => 'Opera',
						'msie'    => 'Internet Explorer'
					],
					'placeholder' => esc_html__( 'Select', 'bricks' ),
				],
			],

			'operating_system' => [
				'label'   => esc_html__( 'Operating system', 'bricks' ),
				'compare' => [
					'type'        => 'select',
					'options'     => $is_not_options,
					'placeholder' => esc_html__( 'is', 'bricks' ),
				],
				'value'   => [
					'type'        => 'select',
					'options'     => [
						'windows'    => 'Windows',
						'mac'        => 'macOS',
						'linux'      => 'Linux',
						'ubuntu'     => 'Ubuntu',
						'iphone'     => 'iPhone',
						'ipad'       => 'iPad',
						'ipod'       => 'iPod',
						'android'    => 'Android',
						'blackberry' => 'Blackberry',
						'webos'      => 'Mobile (webOS)',
					],
					'placeholder' => esc_html__( 'Select', 'bricks' ),
				],
			],

			'referer'          => [
				'label'   => esc_html__( 'Referrer URL', 'bricks' ),
				'compare' => [
					'type'        => 'select',
					'options'     => [
						'=='           => esc_html__( 'is', 'bricks' ),
						'!='           => esc_html__( 'is not', 'bricks' ),
						'contains'     => esc_html__( 'contains', 'bricks' ),
						'contains_not' => esc_html__( 'does not contain', 'bricks' ),
					],
					'placeholder' => esc_html__( 'is', 'bricks' ),
				],
				'value'   => [
					'type' => 'text',
				],
			],

			// 'location' => [
			// 'label' => esc_html__( 'Location', 'bricks' ),
			// 'compare' => [
			// 'type'        => 'select',
			// 'options'     => [
			// '=='           => esc_html__( 'is', 'bricks' ),
			// '!='           => esc_html__( 'is not', 'bricks' ),
			// 'contains'     => esc_html__( 'contains', 'bricks' ),
			// 'contains_not' => esc_html__( 'does not contain', 'bricks' ),
			// ],
			// 'placeholder' => esc_html__( 'is', 'bricks' ),
			// ],
			// 'value' => [
			// 'type' => 'text',
			// ],
			// ],
		];
	}

	/**
	 * Check element conditions
	 *
	 * At least one condition set must be fulfilled for the element to be rendered.
	 *
	 * Inside a condition all items must evaluate to true.
	 *
	 * @return boolean true = render element | false = don't render element
	 *
	 * @since 1.5.4
	 */
	public static function check( $conditions, $instance ) {
		// Return: Always render element in builder
		if ( bricks_is_builder() ) {
			return true;
		}

		$user_agent     = isset( $_SERVER['HTTP_USER_AGENT'] ) ? $_SERVER['HTTP_USER_AGENT'] : null;
		$post_id        = $instance->post_id;
		$post           = get_post( $post_id );
		$user           = wp_get_current_user();
		$render_element = false;

		// Loop over condition sets (logic between sets: OR)
		foreach ( $conditions as $condition_set ) {
			$render_set = true;

			// Loop over conditions inside a set (logic inside a set: AND)
			foreach ( $condition_set as $condition ) {
				// Skip further checks in condition set if we already have a false condition inside this set
				if ( $render_set === false ) {
					continue;
				}

				$key      = isset( $condition['key'] ) ? $condition['key'] : false;
				$compare  = isset( $condition['compare'] ) ? $condition['compare'] : '==';
				$required = isset( $condition['value'] ) ? $instance->render_dynamic_data( $condition['value'] ) : false;

				$value = false;

				// STEP: Get current value
				switch ( $key ) {
					// POST
					case 'post_id':
						$value = $post_id;
						break;

					case 'post_title':
						$value = $post->post_title;
						break;

					case 'post_parent':
						$value = $post->post_parent;
						break;

					case 'post_status':
						$value = $post->post_status;
						break;

					case 'post_author':
						$value = $post->post_author;
						break;

					case 'post_date':
						$value = date( 'Y-m-d', strtotime( $post->post_date ) ); // 2022-12-31
						break;

					case 'featured_image':
						$value = has_post_thumbnail( $post_id );
						break;

					// USER
					case 'user_logged_in':
						$value = is_user_logged_in();
						break;

					case 'user_id':
						$value = $user->ID;
						break;

					case 'user_registered':
						$value = date( 'Y-m-d', strtotime( $user->user_registered ) );

						if ( ! $required ) {
							$required = date( 'Y-m-d' );
						}
						break;

					case 'user_role':
						$value = $user->roles;
						break;

					// DATE
					case 'weekday':
						$value = date( 'N' ); // 1 = monday, 2 = tuesday, etc.
						break;

					case 'date':
						$value = date( 'Y-m-d' );

						if ( ! $required ) {
							$required = date( 'Y-m-d' );
						}
						break;

					case 'time':
						$value    = time();
						$required = strtotime( $required );
						break;

					// OTHER
					case 'dynamic_data':
						if ( ! empty( $condition['dynamic_data'] ) ) {
							$dynamic_data_tag = $condition['dynamic_data'];

							// NOTE: Not in use (keep for reference in case we provide a "compare_against" value/label select control)
							// if ( strpos( $dynamic_data_tag, '{' ) === 0 ) {
								// Add 'value' filter to dynamic data tag: For element conditions like MB checkbox_list, ACF true_false, etc. (@since 1.5.7)
								// $dynamic_data_tag = str_replace( '}', ':value}', $required );
							// }

							$value = $instance->render_dynamic_data( $dynamic_data_tag );
						}
						break;

					case 'browser':
						if ( preg_match( '/chrome/i', $user_agent ) ) {
							$value = 'chrome';
						} elseif ( preg_match( '/firefox/i', $user_agent ) ) {
							$value = 'firefox';
						} elseif ( preg_match( '/safari/i', $user_agent ) ) {
							$value = 'safari';
						} elseif ( preg_match( '/edge/i', $user_agent ) ) {
							$value = 'edge';
						} elseif ( preg_match( '/opera/i', $user_agent ) ) {
							$value = 'opera';
						} elseif ( preg_match( '/msie/i', $user_agent ) ) {
							$value = 'msie';
						}
						break;

					case 'operating_system':
						if ( preg_match( '/win/i', $user_agent ) ) {
							$value = 'windows';
						} elseif ( preg_match( '/mac/i', $user_agent ) ) {
							$value = 'mac';
						} elseif ( preg_match( '/linux/i', $user_agent ) ) {
							$value = 'linux';
						} elseif ( preg_match( '/ubuntu/i', $user_agent ) ) {
							$value = 'ubuntu';
						} elseif ( preg_match( '/iphone/i', $user_agent ) ) {
							$value = 'iphone';
						} elseif ( preg_match( '/ipad/i', $user_agent ) ) {
							$value = 'ipad';
						} elseif ( preg_match( '/ipod/i', $user_agent ) ) {
							$value = 'ipod';
						} elseif ( preg_match( '/android/i', $user_agent ) ) {
							$value = 'android';
						} elseif ( preg_match( '/blackberry/i', $user_agent ) ) {
							$value = 'blackberry';
						} elseif ( preg_match( '/webos/i', $user_agent ) ) {
							$value = 'webos';
						}
						break;

					case 'referer':
						$value = isset( $_SERVER['HTTP_REFERER'] ) ? $_SERVER['HTTP_REFERER'] : '';
						break;

					// case 'location':
						// error_log( print_r( $_SERVER['REMOTE_ADDR'], true ) );
						// $ip = '93.109.125.21';
						// $value = unserialize( file_get_contents( "//www.geoplugin.net/php.gp?ip=$ip" ) );
						// $value = json_decode( file_get_contents( "//ipinfo.io/{$ip}/json" ) );
						// break;
				}

				/**
				 * Convert boolean-like strings to actual booleans for proper true/false comparisions
				 *
				 * @since 1.7
				 */
				$possible_boolean = [ 'True', 'False', 'true', 'false', true, false, '1', '0', '' ];

				if ( in_array( $required, $possible_boolean, true ) && in_array( $value, $possible_boolean, true ) ) {
					$required = filter_var( $required, FILTER_VALIDATE_BOOLEAN );
					$value    = filter_var( $value, FILTER_VALIDATE_BOOLEAN );
				}

				// COMPARISON OPERANDS
				switch ( $compare ) {
					case '==':
						// user_role (one of the user roles must be in requested roles array)
						if ( is_array( $value ) && is_array( $required ) ) {
							$render_set = count( array_intersect( $value, $required ) ) > 0;
						}

						// post_status (multiple)
						elseif ( is_array( $required ) ) {
							$render_set = in_array( $value, $required );
						} else {
							$render_set = $value == $required;
						}
						break;

					case '!=':
						$render_set = $value != $required;
						break;

					case '>=':
						$render_set = $value >= $required;
						break;

					case '<=':
						$render_set = $value <= $required;
						break;

					case '>':
						$render_set = $value > $required;
						break;

					case '<':
						$render_set = $value < $required;
						break;

					// post_title
					case 'contains':
						// Check if string contains keyword
						if ( $value && gettype( $value ) === 'string' && gettype( $required ) === 'string' ) {
							$render_set = strpos( $value, $required ) !== false;
						} else {
							$render_set = false;
						}
						break;

					// post_title
					case 'contains_not':
						// Check if string does not contain keyword
						if ( $value && gettype( $value ) === 'string' && gettype( $required ) === 'string' ) {
							$render_set = strpos( $value, $required ) === false;
						} else {
							$render_set = false;
						}
						break;
				}
			}

			// All items inside condition are fulfilled: Render element
			if ( $render_set ) {
				$render_element = true;
			}
		}

		return $render_element;
	}
}
