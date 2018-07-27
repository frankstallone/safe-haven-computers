<!doctype html>
<!-- paulirish.com/2008/conditional-stylesheets-vs-css-hacks-answer-neither/ -->
<!--[if lt IE 7]> <html class="no-js ie6 oldie lt-ie9 lt-ie8 lt-ie7" lang="en"> <![endif]-->
<!--[if IE 7]>    <html class="no-js ie7 oldie lt-ie9 lt-ie8" lang="en"> <![endif]-->
<!--[if IE 8]>    <html class="no-js ie8 oldie lt-ie9" lang="en"> <![endif]-->
<!--[if gt IE 8]><!--> <html class="no-js" lang="en"> <!--<![endif]-->
<head>
	<meta charset="<?php bloginfo('charset'); ?>">
	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
	<meta name="format-detection" content="telephone=no" />

	<title><?php wp_title('|', true, 'right'); bloginfo('name'); ?></title>

	<!-- Mobile viewport optimized: j.mp/bplateviewport -->
    <meta name="viewport" content="width=device-width, initial-scale=1">

	<!-- Favicon and Feed -->
	<!--<link rel="shortcut icon" type="image/png" href="<?php // echo get_template_directory_uri(); ?>/favicon.ico">-->
	<link rel="alternate" type="application/rss+xml" title="<?php bloginfo('name'); ?> Feed" href="<?php echo home_url(); ?>/feed/">
  <script src="https://use.typekit.net/gwr8hje.js"></script>
  <script>try{Typekit.load({ async: true });}catch(e){}</script>

	<?php wp_head(); ?>

	<!-- Blog post's Featured Image as .marketing-area background -->
	<?php
	if ( is_singular() && has_post_thumbnail( $post->ID )) :
	   $thumb_id = get_post_meta($post->ID, '_thumbnail_id', true);
	   $thumb_url = wp_get_attachment_url($thumb_id); ?>
	<style type="text/css">
		.marketing-area { background: transparent url('<?php echo $thumb_url ;?>') no-repeat center top; background-size: cover; }
	</style>
	<?php endif;
	?>
</head>

<body <?php body_class(); ?>>
<!--[if lt IE 9]>
<link rel="stylesheet" href="<?php echo get_stylesheet_directory_uri(); ?>/css/ie8.css" />
<![endif]-->

	<header>
		<div class="contain-to-grid fixed">
			<!-- Starting the Top-Bar -->
			<nav class="top-bar">
			    <ul class="title-area">
			        <li class="name">
		                <h1 class="hide-for-small"><a href="<?php echo esc_url( home_url( '/' ) ); ?>" title="<?php echo esc_attr( get_bloginfo( 'name', 'display' ) ); ?>" rel="home"><?php bloginfo( 'name' ); ?></a></h1>
			        	<h1 class="show-for-small"><a href="<?php echo esc_url( home_url( '/' ) ); ?>" title="<?php echo esc_attr( get_bloginfo( 'name', 'display' ) ); ?>" rel="home">SHC</a></h1>
			        </li>
					<!-- Remove the class "menu-icon" to get rid of menu icon. Take out "Menu" to just have icon alone -->
					<li class="toggle-topbar menu-icon"><a href="#"><span></span></a></li>
			    </ul>
			    <section class="top-bar-section">
			    <?php
			        wp_nav_menu( array(
			            'theme_location' => 'primary',
			            'container' => false,
			            'depth' => 0,
			            'items_wrap' => '<ul class="right">%3$s</ul>',
			            'fallback_cb' => 'reverie_menu_fallback', // workaround to show a message to set up a menu
			            'walker' => new reverie_walker( array(
			                'in_top_bar' => true,
			                'item_type' => 'li'
			            ) ),
			        ) );
			    ?>
			    </section><!-- /top-bar-section -->
			</nav><!-- top-bar -->
		</div>
	</header>
    <div class="marketing-area">
        <div class="row">
            <div class="small-12 large-7 columns">
                <div class="boxed box-blue">
                    <h2>Your mobile IT department.</h2>
                </div>
            </div><!-- /small-12 large-7 columns -->
            <div class="small-8 large-5 columns">
                <div class="call-now box-green text-right">
                    <h3>Call Today: <a href="tel:+19082521940">908-252-1940</a></h3>
                </div>
            </div>
        </div>
    </div><!-- /marketing-area -->
	<div class="wrapper">
        <article class="who-we-are">
            <div class="row">