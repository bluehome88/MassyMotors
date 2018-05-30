<?php
$ultimate_keys = get_option('ultimate_keys');
$ultimate_user_email_option = get_option('ultimate_user_email');
$ultimate_skip = get_option('ultimate_skip');
//delete_transient( 'ultimate_license_activation' );die();
$envato_username = isset($ultimate_keys['envato_username']) ? $ultimate_keys['envato_username'] : '';
$envato_api_key = isset($ultimate_keys['envato_api_key']) ? $ultimate_keys['envato_api_key'] : '';
$purchase_code = isset($ultimate_keys['ultimate_purchase_code']) ? $ultimate_keys['ultimate_purchase_code'] : '';
$ultimate_user_mail = (isset($ultimate_keys['ultimate_user_email'])) ? $ultimate_keys['ultimate_user_email'] : '';
$process = $msg = $response = $code = $status = $disable = $verify_label = $activate_lable = '';
$button = __('Activate','ultimate_vc');

//if($purchase_code !== ''){
	//delete_transient( 'ultimate_license_activation' ); delete_option('ultimate_license_activation'); die();
	$activation_check = get_option('ultimate_license_activation');
	
	$status = $response = $code = $signup_form_html = $dev_act_btn_txt = '';
	
	if(false === ( get_transient( 'ultimate_license_activation' ) )){
		if(!empty($activation_check)){
			$get_activation_data = check_license_activation($purchase_code, $envato_username, $ultimate_user_mail);
			$activation_check_temp = json_decode($get_activation_data);
			$val = array(
				'response' => $activation_check_temp->response,
				'status' => $activation_check_temp->status,
				'code' => $activation_check_temp->code
			);
			update_option('ultimate_license_activation', $val);
			delete_transient( 'ultimate_license_activation' );
			set_transient( "ultimate_license_activation", true, 60*60*12);
		}
	}
	
	$activation_check = get_option('ultimate_license_activation');
	
	if(!empty($activation_check)) :
		$status = $activation_check['status'];
		$response = $activation_check['response'];
		$code = $activation_check['code'];
	endif;
	
	if($status == "Activated" && $code == 200){
		$disable = 'disabled="disabled" style=" cursor: no-drop;" title="'.__('Please deactivate the license on this site to change the credentials.','ultimate_vc').'"';
		$activate_lable = '<span class="activation" style="top: -41px; position: absolute; right: 20px; cursor: context-menu;"> '.__('Activated','ultimate_vc').' </span>';
	}
	if($status == 'Activated' ){
		$process = 'deactivate';
		$button = __('Deregister','ultimate_vc');
		$activate_lable = '<span class="activation" style="top: -41px; position: absolute; right: 20px;  padding: 1px 5px; background: green;  color: #fff; border-radius: 2px; cursor: context-menu;">'.__('Activated','ultimate_vc').'</span>';
	} elseif($status == 'Deactivated') {
		$process = "reactivate";
		$button = __('Register','ultimate_vc');
		$activate_lable = '<span class="activation" style="top: -41px; position: absolute; right: 20px;  padding: 1px 5px; background: red;  color: #fff; border-radius: 2px; cursor: context-menu;">'.__('Not Activated','ultimate_vc').'</span>';
	}  else {
		$process = "activate";
		$button = __('Register','ultimate_vc');
		$activate_lable = '<span class="activation" style="top: -41px; position: absolute; right: 20px;  padding: 1px 5px; background: red;  color: #fff; border-radius: 2px; cursor: context-menu;">'.__('Not Activated','ultimate_vc').'</span>';
	}
	//echo $response;
	//if($response == '<div class="error"><p>License is already activated on the site - <a href="http://test-plugin.sharkslab.com">http://test-plugin.sharkslab.com</a></p></div>');
	
