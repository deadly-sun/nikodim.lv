<?php
namespace Bricks;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Element_List extends Element {
	public $category = 'general';
	public $name     = 'list';
	public $icon     = 'ti-list';

	public function get_label() {
		return esc_html__( 'List', 'bricks' );
	}

	public function set_control_groups() {
		$this->control_groups['items'] = [
			'title' => esc_html__( 'Items', 'bricks' ),
			'tab'   => 'content',
		];

		$this->control_groups['item'] = [
			'title' => esc_html__( 'List item', 'bricks' ),
			'tab'   => 'content',
		];

		$this->control_groups['highlight'] = [
			'title' => esc_html__( 'Highlight', 'bricks' ),
			'tab'   => 'content',
		];

		$this->control_groups['title'] = [
			'title' => esc_html__( 'Title', 'bricks' ),
			'tab'   => 'content',
		];

		$this->control_groups['meta'] = [
			'title' => esc_html__( 'Meta', 'bricks' ),
			'tab'   => 'content',
		];

		$this->control_groups['description'] = [
			'title' => esc_html__( 'Description', 'bricks' ),
			'tab'   => 'content',
		];

		$this->control_groups['separator'] = [
			'title' => esc_html__( 'Separator', 'bricks' ),
			'tab'   => 'content',
		];
	}

