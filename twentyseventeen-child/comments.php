<?php
/**
 * The template for displaying comments in theme child
 *
 * This is the template that displays the area of the page that contains both the current comments
 * and the comment form.
 *
 * Author: Amal Amrani
 * Author URI: http://amrani.es/
 *
 * 
 */

/*
 * If the current post is protected by a password and
 * the visitor has not yet entered the password we will
 * return early without loading the comments.
 */
if ( post_password_required() ) {
	return;
}
?>

<!--modified comments-->
<div id="comments" class="comments-area">

	<?php
	// You can start editing here -- including this comment!
	if ( have_comments() ) : ?>
		<h2 class="comments-title">
			<?php
			$comments_number = get_comments_number();
			if ( '1' === $comments_number ) {
				/* translators: %s: post title */
				printf( _x( 'One Reply to &ldquo;%s&rdquo;', 'comments title', 'twenty-seventeen-child' ), get_the_title() );
			} else {
				printf(
					/* translators: 1: number of comments, 2: post title */
					_nx(
						'%1$s Reply to &ldquo;%2$s&rdquo;',
						'%1$s Replies to &ldquo;%2$s&rdquo;',
						$comments_number,
						'comments title',
						'twenty-seventeen-child'
					),
					number_format_i18n( $comments_number ),
					get_the_title()
				);
			}
			?>
		</h2>

		<ol class="comment-list">
			<?php
				wp_list_comments( array(
					'avatar_size' => 200,
					'style'       => 'ol',
					'short_ping'  => true,					
					'reply_text'  => twentyseventeen_get_svg( array( 'icon' => 'mail-reply' ) ) . __( 'Reply', 'twenty-seventeen-child' ),
					'callback' => twenty_seventeen_child_comment, // function defined in functions.php to show avatar or user name letter and color
				) );

				
			?>
		</ol>

		<?php the_comments_pagination( array(
			'prev_text' => twentyseventeen_get_svg( array( 'icon' => 'arrow-left' ) ) . '<span class="screen-reader-text">' . __( 'Previous', 'twenty-seventeen-child' ) . '</span>',
			'next_text' => '<span class="screen-reader-text">' . __( 'Next', 'twenty-seventeen-child' ) . '</span>' . twentyseventeen_get_svg( array( 'icon' => 'arrow-right' ) ),
		) );

	endif; // Check for have_comments().

	// If comments are closed and there are comments, let's leave a little note, shall we?
	if ( ! comments_open() && get_comments_number() && post_type_supports( get_post_type(), 'comments' ) ) : ?>

		<p class="no-comments"><?php _e( 'Comments are closed.', 'twenty-seventeen-child' ); ?></p>
	<?php
	endif;

	comment_form();
	?>

	
</div><!-- #comments -->
