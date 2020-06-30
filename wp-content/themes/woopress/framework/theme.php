<?php 

define('ETHEME_THEME_NAME', 'Woo<span>press</span>');
define('THEME_LOGO', 'Woopress');
define('THEME_SLUG', 'woopress');
define('ETHEME_DOMAIN', 'woopress');
define('ET_DOMAIN', 'woopress');

if(!function_exists('et_get_captcha_color')) {
    function et_get_captcha_color() {
        return apply_filters( 'et_get_captcha_color', array( 204, 168, 97 ) );
    }
}

if(!function_exists('et_enqueue_gfonts')) {
    function et_enqueue_gfonts() {
        wp_enqueue_style("font-open-sans",et_http()."fonts.googleapis.com/css?family=Open+Sans:300,400,700,300italic");
        wp_enqueue_style("font-raleway",et_http()."fonts.googleapis.com/css?family=Raleway:400,100,300,500,700,600,800,900,200");
        wp_enqueue_style("font-satisfy",et_http()."fonts.googleapis.com/css?family=Satisfy");
    }
}

add_action( 'wp_enqueue_scripts', 'et_enqueue_gfonts', 40);

if(!function_exists('et_get_tooltip_html')) {
    function et_get_tooltip_html($item_id) {
        $output = '';
        $post_thumbnail = get_post_thumbnail_id( $item_id, 'thumb' );
        $post_thumbnail_url = wp_get_attachment_url( $post_thumbnail );
        $output .= '<div class="nav-item-tooltip">';
            $output .= '<img src="' . $post_thumbnail_url . '">'; //$output .= '<div data-src="' . $post_thumbnail_url . '"></div>';
        $output .= '</div>';
        return $output;
    }
}


// **********************************************************************// 
// ! Footer Demo Widgets
// **********************************************************************// 

if(!function_exists('etheme_footer_demo')) {
    function etheme_footer_demo($position){
        switch ($position) {
        
            case 'footer1':
            ?>
 
            <?php
            break;
            case 'footer2':

                ?>

                    <div class="row">
                        <div class="col-md-3">
                            <div class="about-company">
                                <a class="pull-left" href="#"><img title="Woopress" src="<?php echo get_template_directory_uri(); ?>/images/small-logo.png" alt="..."><br></a>
                            </div>
                            <h5 class="media-heading">About <span class="default-colored">Woopress</span></h5>
                            <p>Lorem ipsum dolor sit amet, consect etur adipisic ing elit, sed do eiusmod tempor incididunt ut labore.</p>
                            <address class="address-company">30 South Avenue San Francisco<br>
                                <span class="white-text">Phone</span>: +78 123 456 789<br>
                                <span class="white-text">Email</span>: <a href="mailto:Support@woopress.com">Support@woopress.com</a><br>
                                <a class="white-text letter-offset" href="#">www.woopress.com</a><br>
                                <?php echo etheme_share_shortcode(array()); ?>
                            </address>
                        </div>
                        <div class="col-md-3">
                            <div class="widget-container widget_text">
                                <h3 class="widget-title"><span>Informations</span></h3>
                                <div class="textwidget">
                                    <ul class="col-ct-6 list-unstyled">
                                        <li><a href="#">London</a></li>
                                        <li><a href="#">Singapore</a></li>
                                        <li><a href="#">Paris</a></li>
                                        <li><a href="#">Moscow</a></li>
                                        <li><a href="#">Berlin</a></li>
                                        <li><a href="#">Milano</a></li>
                                        <li><a href="#">Amsterdam</a></li>
                                    </ul>
                                    
                                    <ul class="col-ct-6 list-unstyled">
                                        <li><a href="#">London</a></li>
                                        <li><a href="#">Singapore</a></li>
                                        <li><a href="#">Paris</a></li>
                                        <li><a href="#">Moscow</a></li>
                                        <li><a href="#">Berlin</a></li>
                                        <li><a href="#">Milano</a></li>
                                        <li><a href="#">Amsterdam</a></li>
                                    </ul>
                                </div>
                            </div> 
                        </div>
                        <div class="col-md-3">
                            <?php
                                $args = array(
                                    'widget_id' => 'etheme_widget_flickr',
                                    'before_widget' => '<div class="footer-sidebar-widget etheme_widget_flickr">',
                                    'after_widget' => '</div><!-- //sidebar-widget -->',
                                    'before_title' => '<h4 class="widget-title"><span>',
                                    'after_title' => '</span></h4>'
                                );

                                $instance = array(
                                    'screen_name' => '52617155@N08',
                                    'number' => 6,
                                    'show_button' => 1,
                                    'title' => __('Flickr Photos', ETHEME_DOMAIN)
                                );


                                $widget = new Etheme_Flickr_Widget();
                                $widget->widget($args, $instance);
                            ?>
                        </div>
                        <div class="col-md-3">
                            <?php the_widget('Etheme_Recent_Posts_Widget', array('title' => __('Recent posts', ETHEME_DOMAIN), 'number' => 2), array('before_title' => '<h3 class="widget-title">','after_title' => '</h3>', 'number' => 2)); ?>
                        </div>
                    </div>

                <?php 

            break;
            case 'footer9':
                ?>  
                    <div class="textwidget">
                        <p>© Created with <i class="fa fa-heart default-colored"></i> by <a href="#" class="default-link">8Theme</a>. All Rights Reserved</p>
                    </div>
                <?php
                break;
            case 'footer10':
                ?>
                    <img src="<?php echo get_template_directory_uri(); ?>/images/assets/payments.png">
                <?php
                break;
        }
    }
}