	public function set_controls() {
		$this->controls['items'] = [
			'tab'           => 'content',
			'group'         => 'items',
			'placeholder'   => esc_html__( 'List items', 'bricks' ),
			'type'          => 'repeater',
			'titleProperty' => 'title',
			'fields'        => [
				'title'          => [
					'label' => esc_html__( 'Title', 'bricks' ),
					'type'  => 'text',
				],

				'link'           => [
					'label' => esc_html__( 'Link title', 'bricks' ),
					'type'  => 'link',
				],

				'meta'           => [
					'label' => esc_html__( 'Meta', 'bricks' ),
					'type'  => 'text',
				],

				'description'    => [
					'label' => esc_html__( 'Description', 'bricks' ),
					'type'  => 'textarea',
				],

				'highlight'      => [
					'label' => esc_html__( 'Highlight', 'bricks' ),
					'type'  => 'checkbox',
				],

				'highlightLabel' => [
					'label'    => esc_html__( 'Highlight label', 'bricks' ),
					'type'     => 'text',
					'inline'   => true,
					'required' => [ 'highlight', '!=', '' ],
				],
			],
			'default'       => [
				[
					'title' => esc_html__( 'List item #1', 'bricks' ),
					'meta'  => esc_html__( '$10.00', 'bricks' ),
				],
				[
					'title' => esc_html__( 'List item #2', 'bricks' ),
					'meta'  => esc_html__( '$25.00', 'bricks' ),
				],
			],
		];

		/**
		 * List item
		 */

		$this->controls['itemJustifyContent'] = [
			'tab'   => 'content',
			'group' => 'item',
			'label' => esc_html__( 'Alignment', 'bricks' ),
			'type'  => 'justify-content',
			'css'   => [
				[
					'property' => 'justify-content',
					'selector' => '.content',
				],
				[
					'property' => 'justify-content',
					'selector' => '.description',
				],
			],
		];

		$this->controls['itemMargin'] = [
			'tab'   => 'content',
			'group' => 'item',
			'label' => esc_html__( 'Margin', 'bricks' ),
			'type'  => 'spacing',
			'css'   => [
				[
					'property' => 'margin',
					'selector' => 'li',
				],
			],
		];

		$this->controls['itemPadding'] = [
			'tab'   => 'content',
			'group' => 'item',
			'label' => esc_html__( 'Padding', 'bricks' ),
			'type'  => 'spacing',
			'css'   => [
				[
					'property' => 'padding',
					'selector' => 'li',
				],
			],
		];

		$this->controls['itemOddBackground'] = [
			'tab'   => 'content',
			'group' => 'item',
			'label' => esc_html__( 'Odd background', 'bricks' ),
			'type'  => 'color',
			'css'   => [
				[
					'property' => 'background-color',
					'selector' => 'li:nth-child(odd)',
				],
			],
		];

		$this->controls['itemEvenBackground'] = [
			'tab'   => 'content',
			'group' => 'item',
			'label' => esc_html__( 'Even background', 'bricks' ),
			'type'  => 'color',
			'css'   => [
				[
					'property' => 'background-color',
					'selector' => 'li:nth-child(even)',
				],
			],
		];

		$this->controls['itemBorder'] = [
			'tab'   => 'content',
			'group' => 'settings',
			'label' => esc_html__( 'Border', 'bricks' ),
			'type'  => 'border',
			'css'   => [
				[
					'property' => 'border',
					'selector' => 'li',
				],
			],
		];

		$this->controls['itemAutoWidth'] = [
			'tab'   => 'content',
			'group' => 'item',
			'label' => esc_html__( 'Auto width', 'bricks' ),
			'type'  => 'checkbox',
			'css'   => [
				[
					'property' => 'justify-content',
					'selector' => '.content',
					'value'    => 'initial',
				],
				[
					'property' => 'flex-grow',
					'selector' => '.separator',
					'value'    => '0',
				],
			],
		];

		/**
		 * Highlight
		 */

		$this->controls['highlightBlock'] = [
			'tab'   => 'content',
			'group' => 'highlight',
			'label' => esc_html__( 'Block', 'bricks' ),
			'type'  => 'checkbox',
			'css'   => [
				[
					'property' => 'display',
					'selector' => 'li[data-highlight]::before',
					'value'    => 'block',
				],
			],
		];

		$this->controls['highlightLabelPadding'] = [
			'tab'   => 'content',
			'group' => 'highlight',
			'label' => esc_html__( 'Padding', 'bricks' ),
			'type'  => 'spacing',
			'css'   => [
				[
					'property' => 'padding',
					'selector' => 'li[data-highlight]::before',
				],
			],
		];

		$this->controls['highlightLabelBackground'] = [
			'tab'   => 'content',
			'group' => 'highlight',
			'label' => esc_html__( 'Background', 'bricks' ),
			'type'  => 'color',
			'css'   => [
				[
					'property' => 'background-color',
					'selector' => 'li[data-highlight]::before',
				],
			],
		];

		$this->controls['highlightLabelBorder'] = [
			'tab'   => 'content',
			'group' => 'highlight',
			'label' => esc_html__( 'Border', 'bricks' ),
			'type'  => 'border',
			'css'   => [
				[
					'property' => 'border',
					'selector' => 'li[data-highlight]::before',
				],
			],
		];

		$this->controls['highlightLabelTypography'] = [
			'tab'   => 'content',
			'group' => 'highlight',
			'label' => esc_html__( 'Typography', 'bricks' ),
			'type'  => 'typography',
			'css'   => [
				[
					'property' => 'font',
					'selector' => 'li[data-highlight]::before',
				],
			],
		];

		$this->controls['separatorHighlightContent'] = [
			'tab'   => 'content',
			'group' => 'highlight',
			'label' => esc_html__( 'Content', 'bricks' ),
			'type'  => 'separator',
		];

		$this->controls['highlightContentPadding'] = [
			'tab'   => 'content',
			'group' => 'highlight',
			'label' => esc_html__( 'Padding', 'bricks' ),
			'type'  => 'spacing',
			'css'   => [
				[
					'property' => 'padding',
					'selector' => 'li[data-highlight] .content',
				],
			],
		];

		$this->controls['highlightContentBackground'] = [
			'tab'   => 'content',
			'group' => 'highlight',
			'label' => esc_html__( 'Background', 'bricks' ),
			'type'  => 'color',
			'css'   => [
				[
					'property' => 'background-color',
					'selector' => 'li[data-highlight] .content',
				],
			],
		];

		$this->controls['highlightContentBorder'] = [
			'tab'   => 'content',
			'group' => 'highlight',
			'label' => esc_html__( 'Border', 'bricks' ),
			'type'  => 'border',
			'css'   => [
				[
					'property' => 'border',
					'selector' => 'li[data-highlight] .content',
				],
			],
		];

		$this->controls['highlightContentColor'] = [
			'tab'   => 'content',
			'group' => 'highlight',
			'label' => esc_html__( 'Text color', 'bricks' ),
			'type'  => 'color',
			'css'   => [
				[
					'property' => 'color',
					'selector' => 'li[data-highlight] .content .title',
				],
				[
					'property' => 'color',
					'selector' => 'li[data-highlight] .content .meta',
				],
				[
					'property' => 'color',
					'selector' => 'li[data-highlight] .content .description',
				],
			],
		];

		/**
		 * Title
		 */

		$this->controls['titleMargin'] = [
			'tab'   => 'content',
			'group' => 'title',
			'label' => esc_html__( 'Margin', 'bricks' ),
			'type'  => 'spacing',
			'css'   => [
				[
					'property' => 'margin',
					'selector' => '.title',
				],
			],
		];

		$this->controls['titleTag'] = [
			'tab'         => 'content',
			'group'       => 'title',
			'label'       => esc_html__( 'Tag', 'bricks' ),
			'type'        => 'select',
			'options'     => [
				'h2' => esc_html__( 'Heading 2 (h2)', 'bricks' ),
				'h3' => esc_html__( 'Heading 3 (h3)', 'bricks' ),
				'h4' => esc_html__( 'Heading 4 (h4)', 'bricks' ),
				'h5' => esc_html__( 'Heading 5 (h5)', 'bricks' ),
				'h6' => esc_html__( 'Heading 6 (h6)', 'bricks' ),
			],
			'inline'      => true,
			'placeholder' => 'span',
		];

		$this->controls['titleTypography'] = [
			'tab'   => 'content',
			'group' => 'title',
			'label' => esc_html__( 'Typography', 'bricks' ),
			'type'  => 'typography',
			'css'   => [
				[
					'property' => 'font',
					'selector' => '.title',
				],
			],
		];

		/**
		 * Meta
		 */

		$this->controls['metaMargin'] = [
			'tab'   => 'content',
			'group' => 'meta',
			'label' => esc_html__( 'Margin', 'bricks' ),
			'type'  => 'spacing',
			'css'   => [
				[
					'property' => 'margin',
					'selector' => '.meta',
				],
			],
		];

		$this->controls['metaTypography'] = [
			'tab'   => 'content',
			'group' => 'meta',
			'label' => esc_html__( 'Typography', 'bricks' ),
			'type'  => 'typography',
			'css'   => [
				[
					'property' => 'font',
					'selector' => '.meta',
				],
			],
		];

		/**
		 * Description
		 */

		$this->controls['descriptionTypography'] = [
			'tab'   => 'content',
			'group' => 'description',
			'label' => esc_html__( 'Typography', 'bricks' ),
			'type'  => 'typography',
			'css'   => [
				[
					'property' => 'font',
					'selector' => '.description',
				],
			],
		];

		/**
		 * Separator
		 */

		$this->controls['separatorDisable'] = [
			'tab'   => 'content',
			'group' => 'separator',
			'label' => esc_html__( 'Disable', 'bricks' ),
			'type'  => 'checkbox',
			'css'   => [
				[
					'property' => 'display',
					'selector' => '.separator',
					'value'    => 'none',
				],
			],
		];

		$this->controls['separatorStyle'] = [
			'tab'      => 'content',
			'group'    => 'separator',
			'label'    => esc_html__( 'Style', 'bricks' ),
			'type'     => 'select',
			'options'  => $this->control_options['borderStyle'],
			'css'      => [
				[
					'property' => 'border-top-style',
					'selector' => '.separator',
				],
			],
			'inline'   => true,
			'required' => [ 'separatorDisable', '=', '' ],
		];

		$this->controls['separatorWidth'] = [
			'tab'      => 'content',
			'group'    => 'separator',
			'label'    => esc_html__( 'Width', 'bricks' ),
			'type'     => 'number',
			'units'    => true,
			'css'      => [
				[
					'property' => 'flex-basis',
					'selector' => '.separator',
				],
				[
					'property' => 'flex-grow',
					'selector' => '.separator',
					'value'    => '0',
				],
			],
			'required' => [ 'separatorDisable', '=', '' ],
		];

		$this->controls['separatorHeight'] = [
			'tab'      => 'content',
			'group'    => 'separator',
			'label'    => esc_html__( 'Height', 'bricks' ),
			'type'     => 'number',
			'units'    => true,
			'css'      => [
				[
					'property' => 'border-top-width',
					'selector' => '.separator',
				],
			],
			'required' => [ 'separatorDisable', '=', '' ],
		];

		$this->controls['separatorColor'] = [
			'tab'      => 'content',
			'group'    => 'separator',
			'label'    => esc_html__( 'Color', 'bricks' ),
			'type'     => 'color',
			'css'      => [
				[
					'property' => 'border-top-color',
					'selector' => '.separator',
				],
			],
			'required' => [ 'separatorDisable', '=', '' ],
		];
	}

