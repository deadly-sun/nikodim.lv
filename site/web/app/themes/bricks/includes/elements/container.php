<?php
namespace Bricks;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Element_Container extends Element {
	public $category      = 'layout';
	public $name          = 'container';
	public $icon          = 'ti-layout-width-default';
	public $vue_component = 'bricks-nestable';
	public $nestable      = true;

	public function get_label() {
		return esc_html__( 'Container', 'bricks' );
	}

	public function get_keywords() {
		return [ 'query', 'loop', 'repeater' ];
	}

	public function set_controls() {
		if ( bricks_is_builder() && ! Capabilities::current_user_has_full_access() ) {
			$this->controls['infoNoAccess'] = [
				'type'       => 'info',
				'content'    => esc_html__( 'Your builder access level doesn\'t allow you modify these settings.', 'bricks' ),
				'fullAccess' => false,
			];
		}

		/**
		 * Grid item
		 *
		 * Show controls if parent uses display "grid"
		 *
		 * Check via control startsWith '_gridItem'
		 *
		 * @see PanelControl.vue 'settings' watcher
		 * @since 1.x
		 */
		$this->controls['_gridItemSeparator'] = [
			'type'  => 'separator',
			'label' => esc_html__( 'Grid item', 'bricks' ),
		];

		// $this->controls['_gridItemPosition'] = [
		// 'label'       => esc_html__( 'Grid position', 'bricks' ),
		// 'type'        => 'select',
		// 'options'     => [
		// 'auto'   => esc_html__( 'Auto', 'bricks' ),
		// 'manual' => esc_html__( 'Manual', 'bricks' ),
		// 'area'   => esc_html__( 'Area', 'bricks' ),
		// ],
		// 'placeholder' => esc_html__( 'Auto', 'bricks' ),
		// 'inline'      => true,
		// ];

		$this->controls['_gridItemColumnSpan'] = [
			'label'          => esc_html__( 'Grid column', 'bricks' ),
			'tooltip'        => [
				'content'  => 'grid-column',
				'position' => 'top-left',
			],
			'type'           => 'text',
			'inline'         => true,
			'hasDynamicData' => false,
			'css'            => [
				[
					'property' => 'grid-column',
				],
			],
		];

		$this->controls['_gridItemRowSpan'] = [
			'label'          => esc_html__( 'Grid row', 'bricks' ),
			'tooltip'        => [
				'content'  => 'grid-row',
				'position' => 'top-left',
			],
			'type'           => 'text',
			'hasDynamicData' => false,
			'inline'         => true,
			'css'            => [
				[
					'property' => 'grid-row',
				],
			],
		];

		$this->controls['_gridItemSeparatorAfter'] = [
			'type' => 'separator',
		];

		/**
		 * Loop Builder
		 *
		 * Enable for elements: Container, Block, Div (not Section)
		 */
		if ( Capabilities::current_user_has_full_access() && in_array( $this->name, [ 'container', 'block', 'div' ] ) ) {
			$this->controls = array_replace_recursive( $this->controls, $this->get_loop_builder_controls() );

			$this->controls['loopSeparator'] = [
				'type' => 'separator',
			];
		}

		$this->controls['link'] = [
			'label'       => esc_html__( 'Link', 'bricks' ),
			'type'        => 'link',
			'placeholder' => esc_html__( 'Select link type', 'bricks' ),
			'required'    => [ 'tag', '=', 'a' ],
		];

		$this->controls['linkInfo'] = [
			'type'     => 'info',
			'content'  => esc_html__( 'Make sure there are no elements with links inside your linked container (nested links).', 'bricks' ),
			'required' => [ 'link', '!=', '' ],
		];

		$this->controls['tag'] = [
			'label'       => esc_html__( 'HTML tag', 'bricks' ),
			'type'        => 'select',
			'options'     => [
				'div'     => 'div',
				'section' => 'section',
				'a'       => 'a [' . esc_html__( 'link', 'bricks' ) . ']',
				'article' => 'article',
				'nav'     => 'nav',
				'aside'   => 'aside',
				'address' => 'address',
				'figure'  => 'figure',
				'custom'  => esc_html__( 'Custom', 'bricks' ),
			],
			'lowercase'   => true,
			'inline'      => true,
			'placeholder' => $this->tag ? $this->tag : 'div',
			'fullAccess'  => true,
		];

		$this->controls['customTag'] = [
			'label'          => esc_html__( 'Custom tag', 'bricks' ),
			'type'           => 'text',
			'inline'         => true,
			'hasDynamicData' => false,
			'placeholder'    => 'div',
			'required'       => [ 'tag', '=', 'custom' ],
		];

		// Display
		$this->controls['_display'] = [
			'label'     => esc_html__( 'Display', 'bricks' ),
			'type'      => 'select',
			'options'   => [
				'flex'         => 'flex',
				'grid'         => 'grid',
				'block'        => 'block',
				'inline-block' => 'inline-block',
				'inline'       => 'inline',
				'none'         => 'none',
			],
			'inline'    => true,
			'lowercase' => true,
			'css'       => [
				[
					'property' => 'display',
					'selector' => '',
				],
			],
		];

		// Display: grid

		// NOTE: Not in use (too complex)
		// $this->controls['_gridGap'] = [
		// 'label'      => esc_html__( 'Gap', 'bricks' ),
		// 'type'       => 'dimensions',
		// 'directions' => [
		// 'column' => esc_html__( 'Column', 'bricks' ),
		// 'row'    => esc_html__( 'Row', 'bricks' ),
		// ],
		// 'css'        => [
		// [
		// 'property' => 'grid-{key}-gap', // NOTE: Undocumented
		// 'selector' => '',
		// ],
		// ],
		// 'required'   => [ '_display', '=', 'grid' ],
		// ];

		$this->controls['_gridGap'] = [
			'label'       => esc_html__( 'Gap', 'bricks' ),
			'type'        => 'number',
			'units'       => true,
			'css'         => [
				[
					'property' => 'grid-gap', // '{column-gap} {row-gap}' e.g. '20px 40px'
					'selector' => '',
				],
			],
			'placeholder' => '',
			'required'    => [ '_display', '=', 'grid' ],
		];

		$this->controls['_gridTemplateColumns'] = [
			'label'          => esc_html__( 'Grid template columns', 'bricks' ),
			'type'           => 'text',
			'tooltip'        => [
				'content'  => 'grid-tempate-columns',
				'position' => 'top-left',
			],
			'hasDynamicData' => false,
			'css'            => [
				[
					'property' => 'grid-template-columns',
					'selector' => '',
				],
			],
			'placeholder'    => '',
			// 'default'        => '1fr', // fr, auto, min-content, max-content, px, etc.
			'required'       => [ '_display', '=', 'grid' ],
		];

		$this->controls['_gridTemplateRows'] = [
			'label'          => esc_html__( 'Grid template rows', 'bricks' ),
			'type'           => 'text',
			'tooltip'        => [
				'content'  => 'grid-tempate-rows',
				'position' => 'top-left',
			],
			'hasDynamicData' => false,
			'css'            => [
				[
					'property' => 'grid-template-rows',
					'selector' => '',
				],
			],
			'placeholder'    => '',
			// 'default'        => '1fr', // fr, auto, min-content, max-content, px, etc.
			'required'       => [ '_display', '=', 'grid' ],
		];

		$this->controls['_gridAutoColumns'] = [
			'label'          => esc_html__( 'Grid auto columns', 'bricks' ),
			'type'           => 'text',
			'tooltip'        => [
				'content'  => 'grid-auto-columns',
				'position' => 'top-left',
			],
			'hasDynamicData' => false,
			'css'            => [
				[
					'property' => 'grid-auto-columns',
					'selector' => '',
				],
			],
			'required'       => [ '_display', '=', 'grid' ],
		];

		$this->controls['_gridAutoRows'] = [
			'label'          => esc_html__( 'Grid auto rows', 'bricks' ),
			'type'           => 'text',
			'tooltip'        => [
				'content'  => 'grid-auto-rows',
				'position' => 'top-left',
			],
			'hasDynamicData' => false,
			'css'            => [
				[
					'property' => 'grid-auto-rows',
					'selector' => '',
				],
			],
			'required'       => [ '_display', '=', 'grid' ],
		];

		$this->controls['_gridAutoFlow'] = [
			'label'    => esc_html__( 'Grid auto flow', 'bricks' ),
			'type'     => 'select',
			'options'  => [
				'row'    => 'row',
				'column' => 'column',
				'dense'  => 'dense',
			],
			'tooltip'  => [
				'content'  => 'grid-auto-flow',
				'position' => 'top-left',
			],
			'css'      => [
				[
					'property' => 'grid-auto-flow',
					'selector' => '',
				],
			],
			'required' => [ '_display', '=', 'grid' ],
		];

		$this->controls['_justifyItemsGrid'] = [
			'label'     => esc_html__( 'Justify items', 'bricks' ),
			'tooltip'   => [
				'content'  => 'justify-items',
				'position' => 'top-left',
			],
			'type'      => 'justify-content',
			'direction' => 'row',
			'css'       => [
				[
					'property' => 'justify-items',
				],
			],
			'required'  => [ '_display', '=', 'grid' ],
		];

		$this->controls['_alignItemsGrid'] = [
			'label'     => esc_html__( 'Align items', 'bricks' ),
			'tooltip'   => [
				'content'  => 'align-items',
				'position' => 'top-left',
			],
			'type'      => 'align-items',
			'direction' => 'row',
			'css'       => [
				[
					'property' => 'align-items',
				],
			],
			'required'  => [ '_display', '=', 'grid' ],
		];

		$this->controls['_justifyContentGrid'] = [
			'label'     => esc_html__( 'Justify content', 'bricks' ),
			'tooltip'   => [
				'content'  => 'justify-content',
				'position' => 'top-left',
			],
			'type'      => 'justify-content',
			'direction' => 'row',
			'css'       => [
				[
					'property' => 'justify-content',
				],
			],
			'required'  => [ '_display', '=', 'grid' ],
		];

		$this->controls['_alignContentGrid'] = [
			'label'     => esc_html__( 'Align content', 'bricks' ),
			'tooltip'   => [
				'content'  => 'align-content',
				'position' => 'top-left',
			],
			'type'      => 'align-items',
			'direction' => 'row',
			'css'       => [
				[
					'property' => 'align-content',
				],
			],
			'required'  => [ '_display', '=', 'grid' ],
		];

		// Display: flex

		// Flex controls
		$this->controls['_flexWrap'] = [
			'label'    => esc_html__( 'Flex wrap', 'bricks' ),
			'tooltip'  => [
				'content'  => 'flex-wrap',
				'position' => 'top-left',
			],
			'type'     => 'select',
			'options'  => [
				'nowrap'       => esc_html__( 'No wrap', 'bricks' ),
				'wrap'         => esc_html__( 'Wrap', 'bricks' ),
				'wrap-reverse' => esc_html__( 'Wrap reverse', 'bricks' ),
			],
			'inline'   => true,
			'css'      => [
				[
					'property' => 'flex-wrap',
				],
			],
			'required' => [ '_display', '=', 'flex' ],
		];

		$this->controls['_direction'] = [
			'label'    => esc_html__( 'Direction', 'bricks' ),
			'tooltip'  => [
				'content'  => 'flex-direction',
				'position' => 'top-left',
			],
			'type'     => 'direction',
			'css'      => [
				[
					'property' => 'flex-direction',
				],
			],
			'inline'   => true,
			'rerender' => true,
			'required' => [ '_display', '=', 'flex' ],
		];

		$this->controls['_alignSelf'] = [
			'label'    => esc_html__( 'Align self', 'bricks' ),
			'tooltip'  => [
				'content'  => 'align-self',
				'position' => 'top-left',
			],
			'type'     => 'align-items',
			'css'      => [
				[
					'property'  => 'align-self',
					'important' => true,
				],
				[
					'selector' => '',
					'property' => 'width',
					'value'    => '100%',
					'required' => 'stretch', // NOTE: Undocumented (@since 1.4)
				],
			],
			'required' => [ '_display', '=', 'flex' ],
		];

		$this->controls['_justifyContent'] = [
			'label'    => esc_html__( 'Align main axis', 'bricks' ),
			'tooltip'  => [
				'content'  => 'justify-content',
				'position' => 'top-left',
			],
			'type'     => 'justify-content',
			'css'      => [
				[
					'property' => 'justify-content',
				],
			],
			'required' => [ '_display', '=', 'flex' ],
		];

		$this->controls['_alignItems'] = [
			'label'    => esc_html__( 'Align cross axis', 'bricks' ),
			'tooltip'  => [
				'content'  => 'align-items',
				'position' => 'top-left',
			],
			'type'     => 'align-items',
			'css'      => [
				[
					'property' => 'align-items',
				],
			],
			'required' => [ '_display', '=', 'flex' ],
		];

		$this->controls['_columnGap'] = [
			'label'    => esc_html__( 'Column gap', 'bricks' ),
			'type'     => 'number',
			'units'    => true,
			'css'      => [
				[
					'property' => 'column-gap',
				],
			],
			'required' => [ '_display', '=', 'flex' ],
		];

		$this->controls['_rowGap'] = [
			'label'    => esc_html__( 'Row gap', 'bricks' ),
			'type'     => 'number',
			'units'    => true,
			'css'      => [
				[
					'property' => 'row-gap',
				],
			],
			'required' => [ '_display', '=', 'flex' ],
		];

		// @since 1.3.5
		$this->controls['_flexGrow'] = [
			'label'       => esc_html__( 'Flex grow', 'bricks' ),
			'type'        => 'number',
			'min'         => 0,
			'tooltip'     => [
				'content'  => 'flex-grow',
				'position' => 'top-left',
			],
			'css'         => [
				[
					'property' => 'flex-grow',
				],
			],
			'placeholder' => 0,
			'required'    => [ '_display', '=', 'flex' ],
		];

		$this->controls['_flexShrink'] = [
			'label'       => esc_html__( 'Flex shrink', 'bricks' ),
			'type'        => 'number',
			'min'         => 0,
			'tooltip'     => [
				'content'  => 'flex-shrink',
				'position' => 'top-left',
			],
			'css'         => [
				[
					'property' => 'flex-shrink',
				],
			],
			'placeholder' => 1,
			'required'    => [ '_display', '=', 'flex' ],
		];

		$this->controls['_flexBasis'] = [
			'label'          => esc_html__( 'Flex basis', 'bricks' ),
			'type'           => 'text',
			'tooltip'        => [
				'content'  => 'flex-basis',
				'position' => 'top-left',
			],
			'css'            => [
				[
					'property' => 'flex-basis',
				],
			],
			'inline'         => true,
			'small'          => true,
			'placeholder'    => 'auto',
			'hasDynamicData' => false,
			'required'       => [ '_display', '=', 'flex' ],
		];

		// Misc
		$this->controls['_order'] = [
			'label'       => esc_html__( 'Order', 'bricks' ),
			'type'        => 'number',
			'min'         => -999,
			'css'         => [
				[
					'property' => 'order',
				],
			],
			'placeholder' => 0,
			'required'    => [ '_display', '!=',  'none' ],
		];

		// TAB: STYLE

		// Inner container (direct children)
		$this->controls['_innerContainerSeparator'] = [
			'type'  => 'separator',
			'label' => esc_html__( 'Inner container', 'bricks' ) . ' / div',
			'tab'   => 'style',
			'group' => '_layout',
		];

		$this->controls['_innerContainerMargin'] = [
			'tab'   => 'style',
			'group' => '_layout',
			'label' => esc_html__( 'Margin', 'bricks' ),
			'type'  => 'spacing',
			'css'   => [
				[
					'property' => 'margin',
					'selector' => '> .brxe-container',
				],
				[
					'property' => 'margin',
					'selector' => '> .brxe-block',
				],
				[
					'property' => 'margin',
					'selector' => '> .brxe-div',
				],
			],
		];

		$this->controls['_innerContainerPadding'] = [
			'tab'   => 'style',
			'group' => '_layout',
			'label' => esc_html__( 'Padding', 'bricks' ),
			'type'  => 'spacing',
			'css'   => [
				[
					'property' => 'padding',
					'selector' => '> .brxe-container',
				],
				[
					'property' => 'padding',
					'selector' => '> .brxe-block',
				],
				[
					'property' => 'padding',
					'selector' => '> .brxe-div',
				],
			],
		];
	}

