<?php if( $product_vendor_fields ) { ?>
		<div id="export-product_vendor" class="export-types">

			<div class="postbox">
				<h3 class="hndle">
					<?php _e( 'Product Vendor Fields', 'woocommerce-exporter' ); ?>
				</h3>
				<div class="inside">
	<?php if( $product_vendor ) { ?>
					<p class="description"><?php _e( 'Select the Product Vendor fields you would like to export.', 'woocommerce-exporter' ); ?></p>
					<p>
						<a href="javascript:void(0)" id="product_vendor-checkall" class="checkall"><?php _e( 'Check All', 'woocommerce-exporter' ); ?></a> | 
						<a href="javascript:void(0)" id="product_vendor-uncheckall" class="uncheckall"><?php _e( 'Uncheck All', 'woocommerce-exporter' ); ?></a> | 
						<a href="javascript:void(0)" id="product_vendor-resetsorting" class="resetsorting"><?php _e( 'Reset Sorting', 'woocommerce-exporter' ); ?></a>
					</p>
					<table id="product_vendor-fields" class="ui-sortable striped">

		<?php foreach( $product_vendor_fields as $product_vendor_field ) { ?>
						<tr id="product_vendor-<?php echo $product_vendor_field['reset']; ?>">
							<td>
								<label<?php if( isset( $product_vendor_field['hover'] ) ) { ?> title="<?php echo $product_vendor_field['hover']; ?>"<?php } ?>>
									<input type="checkbox" name="product_vendor_fields[<?php echo $product_vendor_field['name']; ?>]" class="product_vendor_field"<?php ( isset( $product_vendor_field['default'] ) ? checked( $product_vendor_field['default'], 1 ) : '' ); ?> disabled="disabled" />
									<?php echo $product_vendor_field['label']; ?>
									<input type="hidden" name="product_vendor_fields_order[<?php echo $product_vendor_field['name']; ?>]" class="field_order" value="<?php echo $product_vendor_field['order']; ?>" />
								</label>
							</td>
						</tr>

		<?php } ?>
					</table>
					<p class="submit">
						<input type="button" class="button button-disabled" value="<?php _e( 'Export Product Vendors', 'woocommerce-exporter' ); ?>" />
					</p>
					<p class="description"><?php _e( 'Can\'t find a particular Product Vendor field in the above export list?', 'woocommerce-exporter' ); ?> <a href="<?php echo $troubleshooting_url; ?>" target="_blank"><?php _e( 'Get in touch', 'woocommerce-exporter' ); ?></a>.</p>
	<?php } else { ?>
					<p><?php _e( 'No Product Vendors were found.', 'woocommerce-exporter' ); ?></p>
	<?php } ?>
				</div>
				<!-- .inside -->
			</div>
			<!-- .postbox -->

		</div>
		<!-- #export-product_vendor -->

<?php } ?>