//}
?>
    
    <?php if($status == 'Activated') : ?>
    
    	<?php 
        	$signup_form_html = '<form method="post" id="ultimate_updater"><input type="hidden" name="action" value="update_ultimate_keys" /><input type="hidden" id="ustep" name="step" value="register_customer"/><input type="hidden" id="envato_username" value="'.$envato_username.'" name="envato_username"/><input type="hidden" id="envato_api_key" value="'.$envato_api_key.'" name="envato_api_key"/><input type="hidden" id="ultimate_purchase_code" value="'.$purchase_code.'" name="ultimate_purchase_code" /><table class="form-table"><tr valign="top"><th scope="row">'.__("Email","ultimate_vc").'</th><td> <input type="text" id="ultimate_user_email" value="'.$ultimate_user_email_option.'" name="ultimate_user_email" style="width: 85%;" /><span class="masterUltTooltip dashicons dashicons-editor-help" title="'.__("Enter your email address","ultimate_vc").'"></span></td></tr><tr valign="top"><th scope="row">'.__("Confirm Email","ultimate_vc").'</th><td> <input type="text" id="ultimate_user_email_confirm" value="" name="ultimate_user_email_confirm" style="width: 85%;"/><span class="masterUltTooltip dashicons dashicons-editor-help" title="'.__("Confirm your email address","ultimate_vc").'"></span></td></tr><tr valign="top"><th scope="row"></th><td colspan="2"> <input type="checkbox" id="ultimate_user_receive" value="receive" name="ultimate_user_receive" checked="checked"/><label style="font-size: 13px;color: #666;" for="ultimate_user_receive">'.__("Receive important news &amp; updates on email.","ultimate_vc").'</label></td></tr></table></form>';
        ?>
    
    	<div class="inside">
        	<div class="main">
            	<?php if($ultimate_skip == 'false' || $ultimate_skip == '') : ?>

                <div class="clear"></div>
                	
                    <?php echo $signup_form_html; ?>
                    
                    <input type="hidden" id="ultimate_next_step" name="ultimate_next_step" value="3"/>
                    
                    <table class="form-table">
                        <tr>
                            <td width="25%"></td>
                            <td width="35%"><input type="button" id="activate" class="button button-primary updater-act-btn updater-act-btn-align half-full-ubtn" style="margin-top: 20px; margin-right: 10px;" value="<?php echo __('Sign Up For Support','ultimate_vc'); ?>" data-process="reactivate"></td>
                            <td width="35%"><input type="button" id="skip" class="button updater-act-btn half-full-ubtn with-ult-spinner" style="margin-top: 20px; " value="<?php echo __('I\'ve already registered','ultimate_vc'); ?>"/></td>
                   			<td width="20px" style="padding:0; vertical-align:top;"><span class="spinner" style="float: none;top: 25px;position: relative;"></span></td>
                        </tr>
                    </table>
                	
                    
                    
                <?php else : ?>
					<div class="main after-regi-text">
						<p><?php echo __('We take pride in providing top notch support through our dedicated help center. But please be sure to check the documentation, video tutorials, FAQs & basic troubleshoot guidelines to ensure which will in most of the cases, help you resolve your issue quickly.','ultimate_vc'); ?> </p>
						<p><?php echo __('If you still need help - allow us a temporary secure access.','ultimate_vc').' <span class="masterUltTooltip dashicons dashicons-editor-help" title="'.__('How does this work?', 'ultimate_vc').'<br/><br/>
