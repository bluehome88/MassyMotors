<?php
    global $page_takeover_trigger;
    global $page_takeover_frequency;
    global $page_takeover_placement;
    global $page_takeover_status;
?>

<form method="post" action="options.php" id="frm1">
<?php settings_fields( 'page-takeover-settings-group' ); ?>
					
<div class="toggle-wrap">
    <span class="trigger">
        <a><?php echo __('Background', 'page-takeover'); ?></a>
    </span>
                        
    <div class="toggle-container" style="display: none;">
        <div class="page-takeover-option-group">
            <div class="page-takeover-option-wide">
                <label for="page_takeover_background" class="nopointer"><?php echo __('Overlay background color', 'page-takeover'); ?></label>
            </div><!--page-takeover-option-wide-->
            <div class="page-takeover-option-wide">
                <input type="text" id="page_takeover_background" name="page_takeover_background" class="page-takeover-color" value="<?php echo page_takeover_default_background(); ?>" data-default-color="#2c3644" />
                <script>
                    jQuery(document).ready(function($){
                        jQuery('#page_takeover_background').wpColorPicker({
                            color: true,
                            hide: true,
                            palettes: true,
                            change: function(event, ui) {
                                $("#page-takeover-inner").css( 'background-color', ui.color.toString());
                            }
                        });
                    }); 
                </script>
            </div><!--page-takeover-option-wide-->
            <div class="clear"></div>
        </div><!--page-takeover-option-group-->
    </div><!--toggle-container-->
    <div class="clear"></div>
</div><!--toggle-wrap-->
                    
<div class="toggle-wrap">
    <span class="trigger">
        <a id="page-takeover-toggle-title"><?php echo __('Title', 'page-takeover'); ?></a>
    </span>
                        
    <div class="toggle-container" style="display: none;">
        <div class="page-takeover-option-group">
            <div class="page-takeover-option-left">
                <label for="page_takeover_title" class="nopointer"><?php echo __('Title', 'page-takeover'); ?></label>
            </div><!--page-takeover-option-left-->
            <div class="page-takeover-option-wide">
                <input type="text" class="page-takeover-input-text" id="page_takeover_title" name="page_takeover_title" value="<?php echo page_takeover_default_title(); ?>" onchange='page_takeover_change_title()' <?php if (get_option('page_takeover_hide_title')== '1') { echo 'disabled="disabled"'; } ?> />
                <script type="text/javascript">
                    function page_takeover_change_title() {
                        document.getElementById('page-takeover-title').innerHTML = document.getElementById('page_takeover_title').value;
                    }
                </script>
            </div><!--page-takeover-option-wide-->
            <div class="page-takeover-option-wide">
                <div class="page-takeover-option-half-left">
                    <select class="page-takeover-select-font" name="page_takeover_title_font" id="page_takeover_title_font" onchange='page_takeover_change_title_font()' <?php if (get_option('page_takeover_hide_title')== '1') { echo 'disabled="disabled"'; } ?>>
                        <?php echo page_takeover_get_title_font_options(); ?>
                    </select>
                    <script type="text/javascript">
                        function page_takeover_change_title_font() {
                            document.getElementById("page-takeover-title").style.fontFamily = document.getElementById('page_takeover_title_font').value;
                        }
                    </script>
                    </div><!--page-takeover-option-half-left-->
                    <div class="page-takeover-option-half-right">
                        <select class="page-takeover-select-font" name="page_takeover_title_size" id="page_takeover_title_size" onchange='page_takeover_change_title_size()' <?php if (get_option('page_takeover_hide_title')== '1') { echo 'disabled="disabled"'; } ?>>
                            <?php echo page_takeover_get_title_size_options(); ?>
                        </select>
                        <script type="text/javascript">
                            function page_takeover_change_title_size() {
                                document.getElementById("page-takeover-title").style.fontSize = document.getElementById('page_takeover_title_size').value;
                            }
                        </script>
                    </div><!--page-takeover-option-half-right-->
                    <div class="clear"></div>          
            </div><!--page-takeover-option-wide-->
            <div class="page-takeover-option-wide">
                    <input type="text" id="page_takeover_title_color" name="page_takeover_title_color" class="page-takeover-color" value="<?php echo page_takeover_default_title_color(); ?>" data-default-color="#ffffff" <?php if (get_option('page_takeover_hide_title')== '1') { echo 'disabled="disabled"'; } ?> />
                    <script>
                        jQuery(document).ready(function($){
                            jQuery('#page_takeover_title_color').wpColorPicker({
                                color: true,
                                hide: true,
                                palettes: true,
                                change: function(event, ui) {
                                    $("#page-takeover-title").css( 'color', ui.color.toString());
                                }
                            });
                        }); 
                    </script>
            </div><!--page-takeover-option-wide-->
            <div class="clear"></div>
        </div><!--page-takeover-option-group-->
        <script type="text/javascript">
            jQuery( '#page-takeover-toggle-title' ).on( 'click', function () {
                jQuery( "#page-takeover-title" ).addClass( "page-takeover-animated page-takeover-pulse" );
                
            });
        </script>
    </div><!--toggle-container-->
    <div class="clear"></div>