	/**
	 * Return shape divider HTML
	 */
	public static function get_shape_divider_html( $settings = [] ) {
		$shape_dividers = ! empty( $settings['_shapeDividers'] ) && is_array( $settings['_shapeDividers'] ) ? $settings['_shapeDividers'] : [];

		$output = '';

		foreach ( $shape_dividers as $shape ) {
			// Skip no shape
			if ( empty( $shape['shape'] ) ) {
				return;
			}

			$svg = Helpers::get_file_contents( BRICKS_URL_ASSETS . "svg/shapes/{$shape['shape']}.svg" );

			// Return: SVG doesn't exist
			if ( ! $svg ) {
				return;
			}

			$shape_classes = [ 'bricks-shape-divider' ];
			$shape_styles  = [];

			// Shape classes
			if ( isset( $shape['front'] ) ) {
				$shape_classes[] = 'front';
			}

			if ( isset( $shape['flipHorizontal'] ) ) {
				$shape_classes[] = 'flip-horizontal';
			}

			if ( isset( $shape['flipVertical'] ) ) {
				$shape_classes[] = 'flip-vertical';
			}

			if ( isset( $shape['overflow'] ) ) {
				$shape_classes[] = 'overflow';
			}

			// Shape styles
			if ( isset( $shape['horizontalAlign'] ) ) {
				$shape_styles[] = "justify-content: {$shape['horizontalAlign']}";
			}

			if ( isset( $shape['verticalAlign'] ) ) {
				$shape_styles[] = "align-items: {$shape['verticalAlign']}";
			}

			// Shape inner styles
			$shape_inner_styles   = [];
			$shape_css_properties = [
				'height',
				'width',
				'top',
				'right',
				'bottom',
				'left',
			];

			foreach ( $shape_css_properties as $property ) {
				$value = isset( $shape[ $property ] ) ? $shape[ $property ] : null;

				if ( $value !== null ) {
					// Append default unit
					if ( is_numeric( $value ) ) {
						$value .= 'px';
					}

					$shape_inner_styles[] = "{$property}: {$value}";
				}
			}

			if ( isset( $shape['rotate'] ) ) {
				$rotate               = intval( $shape['rotate'] );
				$shape_inner_styles[] = "transform: rotate({$rotate}deg)";
			}

			$output .= '<div class="' . join( ' ', $shape_classes ) . '" style="' . join( '; ', $shape_styles ) . '">';
			$output .= '<div class="bricks-shape-divider-inner" style="' . join( '; ', $shape_inner_styles ) . '">';

			$dom = new \DOMDocument();
			libxml_use_internal_errors( true );
			$dom->loadXML( $svg );

			// SVG styles
			$svg_styles = [];

			if ( isset( $shape['fill']['raw'] ) ) {
				$svg_styles[] = "fill: {$shape['fill']['raw']}";
			} elseif ( isset( $shape['fill']['rgb'] ) ) {
				$svg_styles[] = "fill: {$shape['fill']['rgb']}";
			} elseif ( isset( $shape['fill']['hex'] ) ) {
				$svg_styles[] = "fill: {$shape['fill']['hex']}";
			}

			foreach ( $dom->getElementsByTagName( 'svg' ) as $element ) {
				$element->setAttribute( 'style', join( '; ', $svg_styles ) );
			}

			$svg = $dom->saveXML();

			$output .= str_replace( '<?xml version="1.0"?>', '', $svg );

			$output .= '</div>';
			$output .= '</div>';
		}

		return $output;
	}

