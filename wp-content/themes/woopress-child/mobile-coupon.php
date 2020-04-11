<?php
/**
 * The template used for displaying mobile coupons
 *
 * @package WordPress
 * @subpackage Hi-Lo Food Stores
 * Template Name: Mobile Coupons
 */

global $more;	$more = 0; 
$catinclude = 'post_category=coupons';
$temp = $wp_query;
$wp_query= null;
$wp_query = new WP_Query();
$wp_query->query("post_type=post&cat=15&showposts=15&paged=".$paged);

$data = array();
$i = 0;
while(have_posts()):the_post();
   if ( has_post_thumbnail() ) { // check if the post has a Post Thumbnail assigned to it.
     ob_start();
	the_post_thumbnail_url();
	$img = 'http://beta-massy.simplyintense.com'.ob_get_contents();
	ob_end_clean();
   } else {
	$img = '';
   }
$data[$i]['img'] = '<img src="' . $img . '" style="width:100%; height:auto;"/>';

$i++;
endwhile;

$callback = $_GET['callback'];

print "$callback(".json_encode($data).")";