</div><!--toggle-wrap-->
                    
<div class="toggle-wrap">
    <span class="trigger">
        <a id="page-takeover-toggle-subtitle"><?php echo __('Subtitle', 'page-takeover'); ?></a>
    </span>
                        
    <div class="toggle-container" style="display: none;">
        <div class="page-takeover-option-group">
            <div class="page-takeover-option-left">
                <label for="page_takeover_title" class="nopointer"><?php echo __('Subtitle', 'page-takeover'); ?></label>
            </div><!--page-takeover-option-left-->
            <div class="page-takeover-option-wide">
                <?php echo page_takeover_subtitle_textarea(); ?>
                <script type="text/javascript">
                    jQuery( "#page_takeover_subtitle_text" ).change( function () {
                        jQuery( "#page-takeover-subtitle-inner" ).html( jQuery( "#page_takeover_subtitle_text" ).val() );
                    } );
                </script>
                    
            </div><!--page-takeover-option-wide-->
            <div class="page-takeover-option-wide">
                <div class="page-takeover-option-half-left">
                    <select class="page-takeover-select-font" name="page_takeover_subtitle_font" id="page_takeover_subtitle_font" onchange='page_takeover_change_subtitle_font()' <?php if (get_option('page_takeover_hide_subtitle')== '1') { echo 'disabled="disabled"'; } ?>>
                        <?php echo page_takeover_get_subtitle_font_options(); ?>
                    </select>
                    <script type="text/javascript">
                        function page_takeover_change_subtitle_font() {
                            document.getElementById("page-takeover-subtitle-inner").style.fontFamily = document.getElementById('page_takeover_subtitle_font').value;
                        }
                    </script>
                </div><!--page-takeover-option-half-left-->
                <div class="page-takeover-option-half-right">
                    <select class="page-takeover-select-font" name="page_takeover_subtitle_size" id="page_takeover_subtitle_size" onchange='page_takeover_change_subtitle_size()' <?php if (get_option('page_takeover_hide_subtitle')== '1') { echo 'disabled="disabled"'; } ?>>
                        <?php echo page_takeover_get_subtitle_size_options(); ?>
                    </select>
                    <script type="text/javascript">
                        function page_takeover_change_subtitle_size() {
                            document.getElementById("page-takeover-subtitle-inner").style.fontSize = document.getElementById('page_takeover_subtitle_size').value;
                        }
                    </script>
                </div><!--page-takeover-option-half-right-->
                <div class="clear"></div>           
            </div><!--page-takeover-option-wide-->
            <div class="page-takeover-option-wide">
                <input type="text" id="page_takeover_subtitle_color" name="page_takeover_subtitle_color" class="page-takeover-color" value="<?php echo page_takeover_default_subtitle_color(); ?>" data-default-color="#ffffff" <?php if (get_option('page_takeover_hide_subtitle')== '1') { echo 'disabled="disabled"'; } ?> />
                <script>
                    jQuery(document).ready(function($){
                        jQuery('#page_takeover_subtitle_color').wpColorPicker({
                            color: true,
                            hide: true,
                            palettes: true,
                            change: function(event, ui) {
                                $("#page-takeover-subtitle-inner").css( 'color', ui.color.toString());
                            }
                        });
                    }); 
                </script>
            </div><!--page-takeover-option-wide-->
            <div class="clear"></div>
        </div><!--page-takeover-option-group-->
        <script type="text/javascript">
            jQuery( '#page-takeover-toggle-subtitle' ).on( 'click', function () {
                jQuery( "#page-takeover-subtitle-inner" ).addClass( "page-takeover-animated page-takeover-pulse" );
                
            });
        </script>
    </div><!--toggle-container-->
    <div class="clear"></div>
