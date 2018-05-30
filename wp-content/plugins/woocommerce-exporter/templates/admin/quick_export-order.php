<?php if( $order_fields ) { ?>
		<div id="export-order" class="export-types">

			<div class="postbox">
				<h3 class="hndle">
					<?php _e( 'Order Fields', 'woocommerce-exporter' ); ?>
				</h3>
				<div class="inside">

	<?php if( $order ) { ?>
					<p class="description"><?php _e( 'Select the Order fields you would like to export.', 'woocommerce-exporter' ); ?></p>
					<p>
						<a href="javascript:void(0)" id="order-checkall" class="checkall"><?php _e( 'Check All', 'woocommerce-exporter' ); ?></a> | 
						<a href="javascript:void(0)" id="order-uncheckall" class="uncheckall"><?php _e( 'Uncheck All', 'woocommerce-exporter' ); ?></a> |
						<a href="javascript:void(0)" id="order-resetsorting" class="resetsorting"><?php _e( 'Reset Sorting', 'woocommerce-exporter' ); ?></a>
					</p>
					<table id="order-fields" class="ui-sortable striped">

		<?php foreach( $order_fields as $order_field ) { ?>
						<tr id="order-<?php echo $order_field['reset']; ?>">
							<td>
								<label<?php if( isset( $order_field['hover'] ) ) { ?> title="<?php echo $order_field['hover']; ?>"<?php } ?>>
									<input type="checkbox" name="order_fields[<?php echo $order_field['name']; ?>]" class="order_field"<?php ( isset( $order_field['default'] ) ? checked( $order_field['default'], 1 ) : '' ); ?> disabled="disabled" />
									<?php echo $order_field['label']; ?>
									<input type="hidden" name="order_fields_order[<?php echo $order_field['name']; ?>]" class="field_order" value="<?php echo $order_field['order']; ?>" />
								</label>
							</td>
						</tr>

		<?php } ?>
					</table>
					<p class="submit">
						<input type="button" class="button button-disabled" value="<?php _e( 'Export Orders', 'woocommerce-exporter' ); ?>" />
					</p>
					<p class="description"><?php _e( 'Can\'t find a particular Order field in the above export list?', 'woocommerce-exporter' ); ?> <a href="<?php echo $troubleshooting_url; ?>" target="_blank"><?php _e( 'Get in touch', 'woocommerce-exporter' ); ?></a>.</p>
	<?php } else { ?>
					<p><?php _e( 'No Orders were found.', 'woocommerce-exporter' ); ?></p>
	<?php } ?>

				</div>
			</div>
			<!-- .postbox -->

			<div id="export-orders-filters" class="postbox">
				<h3 class="hndle"><?php _e( 'Order Filters', 'woocommerce-exporter' ); ?></h3>
				<div class="inside">

					<?php do_action( 'woo_ce_export_order_options_before_table' ); ?>

					<table class="form-table">
						<?php do_action( 'woo_ce_export_order_options_table' ); ?>
					</table>

					<?php do_action( 'woo_ce_export_order_options_after_table' ); ?>

				</div>
				<!-- .inside -->
			</div>
			<!-- .postbox -->

		</div>
		<!-- #export-order -->

<?php } ?>