<?php
/**
 * Template Name: clea Test slider Evènements
 * !!! this template must be copied in the template directory of a child theme !!!
 */

$do_not_duplicate = array();

get_header(); // Loads the header.php template. ?>

<?php get_sidebar( 'primary' ); // Loads the sidebar-primary.php template. ?>

<!-- Check that the event calendar plugin is active. --->

<?php
/** Detect plugin. */

	if ( is_plugin_active( 'the-events-calendar/the-events-calendar.php' ) ) {
		//plugin is activated
		echo "<p>tout va bien</p>" ; 
	} else { 
		echo "<p>Le plugin the-events-calendar n'est pas activé </p>" ; 
	} ?>

<!-- Create the right query --->

	<!--  begin sidebar-before-front-page area -->
	
	<?php get_sidebar( 'mairie-before-front-page' ); // Loads the sidebar-before-front-page.php template. ?>
	
	<!--  end  sidebar-before-front-page area -->

<section class="query-events">		
	<?php
	 
	$query = new WP_Query( array( 'post_type' => 'tribe_events' ) );
	 
	if ( $query->have_posts() ) : ?>
		<?php while ( $query->have_posts() ) : $query->the_post(); ?>	
			<div class="entry">
				<h2 class="title"><?php the_title(); ?></h2>
				<?php // ignore the more
				global $more;    // Declare global $more (before the loop).
				$more = 1;       // Set (inside the loop) to display all content, including text below more.
				the_content(); ?>
			</div>
		<?php endwhile; wp_reset_postdata(); ?>
		<!-- show pagination here -->
	<?php else : ?>
		<!-- show 404 error here -->
	<?php endif; ?>	
</section>	



<section class="slider-events">	
</section>





<?php get_footer(); // Loads the footer.php template. ?>