</div><!--toggle-wrap-->
                    
<div class="toggle-wrap">
    <span class="trigger">
        <a id="page-takeover-toggle-button"><?php echo __('Call to Action', 'page-takeover'); ?></a>
    </span>
                        
    <div class="toggle-container" style="display: none;">
        <div class="page-takeover-option-group">
            <div class="page-takeover-option-left">
                <label for="page_takeover_button" class="nopointer"><?php echo __('Call to action text', 'page-takeover'); ?></label>
            </div><!--page-takeover-option-left-->
            <div class="page-takeover-option-wide">
                <input type="text" class="page-takeover-input-text" id="page_takeover_button" name="page_takeover_button" value="<?php echo page_takeover_default_button(); ?>" onchange='page_takeover_change_button()' />
                <script type="text/javascript">
                    function page_takeover_change_button() {
                        document.getElementById('page-takeover-button').innerHTML = document.getElementById('page_takeover_button').value;
                    }
                </script>
            </div><!--page-takeover-option-wide-->
            <div class="page-takeover-option-left">
                <label for="page_takeover_button_url" class="nopointer"><?php echo __('Destination URL', 'page-takeover'); ?></label>
            </div><!--page-takeover-option-left-->
            <div class="page-takeover-option-wide">
                <input type="text" class="page-takeover-input-text" id="page_takeover_button_url" name="page_takeover_button_url" value="<?php echo page_takeover_default_button_url(); ?>" />
            </div><!--page-takeover-option-wide-->
            <div class="page-takeover-option-wide">
                <div class="page-takeover-option-half-left">
                    <select class="page-takeover-select-font" name="page_takeover_button_font" id="page_takeover_button_font" onchange='page_takeover_change_button_font()'>
                        <?php echo page_takeover_get_button_font_options(); ?>
                    </select>
                    <script type="text/javascript">
                        function page_takeover_change_button_font() {
                            document.getElementById("page-takeover-button").style.fontFamily = document.getElementById('page_takeover_button_font').value;
                        }
                    </script>
                </div><!--page-takeover-option-half-left-->
                <div class="page-takeover-option-half-right">
                    <select class="page-takeover-select-font" name="page_takeover_button_size" id="page_takeover_button_size" onchange='page_takeover_change_button_size()'>
                        <?php echo page_takeover_get_button_size_options(); ?>
                    </select>
                    <script type="text/javascript">
                        function page_takeover_change_button_size() {
                            document.getElementById("page-takeover-button").style.fontSize = document.getElementById('page_takeover_button_size').value;
                        }
                    </script>
                </div><!--page-takeover-option-half-right-->
                <div class="clear"></div>     
            </div><!--page-takeover-option-wide-->
            <div class="page-takeover-option-wide">
                <input type="text" id="page_takeover_button_color" name="page_takeover_button_color" class="page-takeover-color" value="<?php echo page_takeover_default_button_color(); ?>" data-default-color="#000000" />
                <script>
                    jQuery(document).ready(function($){
                        jQuery('#page_takeover_button_color').wpColorPicker({
                            color: true,
                            hide: true,
                            palettes: true,
                            change: function(event, ui) {
                                $("#page-takeover-button").css( 'color', ui.color.toString());
                            }
                        });
                    }); 
                </script>
                <div class="clear"></div>
            </div><!--page-takeover-option-wide-->
            <div class="page-takeover-option-wide">
                <input type="text" id="page_takeover_button_background" name="page_takeover_button_background" class="page-takeover-color" value="<?php echo page_takeover_default_button_background(); ?>" data-default-color="#fec230" />
                <script>
                    jQuery(document).ready(function($){
                        jQuery('#page_takeover_button_background').wpColorPicker({
                            color: true,
                            hide: true,
                            palettes: true,
                            change: function(event, ui) {
                                $("#page-takeover-button").css( 'background-color', ui.color.toString());
                            }
                        });
                    }); 
                </script>
                <div class="clear"></div>
            </div><!--page-takeover-option-wide-->
        </div><!--page-takeover-option-group-->
        <script type="text/javascript">
            jQuery( '#page-takeover-toggle-button' ).on( 'click', function () {
                jQuery( "#page-takeover-button" ).addClass( "page-takeover-animated page-takeover-pulse" );
                
            });
        </script>
    </div><!--toggle-container-->
    <div class="clear"></div>
