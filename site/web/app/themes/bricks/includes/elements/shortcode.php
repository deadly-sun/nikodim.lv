<?php
namespace Bricks;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Element_Shortcode extends Element {
	public $block    = 'core/shortcode';
	public $category = 'wordpress';
	public $name     = 'shortcode';
	public $icon     = 'ti-shortcode';

	public function get_label() {
		return esc_html__( 'Shortcode', 'bricks' );
	}

	public function set_controls() {
		$this->controls['shortcode'] = [
			'tab'         => 'content',
			'label'       => esc_html__( 'Shortcode', 'bricks' ),
			'type'        => 'textarea',
			'placeholder' => '[gallery ids="72,73,74,75,76,77" columns="3"]',
			'rerender'    => true,
		];
	}

	public function render() {
		$shortcode = ! empty( $this->settings['shortcode'] ) ? stripcslashes( $this->settings['shortcode'] ) : false;

		if ( ! $shortcode ) {
			return $this->render_element_placeholder( [ 'title' => esc_html__( 'No shortcode provided.', 'bricks' ) ] );
		}

		// Render dynamic data first - shortcode attributes might depend on it
		$shortcode = $this->render_dynamic_data( $shortcode );

		// Check: Is 'popup' template
		$is_popup_template = false;

		if ( strpos( $shortcode, BRICKS_DB_TEMPLATE_SLUG ) !== false ) {
			$shortcode_text = str_replace( '[', '', $shortcode );
			$shortcode_text = str_replace( ']', '', $shortcode_text );

			$shortcode_atts = shortcode_parse_atts( $shortcode_text );

			if ( ! empty( $shortcode_atts['id'] ) ) {
				$is_popup_template = Templates::get_template_type( $shortcode_atts['id'] ) === 'popup';
			}
		}

		// Get shortcode content
		$shortcode = do_shortcode( $shortcode );

		if ( empty( $shortcode ) ) {
			return $this->render_element_placeholder( [ 'title' => esc_html__( 'Shortcode content is empty', 'bricks' ) ] );
		}

		// Render 'popup' template without element root wrapper (@since 1.6)
		if ( $is_popup_template ) {
			echo $shortcode;
		} else {
			echo "<div {$this->render_attributes( '_root' )}>" . $shortcode . '</div>';
		}
	}

	public function convert_element_settings_to_block( $settings ) {
		$block = [
			'blockName'    => $this->block,
			'attrs'        => [],
			'innerContent' => isset( $settings['shortcode'] ) ? [ $settings['shortcode'] ] : [ '' ],
		];

		return $block;
	}

	public function convert_block_to_element_settings( $block, $attributes ) {
		$element_settings = [
			'shortcode' => isset( $block['innerContent'] ) && count( $block['innerContent'] ) ? $block['innerContent'][0] : '',
		];

		return $element_settings;
	}
}