	/**
	 * Return background video HTML
	 */
	public function get_background_video_html( $settings ) {
		// Loop over all breakpoints (@since 1.5.5)
		foreach ( Breakpoints::$breakpoints as $breakpoint ) {
			$setting_key = $breakpoint['key'] === 'desktop' ? '_background' : "_background:{$breakpoint['key']}";
			$background  = ! empty( $settings[ $setting_key ] ) ? $settings[ $setting_key ] : false;
			$video_url   = ! empty( $background['videoUrl'] ) ? $background['videoUrl'] : false;

			// Dynamic data video URL
			if ( strpos( $video_url, '{' ) !== false ) {
				$video_url = bricks_render_dynamic_data( $video_url, $this->post_id, 'link' );
			}

			if ( $video_url ) {
				$attributes[] = 'class="bricks-background-video-wrapper bricks-lazy-video"';
				$attributes[] = 'data-background-video-url="' . esc_url( $video_url ) . '"';

				if ( ! empty( $background['videoScale'] ) ) {
					$attributes[] = 'data-background-video-scale="' . $background['videoScale'] . '"';
				}

				if ( ! empty( $background['videoAspectRatio'] ) ) {
					$attributes[] = 'data-background-video-ratio="' . $background['videoAspectRatio'] . '"';
				}

				$attributes = join( ' ', $attributes );

				/**
				 * @since 1.4: Chrome doesn't play the .mp4 background video if the <video> tag is injected programmatically using JavaScript
				 */
				return "<div $attributes><video autoplay loop playsinline muted></video></div>";
			}
		}
	}