</div><!--toggle-wrap-->

<div class="toggle-wrap">
    <span class="trigger">
        <a id="page-takeover-toggle-decline"><?php echo __('Decline Offer', 'page-takeover'); ?></a>
    </span>
                        
    <div class="toggle-container" style="display: none;">
        <div class="page-takeover-option-group">
            <div class="page-takeover-option-left">
                <label for="page_takeover_decline" class="nopointer"><?php echo __('Decline offer text', 'page-takeover'); ?></label>
            </div><!--page-takeover-option-left-->
            <div class="page-takeover-option-wide">
                <input type="text" class="page-takeover-input-text" id="page_takeover_decline" name="page_takeover_decline" value="<?php echo page_takeover_default_decline(); ?>" onchange='page_takeover_change_decline()' <?php if (get_option('page_takeover_hide_decline')== '1') { echo 'disabled="disabled"'; } ?> />
                    <script type="text/javascript">
                        function page_takeover_change_decline() {
                            document.getElementById('page-takeover-decline').innerHTML = document.getElementById('page_takeover_decline').value;
                        }
                    </script>
            </div><!--page-takeover-option-wide-->
            <div class="page-takeover-option-wide">
                <div class="page-takeover-option-half-left">
                    <select class="page-takeover-select-font" name="page_takeover_decline_font" id="page_takeover_decline_font" onchange='page_takeover_change_decline_font()' <?php if (get_option('page_takeover_hide_decline')== '1') { echo 'disabled="disabled"'; } ?>>
                        <?php echo page_takeover_get_decline_font_options(); ?>
                    </select>
                    <script type="text/javascript">
                        function page_takeover_change_decline_font() {
                            document.getElementById("page-takeover-decline").style.fontFamily = document.getElementById('page_takeover_decline_font').value;
                        }
                    </script>
                </div><!--page-takeover-option-half-left-->
                <div class="page-takeover-option-half-right">
                    <select class="page-takeover-select-font" name="page_takeover_decline_size" id="page_takeover_decline_size" onchange='page_takeover_change_decline_size()' <?php if (get_option('page_takeover_hide_decline')== '1') { echo 'disabled="disabled"'; } ?>>
                        <?php echo page_takeover_get_decline_size_options(); ?>
                    </select>
                    <script type="text/javascript">
                        function page_takeover_change_decline_size() {
                            document.getElementById("page-takeover-decline").style.fontSize = document.getElementById('page_takeover_decline_size').value;
                        }
                    </script>
                </div><!--page-takeover-option-half-right-->
                <div class="clear"></div>           
            </div><!--page-takeover-option-wide-->
            <div class="page-takeover-option-wide">
                <input type="text" id="page_takeover_decline_color" name="page_takeover_decline_color" class="page-takeover-color" value="<?php echo page_takeover_default_decline_color(); ?>" data-default-color="#a3a3a3" <?php if (get_option('page_takeover_hide_decline')== '1') { echo 'disabled="disabled"'; } ?> />
                <script>
                    jQuery(document).ready(function($){
                        jQuery('#page_takeover_decline_color').wpColorPicker({
                            color: true,
                            hide: true,
                            palettes: true,
                            change: function(event, ui) {
                                $("#page-takeover-decline").css( 'color', ui.color.toString());
                            }
                        });
                    }); 
                </script>
            </div><!--page-takeover-option-wide-->
            <div class="clear"></div>
        </div><!--page-takeover-option-group-->
        <script type="text/javascript">
            jQuery( '#page-takeover-toggle-decline' ).on( 'click', function () {
                jQuery( "#page-takeover-decline" ).addClass( "page-takeover-animated page-takeover-pulse" );
                
            });
        </script>
    </div><!--toggle-container-->
    <div class="clear"></div>
