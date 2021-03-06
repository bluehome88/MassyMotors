<?php if( $shipping_class_fields ) { ?>
		<div id="export-shipping_class" class="export-types">

			<div class="postbox">
				<h3 class="hndle">
					<?php _e( 'Shipping Class Fields', 'woocommerce-exporter' ); ?>
				</h3>
				<div class="inside">
	<?php if( $shipping_class ) { ?>
					<p class="description"><?php _e( 'Select the Shipping Class fields you would like to export.', 'woocommerce-exporter' ); ?></p>
					<p>
						<a href="javascript:void(0)" id="shipping_class-checkall" class="checkall"><?php _e( 'Check All', 'woocommerce-exporter' ); ?></a> | 
						<a href="javascript:void(0)" id="shipping_class-uncheckall" class="uncheckall"><?php _e( 'Uncheck All', 'woocommerce-exporter' ); ?></a> | 
						<a href="javascript:void(0)" id="shipping_class-resetsorting" class="resetsorting"><?php _e( 'Reset Sorting', 'woocommerce-exporter' ); ?></a>
					</p>
					<table id="shipping_class-fields" class="ui-sortable striped">

		<?php foreach( $shipping_class_fields as $shipping_class_field ) { ?>
						<tr id="shipping_class-<?php echo $shipping_class_field['reset']; ?>">
							<td>
								<label<?php if( isset( $shipping_class_field['hover'] ) ) { ?> title="<?php echo $shipping_class_field['hover']; ?>"<?php } ?>>
									<input type="checkbox" name="shipping_class_fields[<?php echo $shipping_class_field['name']; ?>]" class="shipping_class_field"<?php ( isset( $shipping_class_field['default'] ) ? checked( $shipping_class_field['default'], 1 ) : '' ); ?> disabled="disabled" />
									<?php echo $shipping_class_field['label']; ?>
									<input type="hidden" name="shipping_class_fields_order[<?php echo $shipping_class_field['name']; ?>]" class="field_order" value="<?php echo $shipping_class_field['order']; ?>" />
								</label>
							</td>
						</tr>

		<?php } ?>
					</table>
					<p class="submit">
						<input type="button" class="button button-disabled" value="<?php _e( 'Export Shipping Classes', 'woocommerce-exporter' ); ?>" />
					</p>
					<p class="description"><?php _e( 'Can\'t find a particular Shipping Class field in the above export list?', 'woocommerce-exporter' ); ?> <a href="<?php echo $troubleshooting_url; ?>" target="_blank"><?php _e( 'Get in touch', 'woocommerce-exporter' ); ?></a>.</p>
	<?php } else { ?>
					<p><?php _e( 'No Shipping Classes were found.', 'woocommerce-exporter' ); ?></p>
	<?php } ?>
				</div>
				<!-- .inside -->
			</div>
			<!-- .postbox -->

			<div id="export-shipping-classes-filters" class="postbox">
				<h3 class="hndle"><?php _e( 'Shipping Class Filters', 'woocommerce-exporter' ); ?></h3>
				<div class="inside">

					<?php do_action( 'woo_ce_export_shipping_class_options_before_table' ); ?>

					<table class="form-table">
						<?php do_action( 'woo_ce_export_shipping_class_options_table' ); ?>
					</table>

					<?php do_action( 'woo_ce_export_shipping_class_options_after_table' ); ?>

				</div>
				<!-- .inside -->
			</div>
			<!-- .postbox -->

		</div>
		<!-- #export-shipping_class -->

<?php } ?>