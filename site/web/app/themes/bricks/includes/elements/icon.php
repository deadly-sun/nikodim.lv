<?php
namespace Bricks;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Element_Icon extends Element {
	public $category = 'basic';
	public $name     = 'icon';
	public $icon     = 'ti-star';

	public function get_label() {
		return esc_html__( 'Icon', 'bricks' );
	}

	public function set_controls() {
		$this->controls['icon'] = [
			'tab'     => 'content',
			'label'   => esc_html__( 'Icon', 'bricks' ),
			'type'    => 'icon',
			'default' => [
				'library' => 'themify',
				'icon'    => 'ti-star',
			],
			'root'    => true, // To target 'svg' root
		];

		$this->controls['iconColor'] = [
			'tab'      => 'content',
			'label'    => esc_html__( 'Color', 'bricks' ),
			'type'     => 'color',
			'css'      => [
				[
					'property' => 'color',
				],
				[
					'property' => 'fill',
				],
			],
			'required' => [ 'icon.icon', '!=', '' ],
		];

		$this->controls['iconSize'] = [
			'tab'      => 'content',
			'label'    => esc_html__( 'Size', 'bricks' ),
			'type'     => 'number',
			'units'    => true,
			'css'      => [
				[
					'property' => 'font-size',
				],
			],
			'required' => [ 'icon.icon', '!=', '' ],
		];

		$this->controls['link'] = [
			'tab'   => 'content',
			'label' => esc_html__( 'Link', 'bricks' ),
			'type'  => 'link',
		];
	}

	public function render() {
		$settings = $this->settings;
		$icon     = ! empty( $settings['icon'] ) ? $settings['icon'] : false;
		$link     = ! empty( $settings['link'] ) ? $settings['link'] : false;

		if ( ! $icon ) {
			return $this->render_element_placeholder(
				[
					'title' => esc_html__( 'No icon selected.', 'bricks' ),
				]
			);
		}

		// Linked icon: Remove custom attributes from root to add to the link (@since 1.7)
		if ( $link ) {
			$custom_attributes = $this->get_custom_attributes( $settings );

			if ( is_array( $custom_attributes ) ) {
				foreach ( $custom_attributes as $key => $value ) {
					if ( isset( $this->attributes['_root'][ $key ] ) ) {
						unset( $this->attributes['_root'][ $key ] );
					}
				}
			}
		}

		$icon = self::render_icon( $icon, $this->attributes['_root'] );

		if ( $link ) {
			$this->set_link_attributes( 'link', $link );

			// ADD custom attributes to the link instead of the icon (@since 1.7)
			echo "<a {$this->render_attributes( 'link', true )}>";
			echo $icon;
			echo '</a>';
		} else {
			echo $icon;
		}
	}
}