</div><!--toggle-wrap-->

<div class="toggle-wrap">
    <span class="trigger">
        <a><?php echo __('Custom CSS', 'page-takeover'); ?></a>
    </span>
                        
    <div class="toggle-container" style="display: none;">
        <div class="page-takeover-option-group">
            <div class="page-takeover-option-wide">
                <label for="page_takeover_custom_css" class="nopointer"><?php echo __('Custom CSS', 'page-takeover'); ?></label>
                <textarea id="page_takeover_custom_css" name="page_takeover_custom_css" class="page-takeover-custom-css autoExpand" rows="3" data-min-rows="3"><?php echo page_takeover_custom_css(); ?></textarea>
            </div><!--page-takeover-option-wide-->
            <div class="clear"></div>
        </div><!--page-takeover-option-group-->
    </div><!--toggle-container-->
    <div class="clear"></div>
</div><!--toggle-wrap-->

<div class="toggle-wrap">
    <span class="trigger">
        <a><?php echo __('Impression Trigger', 'page-takeover'); ?></a>
    </span>
                        
    <div class="toggle-container" style="display: none;">
        <div class="page-takeover-option-group">
            <div class="page-takeover-option-wide">
                <label for="page_takeover_trigger" class="nopointer"><?php echo __('When do we load your overlay?', 'page-takeover'); ?></label>
                <select name="page_takeover_trigger" id="page_takeover_trigger" class="page-takeover-select-option">
                    <option value="0" <?php if (empty($page_takeover_trigger) || $page_takeover_trigger == 0) { echo 'selected="selected"'; } ?>><?php echo __('Immediately', 'page-takeover'); ?></option>
                    <option value="5" <?php if ($page_takeover_trigger == 5) { echo 'selected="selected"'; } ?>><?php echo __('After 5 seconds', 'page-takeover'); ?></option>
                    <option value="10" <?php if ($page_takeover_trigger == 10) { echo 'selected="selected"'; } ?>><?php echo __('After 10 seconds', 'page-takeover'); ?></option>
                    <option value="30" <?php if ($page_takeover_trigger == 30) { echo 'selected="selected"'; } ?>><?php echo __('After 30 seconds', 'page-takeover'); ?></option>
                    
                    <?php if (function_exists('page_takeover_pro_exit_intent')) { ?>
                    <option value="exit" <?php if ($page_takeover_trigger == 'exit') { echo 'selected="selected"'; } ?>><?php echo __('On exit', 'page-takeover'); ?></option>
                    <?php } ?>
                    
                </select>
            </div><!--page-takeover-option-wide-->
            <div class="clear"></div>
            
            <?php if (!function_exists('page_takeover_pro_exit_intent')) { ?>
                <p><?php echo __('Get the exit-intent feature', 'page-takeover'); ?></p>
            <?php } ?>
            
        </div><!--page-takeover-option-group-->
    </div><!--toggle-container-->
    <div class="clear"></div>
