<?php
/**
 * Shortcode attributes
 * @var $atts
 * @var $type
 * @var $annotation // TODO: check why annotation doesn't set before
 * @var $css
 * Shortcode class
 * @var $this WPBakeryShortCode_VC_Pinterest
 */
$type = $annotation = $css = '';
global $post;
$atts = vc_map_get_attributes( $this->getShortcode(), $atts );
extract( $atts );

$url = rawurlencode( get_permalink() );
if ( has_post_thumbnail() ) {
	$img_url = wp_get_attachment_image_src( get_post_thumbnail_id(), 'large' );
	$media = ( is_array( $img_url ) ) ? '&amp;media=' . rawurlencode( $img_url[0] ) : '';
} else {
	$media = '';
}
$excerpt = is_object( $post ) && isset( $post->post_excerpt ) ? $post->post_excerpt : '';
$description = ( '' !== $excerpt ) ? '&amp;description=' . rawurlencode( strip_tags( $excerpt ) ) : '';

$el_class = isset( $el_class ) ? $el_class : '';
$class_to_filter = 'wpb_pinterest wpb_content_element wpb_pinterest_type_' . $type;
$class_to_filter .= vc_shortcode_custom_css_class( $css, ' ' ) . $this->getExtraClass( $el_class );
$css_class = apply_filters( VC_SHORTCODE_CUSTOM_CSS_FILTER_TAG, $class_to_filter, $this->settings['base'], $atts );

$output .= '<div class="' . esc_attr( $css_class ) . '">';
$output .= '<a href="http://pinterest.com/pin/create/button/?url=' . $url . $media . $description . '" class="pin-it-button" count-layout="' . $type . '"><img border="0" src="//assets.pinterest.com/images/PinExt.png" title="Pin It" /></a>';
$output .= '</div>';

echo $output;