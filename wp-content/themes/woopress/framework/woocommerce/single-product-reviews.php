<?php
/**
 * Display single product reviews (comments)
 *
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version     3.2.0
 */
global $product;

if ( ! defined( 'ABSPATH' ) )
	exit; // Exit if accessed directly

if ( ! comments_open() )
	return;
?>
<div id="reviews">
	<div id="comments">
		<h2><?php
			if ( get_option( 'woocommerce_enable_review_rating' ) === 'yes' && ( $count = $product->get_rating_count() ) )
				printf( _n( '%s review for %s', '%s reviews for %s', $count, 'woopress' ), $count, get_the_title() );
			else
				esc_html_e( 'Reviews', 'woopress' );
		?></h2>

		<?php if ( have_comments() ) : ?>

			<ul class="comments-list">
				<?php wp_list_comments( apply_filters( 'woocommerce_product_review_list_args', array( 'callback' => 'etheme_comments' ) ) ); ?>
			</ul>

			<?php if ( get_comment_pages_count() > 1 && get_option( 'page_comments' ) ) :
				echo '<nav class="pagination-cubic">';
				paginate_comments_links( apply_filters( 'woocommerce_comment_pagination_args', array(
					'prev_text' => '<i class="fa fa-angle-double-left"></i>',
					'next_text' => '<i class="fa fa-angle-double-right"></i>',
					'type'      => 'list',
				) ) );
				echo '</nav>';
			endif; ?>

		<?php else : ?>

			<p class="woocommerce-noreviews"><?php esc_html_e( 'There are no reviews yet.', 'woopress' ); ?></p>

		<?php endif; ?>
	</div>

	<?php if ( get_option( 'woocommerce_review_rating_verification_required' ) === 'no' || wc_customer_bought_product( '', get_current_user_id(), $product->get_ID() ) ) : ?>

		<div id="review_form_wrapper">
			<div id="review_form">
				<?php
					$commenter = wp_get_current_commenter();
					
			        $req = get_option('require_name_email');
			        $reqT = '<span class="required">*</span>';
		        	$aria_req = ($req ? " aria-required='true'" : ' ');

					$comment_form = array(
						'title_reply'          => '<span>' . esc_html__('Leave a reply', 'woopress') . '</span></span><span class="divider"></span>',
						'title_reply_to'       => '<span>' . esc_html__( 'Leave a Reply to %s', 'woopress' ). '</span></span><span class="divider"></span>',
						'comment_notes_before' => '',
						'comment_notes_after'  => '',
						'fields'               => array(
				            'author' => '<div class="form-group comment-form-author">'.
				                            '<label for="author" class="control-label">'.__('Name', 'woopress').' '.($req ? $reqT : '').'</label>'.
				                            '<input id="author" name="author" type="text" class="form-control ' . ($req ? ' required-field' : '') . '" value="' . esc_attr($commenter['comment_author']) . '" size="30" ' . $aria_req . '>'.
				                        '</div>',
				            'email' => '<div class="form-group comment-form-email">'.
				                            '<label for="email" class="control-label">'.__('Email', 'woopress').' '.($req ? $reqT : '').'</label>'.
				                            '<input id="email" name="email" type="text" class="form-control ' . ($req ? ' required-field' : '') . '" value="' . esc_attr($commenter['comment_author_email']) . '" size="30" ' . $aria_req . '>'.
				                        '</div>',
						),
						'label_submit'  => esc_html__( 'Submit', 'woopress' ),
						'logged_in_as'  => '',
						'comment_field' => ''
					);

					if ( get_option( 'woocommerce_enable_review_rating' ) === 'yes' ) {
						$comment_form['comment_field'] = '<p class="comment-form-rating"><label for="rating">' . esc_html__( 'Your Rating', 'woopress' ) .'</label><select name="rating" id="rating">
							<option value="">' . esc_html__( 'Rate&hellip;', 'woopress' ) . '</option>
							<option value="5">' . esc_html__( 'Perfect', 'woopress' ) . '</option>
							<option value="4">' . esc_html__( 'Good', 'woopress' ) . '</option>
							<option value="3">' . esc_html__( 'Average', 'woopress' ) . '</option>
							<option value="2">' . esc_html__( 'Not that bad', 'woopress' ) . '</option>
							<option value="1">' . esc_html__( 'Very Poor', 'woopress' ) . '</option>
						</select></p>';
					}

					$comment_form['comment_field'] .= '<div class="form-group"><label for="comment" class="control-label">'.__('Your Review', 'woopress').'</label><textarea class="form-control required-field"  id="comment" name="comment" cols="45" rows="12" aria-required="true"></textarea></div>';

					comment_form( apply_filters( 'woocommerce_product_review_comment_form_args', $comment_form ) );
				?>
			</div>
		</div>

	<?php else : ?>

		<p class="woocommerce-verification-required"><?php esc_html_e( 'Only logged in customers who have purchased this product may leave a review.', 'woopress' ); ?></p>

	<?php endif; ?>

	<div class="clear"></div>
</div>