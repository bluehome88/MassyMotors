<?php ?>
<?php if (!is_admin()) { echo page_takeover_code_comment(); } ?>
<?php global $page_takeover_custom_css; if (!is_admin() && (!empty($page_takeover_custom_css))) { echo "<style>" . page_takeover_custom_css() . "</style>\n"; } ?>
<div id="page-takeover-output"<?php if (!is_admin()) { ?> style="display:none;"<?php } ?> class="pagetakeover-container">
    <div class="page-takeover-container" id="page-takeover-container">
        <div class="page-takeover-overlay" id="page-takeover-inner" style="background-color:<?php echo page_takeover_default_background(); ?>;">
            <?php if (!is_admin()) { ?>
            <div class="page-takeover-close-button" id="page-takeover-close-button"></div>
            <?php } ?>
            <p class="page-takeover-title" id="page-takeover-title" style="font-family:<?php echo page_takeover_default_title_font(); ?>;font-size:<?php echo page_takeover_default_title_size(); ?>;color:<?php echo page_takeover_default_title_color(); ?>;"><?php echo page_takeover_default_title(); ?></p>
            <div class="page-takeover-subtitle" id="page-takeover-subtitle-inner" style="font-family:<?php echo page_takeover_default_subtitle_font(); ?>;font-size:<?php echo page_takeover_default_subtitle_size(); ?>;color:<?php echo page_takeover_default_subtitle_color(); ?>;"><?php echo do_shortcode(page_takeover_default_subtitle()); ?></div>
            <p class="page-takeover-button" id="page-takeover-button" style="font-family:<?php echo page_takeover_default_button_font(); ?>;font-size:<?php echo page_takeover_default_button_size(); ?>;color:<?php echo page_takeover_default_button_color(); ?>;background-color:<?php echo page_takeover_default_button_background(); ?>;"><?php if (!is_admin()) { ?><a href="<?php echo page_takeover_default_button_url(); ?>" target="_blank"><?php } ?><?php echo page_takeover_default_button(); ?></a></p>
            <p class="page-takeover-decline" id="page-takeover-decline" style="font-family:<?php echo page_takeover_default_decline_font(); ?>;font-size:<?php echo page_takeover_default_decline_size(); ?>;color:<?php echo page_takeover_default_decline_color(); ?>;"><?php echo page_takeover_default_decline(); ?></p>
        </div><!--page-takeover-overlay-->
    </div><!--page-takeover-container-->
</div><!--page-takeover-output-->

<?php if ( is_admin() ) { ?>

<?php } else { ?>

<?php global $page_takeover_trigger; if (function_exists('page_takeover_pro_exit_intent') && ($page_takeover_trigger == "exit")) {
    echo page_takeover_pro_exit_intent();
} else {
    echo page_takeover_trigger();
} ?>

<script type="text/javascript">
    jQuery( '#page-takeover-decline' ).on( 'click', function () {
        jQuery( ".page-takeover-dialog" ).hide();
        jQuery( ".ui-widget-overlay" ).hide();
    });
    jQuery( '#page-takeover-close-button' ).on( 'click', function () {
        jQuery( ".page-takeover-dialog" ).hide();
        jQuery( ".ui-widget-overlay" ).hide();
    });
</script>
<?php if (!is_admin()) { echo page_takeover_code_comment_end(); } ?>
<?php } ?>