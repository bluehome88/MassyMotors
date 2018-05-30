<?php if( $customer_fields ) { ?>
		<div id="export-customer" class="export-types">

			<div class="postbox">
				<h3 class="hndle">
					<?php _e( 'Customer Fields', 'woocommerce-exporter' ); ?>
				</h3>
				<div class="inside">
	<?php if( $customer ) { ?>
					<p class="description"><?php _e( 'Select the Customer fields you would like to export.', 'woocommerce-exporter' ); ?></p>
					<p>
						<a href="javascript:void(0)" id="customer-checkall" class="checkall"><?php _e( 'Check All', 'woocommerce-exporter' ); ?></a> | 
						<a href="javascript:void(0)" id="customer-uncheckall" class="uncheckall"><?php _e( 'Uncheck All', 'woocommerce-exporter' ); ?></a> | 
						<a href="javascript:void(0)" id="customer-resetsorting" class="resetsorting"><?php _e( 'Reset Sorting', 'woocommerce-exporter' ); ?></a>
					</p>
					<table id="customer-fields" class="ui-sortable striped">

		<?php foreach( $customer_fields as $customer_field ) { ?>
						<tr id="customer-<?php echo $customer_field['reset']; ?>">
							<td>
								<label<?php if( isset( $customer_field['hover'] ) ) { ?> title="<?php echo $customer_field['hover']; ?>"<?php } ?>>
									<input type="checkbox" name="customer_fields[<?php echo $customer_field['name']; ?>]" class="customer_field"<?php ( isset( $customer_field['default'] ) ? checked( $customer_field['default'], 1 ) : '' ); ?> disabled="disabled" />
									<?php echo $customer_field['label']; ?>
									<input type="hidden" name="customer_fields_order[<?php echo $customer_field['name']; ?>]" class="field_order" value="<?php echo $customer_field['order']; ?>" />
								</label>
							</td>
						</tr>

		<?php } ?>
					</table>
					<p class="submit">
						<input type="button" class="button button-disabled" value="<?php _e( 'Export Customers', 'woocommerce-exporter' ); ?>" />
					</p>
					<p class="description"><?php _e( 'Can\'t find a particular Customer field in the above export list?', 'woocommerce-exporter' ); ?> <a href="<?php echo $troubleshooting_url; ?>" target="_blank"><?php _e( 'Get in touch', 'woocommerce-exporter' ); ?></a>.</p>
	<?php } else { ?>
					<p><?php _e( 'No Customers were found.', 'woocommerce-exporter' ); ?></p>
	<?php } ?>
				</div>
				<!-- .inside -->
			</div>
			<!-- .postbox -->

			<div id="export-customers-filters" class="postbox">
				<h3 class="hndle"><?php _e( 'Customer Filters', 'woocommerce-exporter' ); ?></h3>
				<div class="inside">

					<?php do_action( 'woo_ce_export_customer_options_before_table' ); ?>

					<table class="form-table">
						<?php do_action( 'woo_ce_export_customer_options_table' ); ?>
					</table>

					<?php do_action( 'woo_ce_export_customer_options_after_table' ); ?>

				</div>
				<!-- .inside -->
			</div>
			<!-- .postbox -->

		</div>
		<!-- #export-customer -->

<?php } ?>