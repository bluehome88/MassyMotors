<?php if( $review_fields ) { ?>
		<div id="export-review" class="export-types">

			<div class="postbox">
				<h3 class="hndle">
					<?php _e( 'Review Fields', 'woocommerce-exporter' ); ?>
				</h3>
				<div class="inside">
	<?php if( $review ) { ?>
					<p class="description"><?php _e( 'Select the Review fields you would like to export.', 'woocommerce-exporter' ); ?></p>
					<p>
						<a href="javascript:void(0)" id="review-checkall" class="checkall"><?php _e( 'Check All', 'woocommerce-exporter' ); ?></a> | 
						<a href="javascript:void(0)" id="review-uncheckall" class="uncheckall"><?php _e( 'Uncheck All', 'woocommerce-exporter' ); ?></a> | 
						<a href="javascript:void(0)" id="review-resetsorting" class="resetsorting"><?php _e( 'Reset Sorting', 'woocommerce-exporter' ); ?></a>
					</p>
					<table id="review-fields" class="ui-sortable striped">

		<?php foreach( $review_fields as $review_field ) { ?>
						<tr id="review-<?php echo $review_field['reset']; ?>">
							<td>
								<label<?php if( isset( $review_field['hover'] ) ) { ?> title="<?php echo $review_field['hover']; ?>"<?php } ?>>
									<input type="checkbox" name="review_fields[<?php echo $review_field['name']; ?>]" class="review_field"<?php ( isset( $review_field['default'] ) ? checked( $review_field['default'], 1 ) : '' ); ?> disabled="disabled" />
									<?php echo $review_field['label']; ?>
									<input type="hidden" name="review_fields_order[<?php echo $review_field['name']; ?>]" class="field_order" value="<?php echo $review_field['order']; ?>" />
								</label>
							</td>
						</tr>

		<?php } ?>
					</table>
					<p class="submit">
						<input type="button" class="button button-disabled" value="<?php _e( 'Export Reviews', 'woocommerce-exporter' ); ?>" />
					</p>
					<p class="description"><?php _e( 'Can\'t find a particular Review field in the above export list?', 'woocommerce-exporter' ); ?> <a href="<?php echo $troubleshooting_url; ?>" target="_blank"><?php _e( 'Get in touch', 'woocommerce-exporter' ); ?></a>.</p>
	<?php } else { ?>
					<p><?php _e( 'No Reviews were found.', 'woocommerce-exporter' ); ?></p>
	<?php } ?>
				</div>
				<!-- .inside -->
			</div>
			<!-- .postbox -->

			<div id="export-reviews-filters" class="postbox">
				<h3 class="hndle"><?php _e( 'Review Filters', 'woocommerce-exporter' ); ?></h3>
				<div class="inside">

					<?php do_action( 'woo_ce_export_review_options_before_table' ); ?>

					<table class="form-table">
						<?php do_action( 'woo_ce_export_review_options_table' ); ?>
					</table>

					<?php do_action( 'woo_ce_export_review_options_after_table' ); ?>

				</div>
				<!-- .inside -->
			</div>
			<!-- .postbox -->

		</div>
		<!-- #export-review -->

<?php } ?>