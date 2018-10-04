<?php
/**
 * Template Name: template get comments by id
 *
 * This is the template for displaying WordPress page/post comments 
 * Author: Amal Amrani
 * Author URI: http://amrani.es/
 *
 * @link https://codex.wordpress.org/Template_Hierarchy
 *
 * @package WordPress
 * @subpackage Twenty_Seventeen
 * @since 1.0
 * @version 1.0
 */


header( "Content-type: application/json" );

$thePost_id = -1;

$comments_json = array('wp_page_exist' => -1,
						'wp_comments_list' => array()
					);


if (isset($_GET['IDpage'])) {

    $ID_original_page = $_GET['IDpage'];

	// get post_id from $ID_original_page in  post_meta table
	$args1 = array(
					'author_email' => '',
					'author__in' => '',
					'author__not_in' => '',
					'include_unapproved' => '',
					'fields' => '',
					'ID' => '',
					'comment__in' => '',
					'comment__not_in' => '',
					'karma' => '',
					'number' => '',
					'offset' => '',
					'orderby' => '',
					'order' => 'DESC',
					'parent' => '',
					'post_author__in' => '',
					'post_author__not_in' => '',
					'post_ID' => '', // ignored (use post_id instead)
					'post_id' => 0,//$thePost_id,//0,
					'post__in' => '',
					'post__not_in' => '',
					'post_author' => '',
					'post_name' => '',
					'post_parent' => '',
					'post_status' => 'publish',
					'post_type' => 'post',
					'status' => 'all',
					'type' => '',
				        'type__in' => '',
				        'type__not_in' => '',
					'user_id' => '',
					'search' => '',
					'count' => false,
					'meta_key' => 'id_original_page',
					'meta_value' =>  $ID_original_page,
					'meta_query' => '',
					'date_query' => null, // See WP_Date_Query
				);
		
		$posts = get_posts($args1); 
		
		if( isset($posts) && isset($posts[0]->ID) )	:

		// the page exist in WP	
		$thePost_id = $posts[0]->ID;  
		$comments_json['wp_page_exist'] = $thePost_id;	
		
		$recent_post = wp_get_recent_posts(array('post_type' => 'page','numberposts' => 1 ), OBJECT);
		
		$next_page_to_create = (int)($recent_post[0]->ID)+1;
		
					
			//Gather comments for a specific page/post 
			$comments = get_comments(array(
				'post_id' => $thePost_id,
				'status' => 'approve', //Change this to the type of comments to be displayed
				//'order' => 'ASC', by default is DESC
				//'number' => '',//Maximum number of comments to retrieve.
				
			));	


			foreach ($comments as $key => $value) {

				
				$email_author = $comments[$key]->{'comment_author_email'};
																	

				// user avatar. if he doesn'n have, return transparent image transparente; in url with param ?d=blank
				$user_gravatar =md5( strtolower( trim( $email_author ) ) );
            	$user_gravatar ="https://www.gravatar.com/avatar/".$user_gravatar."?d=blank";

            	$color_and_letter = twenty_seventeen_child_assign_color_and_letter_for_user_non_has_avatar($comments[$key]->comment_author);
            	$comments[$key]->url_avatar_email = $user_gravatar;
				$comments[$key]->{'comment_color_and_letter'} = $color_and_letter;
																				
				
				// Human format date
				$auxdate = human_time_diff(  strtotime($comments[$key]->{'comment_date_gmt'}) , current_time( 'timestamp' ) ).' '.__( 'ago', 'twentyseventeen-child' );

				//internationalized version
				//printf( _x( '%s ago', '%s = human-readable time difference', 'your-text-domain' ), human_time_diff( get_the_time( 'U' ), current_time( 'timestamp' ) ) );

				$comments[$key]->{'comment_human_date'} = $auxdate;				
				
			}

							
			$comments_aux = $comments;
			
			$comments_json['wp_comments_list'] = $comments_aux;
			$tamano_array_comentarios = count($comments_aux);

		

			echo json_encode($comments_json, JSON_FORCE_OBJECT);
			// the requested post doesn't exist yet in wp
		    // send and email to the admin as: 
		    //wp_mail ('xxxxx@xxx.xx', "Test notice by email", "The requested page $ ID_original_page has not yet been created in WP");
								
														
							
		else:

			echo json_encode($comments_json, JSON_FORCE_OBJECT);//json_encode($comments_json);
		    // no se ha creado aún la página solicitada. enviar aviso al administrador?? 
		  //  echo '<input type="hidden" id="wp-page-not-created-yet" value="'.$thePost_id.'" />';
		endif; // $posts
		

} else {
    // Fallback behaviour goes here
    // si no exite isset($_GET['IDpage']
    $comments_json['wp_page_exist'] = -2;
    echo json_encode($comments_json, JSON_FORCE_OBJECT);

}

			



?>
			

		