</div><!--toggle-wrap-->

<div class="toggle-wrap">
    <span class="trigger">
        <a><?php echo __('Impression Frequency', 'page-takeover'); ?></a>
    </span>
                        
    <div class="toggle-container" style="display: none;">
        <div class="page-takeover-option-group">
            <div class="page-takeover-option-wide">
                <label for="page_takeover_frequency" class="nopointer"><?php echo __('How often do we load your overlay?', 'page-takeover'); ?></label>
                <select name="page_takeover_frequency" id="page_takeover_frequency" class="page-takeover-select-option">
                    <option value="0" <?php if (empty($page_takeover_frequency) || $page_takeover_frequency == 0) { echo 'selected="selected"'; } ?>><?php echo __('Always', 'page-takeover'); ?></option>
                    <option value="1" <?php if ($page_takeover_frequency == 1) { echo 'selected="selected"'; } ?>><?php echo __('Once every session', 'page-takeover'); ?></option>
                    <option value="2" <?php if ($page_takeover_frequency == 2) { echo 'selected="selected"'; } ?>><?php echo __('Once every day', 'page-takeover'); ?></option>
                    <option value="3" <?php if ($page_takeover_frequency == 3) { echo 'selected="selected"'; } ?>><?php echo __('Once every week', 'page-takeover'); ?></option>
                </select>
            </div><!--page-takeover-option-wide-->
            <div class="clear"></div>
        </div><!--page-takeover-option-group-->
    </div><!--toggle-container-->
    <div class="clear"></div>
</div><!--toggle-wrap-->

<div class="toggle-wrap">
    <span class="trigger">
        <a><?php echo __('Overlay Placement', 'page-takeover'); ?></a>
    </span>
                        
    <div class="toggle-container" style="display: none;">
        <div class="page-takeover-option-group">
            <div class="page-takeover-option-wide">
                <label for="page_takeover_placement" class="nopointer"><?php echo __('Where do we load your overlay?', 'page-takeover'); ?></label>
                <select name="page_takeover_placement" id="page_takeover_placement" class="page-takeover-select-option">
                    <option value="0" <?php if (empty($page_takeover_placement) || $page_takeover_placement == 0) { echo 'selected="selected"'; } ?>><?php echo __('Sitewide', 'page-takeover'); ?></option>
                </select>
            </div><!--page-takeover-option-wide-->
            <div class="clear"></div>
        </div><!--page-takeover-option-group-->
    </div><!--toggle-container-->
    <div class="clear"></div>
</div><!--toggle-wrap-->
                        
<div class="toggle-wrap">
    <span class="trigger">
        <a><?php echo __('Overlay Status', 'page-takeover'); ?></a>
    </span>
                        
    <div class="toggle-container" style="display: none;">
        <div class="page-takeover-option-group">
            <div class="page-takeover-option-wide">
                <label for="page_takeover_status" class="nopointer"><?php echo __('Is the overlay active?', 'page-takeover'); ?></label>
                <select name="page_takeover_status" id="page_takeover_status" class="page-takeover-select-option">
                    <option value="inactive" <?php if (empty($page_takeover_status) || $page_takeover_status == "inactive") { echo 'selected="selected"'; } ?>><?php echo __('Inactive', 'page-takeover'); ?></option>
                    <option value="active" <?php if ($page_takeover_status == "active") { echo 'selected="selected"'; } ?>><?php echo __('Active', 'page-takeover'); ?></option>
                </select>
            </div><!--page-takeover-option-wide-->
            <div class="clear"></div>
        </div><!--page-takeover-option-group-->
    </div><!--toggle-container-->
    <div class="clear"></div>
</div><!--toggle-wrap-->

<div class="page-takeover-save-container">
    <input type="submit" class="page-takeover-save-button" value="<?php _e('Save Changes') ?>" />
    <div class="clear"></div>
</div><!--page-takeover-save-container-->
<div class="clear"></div>

</form>