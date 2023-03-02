<?php
namespace Bricks;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Element_Text_Basic extends Element {
	public $block    = 'core/paragraph';
	public $category = 'basic';
	public $name     = 'text-basic';
	public $icon     = 'ti-align-justify';

	public function get_label() {
		return esc_html__( 'Basic Text', 'bricks' );
	}

	public function set_controls() {
		$this->controls['text'] = [
			'tab'         => 'content',
			'type'        => 'textarea',
			'lineBreak'   => 'br',
			'default'     => esc_html__( 'Here goes your text ... Select any part of your text to access the formatting toolbar.', 'bricks' ),
			'description' => esc_html__( 'Select text on canvas to format it. To add headings, paragraphs, and images use the "Rich Text" element.', 'bricks' ),
		];

		$this->controls['tag'] = [
			'tab'         => 'content',
			'label'       => esc_html__( 'HTML tag', 'bricks' ),
			'type'        => 'select',
			'options'     => [
				'div'        => 'div',
				'p'          => 'p',
				'span'       => 'span',
				'figcaption' => 'figcaption',
				'address'    => 'address',
				'figure'     => 'figure',
				'custom'     => esc_html__( 'Custom', 'bricks' ),
			],
			'lowercase'   => true,
			'inline'      => true,
			'placeholder' => 'div',
		];

		$this->controls['customTag'] = [
			'tab'         => 'content',
			'label'       => esc_html__( 'Custom tag', 'bricks' ),
			'type'        => 'text',
			'inline'      => true,
			'placeholder' => 'div',
			'required'    => [ 'tag', '=', 'custom' ],
		];

		$this->controls['link'] = [
			'tab'   => 'content',
			'label' => esc_html__( 'Link to', 'bricks' ),
			'type'  => 'link',
		];
	}

	public function render() {
		$settings = $this->settings;

		$content = isset( $settings['text'] ) ? $settings['text'] : '';

		// Link
		if ( ! empty( $settings['link'] ) ) {
			$this->set_link_attributes( '_root', $settings['link'] );
			$this->tag = 'a';
		}

		echo "<{$this->tag} {$this->render_attributes( '_root' )}>{$content}</{$this->tag}>";
	}

	public static function render_builder() { ?>
		<script type="text/x-template" id="tmpl-bricks-element-text-basic">
			<contenteditable
				:key="tag"
				:name="name"
				controlKey="text"
				toolbar="style align link"
				lineBreak="br"
				:settings="settings"/>
		</script>
		<?php
	}

	public function convert_element_settings_to_block( $settings ) {
		if ( empty( $settings['text'] ) ) {
			return;
		}

		$block = [
			'blockName'    => $this->block,
			'attrs'        => [],
			'innerContent' => [ trim( $settings['text'] ) ],
		];

		return $block;
	}

	// NOTE: Convert block to element settings: Use Bricks "Rich Text" element instead
	// public function convert_block_to_element_settings( $block, $attributes ) {}
}