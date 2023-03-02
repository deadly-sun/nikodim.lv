<?php
$search_text = isset( $settings['placeholder'] ) ? $settings['placeholder'] : esc_html__( 'Search ...', 'bricks' );
$icon        = isset( $settings['icon'] ) ? Bricks\Element::render_icon( $settings['icon'], [ 'overlay-trigger' ] ) : false;
$for         = isset( $element_id ) ? "search-input-{$element_id}" : 'search-input';
?>

<form role="search" method="get" class="bricks-search-form" action="<?php echo esc_url( home_url( '/' ) ); ?>">
	<label for="<?php echo esc_attr( $for ); ?>" class="screen-reader-text"><span><?php esc_html_e( 'Search ...', 'bricks' ); ?></span></label>
	<input type="search" placeholder="<?php esc_attr_e( $search_text ); ?>" value="<?php echo get_search_query(); ?>" name="s" id="<?php echo esc_attr( $for ); ?>" />

	<?php if ( $icon ) { ?>
	<div class="bricks-search-submit">
		<input type="submit" value="" autocomplete="off">
		<div class="bricks-search-icon"><?php echo $icon; ?></div>
	</div>
	<?php } ?>

	<button type="submit" class="screen-reader-text search-submit"><span><?php esc_html_e( 'Search', 'bricks' ); ?></span></button>
</form>
