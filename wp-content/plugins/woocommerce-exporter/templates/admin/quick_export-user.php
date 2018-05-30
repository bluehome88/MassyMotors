<?php if( $user_fields ) { ?>
		<div id="export-user" class="export-types">

			<div class="postbox">
				<h3 class="hndle">
					<?php _e( 'User Fields', 'woocommerce-exporter' ); ?>
					<a href="<?php echo esc_url( add_query_arg( array( 'tab' => 'fields', 'type' => 'user' ) ) ); ?>" style="float:right;"><?php _e( 'Configure', 'woocommerce-exporter' ); ?></a>
				</h3>
				<div class="inside">
	<?php if( $user ) { ?>
					<p class="description"><?php _e( 'Select the User fields you would like to export.', 'woocommerce-exporter' ); ?></p>
					<p>
						<a href="javascript:void(0)" id="user-checkall" class="checkall"><?php _e( 'Check All', 'woocommerce-exporter' ); ?></a> | 
						<a href="javascript:void(0)" id="user-uncheckall" class="uncheckall"><?php _e( 'Uncheck All', 'woocommerce-exporter' ); ?></a> | 
						<a href="javascript:void(0)" id="user-resetsorting" class="resetsorting"><?php _e( 'Reset Sorting', 'woocommerce-exporter' ); ?></a>
					</p>
					<table id="user-fields" class="ui-sortable striped">

		<?php foreach( $user_fields as $user_field ) { ?>
						<tr id="user-<?php echo $user_field['reset']; ?>">
							<td>
								<label<?php if( isset( $user_field['hover'] ) ) { ?> title="<?php echo $user_field['hover']; ?>"<?php } ?>>
									<input type="checkbox" name="user_fields[<?php echo $user_field['name']; ?>]" class="user_field"<?php ( isset( $user_field['default'] ) ? checked( $user_field['default'], 1 ) : '' ); ?><?php disabled( $user_field['disabled'], 1 ); ?> />
									<?php echo $user_field['label']; ?>
									<?php if( $user_field['disabled'] ) { ?><span class="description"> - <?php printf( __( 'available in %s', 'woocommerce-exporter' ), $woo_cd_link ); ?></span><?php } ?>
									<input type="hidden" name="user_fields_order[<?php echo $user_field['name']; ?>]" class="field_order" value="<?php echo $user_field['order']; ?>" />
								</label>
							</td>
						</tr>

		<?php } ?>
					</table>
					<p class="submit">
						<input type="submit" id="export_user" class="button-primary" value="<?php _e( 'Export Users', 'woocommerce-exporter' ); ?>" />
					</p>
					<p class="description"><?php _e( 'Can\'t find a particular User field in the above export list?', 'woocommerce-exporter' ); ?> <a href="<?php echo $troubleshooting_url; ?>" target="_blank"><?php _e( 'Get in touch', 'woocommerce-exporter' ); ?></a>.</p>
	<?php } else { ?>
					<p><?php _e( 'No Users were found.', 'woocommerce-exporter' ); ?></p>
	<?php } ?>
				</div>
				<!-- .inside -->
			</div>
			<!-- .postbox -->

			<div id="export-users-filters" class="postbox">
				<h3 class="hndle"><?php _e( 'User Filters', 'woocommerce-exporter' ); ?></h3>
				<div class="inside">

					<?php do_action( 'woo_ce_export_user_options_before_table' ); ?>

					<table class="form-table">
						<?php do_action( 'woo_ce_export_user_options_table' ); ?>
					</table>

					<?php do_action( 'woo_ce_export_user_options_after_table' ); ?>

				</div>
				<!-- .inside -->
			</div>
			<!-- .postbox -->

		</div>
		<!-- #export-user -->

<?php } ?>