# Integrate WordPress comments into external site
# Author Amal Amrani 
# Author URI: [Amal Amrani Website link](http://amrani.es/ "Amal Amrani website")

This code/project explains the way how you can to integrate the WordPress comments into an external and independent site.
You can manage the comments from your WordPress installation to moderate them; approve, reject, delete, etc. 
In your external site you can show all the approved comments, the comment box for the new comment post and captcha for controlling spam before sending the comment. 


I've chosen for this example the latest theme twentyseventeen to which i've created a theme child, twentyseventeen-child. 
**You must to put it in themes folder under twentyseventeen theme and active it from admin panel appearance->themes**

The folder **twentyseventeen-child** contains:

The file *comment.php* redefine comment list with 'twenty_seventeen_child_comment' callback function

The file *functions.php* add the new functions to the theme child

*style.css* to define the new styles for theme child

*page-getcommentsbyid.php* template to assign to page created for getting WordPress comments.
*page-insert-comment.php* template to assign to page created to insert new comment from external site into WordPress BD

*screenshot.jpg* for the theme child


The **js** folder contains:

*control-comments.js* the file that makes ajax requests to WordPress, to get the comments list to be shown and to insert a new comment


The **css** folder contains:

*comments-styles.css* define styles of comments list and comment box in the external site


The **img** folder contains:

*reply.png* image to reply comment
*screencapture.png* of example result



*The comment entries must have been previously created. Each entry will correspond to a page of the external site that will host comments.*

 
In order to include the google reCaptcha, you need two keys:
  
*1-* 'sitekey' ​: 'xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx'
The sitekey is added in Javascript file as the example below

The validation of token provided by google reCaptcha is done on the server side using curl and needs the secret key of the website
*2-*  secret​=xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx 
This key go in template (included in theme child)  *page-insert-comment*

You can find this line in page-onsert-comment.php:
`curl_setopt($ch, CURLOPT_POSTFIELDS,"secret=put-here-your-secret-key&response=".$tokenCaptcha);`

You can access to [Google reCaptcha link](https://www.google.com/recaptcha/ "Google reCaptcha") for getting your Google reCaptcha keys 


You can define the levels deep of nested comments from WordPress settings and from *Javascript file* of your external site as it's shown below in the example

IMPORTANT! 
You must create two pages from WordPress panel:

**1-** page for getting WordPress comments. This page must have the template page-getcommentsbyid. Located in theme twentyseventeen-child 
**2-** page to insert new comment from external site into WordPress BD. This page must have the template page-insert-comment. Located in theme twentyseventeen-child 

and define them in **control-comments.js**:

``` [Javascript]
var url_get_comments_list = "http://the url of page 1"; // page assosiated to template get comments by id
var url_insert_comment = "the url of page 2"; // page assosiated to template Insert comment in BD

// VARIABLES 
var max_anidamientos = 5;//level deep of nested comments. Fixed in same comment level deep in WordPress
var id_page_original = 1234;// the param id of original page

var onloadCallback = function() {
  grecaptcha.render('html_element', {
    'sitekey' : 'xxxxxxxxxxxxxxxxxxxxxxxxxxxxxx',// you must to put here your reCaptcha sitekey
    'callback' : verifyCallback_render
  }); 
};
```


You can see the index.php example page where there are:

*The HTML fragment example, where the comment list will be shown and comment form to be able to send a new comments*


``` [HTML]
<div id="wp-comments-response"><div class="lds-dual-ring"></div><div class="LoadingCommentsLabel">Loading comments...</div></div>
<div id="container-form-comment">
  <div id="respond-comment-fromWP-to-external-site"  class="hide">
    <div id="feedback_insert_comment"></div>
    <h3>Leave a comment</h3> 
        <form id="form-submit-comment-fromWP-to-external-site" > 
          <p>
            <label for="comment-fromWP-to-external-site-name-user">Name</label>
            <input id="comment-fromWP-to-external-site-name-user" name="comment-fromWP-to-external-site-name-user" type="text" size="20"/>
            <span class="validation-form-messaje nonDisplay" id="nombre-validation">* Required</span>
          </p>
          <p>
            <label for="comment-fromWP-to-external-site-email-user">Email<br /><span class="emailnote">(will not be published)</span></label>
            <input id="comment-fromWP-to-external-site-email-user" name="comment-fromWP-to-external-site-email-user" type="text" size="30"/>
            <span class="validation-form-messaje nonDisplay" id="email-validation">* Required</span>
          </p>
          <p>         
            <textarea id="comment-fromWP-to-external-site" name="comment-fromWP-to-external-site" cols="45" rows="8" aria-required="true"></textarea>
            <span class="validation-form-messaje nonDisplay" id="textarea-validation">* Required</span>
          </p>
          <p class="form-submit"> 
            <div id="html_element"></div>  
            <span class="validation-form-messaje nonDisplay" id="captcha-validation" >* Required</span>     
            <button type="button" id="cancel_reply" class="hide" >Cancelar</button>
            <input name="submit-comment-fromWP-to-external-site" type="button" id="submit-comment-fromWP-to-external-site" class="submit"  onclick="addEventsListenerForm()" value="Submit">  
            <input type="hidden" name="comment_fromWP-to-external-site_post_ID" value="" id="comment_fromWP-to-external-site_post_ID">
            <input type="hidden" name="comment_fromWP-to-external-site_parent" id="comment_fromWP-to-external-site_parent" value="0">
          </p>
        </form>
        **<script src="https://www.google.com/recaptcha/api.js?onload=onloadCallback&render=explicit"
          async defer>
        </script>**
  </div>
</div>
```

You can also see the function Javascript called on load the page, to request WordPress comments list as:

`<body onload="callOutsideComments(parseInt(id_page_original),-1)" >`



Bellow you have an image showing the result

[reference to image](img/screencapture.png)

