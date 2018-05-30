<?php if( $category_fields ) { ?>
		<div id="export-category" class="export-types">

			<div class="postbox">
				<h3 class="hndle">
					<?php _e( 'Category Fields', 'woocommerce-exporter' ); ?>
					<a href="<?php echo esc_url( add_query_arg( array( 'tab' => 'fields', 'type' => 'category' ) ) ); ?>" style="float:right;"><?php _e( 'Configure', 'woocommerce-exporter' ); ?></a>
				</h3>
				<div class="inside">
					<p class="description"><?php _e( 'Select the Category fields you would like to export.', 'woocommerce-exporter' ); ?></p>
					<p>
						<a href="javascript:void(0)" id="category-checkall" class="checkall"><?php _e( 'Check All', 'woocommerce-exporter' ); ?></a> | 
						<a href="javascript:void(0)" id="category-uncheckall" class="uncheckall"><?php _e( 'Uncheck All', 'woocommerce-exporter' ); ?></a> | 
						<a href="javascript:void(0)" id="category-resetsorting" class="resetsorting"><?php _e( 'Reset Sorting', 'woocommerce-exporter' ); ?></a>
					</p>
					<table id="category-fields" class="ui-sortable striped">

	<?php foreach( $category_fields as $category_field ) { ?>
						<tr id="category-<?php echo $category_field['reset']; ?>">
							<td>
								<label<?php if( isset( $category_field['hover'] ) ) { ?> title="<?php echo $category_field['hover']; ?>"<?php } ?>>
									<input type="checkbox" name="category_fields[<?php echo $category_field['name']; ?>]" class="category_field"<?php ( isset( $category_field['default'] ) ? checked( $category_field['default'], 1 ) : '' ); ?><?php disabled( $category_field['disabled'], 1 ); ?> />
									<?php echo $category_field['label']; ?>
									<input type="hidden" name="category_fields_order[<?php echo $category_field['name']; ?>]" class="field_order" value="<?php echo $category_field['order']; ?>" />
								</label>
							</td>
						</tr>

	<?php } ?>
					</table>
					<p class="submit">
						<input type="submit" id="export_category" value="<?php _e( 'Export Categories', 'woocommerce-exporter' ); ?> " class="button-primary" />
					</p>
					<p class="description"><?php _e( 'Can\'t find a particular Category field in the above export list?', 'woocommerce-exporter' ); ?> <a href="<?php echo $troubleshooting_url; ?>" target="_blank"><?php _e( 'Get in touch', 'woocommerce-exporter' ); ?></a>.</p>
				</div>
				<!-- .inside -->
			</div>
			<!-- .postbox -->

			<div id="export-categories-filters" class="postbox">
				<h3 class="hndle"><?php _e( 'Category Filters', 'woocommerce-exporter' ); ?></h3>
				<div class="inside">

					<?php do_action( 'woo_ce_export_category_options_before_table' ); ?>

					<table class="form-table">
						<?php do_action( 'woo_ce_export_category_options_table' ); ?>
					</table>

					<?php do_action( 'woo_ce_export_category_options_after_table' ); ?>

				</div>
				<!-- .inside -->
			</div>
			<!-- #export-categories-filters -->

		</div>
		<!-- #export-category -->
<?php } ?>