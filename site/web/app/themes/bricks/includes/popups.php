<?php
namespace Bricks;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Popups
 *
 * @since 1.6
 */
class Popups {
	public static $controls                                   = [];
	public static $generated_template_settings_inline_css_ids = [];

	public function __construct() {
		// Add popups HTML to frontend
		if ( ! bricks_is_builder() ) {
			add_action( 'wp_footer', [ $this, 'render_popups' ], 10 );
		}

		self::set_controls();
	}

	public static function get_controls() {
		return self::$controls;
	}

	/**
	 * Set popup controls once initially
	 *
	 * For builder theme style & template settings panel.
	 *
	 * No need to run on hook as it does not contain any db data.
	 */
	public static function set_controls() {
		self::$controls['popupPadding'] = [
			'group' => 'popup',
			'label' => esc_html__( 'Padding', 'bricks' ),
			'type'  => 'spacing',
			'css'   => [
				[
					'property' => 'padding',
					'selector' => '&.brx-popup',
				],
			],
		];

		self::$controls['popupJustifyConent'] = [
			'group'   => 'popup',
			'label'   => esc_html__( 'Align main axis', 'bricks' ),
			'tooltip' => [
				'content'  => 'justify-content',
				'position' => 'top-left',
			],
			'type'    => 'justify-content',
			'inline'  => true,
			'exclude' => [
				'space',
			],
			'css'     => [
				[
					'property' => 'justify-content',
					'selector' => '&.brx-popup',
				],
			],
		];

		self::$controls['popupAlignItems'] = [
			'group'   => 'popup',
			'label'   => esc_html__( 'Align cross axis', 'bricks' ),
			'tooltip' => [
				'content'  => 'align-items',
				'position' => 'top-left',
			],
			'type'    => 'align-items',
			'inline'  => true,
			'exclude' => [
				'stretch',
			],
			'css'     => [
				[
					'property' => 'align-items',
					'selector' => '&.brx-popup',
				],
			],
		];

		self::$controls['popupZindex'] = [
			'group'       => 'popup',
			'label'       => 'Z-index',
			'type'        => 'number',
			'css'         => [
				[
					'property' => 'z-index',
					'selector' => '&.brx-popup',
				],
			],
			'placeholder' => 10000,
		];

		self::$controls['popupBackground'] = [
			'group' => 'popup',
			'label' => esc_html__( 'Background', 'bricks' ),
			'type'  => 'background',
			'css'   => [
				[
					'property' => 'background',
					'selector' => '&.brx-popup',
				],
			],
		];

		// Popup content

		self::$controls['popupContentSep'] = [
			'group' => 'popup',
			'label' => esc_html__( 'Content', 'bricks' ),
			'type'  => 'separator',
		];

		self::$controls['popupContentPadding'] = [
			'group'       => 'popup',
			'label'       => esc_html__( 'Padding', 'bricks' ),
			'type'        => 'spacing',
			'css'         => [
				[
					'property' => 'padding',
					'selector' => '.brx-popup-content',
				],
			],
			'placeholder' => [
				'top'    => '30px',
				'right'  => '30px',
				'bottom' => '30px',
				'left'   => '30px',

			],
		];

		self::$controls['popupContentWidth'] = [
			'group' => 'popup',
			'label' => esc_html__( 'Width', 'bricks' ),
			'type'  => 'number',
			'units' => true,
			'css'   => [
				[
					'property' => 'width',
					'selector' => '.brx-popup-content',
				],
			],
		];

		self::$controls['popupContentHeight'] = [
			'group' => 'popup',
			'label' => esc_html__( 'Height', 'bricks' ),
			'type'  => 'number',
			'units' => true,
			'css'   => [
				[
					'property' => 'height',
					'selector' => '.brx-popup-content',
				],
			],
		];

		self::$controls['popupContentBackground'] = [
			'group' => 'popup',
			'label' => esc_html__( 'Background', 'bricks' ),
			'type'  => 'background',
			'css'   => [
				[
					'property' => 'background',
					'selector' => '.brx-popup-content',
				],
			],
		];

		self::$controls['popupContentBorder'] = [
			'group' => 'popup',
			'label' => esc_html__( 'Border', 'bricks' ),
			'type'  => 'border',
			'css'   => [
				[
					'property' => 'border',
					'selector' => '.brx-popup-content',
				],
			],
		];

		self::$controls['popupContentBoxShadow'] = [
			'group' => 'popup',
			'label' => esc_html__( 'Box shadow', 'bricks' ),
			'type'  => 'box-shadow',
			'css'   => [
				[
					'property' => 'box-shadow',
					'selector' => '.brx-popup-content',
				],
			],
		];

		// Popup limits

		self::$controls['popupLimitsSep'] = [
			'group'       => 'popup',
			'type'        => 'separator',
			'label'       => esc_html__( 'Popup limit', 'bricks' ),
			'description' => esc_html__( 'Limit how often this popup appears.', 'bricks' ),
		];

		self::$controls['popupLimitWindow'] = [
			'group'   => 'popup',
			'type'    => 'number',
			'label'   => esc_html__( 'Per page load', 'bricks' ),
			'tooltip' => [
				'content'  => 'window.brx_popup_{id}_total',
				'position' => 'top-left',
			],
		];

		self::$controls['popupLimitSessionStorage'] = [
			'group'   => 'popup',
			'type'    => 'number',
			'label'   => esc_html__( 'Per session', 'bricks' ),
			'tooltip' => [
				'content'  => 'sessionStorage.brx_popup_{id}_total',
				'position' => 'top-left',
			],
		];

		self::$controls['popupLimitLocalStorage'] = [
			'group'   => 'popup',
			'type'    => 'number',
			'label'   => esc_html__( 'Across sessions', 'bricks' ),
			'tooltip' => [
				'content'  => 'localStorage.brx_popup_{id}_total',
				'position' => 'top-left',
			],
		];
	}