if(!function_exists('et_get_versions_option')) {
    function et_get_versions_option() {
        return apply_filters('et_get_versions_option', array(
          'onepage' => array(
          'home_id' => 16290,
          'title'   => 'OnePage',
          'cat'     => 'one_page'
      ),
        'fullwidth' => array(
          'home_id' => 16491,
          'title'   => 'FullWidth',
          'cat'     => 'simple'
      ),
        'sidebar' => array(
        'home_id' => 15184,
          'title'   => 'Sidebar',
          'cat'     => 'simple'
      ),
        'lookbook' => array(
        'home_id' => 16439,
          'title'   => 'LookBook',
          'cat'     => 'simple'
      ),
        'parallax' => array(
        'home_id' => 16515,
          'title'   => 'Parallax',
          'cat'     => 'simple'
      ),
        'sidebar-boxed' => array(
        'home_id' => 16529,
          'title'   => 'Sidebar Boxed',
          'cat'     => 'simple'
      ),
        'coming-soon' => array(
        'home_id' => 16666,
          'title'   => 'Coming Soon',
          'cat'     => 'simple'
      ),
        'cosmetics' => array(
        'home_id' => 17238,
          'title'   => 'Cosmetics',
          'cat'     => 'simple'
      ),
        'kids' => array(
        'home_id' => 17235,
          'title'   => 'Kids',
          'cat'     => 'simple'
      ),
        'flowers' => array(
        'home_id' => 17482,
          'title'   => 'Flowers',
          'cat'     => 'simple'
      ),
        'furniture' => array(
        'home_id' => 17237,
          'title'   => 'Furniture',
          'cat'     => 'simple'
      ),
        'jewellery' => array(
        'home_id' => 17479,
          'title'   => 'Jewellery',
          'cat'     => 'simple'
      ),
        'bags' => array(
        'home_id' => 17671,
          'title'   => 'Bags',
          'cat'     => 'simple'
      ),
        'animals' => array(
        'home_id' => 18701,
          'title'   => 'Animals',
          'cat'     => 'simple'
      ),
        'man' => array(
        'home_id' => 17817,
          'title'   => 'Man',
          'cat'     => 'simple'
      ),
        'handtools' => array(
        'home_id' => 19245,
          'title'   => 'Handtools',
          'cat'     => 'simple'
      ),
        'shoes' => array(
        'home_id' => 20505,
          'title'   => 'Shoes',
          'cat'     => 'simple'
      ),
        'handmade' => array(
        'home_id' => 20681,
          'title'   => 'Handmade',
          'cat'     => 'simple'
      ),
        'watches' => array(
        'home_id' => 19274,
          'title'   => 'Watches',
          'cat'     => 'simple'
      ),
        'coffee' => array(
        'home_id' => 19566,
          'title'   => 'coffee',
          'cat'     => 'simple'
      ),
        ));
    }
}