	public function render() {
		$element  = $this->element;
		$settings = ! empty( $element['settings'] ) ? $element['settings'] : [];
		$output   = '';

		// Bricks Query Loop
		if ( isset( $settings['hasLoop'] ) ) {
			// STEP: Query
			add_filter( 'bricks/posts/query_vars', [ $this, 'maybe_set_preview_query' ], 10, 3 );

			$query = new \Bricks\Query( $element );

			remove_filter( 'bricks/posts/query_vars', [ $this, 'maybe_set_preview_query' ], 10, 3 );

			// Prevent endless loop
			unset( $element['settings']['hasLoop'] );

			// STEP: Render loop
			$output = $query->render( 'Bricks\Frontend::render_element', compact( 'element' ) );

			echo $output;

			// STEP: Infinite scroll
			$this->render_query_loop_trail( $query );

			// Destroy Query to explicitly remove it from global store
			$query->destroy();
			unset( $query );

			return;
		}

		// Render the video wrapper first so we know it before adding the has-bg-video class (@since 1.5.1)
		$video_wrapper_html = $this->get_background_video_html( $settings );

		// No background video set on element ID: Loop over element global classes (@since 1.7)
		if ( ! $video_wrapper_html ) {
			$elements_class_ids = ! empty( $settings['_cssGlobalClasses'] ) ? $settings['_cssGlobalClasses'] : [];

			if ( count( $elements_class_ids ) ) {
				$global_classes = Database::$global_data['globalClasses'];

				foreach ( $global_classes as $global_class ) {
					$global_class_id = ! empty( $global_class['id'] ) ? $global_class['id'] : '';

					if ( ! $video_wrapper_html && in_array( $global_class_id, $elements_class_ids ) ) {
						if ( ! empty( $global_class['settings'] ) ) {
							$video_wrapper_html = $this->get_background_video_html( $global_class['settings'] );
						}
					}
				}
			}
		}

		// Add .has-bg-video to set z-index: 1 (#2g9ge90)
		if ( ! empty( $video_wrapper_html ) ) {
			$this->set_attribute( '_root', 'class', 'has-bg-video' );
		}

		// Add .has-shape to set position: relative (#2t7w2bq)
		if ( ! empty( $settings['_shapeDividers'] ) ) {
			$this->set_attribute( '_root', 'class', 'has-shape' );
		}

		// Default: Non Query Loop
		$output .= "<{$this->tag} {$this->render_attributes( '_root' )}>";

		$output .= self::get_shape_divider_html( $settings );

		$output .= $video_wrapper_html;

		if ( ! empty( $element['children'] ) && is_array( $element['children'] ) ) {
			foreach ( $element['children'] as $child_id ) {
				if ( ! array_key_exists( $child_id, Frontend::$elements ) ) {
					continue;
				}

				$child = Frontend::$elements[ $child_id ];

				$output .= Frontend::render_element( $child ); // Recursive
			}
		}

		$output .= "</{$this->tag}>";

		echo $output;
	}
}
