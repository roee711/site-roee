<?php
/**
 * The template for displaying archive pages.
 *
 * @link https://codex.wordpress.org/Template_Hierarchy
 *
 * @package marin
 */

get_header();
?>
<section id="post-section" class="post-section av-py-default">
	<div class="av-container">
		<div class="av-columns-area">
			<?php
			$marin_archive_blog_pages_layout = get_theme_mod( 'marin_archive_blog_pages_layout', 'marin_right_sidebar' );
			if ( 'marin_left_sidebar' === $marin_archive_blog_pages_layout ) :
				get_sidebar();
				endif;
			?>
			<div id="av-primary-content" class="<?php echo esc_attr( marin_post_layout() ); ?>">
				<?php
                $index =0;
                $count =wp_count_posts('project');
                $count =(property_exists($count,'publish'))?$count->publish:0;

                if ( have_posts() ) : ?>


                    <?php
					while ( have_posts() ) :
						the_post();
                        if($index % 3 ==0):?>
                            <div class="flex-item">
                        <?php endif;
							get_template_part( 'template-parts/content/content', 'project' );
                        if( ($index+1) %3 ==0 || $index+1==$count):?>
                            </div>
                        <?php endif;
                        $index++;
					endwhile;
					?>

					<!-- Pagination -->
					<?php
						$posts_pagination = get_the_posts_pagination(
							array(
								'mid_size'  => 1,
								'prev_text' => '<i class="fa fa-angle-double-left"></i>',
								'next_text' => '<i class="fa fa-angle-double-right"></i>',
							)
						);
						echo wp_kses_post( $posts_pagination );
					?>
					<!-- Pagination -->	

				<?php else : ?>

					<?php get_template_part( 'template-parts/content/content', 'none' ); ?>

				<?php endif; ?>
			</div>
			<?php if ( 'marin_right_sidebar' === $marin_archive_blog_pages_layout ) : ?>
				<?php get_sidebar(); ?>
			<?php endif; ?>
		</div>
	</div>
</section>
<?php get_footer(); ?>
