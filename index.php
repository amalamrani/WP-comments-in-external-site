<?php
/* 
Example to integrate WordPress Comments in an external site

*/
?>
<!DOCTYPE html>
<html  lang="es">
<head>
<title>Example call outside WordPress comments in my website</title>
<meta charset="utf-8"> 

<link rel="stylesheet" type="text/css" href="css/comments-styles.css">
<script src="js/control-comments.js"></script>
<script>


</script>

</head>

<body onload="callOutsideComments(parseInt(id_page_original),-1)" > 





<!-- SECTION COMMENTS -->
<h1 class="center">Showing WordPress comments in an external website</h1>

<div id="wp-comments-response"><div class="lds-dual-ring"></div><div class="LoadingCommentsLabel">Loading comments...</div></div>

<div id="container-form-comment">
  <div id="respond-comment-fromWP-to-external-site"  class="hide">
    <div id="feedback_insert_comment"></div>
    <h3>Leave a comment</h3>
    <span class="emailnote">Your email address will not be published</span>
        
        <form id="form-submit-comment-fromWP-to-external-site" >

          
          <p>
            <label for="comment-fromWP-to-external-site-name-user">Name</label>
            <input id="comment-fromWP-to-external-site-name-user" name="comment-fromWP-to-external-site-name-user" type="text" size="20"/>
            <span class="validation-form-messaje nonDisplay" id="nombre-validation">* Required</span>
          </p>
          <p>
            <label for="comment-fromWP-to-external-site-email-user">Email<br /></label>
            <input id="comment-fromWP-to-external-site-email-user" name="comment-fromWP-to-external-site-email-user" type="text" size="30"/>
            <span class="validation-form-messaje nonDisplay" id="email-validation">* Required</span>
          </p>
          <p>
            <label for="comment-fromWP-to-external-site-comment-user">Comment<br></label>
            <textarea id="comment-fromWP-to-external-site" name="comment-fromWP-to-external-site" cols="45" rows="8" aria-required="true"></textarea>
            <span class="validation-form-messaje nonDisplay" id="textarea-validation">* Required</span>
          </p>

          
          <p class="form-submit"> 
            <div id="html_element"></div>  
            <span class="validation-form-messaje nonDisplay" id="captcha-validation" >* Required</span>  
             
            <p class="submit-buttons">
            <button type="button" id="cancel_reply" class="hide" >Cancel</button>
            <input name="submit-comment-fromWP-to-external-site" type="button" id="submit-comment-fromWP-to-external-site" class="submit"  onclick="addEventsListenerForm()" value="Submit" />
            </p>
            
            <input type="hidden" name="comment_fromWP-to-external-site_post_ID" value="" id="comment_fromWP-to-external-site_post_ID"/>
            <input type="hidden" name="comment_fromWP-to-external-site_parent" id="comment_fromWP-to-external-site_parent" value="0" />
          </p>
         
         
        </form>
        <div class="div-captcha">
        <script src="https://www.google.com/recaptcha/api.js?onload=onloadCallback&render=explicit"
          async defer>
        </script>
        </div>
  </div>
</div>

<!-- END SECTION COMMENTS-->




</body>
</html>