	/**
	 * Check if there is any popup to render and adds popup HTML to the footer
	 *
	 * @since 1.6
	 *
	 * @return void
	 */
	public static function render_popups( $popup_ids ) {
		$popup_ids        = $popup_ids ? $popup_ids : Database::$active_templates['popup'];
		$is_popup_preview = Templates::get_template_type() === 'popup';

		// Is popup preview: Add popup ID
		if ( $is_popup_preview ) {
			$popup_ids = [ get_the_ID() ];
		}

		if ( empty( $popup_ids ) ) {
			return;
		}

		foreach ( $popup_ids as $popup_id ) {
			$elements = Database::get_data( $popup_id );

			if ( empty( $elements ) ) {
				continue;
			}

			$popup_content = Frontend::render_data( $elements, 'popup' );

			// Skip adding popup HTML if empty (e.g. popup outermost element condition not fulfilled)
			if ( empty( $popup_content ) ) {
				continue;
			}

			$popup_template_settings = Helpers::get_template_settings( $popup_id );

			$attributes = [
				'data-popup-id' => $popup_id,
				'class'         => [ 'brx-popup', "brxe-popup-{$popup_id}" ],
			];

			if ( ! $is_popup_preview ) {
				// Not previewing popup template: Hide it
				$attributes['class'][] = 'hide';

				// STEP: Add popup show limits
				$limits = [];

				$limit_options = [
					'popupLimitWindow'         => 'windowStorage',
					'popupLimitSessionStorage' => 'sessionStorage',
					'popupLimitLocalStorage'   => 'localStorage',
				];

				foreach ( $limit_options as $limit => $storage ) {
					if ( empty( $popup_template_settings[ $limit ] ) ) {
						continue;
					}

					$limits[ $storage ] = intval( $popup_template_settings[ $limit ] );
				}

				if ( ! empty( $limits ) ) {
					$attributes['data-popup-limits'] = htmlspecialchars( json_encode( $limits ) );
				}

				// NOTE: Undocumented
				$attributes = apply_filters( 'bricks/popup/attributes', $attributes, $popup_id );
			}

			$attributes = Helpers::stringify_html_attributes( $attributes );

			$popup_content_classes = 'brx-popup-content';

			// Default popup width = Container width
			if ( ! isset( $popup_template_settings['popupContentWidth'] ) ) {
				$popup_content_classes .= ' brxe-container';
			}

			echo "<div {$attributes}>";

			echo "<div class=\"$popup_content_classes\">$popup_content</div>";

			echo '</div>';
		}

		/**
		 * Inside query loop: Get popup template settings
		 *
		 * To generate inline CSS for the popup template located inside a query loop.
		 */
		if ( Query::is_looping() && ! in_array( $popup_id, self::$generated_template_settings_inline_css_ids ) ) {
			$popup_template_settings = Helpers::get_template_settings( $popup_id );

			if ( $popup_template_settings ) {
				$template_settings_controls = Settings::get_controls_data( 'template' );

				$template_settings_inline_css = Assets::generate_inline_css_from_element(
					[
						'settings'             => $popup_template_settings,
						'_templateCssSelector' => ".brxe-popup-{$popup_id}"
					],
					$template_settings_controls['controls'],
					'popup'
				);

				if ( $template_settings_inline_css ) {
					echo "<style>$template_settings_inline_css</style>";

					self::$generated_template_settings_inline_css_ids[] = $popup_id;
				}
			}
		}

		/**
		 * Template settings "Popup" load as inline CSS
		 *
		 * NOTE: Not optimal, but needed as template settings are not part of popup CSS file
		 */
		if ( Database::get_setting( 'cssLoading' ) === 'file' && ! empty( Assets::$inline_css['popup'] ) ) {
			echo '<style>' . Assets::$inline_css['popup'] . '</style>';
		}
	}
}
