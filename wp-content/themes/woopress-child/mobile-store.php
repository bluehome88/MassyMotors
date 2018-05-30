<?php
/**
 * The template used for displaying pages under About Us
 *
 * @package WordPress
 * @subpackage Hi-Lo Food Stores
 * Template Name: Stores Mobile
 */

global $more;	$more = 0; 
//$catinclude = 'post_category=recipes';
$temp = $wp_query;
$wp_query= null;
$wp_query = new WP_Query();
$wp_query->query("post_type=post&category_name=". $_GET['cat_name'] . "&showposts=25&orderby=title&order=ASC&paged=".$paged);

//$query = new WP_Query( array( 'category_name' => 'promotions' ) );

$data = array();
$i = 0;
while ( have_posts() ) : the_post();
   ob_start();
	the_permalink();
	$link = ob_get_contents();
   ob_end_clean();
	
   if ( has_post_thumbnail() ) { // check if the post has a Post Thumbnail assigned to it.
      ob_start();
         the_post_thumbnail_url();
	 $img = 'http://massystorestt.com'.ob_get_contents();
      ob_end_clean();
   } else {
	$img = '';
   }	
   ob_start();
      the_title();
      $title = ob_get_contents();
   ob_end_clean();

   ob_start();
      echo the_excerpt();
      $headline = ob_get_contents();
   ob_end_clean();

   ob_start();
      the_content();
      $excerpt = ob_get_contents();
   ob_end_clean();
	
   $data[$i]['title'] = $title;
   $data[$i]['headline'] = $headline;
   $data[$i]['excerpt'] = $excerpt;
   $data[$i]['image'] = $img;
   $data[$i]['link'] = $link;
	
$i++;
endwhile;

$callback = $_GET['callback'];

print "$callback(".json_encode($data).")";