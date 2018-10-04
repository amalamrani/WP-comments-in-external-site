<?php
/** 
 * Twenty Seventeen child functions  to define functions to the theme child
 *
 * Author: Amal Amrani
 * Author URI: http://amrani.es/
 *
 * @link https://developer.wordpress.org/themes/basics/theme-functions/
 *
 * @package WordPress
 * @subpackage Twenty_Seventeen
 * @since 1.0
 */



function my_twenty_seventeen_child_theme_enqueue_styles() {

    $parent_style = 'parent-style'; // This is 'twentyseventeen-style' for the Twenty Seventeen theme.

    wp_enqueue_style( $parent_style, get_template_directory_uri() . '/style.css' );
    wp_enqueue_style( 'child-style',
        get_stylesheet_directory_uri() . '/style.css',
        array( $parent_style ),
        wp_get_theme()->get('Version')
    );
}
add_action( 'wp_enqueue_scripts', 'my_twenty_seventeen_child_theme_enqueue_styles' );




// Disable HTML into comments. filter comments to convert html to text plain.
add_filter('comment_text', 'wp_filter_nohtml_kses');
add_filter('comment_text_rss', 'wp_filter_nohtml_kses');
add_filter('comment_excerpt', 'wp_filter_nohtml_kses');



/**
* function callback in wp_list_comments to custom date to be showed and avatar
* to change comment avatar or show the first user name letter and color assigned
* @see wp_list_comments()
*
* @param WP_Comment $comment Comment to display.
* @param int        $depth   Depth of the current comment.
* @param array      $args    An array of arguments.
*/
function twenty_seventeen_child_comment($comment, $depth, $args){

 
    $tag = ( 'div' === $args['style'] ) ? 'div' : 'li';
  ?>
  <<?php echo $tag; ?> id="comment-<?php comment_ID(); ?>" <?php comment_class( empty( $args['has_children'] ) ? '' : 'parent', $comment ); ?>>
  <article id="div-comment-<?php comment_ID(); ?>" class="comment-body">
    <footer class="comment-meta">
      <div class="comment-author vcard">
            <?php 

            // gravatar user          
            $user_gravatar =md5( strtolower( trim( $comment->comment_author_email ) ) );
            $user_gravatar ="https://www.gravatar.com/avatar/".$user_gravatar;
           
                  // asign first letter and color for user                      
                  $color_and_letter = twenty_seventeen_child_assign_color_and_letter_for_user_non_has_avatar($comment->comment_author);
                  echo '<div class="avatar_color" style="background: '.$color_and_letter['color'].';">';
                      echo'<img class="avatarImg" src="'.$user_gravatar.'?d=blank" />';
                      echo '<span class="letter-name">'.$color_and_letter['letter'].'</span>';
                  echo'</div>';                              
              // translators: %s: comment author link 
              echo '<div class="author-comment-details">';
              printf( __( '%s <span class="says">says:</span>' ),
                sprintf( '<b class="fn">%s</b>', get_comment_author_link( $comment ) )
              );
              echo'</div>';
            ?>
      </div><!-- .comment-author -->


      <div class="comment-metadata">
        
          <time datetime="<?php comment_time( 'c' ); ?>">
            <?php
            // show date in human date format like 2 month ago
            printf( _x( '%s ago', '%s = human-readable time difference', 'twenty-seventeen-child' ), human_time_diff( get_comment_time( 'U' ), current_time( 'timestamp' ) ) );
            ?>
          </time>
        <!--</a>-->
        <?php edit_comment_link( __( 'Edit' ), '<span class="edit-link">', '</span>' ); ?>
      </div><!-- .comment-metadata -->

      <?php if ( '0' == $comment->comment_approved ) : ?>
        <p class="comment-awaiting-moderation"><?php _e( 'Your comment is awaiting moderation.' ); ?></p>
      <?php endif; ?>
    </footer><!-- .comment-meta -->

    <div class="comment-content">
      <?php comment_text(); ?>
    </div><!-- .comment-content -->
    <?php
      
        
        //add max_depth to the array and give it the value from above and set the depth to 1
        $default = array(
            'add_below'  => 'div-comment',
            //'respond_id' => 'respond',
            'reply_text' =>  $depth["reply_text"],//__('Reply'),
            //'login_text' => __('Log in to Reply'),
            'depth'      => 1,
            'before'     => '<div class="reply">',
            'after'      => '</div>',
            'max_depth'  => $depth["max_depth"],
            );

        comment_reply_link( $default, $commentId, $postID );     


        ?>

  </article><!-- .comment-body -->
  <?php


}


/*
  function to assign color and first letter of user name if he doesn't has avatar
*/
function twenty_seventeen_child_assign_color_and_letter_for_user_non_has_avatar($userName){

  
  $color = 'BlueViolet';  
  $letter = substr($userName, 0, 1 );

  // switch [A...Z]
  switch ($letter) {

  case ($letter == 'a' || $letter == 'A'):
        $color = 'Aqua';
        break;
    case ($letter == 'b' || $letter == 'B'):
        $color = 'Black';
        break;
    case ($letter == 'c' || $letter == 'C'):
        $color = 'Crimson'; //Chartreuse
        break;
    case ($letter == 'd' || $letter == 'D'):
        $color = 'DeepSkyBlue';
        break;

    case ($letter == 'e' || $letter == 'E'):
        $color = 'Gainsboro';
        break;

    case ($letter == 'f' || $letter == 'F'):
        $color = 'Fuchsia';
        break;

    case ($letter == 'g' || $letter == 'G'):
        $color = 'Gray';
        break;


    case ($letter == 'h' || $letter == 'H'):
        $color = 'HotPink';
        break;
    case ($letter == 'i' || $letter == 'I'):
        $color = 'Indigo';
        break;
    case ($letter == 'j' || $letter == 'J'):
        $color = 'IndianRed';
        break;
    case ($letter == 'k' || $letter == 'K'):
        $color = 'Khaki';
        break;
    case ($letter == 'l' || $letter == 'L'):
        $color = 'LightSeaGreen';
        break;
    case ($letter == 'm' || $letter == 'M'):
        $color = 'MediumPurple';
        break;
    case ($letter == 'n' || $letter == 'N'):
        $color = 'NavajoWhite';
        break;
    case ($letter == 'o' || $letter == 'O'):
        $color = 'Olive';
        break;
    case ($letter == 'p' || $letter == 'P'):
        $color = 'PaleTurquoise';
        break;
    case ($letter == 'q' || $letter == 'Q'):
        $color = 'PeachPuff';
        break;
    case ($letter == 'r' || $letter == 'R'):
        $color = 'RoyalBlue';
        break;
    case ($letter == 's' || $letter == 'S'):
        $color = 'Salmon';
        break;
    case ($letter == 't' || $letter == 'T'):
        $color = 'Teal';
        break;
    case ($letter == 'u' || $letter == 'U'):
        $color = 'Turquoise';
        break;
    case ($letter == 'v' || $letter == 'V'):
        $color = 'Violet';
        break;
    case ($letter == 'w' || $letter == 'W'):
        $color = 'Wheat';
        break;
    case ($letter == 'y' || $letter == 'Y'):
        $color = 'YellowGreen';
        break;
    case ($letter == 'z' || $letter == 'Z'):
        $color = 'SlateGray';
        break;
    
    default:
      //$color = 'BlueViolet';
    break;
    }


  return array( 'color'=> $color, 'letter'=> $letter ); 

}

?>