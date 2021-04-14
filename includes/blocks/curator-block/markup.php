<?php
/**
 * Curator block markup.
 *
 * @package SophiWP
 */

?>
<ul class="sophi-curator-block" id="<?php printf( 'sophi-curator-%1$s-%2$s', esc_attr( $attributes['pageName'] ), esc_attr( $attributes['widgetName'] ) ); ?>">
	<?php foreach ( $curated_posts as $curated_post ) : ?>
		<li class="curated-item">
			<?php if ( ! empty( $attributes['displayFeaturedImage'] ) && has_post_thumbnail( $curated_post ) ) : ?>
				<?php if ( ! empty( $attributes['addLinkToFeaturedImage'] ) ) : ?>
					<a href="<?php echo esc_url( get_permalink( $curated_post ) ); ?>" title="<?php echo esc_attr( get_the_title( $curated_post ) ); ?>">
						<?php echo get_the_post_thumbnail( $curated_post ); ?>
					</a>
				<?php else : ?>
					<?php echo get_the_post_thumbnail( $curated_post ); ?>
				<?php endif; ?>
			<?php endif; ?>
			<a href="<?php echo esc_url( get_permalink( $curated_post ) ); ?>" title="<?php echo esc_attr( get_the_title( $curated_post ) ); ?>">
				<?php echo esc_html( get_the_title( $curated_post ) ); ?>
			</a>
			<?php if ( ! empty( $attributes['displayAuthor'] ) ) : ?>
				<?php
				$author_display_name = get_the_author_meta( 'display_name', $curated_post->post_author );

				/* translators: byline. %s: current author. */
				$byline = sprintf( __( 'by %s', 'sophi-wp' ), $author_display_name );
				?>

				<?php if ( ! empty( $author_display_name ) ) : ?>
					<div class="post-author">
						<?php echo esc_html( $byline ); ?>
					</div>
				<?php endif; ?>
			<?php endif; ?>

			<?php if ( ! empty( $attributes['displayPostDate'] ) ) : ?>
				<time datetime="<?php echo esc_attr( get_the_date( 'c', $curated_post ) ); ?>" class="post-date">
					<?php echo esc_html( get_the_date( '', $curated_post ) ); ?>
				</time>
			<?php endif; ?>

			<?php if ( ! empty( $attributes['displayPostExcept'] ) ) : ?>
				<p class="post-excerpt">
					<?php echo wp_kses_post( get_the_excerpt( $curated_post ) ); ?>
				</p>
			<?php endif; ?>
		</li>
	<?php endforeach; ?>
</ul>
