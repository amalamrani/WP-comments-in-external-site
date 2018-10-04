<?php
/**
 * Template Name: Insert comment in BD
 *
 * Template to insert a new comment from external site into WordPress BD using  Ajax post request
 * 
 * Author: Amal Amrani
 * Author URI: http://amrani.es/
 *
 * @link https://codex.wordpress.org/Template_Hierarchy
 *
 * @package WordPress
 * @since 1.0
 * @version 1.0
 */


header( "Content-type: application/json" );
/*
// Script  to insert a comment into WordPress table comments 
// get the params 
*/
$tokenCaptcha = false;

$objComment = array("comment"=>"", 
                    "comment_parent" => 0,
                    "name"=>"", 
                    "email"=>"",
                    "post_id"=>"",
                    );

if ( isset($_POST['jrname']) && !empty($_POST['jrname']) )   $objComment["name"] = $_POST['jrname']; 
if ( isset($_POST['jremail']) && !empty($_POST['jremail']) )   $objComment["email"] = $_POST['jremail']; 
if ( isset($_POST['post_id']) && !empty($_POST['post_id']) )   $objComment["post_id"] = $_POST['post_id']; 
if ( isset($_POST['jrcomment']) && !empty($_POST['jrcomment']) )  $objComment["comment"] = $_POST['jrcomment']; 
if ( isset($_POST['jrcomment_parent']) && !empty($_POST['jrcomment_parent']) )  $objComment["comment_parent"] = $_POST['jrcomment_parent']; 



 
if ( isset($_POST['tokenReCaptcha']) && !empty($_POST['tokenReCaptcha']) ){
    
    $tokenCaptcha = $_POST['tokenReCaptcha'];
    $response_validation = comprobartoken($tokenCaptcha);

    if ($response_validation === 'success'){
       // all ok for inserting
        $comment_approved = 1;

        if( $objComment["comment"] && $objComment["comment"] != ''){

            $author_has_total_unapproved_comment = get_comments( array(
            'author_email' => $objComment["email"],//'soynuevo@dom.com',
            'status' => 'hold'
            //'comment_author' =>'user2',
            //'post_id' => 0,// id pagina de los comentarios   
            ));

            $author_has_total_approved_comment = get_comments( array(
            'author_email' => $objComment["email"],//'soynuevo@dom.com',
            'status' => 'approve'
            //'comment_author' =>'user2',
            //'post_id' => 0,// id pagina de los comentarios   
            ));
            
            if( !count($author_has_total_approved_comment) || count($author_has_total_unapproved_comment) >0 ) $comment_approved = 0;
        }
       
        $time = current_time('mysql');

        $data = array(
            'comment_post_ID' => (int)$objComment["post_id"],//1,
            'comment_author' =>   $objComment["name"],//'admin',
            'comment_author_email' => $objComment["email"],//'admin@admin.com',
            'comment_author_url' => 'http://',
            'comment_content' =>   $objComment["comment"],//'content here',
            'comment_type' => '',
            'comment_parent' => $objComment["comment_parent"],//0,
            'user_id' => 1,
            'comment_author_IP' => '127.0.0.1',
            'comment_agent' => 'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.9.0.10) Gecko/2009042316 Firefox/3.0.10 (.NET CLR 3.5.30729)',
            'comment_date' => $time,
            'comment_approved' => $comment_approved,
        );        

        // check the insertion result
        if ( $objComment["post_id"] != ''){

            $ID_page_original = get_post_custom_values( 'id_page_scimagojr_journal' , $objComment["post_id"] );           
            
            $result_insercion = wp_insert_comment($data);

            if ( $result_insercion ){ 
                        
                // json with insertion result
                // status_inserted_comment -> -1 initial. comment not inserted yet. 0 comment inserted and pending moderation. 1 comment inserted and approved
                echo json_encode( array("result_insert_comment" => $result_insercion,
                                        "status_inserted_comment" => $comment_approved,
                                        //"autor" => $objComment["name"],
                                       // "email" => $objComment["email"],
                                        "result_insert_comment_text" => 'success'), JSON_FORCE_OBJECT);


            }
            if(!$result_insercion){
                // an error                
                // status_inserted_comment -> -1 initial. comment not inserted yet. 0 comment inserted and pending moderation. 1 comment inserted and approved
                echo json_encode( array("result_insert_comment" => -1,
                                        "status_inserted_comment" => -1,
                                       // "autor" => $objComment["name"],
                                       // "email" => $objComment["email"],
                                        "result_insert_comment_text" => 'error_al_insertar_comentario'), JSON_FORCE_OBJECT);
            }

            
        }       
    }      
    else{
        // status_inserted_comment -> -1 estado inicial. no se ha insertado comentario. 0 insertado comentario y pendiente de moderación. 1 insertado y aprobado comentario 
        echo json_encode( array("result_insert_comment" => -1,
                                "status_inserted_comment" => -1,
                               // "autor" => $objComment["name"],
                               // "email" => $objComment["email"],
                                "result_insert_comment_text" => 'error_validation_recaptcha'), JSON_FORCE_OBJECT);

    }
}   
else
     echo json_encode( array("result_insert_comment" => -1,
                                "status_inserted_comment" => -1,
                               // "autor" => $objComment["name"],
                               // "email" => $objComment["email"],
                                "result_insert_comment_text" => 'token empty'), JSON_FORCE_OBJECT);



/*

checking token google recaptcha by Curl

API Request
URL: https://www.google.com/recaptcha/api/siteverify

METHOD: POST

POST Parameter  Description
secret  Required. The shared key between your site and reCAPTCHA. 
response    Required. The user response token provided by reCAPTCHA, verifying the user on your site. $tokenCaptcha
remoteip    Optional. The user's IP address.
*/
/*The response is a JSON object:
{
  "success": true|false,
  "challenge_ts": timestamp,  // timestamp of the challenge load (ISO format yyyy-MM-dd'T'HH:mm:ssZZ)
  "hostname": string,         // the hostname of the site where the reCAPTCHA was solved
  "error-codes": [...]        // optional
}
*/
function comprobartoken($tokenCaptcha){      

    $ch = curl_init();

    curl_setopt($ch, CURLOPT_URL,"https://www.google.com/recaptcha/api/siteverify");
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS,"secret=put-here-your-secret-key&response=".$tokenCaptcha);

    // receive server response ...
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    $server_output = curl_exec ($ch);

    curl_close ($ch);
    
    
    if( $server_output["success"] ){
        return 'success';
    } 
    else { 
        return 'error_validation_recaptcha';
    }

}




?>