if(!function_exists('et_filter_option_tree_settings')) {
    function et_filter_option_tree_settings( $settings ) {
        $theme_defaults = array(
            /*'activecol' => array(
                'default' => '#cda85c',
            ),*/
            'activecol' => array(
                'default' => '#e5534c',
            ),
            'top_bar' => array(
                'default' => 0,
            ),
            'header_type' => array(
                'default' => 2,
            ),
            'breadcrumb_type' => array(
                'default' => '3',
            ),
            'breadcrumb_bg' => array(
                'default' => get_template_directory_uri() . '/images/breadcrumbs1.jpg',
            ),
            'footer_type' => array(
                'default' => 1,
            ),
            'sidebar_hidden' => array(
                'default' => 0,
            ),
            'promo_popup' => array(
                'default' => 0,
            ),
        );
        foreach ($settings as $index => $option) {
            if( isset( $theme_defaults[$option['id']] ) ) {
                $settings[$index] = wp_parse_args( $theme_defaults[$option['id']], $settings[$index] );
            }
        }

        return $settings;
    }

    add_filter( 'et_options_tree_settings', 'et_filter_option_tree_settings', 10, 1 );
}


if(!function_exists('et_get_color_selectors')) {
    function et_get_color_selectors() {
$selectors = array();

$selectors['main_font'] = '
h1,
h2,
h3,
h4,
h5,
h6,
h3.underlined,
.title-alt,
.menu >li a,
.header-type-8 .menu-wrapper .languages-area .lang_sel_list_horizontal a,
.header-type-8 .menu-wrapper .widget_currency_sel_widget ul.wcml_currency_switcher li,
.header-type-10 .menu-wrapper .languages-area .lang_sel_list_horizontal a,
.header-type-10 .menu-wrapper .widget_currency_sel_widget ul.wcml_currency_switcher li,
.shopping-container .small-h,
.order-list .media-heading,
.btn,
.button,
.wishlist_table .add_to_cart.button,
.review,
.products-grid .product-title,
.products-list .product .product-details .product-title,
.out-stock .wr-c,
.product-title,
.added-text,
.widget_layered_nav li a,
.widget_layered_nav li .count,
.widget_layered_nav_filters ul li a,
.blog-post-list .media-heading,
.date-event,
.read-more,
.teaser-box h3,
.widget-title,
.footer-top .title,
.product_list_widget .media-heading a,
.alert-message,
.main-footer h5,
.main-footer .vc_separator,
.main-footer .widget-title,
.address-company,
.page-heading .title,
.post h2,
.share-post .share-title,
.related-posts .title,
.comment-reply-title,
.control-label,
.widget_categories a,
.latest-post-list .media-heading a,
.later-product-list .media-heading a,
.tab-content .comments-list .media-heading a,
.woocommerce-product-rating .woocommerce-review-link,
.comment-form-rating label,
.product_meta,
.product-navigation .next-product .hide-info span,
.product-navigation .prev-product .hide-info span,
.meta-title,
.categories-mask span.more,
.recentCarousel .slide-item .caption h3,
.recentCarousel .slide-item .caption h2,
.simple-list strong,
.amount-text,
.amount-text .slider-amount,
.custom-checkbox a,
.custom-checkbox .count,
.toggle-block .toggle-element > a,
.toggle-block .panel-body ul a,
.shop-table .table-bordered td.product-name a,
.coupon input[type="text"],
.shop_table.wishlist_table td.product-name,
.cust-checkbox a,
.shop_table tr > td,
.shop_table td.product-name,
.payment_methods li label,
form .form-row label,
.widget_nav_menu li a,
.header-type-12 .shopping-container .shopping-cart-widget .shop-text,
.mobile-nav-heading,
.mobile-nav .links li a,
.et-mobile-menu li a,
.register-link .register-popup,
.register-link .login-popup,
.login-link .register-popup,
.login-link .login-popup,
.register-link .register-popup label,
.register-link .login-popup label,
.login-link .register-popup label,
.login-link .login-popup label,
.active-filters li a,
.product-categories >li >a,
.product-categories >li >ul.children li >a,
.emodal .emodal-text .btn,
#bbpress-forums .bbp-forum-title,
#bbpress-forums .bbp-topic-title > a,
#bbpress-forums .bbp-reply-title > a,
#bbpress-forums li.bbp-header,
#bbpress-forums li.bbp-footer,
.filter-title,
.medium-coast,
.big-coast,
.count-p .count-number,
.footer-product a,
.price,
.small-coast,
.blog-post-list .media-heading a,
.author-info .media-heading,
.comments-list .media-heading a,
.comments-list .media-heading,
.comment-reply-link,
.later-product-list .small-coast,
.product-information .woocommerce-price-suffix,
.quantity input[type="text"],
.product-navigation .next-product .hide-info span.price,
.product-navigation .prev-product .hide-info span.price,
table.variations td label,
.tabs .tab-title,
.etheme_widget_qr_code .widget-title,
.project-navigation .next-project .hide-info span,
.project-navigation .prev-project .hide-info span,
.project-navigation .next-project .hide-info span.price,
.project-navigation .prev-project .hide-info span.price,
.pagination-cubic li a,
.pagination-cubic li span.page-numbers.current,
.toggle-block.bordered .toggle-element > a,
.shop-table thead tr th,
.xlarge-coast,
.address .btn,
.step-nav li,
.xmedium-coast,
.cart-subtotal th,
.shipping th,
.order-total th,
.step-title,
.bel-title,
.lookbook-share,
.tabs.accordion .tab-title,
.register-link .register-popup .popup-title span,
.register-link .login-popup .popup-title span,
.login-link .register-popup .popup-title span,
.login-link .login-popup .popup-title span,
.show-quickly
';


$selectors['active_color'] = '
a:hover, 
a:focus,
a.active,
p.active,
em.active,
li.active,
strong.active,
span.active,
span.active a,
h1.active,
h2.active,
h3.active,
h4.active,
h5.active,
h6.active,
h1.active a,
h2.active a,
h3.active a,
h4.active a,
h5.active a,
h6.active a,
.color-main,
ins,
.product-information .out-of-stock,
.languages-area .widget_currency_sel_widget ul.wcml_currency_switcher li:hover,
.menu > li > a:hover,
.menu .nav-sublist-dropdown ul > li.menu-item-has-children:hover:after,
.title-banner .small-h,
.header-vertical-enable .page-wrapper .header-type-vertical .header-search a .fa-search, 
.header-vertical-enable .page-wrapper .header-type-vertical2 .header-search a .fa-search
.header-type-7 .menu-wrapper .menu >li >a:hover,
.header-type-10 .menu-wrapper .navbar-collapse .menu-main-container .menu >li > a:hover,
.big-coast,
.big-coast:hover,
.big-coast:focus,
.reset-filter,
.carousel-area li.active a,
.carousel-area li a:hover,
.filter-wrap .view-switcher .switchToGrid:hover,
.filter-wrap .view-switcher .switchToList:hover,
.products-page-cats a,
.read-more:hover,
.et-twitter-slider .et-tweet a,
.product_list_widget .small-coast .amount,
.default-link,
.default-colored,
.twitter-list li a,
.copyright-1 .textwidget .active,
.breadcrumbs li a,
.comment-reply-link,
.later-product-list .small-coast,
.product-categories.with-accordion ul.children li a:hover,
.product-categories >li >ul.children li.current-cat >a,
.product-categories >li >ul.children > li.current-cat >a+span,
.product_meta >span span,
.product_meta a,
.product-navigation .next-product .hide-info span.price,
.product-navigation .prev-product .hide-info span.price,
table.variations .reset_variations,
.products-tabs .tab-title.opened,
.categories-mask span,
.product-category:hover .categories-mask span.more,
.project-navigation .next-project .hide-info span,
.project-navigation .prev-project .hide-info span,
.caption .zmedium-h a,
.ship-title,
.mailto-company,
.blog-post .zmedium-h a,
.post-default .zmedium-h a,
.before-checkout-form .showlogin,
.before-checkout-form .showcoupon,
.cta-block .active,
.list li:before,
.pricing-table ul li.row-price,
.pricing-table.style3 ul li.row-price,
.pricing-table.style3 ul li.row-price sub,
.tabs.accordion .tab-title:hover,
.tabs.accordion .tab-title:focus,
.left-titles a:hover,
.tab-title-left:hover,
.team-member .member-details h5,
.plus:after,
.minus:after,
.header-type-12 .header-search a:hover,
.et-mobile-menu li > ul > li a:active,
.mobile-nav-heading a:hover,
.mobile-nav ul.wcml_currency_switcher li:hover,
.mobile-nav #lang_sel_list a:hover,
.mobile-nav .menu-social-icons li.active a,
.mobile-nav .links li a:hover,
.et-mobile-menu li a:hover,
.et-mobile-menu li .open-child:hover,
.et-mobile-menu.line-items li.active a,
.register-link .register-popup .popup-terms a,
.register-link .login-popup .popup-terms a,
.login-link .register-popup .popup-terms a,
.login-link .login-popup .popup-terms a,
.product-categories >li >ul.children li >a:hover,
.product-categories >li >ul.children li.current-cat >a,
.product-categories >li.current-cat,
.product-categories >li.current-cat a,
.product-categories >li.current-cat span,
.product-categories >li span:hover,
.product-categories.categories-accordion ul.children li a:hover,
.portfolio-descr .posted-in,
.menu .nav-sublist-dropdown ul li a:hover,
.show-quickly:hover,
.menu >li.current-menu-item >a,
.menu >li.current_page_ancestor >a,
.widget_nav_menu .menu-shortcodes-container .menu > li.current-menu-item > a,
.widget_nav_menu .menu-shortcodes-container .menu > li.current-menu-item > a:hover,
.languages-area .lang_sel_list_horizontal a:hover,
.menu .nav-sublist-dropdown ul > li.current-menu-item >a,
.product-information .out-stock-wrapper .out-stock .wr-c,
.menu .menu-full-width .nav-sublist-dropdown ul >li.menu-item-has-children .nav-sublist ul li a:hover,
.header-type-2.slider-overlap .header .menu > li > a:hover,
.page-heading .breadcrumbs,
.bc-type-3 a:hover,
.bc-type-4 a:hover,
.bc-type-5 a:hover,
.bc-type-6 a:hover,
.back-history:hover:before,
.testimonial-info .testimonial-author .url a,
.product-image-wrapper.hover-effect-mask .hover-mask .mask-content .product-title a:hover,
.header-type-10 .menu-wrapper .languages li a:hover,
.header-type-10 .menu-wrapper .currency li a:hover,
.widget_nav_menu li.current-menu-item a:before,
.header-type-3.slider-overlap .header .menu > li > a:hover,
.et-tooltip >div a:hover, .et-tooltip >div .price,
.black-white-category .product-category .categories-mask span.more,
.etheme_widget_brands li a strong,
.main-footer-1 .blog-post-list .media-heading a:hover,
.category-1 .widget_nav_menu li .sub-menu a:hover,
.sidebar-widget .tagcloud a:hover,
.church-hover .icon_list_icon:hover i,
.tabs .tab-title:hover,
footer .address-company a.white-text,
.blog-post-list .media-heading a:hover,
.footer-top-2 .product_list_widget li .media-heading a:hover,
.tagcloud a:hover,
.product_list_widget .media-heading a:hover,
.menu .menu-full-width .nav-sublist-dropdown ul > li.menu-item-has-children .nav-sublist ul li.current-menu-item a,
.header-vertical-enable .page-wrapper .header-type-vertical .header-search a .fa-search, 
.header-vertical-enable .page-wrapper .header-type-vertical2 .header-search a .fa-search,
.main-footer-1 .container .hidden-tooltip i:hover,
.date-event .number,
.list-unstyled a:hover,
.back-history:hover, .back-history:focus,
.portfolio-descr a,
.products-tabs .wpb_tabs_nav li.ui-state-active a,
.date-event .number

';

// important
$selectors['active_color_important'] = '
.header-vertical-enable .shopping-container a:hover,
.header-vertical-enable .header-search a:hover,
.header-vertical-enable .container .menu >li >a:hover,
.products-tabs .tab-title.opened:hover,
.header-vertical-enable .container .menu >li.current-menu-item >a,
.header-vertical-enable .page-wrapper .container .menu .nav-sublist-dropdown ul >li.menu-item-has-children .nav-sublist ul li a:hover,
.header-vertical-enable .page-wrapper .container .menu .menu-full-width .nav-sublist-dropdown ul >li >a:hover,
.header-vertical-enable .page-wrapper .container .menu .nav-sublist-dropdown ul >li.menu-item-has-children .nav-sublist ul >li.current-menu-item >a,
.header-vertical-enable .page-wrapper .container .menu .nav-sublist-dropdown ul >li.menu-item-has-children .nav-sublist ul li a:hover,
.slid-btn.active:hover

';

// Price COLOR!
$selectors['pricecolor'] = '
';

$selectors['active_bg'] = '
hr.active,
.btn.filled.active,
.header-type-9 .top-bar,
.shopping-container .btn.border-grey:hover,
.bottom-btn .btn.btn-black:hover,
#searchModal .large-h:after,
#searchModal .btn-black,
.details-tools .btn-black:hover,
.product-information .cart button[type="submit"]:hover,
.all-fontAwesome .fa-hover a:hover,
.all-fontAwesome .fa-hover a:hover span,
.header-type-12 .shopping-container,
.portfolio-filters li .btn.active,
.progress-bar > div,
.wp-picture .zoom >i, 
.swiper-slide .zoom >i, 
.portfolio-image .zoom >i, 
.thumbnails-x .zoom >i, 
.teaser_grid_container .post-thumb .zoom >i,
.teaser-box h3:after,
.mc4wp-form input[type="submit"],
.ui-slider .ui-slider-handle,
.et-tooltip:hover,
.btn-active,
.rev_slider_wrapper .type-label-2,
.menu-social-icons.larger li a:hover, .menu-social-icons.larger li a:focus,
.ui-slider .ui-slider-handle:hover,
.category-1 .widget_product_categories .widget-title,
.category-1 .widget_product_categories .widgettitle,
.category-1 .widget_nav_menu .widget-title,
.menu-social-icons.larger.white li a:hover,
.type-label-2,
.btn.filled:hover, .btn.filled:focus,
.widget_shopping_cart .bottom-btn a:hover,
.horizontal-break-alt:after,
.price_slider_wrapper .price_slider_amount button:hover,
.btn.btn-black:hover,
.etheme_widget_search .button:hover,
input[type=submit]:hover,
.project-navigation .prev-project a:hover,
.project-navigation .next-project a:hover,
.button:hover,
.mfp-close:hover,
.mfp-close:focus,
.tabs.accordion .tab-title:before,
#searchModal .btn-black:hover,
.toggle-block.bordered .toggle-element > a:before,
.place-order .button:hover,
.cart-bag .ico-sum,
.cart-bag .ico-sum:after,
input[type=submit]:focus,
.button:focus,
#order_review .place-order .button,
.slider-active-button:hover,
.slider-active-button.filled

';
$selectors['active_bg_important'] = '
.active-hover .top-icon:hover .aio-icon,
.active-hover .left-icon:hover .aio-icon,
.project-navigation .next-project:hover,
.project-navigation .prev-project:hover,
.active-hover-icon .aio-icon:hover
';
$selectors['active_border'] = '
.btn.filled.active,
.btn.filled.active.medium,
.bottom-btn .btn.btn-black:hover,
.details-tools .btn-black:hover,
a.list-group-item.active,
a.list-group-item.active:hover,
a.list-group-item.active:focus,
.shopping-container .btn.border-grey:hover,
.btn-active,
.category-1 .widget_product_categories,
.category-1 .widget_nav_menu,
.main-footer-1 .blog-post-list li .date-event,
.sidebar-widget .tagcloud a:hover,
.dotted-menu-link a:hover,
.header-type-3.slider-overlap .header .menu > li.dotted-menu-link > a:hover,
.header-vertical-enable .page-wrapper .header-type-vertical .container .menu > li.dotted-menu-link > a,
.btn.filled:hover, .btn.filled:focus,
.btn.btn-black:hover,
.etheme_widget_search .button:hover,
.project-navigation .prev-project a:hover,
.project-navigation .next-project a:hover,
.button:hover,
.project-navigation .next-project:hover a, 
.project-navigation .prev-project:hover a,
.tagcloud a:hover,
.slid-btn.active:hover,
.date-event .number,
.cart-bag .ico-sum:before,
.tp-caption .slider-active-button.btn:hover, 
.tp-caption .slider-active-button.btn.filled
';


$selectors['darken_color'] = '
';

$selectors['darken_bg'] = '
';

$selectors['darken_border'] = '
';
        return $selectors;
    }
}
if(!function_exists('et_get_active_color')) {
    function et_get_active_color() {
        return apply_filters('et_get_active_color', '#e5534c');
    }
}