	public function render() {
		$settings = $this->settings;

		if ( empty( $settings['items'] ) ) {
			return $this->render_element_placeholder(
				[
					'title' => esc_html__( 'No list items defined.', 'bricks' ),
				]
			);
		}

		$output = "<ul {$this->render_attributes( '_root' )}>";

		foreach ( $settings['items'] as $index => $item ) {
			$highlight = isset( $item['highlight'] ) && ! empty( $item['highlightLabel'] ) ? $item['highlightLabel'] : false;

			if ( $highlight ) {
				$this->set_attribute( "item-$index", 'data-highlight', $highlight );
			}

			$output .= "<li {$this->render_attributes( "item-$index" )}>";

			$output .= '<div class="content">';

			if ( ! empty( $item['title'] ) ) {
				$title_tag = ! empty( $settings['titleTag'] ) ? esc_attr( $settings['titleTag'] ) : 'span';

				$this->set_attribute( "title-$index", $title_tag );
				$this->set_attribute( "title-$index", 'class', [ 'title' ] );

				if ( ! empty( $item['link'] ) ) {
					$this->set_link_attributes( "a-$index", $item['link'] );
					$output .= "<a {$this->render_attributes( "a-$index" )}>";
				}

				$output .= "<{$this->render_attributes( "title-$index" )}>{$item['title']}</{$title_tag}>";

				if ( ! empty( $item['link'] ) ) {
					$output .= '</a>';
				}
			}

			if ( ! isset( $settings['separatorDisable'] ) ) {
				$output .= '<span class="separator"></span>';
			}

			if ( ! empty( $item['meta'] ) ) {
				$this->set_attribute( "meta-$index", 'class', [ 'meta' ] );

				$output .= "<span {$this->render_attributes( "meta-$index" )}>{$item['meta']}</span>";
			}

			$output .= '</div>';

			if ( ! empty( $item['description'] ) ) {
				$this->set_attribute( "description-$index", 'class', [ 'description' ] );

				$output .= "<div {$this->render_attributes( "description-$index" )}>{$item['description']}</div>";
			}

			$output .= '</li>';
		}

		$output .= '</ul>';

		echo $output;
	}
}