'.__('When you grant us an access to your website by clicking the button below.','ultimate_vc').',<br/>
'.__('a unique & very secure access token will be generated for us.','ultimate_vc').'<br/> '.__('We receive this token on our secure APIs.','ultimate_vc').'<br/><br/>
'.__('This is in no way related to your password or any confidential login information','ultimate_vc').' <br/>
'.__('and you may at any time revoke this access which invalidates the token and it will','ultimate_vc').' <br/>
'.__('no longer be usable.'); ?>"></span> <?php echo __('to your website & write us about your issue.','ultimate_vc').'</p>'; ?>
					</div>
                    
                    <?php 
						$current_dev_access = get_option('developer_access');
						if(!$current_dev_access)
						{
							$dev_act_btn_txt = __('Grant Temporary Access to Developers','ultimate_vc'); 
							$button_id = 'developer-access';
						}
						else
						{
							$dev_act_btn_txt = __('Revoke Access','ultimate_vc'); 
							$button_id = 'developer-access-revoke';
						}
					?>
                    
                    <div class="after-regi-btn">
                    
                        <span class="access-buttons">
                            <button class="button updater-act-btn btn-dev-access" id="<?php echo $button_id ?>">
                            	<span class="dashicons dashicons-admin-network"></span>
                                <?php echo $dev_act_btn_txt; ?>
                            </button>
                        </span>
                        
                        <form method="post" id="ultimate_updater">
                            <input type="hidden" name="action" value="update_ultimate_keys" />
                            <input type="hidden" id="envato_username" value="<?php echo $envato_username; ?>" name="envato_username"/>
                            <input type="hidden" id="envato_api_key" value="<?php echo $envato_api_key; ?>" name="envato_api_key"/>
                            <input type="hidden" id="ultimate_purchase_code" value="<?php echo $purchase_code; ?>" name="ultimate_purchase_code" />
                            
                            <?php if($ultimate_user_email_option != '') : ?>
                            	<input type="hidden" id="ultimate_user_email" value="<?php echo $ultimate_user_mail ; ?>" name="ultimate_user_email" />
                            <?php endif; ?>
                        </form>
                        
                        <?php if($ultimate_user_mail != '' || $ultimate_user_email_option != ''): ?>
                            <a href="https://www.brainstormforce.com/support?access_id=<?php echo base64_encode($envato_username) ?>&access_token=<?php echo get_option('bsf-support-profile-access-token'); ?>&site=<?php echo urlencode(site_url()); ?>&wp_nonce=<?php echo wp_nonce_url(urlencode(site_url())); ?>" class="button button-primary updater-act-btn" target="_blank">
                            	<span class="dashicons dashicons-sos"></span>
                                <?php echo __('Visit our Support Center','ultimate_vc'); ?>
                            </a>
                        <?php else : ?>
                            <input type="hidden" id="ultimate_next_step" name="ultimate_next_step" value="3"/>
                            <a href="javascript:void(0)" id="after-signup" class="button button-primary updater-act-btn no-email" data-process="reactivate" onclick="signup_for_customer_portal();" target="_blank">
                            	<span class="dashicons dashicons-external"></span>
                                <?php echo __('Sign Up for Customer Profile','ultimate_vc'); ?>
                            </a>
                        <?php endif; ?>

                        <span id="grant-dev-spinner" class="spinner"></span>
                	</div>
           		<?php endif; ?>
              
                <div class="clear"></div>
            </div>
     	</div>
    <?php else : ?>
    <?php //echo $response; ?>
    <div class="inside">
        <div class="main">

        <div class="clear"></div>

        <form method="post" id="ultimate_updater">
        	<?php $readonly = ($status == 'Activated') ? 'readonly' : ''; ?>
            <input type="hidden" name="action" value="update_ultimate_keys" />
            <input type="hidden" id="ustep" name="step" value="register_plugin"/>
            <input type="hidden" id="ultimate_next_step" name="ultimate_next_step" value="2"/>
            <table class="form-table">
                <tbody>
                    <tr valign="top">
                        <th scope="row"><?php echo __("Envato Username","ultimate_vc"); ?></th>
                        <td> <input type="text" id="envato_username" value="<?php echo $envato_username; ?>" name="envato_username" style="width: 85%;" <?php echo $readonly ?>/><span class="masterUltTooltip dashicons dashicons-editor-help" title="<?php echo __("Enter your envato username","ultimate_vc"); ?>"></span>
                        </td>
                    </tr>
                    <tr valign="top">
                        <th scope="row"><?php echo __("API Key","ultimate_vc"); ?></th>
                        <td> <input type="text" id="envato_api_key" value="<?php echo $envato_api_key; ?>" name="envato_api_key" style="width: 85%;" <?php echo $readonly ?>/><span class="masterUltTooltip dashicons dashicons-editor-help" title="<?php echo __("Enter your envato API Key","ultimate_vc"); ?>"></span>
                        </td>
                    </tr>
                    <tr valign="top">
                        <th scope="row"><?php echo __("Purchase Code","ultimate_vc"); ?></th>
                        <td> <input type="text" id="ultimate_purchase_code" value="<?php echo $purchase_code; ?>" name="ultimate_purchase_code" style="width: 85%;" <?php echo $readonly ?>/><span class="masterUltTooltip dashicons dashicons-editor-help" title="<?php echo __("Enter your purchase key of Ultimate Addons for Visual Composer","ultimate_vc"); ?>"></span>
                        </td>
                    </tr>
                </tbody>
            </table>
        </form>
        
        </div>
	</div>
    <div class="hndle" style="padding:0 10px 10px 10px; border-top:1px solid #eee;">
       	<input type="submit" id="activate" class="button button-primary updater-act-btn updater-act-btn-align" value="<?php echo $button;?>" data-process="<?php echo $process; ?>">
        <span class="spinner updater-act-btn-spinner" style="float: none;"></span>
    </div>
    
	<!-- <?php //echo $response; ?> -->
	<?php endif; ?>
    <style>
    .small-txt-error {
		font-size: 11px;
	}
    </style>
