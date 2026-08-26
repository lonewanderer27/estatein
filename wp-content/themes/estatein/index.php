<?php
/**
 * Fallback template (required by WordPress) for any request that doesn't
 * match a more specific template — e.g. the posts page, search results.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();
?>
<main class="container py-5">
	<?php if ( have_posts() ) : ?>
		<?php while ( have_posts() ) : the_post(); ?>
			<article <?php post_class( 'mb-5' ); ?>>
				<h1 class="h2"><a class="text-decoration-none" href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h1>
				<div class="est-text-muted"><?php the_excerpt(); ?></div>
			</article>
		<?php endwhile; ?>

		<?php the_posts_navigation(); ?>
	<?php else : ?>
		<p><?php esc_html_e( 'Nothing found.', 'estatein' ); ?></p>
	<?php endif; ?>
</main>
<?php
get_footer();
