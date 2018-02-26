<?php
/**
 * Header for utsic-blue theme. Displays <head> section, banner, and #main.
 *
 * Mike Thicke
 * June 14, 2012
 */
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo( 'charset' ); ?>" />
    <meta name="viewport" content="width=device-width" />
    <title><?php
	/*
	 * Source: twentyeleven theme
	 * Print the <title> tag based on what is being viewed.
	 */
	global $page, $paged;

	wp_title( '|', true, 'right' );

	// Add the blog name.
	bloginfo( 'name' );

	?>
    </title>
    <link rel="profile" href="http://gmpg.org/xfn/11" />
    <link rel="stylesheet" type="text/css" media="all" href="<?php bloginfo( 'stylesheet_url' ); ?>" />
    <link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>" />
    <?php wp_head(); ?> 
</head>

<body <?php body_class(); ?>>
<div id="page">
    <div id="header-small">
        <h1 id="site-title"><span><a href="<?php echo esc_url ( home_url( '/' ) ); ?>" title="<?php echo esc_attr( get_bloginfo( 'name', 'display' ) ); ?>" rel="home"><?php bloginfo( 'name' ); ?></a></span></h1>
        <h2 id="site-description"><?php bloginfo( 'description' ); ?></h2>
        <a href="<?php echo esc_url( home_url( '/' ) ); ?>">
            <img id="header-image" src="<?php bloginfo('template_directory'); ?>/images/banner-small.jpg" alt="" />
        </a>
        
        <div id="nav"><!-- Source: twentyeleven theme --> 
            <h3 class="assistive-text"><?php _e( 'Main menu', 'twentyeleven' ); ?></h3>
            <?php /*  Allow screen readers / text browsers to skip the navigation menu and get right to the good stuff. */ ?>
            <div class="skip-link"><a class="assistive-text" href="#content" title="<?php esc_attr_e( 'Skip to primary content', 'twentyeleven' ); ?>"><?php _e( 'Skip to primary content', 'twentyeleven' ); ?></a></div>
            <div class="skip-link"><a class="assistive-text" href="#secondary" title="<?php esc_attr_e( 'Skip to secondary content', 'twentyeleven' ); ?>"><?php _e( 'Skip to secondary content', 'twentyeleven' ); ?></a></div>
            <?php /* Our navigation menu.  If one isn't filled out, wp_nav_menu falls back to wp_page_menu. The menu assiged to the primary position is the one used. If none is assigned, the menu with the lowest ID is used. */ ?>
            <?php wp_nav_menu( array( 'theme_location' => 'primary' ) ); ?>    
        </div><!-- #nav --> 
    
    </div><!-- #header -->
    
    <div id="main">