<script type="text/javascript">
var activate = jQuery("#activate");
var confirm_deactivate = jQuery('#confirm-deactivate');
var loader = jQuery(".spinner");
//function process_activation(){
function hide_bsf_popup()
{
	jQuery(".bsf-overlay").fadeOut(300);
}
confirm_deactivate.click(function(){
	var confirm_html = '<div><h2><?php echo __('Are you sure?','ultimate_vc'); ?></h2><p><?php echo __('This means you can not receive updates with new features and support on this website.','ultimate_vc'); ?></p><p>&nbsp;</p><a href="javascript:void(0)" id="deactivate-plugin" class="button button-primary" onclick="init_activate_process(\'#confirm-deactivate\');"><?php echo __('Yes','ultimate_vc'); ?></a>&nbsp;&nbsp;<a href="javascript:void(0)" id="deactivate-plugin-cancel" class="button" onclick="hide_bsf_popup(); return false;"><?php echo __('Cancel','ultimate_vc'); ?></a><span id="popup-spinner" class="spinner" style="float: none;"></span></div>';
	jQuery("#bsf-overlay-inner-msg").html(confirm_html);
	jQuery(".bsf-overlay").fadeIn(300);
});
	
jQuery('body').on('click','#activate', function(e){
	init_activate_process('#activate');
});
function signup_for_customer_portal()
{
	var signup_html = '<div><h2><?php echo __('Signup Form','ultimate_vc'); ?></h2><?php echo $signup_form_html ?><input type="button" id="activate" class="button button-primary updater-act-btn updater-act-btn-align" style="margin-top: 15px;" value="<?php echo __('Sign Up','ultimate_vc'); ?>" data-process="reactivate"><span class="spinner" style="float: none; margin-top:20px; margin-left:10px"></span></div>';
	jQuery("#bsf-overlay-inner-msg").html(signup_html);
	jQuery(".bsf-overlay").fadeIn(300);
}
function init_activate_process(element)
{
	var $element = jQuery(element);
	//e.preventDefault();
	var url = "aHR0cHM6Ly93d3cuYnJhaW5zdG9ybWZvcmNlLmNvbS9zdXBwb3J0L3dwLWFkbWluL2FkbWluLWFqYXgucGhw";
	var user = jQuery("#envato_username").val();
	var purchase_key = jQuery("#ultimate_purchase_code").val();
	var process = $element.data('process');
	var email = jQuery('#ultimate_user_email').val();
	var step = jQuery('#ustep').val();
	var envato_api_key = jQuery('#envato_api_key').val();
	var next_step = jQuery('#ultimate_next_step').val();
	
	jQuery('.small-txt-error').remove();
	
	var receive = 'false';
	
	if(step == 'register_customer')
	{
		var confirm_email = jQuery('#ultimate_user_email_confirm').val();
		
		if(jQuery('#ultimate_user_receive').is(':checked'))
			var receive = 'true';
		
		if(email == '')
		{
			jQuery('#ultimate_user_email').css({'border':'1px solid red'});
			return false;
		}
		else
		{
			jQuery('#ultimate_user_email').css({'border':'1px solid #ddd'});
		}
		
		if(email != confirm_email)
		{
			jQuery('#ultimate_user_email_confirm').css({'border':'1px solid red'});
			return false;
		}
		else
		{
			jQuery('#ultimate_user_email_confirm').css({'border':'1px solid #ddd'});
		}
		$element.next('.spinner').css({'display':'inline-block'});
	}
	else if(step == 'register_plugin')
	{
		var errors = 0;
		if(user == '')
		{
			jQuery('#envato_username').css({'border':'1px solid red'});
			errors++;
		}
		else
			jQuery('#envato_username').css({'border':'1px solid #ddd'});
			
		if(purchase_key == '')
		{
			jQuery('#ultimate_purchase_code').css({'border':'1px solid red'});
			errors++;
		}
		else
			jQuery('#ultimate_purchase_code').css({'border':'1px solid #ddd'});
		
		if(envato_api_key == '')
		{
			jQuery('#envato_api_key').css({'border':'1px solid red'});
			errors++;
		}
		else
		{
			envato_api_key = envato_api_key.trim();
			var api_length = parseInt(envato_api_key.length);
			if(api_length != 32)
			{
				jQuery('#envato_api_key').css({'border':'1px solid red'});
				jQuery('#envato_api_key').parent().append('<span class="small-txt-error"><?php echo __('Invalid API Key','ultimate_vc'); ?>. <a href="https://www.youtube.com/watch?v=1E8taGHkYKY#t=150" target="_blank"><?php echo __('Where to find this?','ultimate_vc') ?></a>.</span>');
				errors++;
			}
			else
			{
				jQuery('#envato_api_key').css({'border':'1px solid #ddd'});
			}
		}
			
		if(errors > 0)
			return false;
	}
	
	var data = "action=ultimate_activation&process="+process+"_license&purchase_code="+purchase_key+"&site_url=<?php echo get_site_url(); ?>&plugin=Ultimate%20Addons&userid="+user+"&user_email="+email+'&step='+step+'&receive='+receive+'&next_step='+next_step;
	
	var formdata = jQuery("#ultimate_updater").serialize();
	loader.css("display","inline-block");
	jQuery('#popup-spinner').css("display","inline-block");
	jQuery.ajax({
		url: ajaxurl,
		data: formdata,
		dataType: 'html',
		type: 'post',
		success: function(result){
			 setTimeout(function(){
				jQuery.ajax({
					url: ajaxurl,
					data: data,
					//crossDomain: true,
					type: 'POST',
					dataType: 'html',
					success: function(responseData) {
						console.log(responseData);
						var rdata = jQuery.parseJSON(responseData);
						if(typeof rdata.reload_page != 'undefined' && rdata.reload_page == 'false')
						{
						//jQuery("#msg").html(rdata.response);
							jQuery("#bsf-overlay-inner-msg").html(rdata.response);
							jQuery(".bsf-overlay").fadeIn(300);
						}
						else
						{
							jQuery("#bsf-message").html(rdata.response);
							jQuery("#bsf-message").addClass('bsf-start-push-effect');
							setTimeout(function(){
								window.location = window.location;
							},700);
						}
						loader.css("display","none");
						jQuery('#popup-spinner').css("display","none");
						
					},
					error: function (responseData, textStatus, errorThrown) {
						jQuery("#bsf-message").html(responseData);
						jQuery("#bsf-message").addClass('bsf-start-push-effect');
					}
				});
			 },200);
		}
	});
}
jQuery('#skip').click(function(){
	loader.css("display","inline-block");
	var user_email = jQuery('#ultimate_user_email').val();
	jQuery.ajax({
		url: ajaxurl,
		data: 'action=ultimate_skip_registration&email='+user_email,
		//crossDomain: true,
		type: 'POST',
		dataType: 'html',
		success: function(responseData) {
			console.log(responseData);
			setTimeout(function(){
				window.location = window.location;
				loader.css("display","none");
			},200);
		},
		error: function (responseData, textStatus, errorThrown) {
			jQuery("#msg").html(responseData);
		}
	});
});
jQuery(document).ready(function(){
	jQuery('body').append('<div class="bsf-overlay"><div class="bsf-overlay-message"><i id="close-bsf-popup" class="dashicons dashicons-no-alt"></i><div id="bsf-overlay-inner-msg"></div></div></div>');
	
	jQuery('#close-bsf-popup').click(function(){
		jQuery(".bsf-overlay").fadeOut(300);
	});
});
</script>
<script type="text/javascript">
jQuery(document).ready(function() {
	// Tooltip only Text
	jQuery(document).on('mouseover','.masterUltTooltip', function(){
		if(jQuery(this).hasClass('active-tooltip'))
			return false;
		
		jQuery('.tooltip').remove();
			// Hover over code
		var title = jQuery(this).attr('title');
		jQuery(this).data('tipText', title).removeAttr('title');
		jQuery('<p class="tooltip"></p>')
		.html(title)
		.appendTo('body')
		.fadeIn('slow')
		.css({'z-index':'9999'});
	});
	jQuery(document).on('mouseout','.masterUltTooltip', function() {
		if(jQuery(this).hasClass('active-tooltip'))
			return false;
		// Hover out code
		jQuery(this).attr('title', jQuery(this).data('tipText'));
		jQuery('.tooltip').remove();
	});
	jQuery(document).on('mousemove','.masterUltTooltip', function(e) {
		var mousex = e.pageX + 25; //Get X coordinates
		var mousey = e.pageY - 34; //Get Y coordinates
		jQuery('.tooltip').css({ top: mousey, left: mousex });
	});
	
	jQuery(document).on('focus','#envato_username, #envato_api_key, #ultimate_purchase_code, #ultimate_user_email, #ultimate_user_email_confirm', function(e) {
		jQuery('.tooltip').remove();
		var toolTip = jQuery(this).next();
		toolTip.addClass('active-tooltip');
		var positions = toolTip.offset();
		var mousex = positions.left + 25; //Get X coordinates
		var mousey = positions.top - 20; //Get Y coordinates
		var title = toolTip.attr('title');
			toolTip.data('tipText', title).removeAttr('title');
			jQuery('<p class="tooltip"></p>')
			.html(title)
			.appendTo('body')
			.fadeIn('slow')
			.css({'z-index':'9999', 'top' : mousey, 'left' : mousex});
	});
	jQuery(document).on('blur','#envato_username, #envato_api_key, #ultimate_purchase_code, #ultimate_user_email, #ultimate_user_email_confirm', function() {
			// Hover out code
			var toolTip = jQuery(this).next();
			toolTip.removeClass('active-tooltip');
			toolTip.attr('title', toolTip.data('tipText'));
			jQuery('.tooltip').remove();
	});
});
</script>
<script type="text/javascript">
jQuery('body').on('click', '#developer-access', function(){
		jQuery('#grant-dev-spinner').css({'display':'inline-block', 'float':'none'});
		jQuery.ajax(
			{
				url:ajaxurl,
				data:"action=grant_access",
				dataType:"html",
				type:"POST",
				success: function(result){
					jQuery('#grant-dev-spinner').hide();
					if(result == "Access Granted!"){
						var buttons = '<button class="button updater-act-btn btn-dev-access" id="developer-access-revoke"><span class="dashicons dashicons-admin-network"></span><?php echo __('Revoke Access', 'ultimate_vc'); ?></button>';
						var html = '<p><?php echo __('You have granted access to developer. The developer access will be automatically revoked.','ultimate_vc'); ?></strong></p>'+buttons;
						jQuery(".access-buttons").html(buttons);
						//jQuery(".access-status").html('Developer Access : <span class="active-access">Active</span>');
					}
				}
			}
		);
	});
	// Revoke developer access
	//jQuery("#developer-access-revoke").click(function(){
	jQuery('body').on('click', '#developer-access-revoke', function(){
		jQuery('#grant-dev-spinner').css({'display':'inline-block', 'float':'none'});
		jQuery.ajax(
			{
				url:ajaxurl,
				data:"action=update_access&access=revoke",
				dataType:"html",
				type:"POST",
				success: function(result){
					jQuery('#grant-dev-spinner').hide();
					if(result == "Access Revoked!"){
						var buttons = '<button class="button updater-act-btn btn-dev-access" id="developer-access"><span class="dashicons dashicons-admin-network"></span><?php echo __('Grant Temporary Access to Developers','ultimate_vc'); ?></button>';
						jQuery(".access-buttons").html(buttons);
						//jQuery(".access-status").html('Developer Access : <span class="inactive-access">Inactive</span>');
					}
				}
			}
		);
	});
</script>