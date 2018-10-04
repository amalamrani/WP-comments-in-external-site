/** Javascript to manage WordPress comments. The AJAX requests to WP to list the post comments, and insert a new comment from external site to WordPress
*   Author: Amal Amrani
*   Author URI: http://amrani.es/
*/

// DEFINE THE TWO PAGES TO BE CALLED FOR LISTING OR INSERTING COMMENT
var url_get_comments_list = "http://yourdomain/wordpress-page-for-getting-comments/";
var url_insert_comment = "http://yourdomain/wordpress-page-to-insert-new-comment/"; 
var tokenCaptcha = "";


// VARIABLES GLOBALES
var listComments = false;
var max_comments_raiz = 0;// MAX NUMBER OF COMMENTS TO BE SHOWN// 0 = SHOW ALL COMMENTS
var max_anidamientos = 5;//level deep of nested comments
var literal_text_reply = "Reply";

var id_page_original = 1234;//;


// literales feedback
var feedback_comment_waiting_for_moderation = "Your message is waiting for moderation";



/* 
*functions to  validate RECAPTCHA
*/
function verifyCallback_render(){

  
   tokenCaptcha = document.getElementById('g-recaptcha-response').value;
   
   
}
var onloadCallback = function() {
  grecaptcha.render('html_element', {
    'sitekey' : 'xxxxxxxxxxxxxxxxxxxxxxxxxxxxxx',// you must to put here your reCaptcha sitekey
    'callback' : verifyCallback_render
  }); 
};

/**
*  function to validate email
*/
function validateEmail(mail) {
  if (/^(([^<>()[\]\.,;:\s@\"]+(\.[^<>()[\]\.,;:\s@\"]+)*)|(\".+\"))@(([^<>()[\]\.,;:\s@\"]+\.)+[^<>()[\]\.,;:\s@\"]{2,})$/i.test(mail)){
   return true;
  } 
  else return false;  
}


/**
*  function to validate comment form info 
*/

function show_messaje_validation(id_element){

  var element = document.getElementById(id_element);

  if ( element.classList.contains('nonDisplay') ){
    element.className=element.className.replace('nonDisplay','');    
    element.previousElementSibling.className +='borderRed';   
  }
}

function hide_messaje_validation(id_element){

  var element = document.getElementById(id_element);

  if ( !element.classList.contains('nonDisplay') ){
    element.className += ' nonDisplay';
    element.previousElementSibling.className = element.previousElementSibling.className.replace('borderRed','');   
  }  
}

/*
* function to request comments list from page id   
* @param status_comment status inserted comment
* -1 initial, no inserted comment yet
* 0 inserted comment and pending moderation
* 1 inserted and approved comment
*/
function callOutsideComments(str,status_comment, name, comment, comment_parent_id) {


   
  var xmlhttp = new XMLHttpRequest();
  xmlhttp.onreadystatechange = function() {
      
      if (this.readyState == 4 && this.status == 200) {
          
          var responseJson = JSON.parse(this.responseText);
          listComments = callbackResponseCommentsList(responseJson);          

          // The page exist in WP
          if ( listComments ){
                    

          // the page has 'approved comments'
          if ( Object.keys(listComments['wp_comments_list']).length > 0 ) showCommentsListGetedFromWP(listComments);
          
          var formCommentNode = document.getElementById("respond-comment-fromWP-to-external-site");
         
          listenEventReply();
           

          // WP page exist.  --> show comment box
         
          formCommentNode.className= formCommentNode.className.replace('hide','');
          
          // post_id  WP page
          document.getElementById("comment_fromWP-to-external-site_post_ID").value = listComments['wp_page_exist'];
         

         //feedback comment inserted
         if ( status_comment != -1 )  show_feedback_inserted(status_comment, name, comment, comment_parent_id);
        }   
                                        
      }
      else{
        if( this.status == 404 ){         
          var error_messaje = document.getElementById('feedback_insert_comment');
          error_messaje.innerHTML = 'The list of comments could not be retrieved. Please contact the administrator';
        }
      }
      
  };
  // IDpage         
  
  xmlhttp.open("GET", url_get_comments_list+"&IDpage=" + str, true); 
  
  xmlhttp.send();
  
}

/*
* function to listen click event in comment reply 
*/
function listenEventReply(){

  

  var elements = document.getElementsByClassName("comment-reply-link");

  var formCommentNode = document.getElementById("respond-comment-fromWP-to-external-site");
  var bt_cancel_reply = document.getElementById("cancel_reply");

  //listen event click of cancel-reply
  if( bt_cancel_reply ){ 
  bt_cancel_reply.onclick = function(e){
      
      e.preventDefault();
      
      this.className ='hide';      

      var elinitial = document.getElementById("container-form-comment");
      elinitial.appendChild(formCommentNode);
      var parent = document.getElementById("comment_fromWP-to-external-site_parent");
      parent.value = 0;

    return false;
  };
   
  }
  for( var i=0; i<elements.length; i++ ){
    
    elements[i].addEventListener("click", function (e) {
    var that = this.id;
    var element_id = that.split("reply-");
    var article_id = "div-comment-"+element_id[1];

    var parent = document.getElementById("comment_fromWP-to-external-site_parent");
    parent.value = element_id[1];
    
    var article = document.getElementById(article_id);
    article.appendChild(formCommentNode);
    
    bt_cancel_reply.className ='';   

    return false;
    });
  }  
 
}

/*
* function to show the received post comment list from WordPress 
*/
function showCommentsListGetedFromWP(listComments){

        
    var nodos_parents = pintar_comentarios_raiz(listComments,max_comments_raiz);
    
    var container_comments = document.getElementById("wp-comments-response");
    container_comments.innerHTML = nodos_parents['list_html'];
    
    

    var padres = nodos_parents['father_comments'];
    var count_anidados = 1;// el nodo raíz cuenta 1
    var add_reply = false;
    
    while( padres.length > 0 ){
                

      var childrens_become_fathers = [];
      for(p=0;p<padres.length; p++){
        
        count_anidados = 1;// el nodo raíz equivale a 1
        add_reply = false;
    
        //draw children
        var elpadre = document.getElementById("comment-"+padres[p]);
        
        var antecesor = elpadre;
        
       
        while( antecesor.parentNode.parentNode.nodeName == 'LI' && (antecesor.parentNode.parentNode.id.indexOf("comment-") > -1) ){          
          antecesor = antecesor.parentNode.parentNode;        
          count_anidados++;
        }        

        if ( count_anidados < max_anidamientos-1 ) add_reply = true;


        var hijos= get_children(padres[p],listComments,add_reply);        
        
        if( elpadre && hijos["list_children_html"] ) elpadre.appendChild(hijos["list_children_html"]);
        
        var children = childrens_become_fathers.concat(hijos["children"]);
        childrens_become_fathers = children;
    
      }
      padres = childrens_become_fathers;
     
    }

}

/*
* function to search children of parent parameter, until a node has no more children
*
*/
function get_children(father,listComments,add_reply){

  
  var hijos = [];
  var index_to_delete = [];  
  

  var olChildren = document.createElement("ul"); 
      olChildren.className += "children";

  for( var key in listComments['wp_comments_list'] ){

    if( father == listComments['wp_comments_list'][key]['comment_parent'] ){    
    
      var nodeli = document.createElement("li");    
      nodeli.setAttribute("id", "comment-"+listComments['wp_comments_list'][key]['comment_ID']);     
      
      var articlenode = document.createElement("article");
      articlenode.setAttribute("id", "div-comment-"+listComments['wp_comments_list'][key]['comment_ID']); 
      // create header comment
      var headernode = document.createElement("header");
      headernode.className += "comment-meta";
      var divMetanode = document.createElement("div");//
      divMetanode.className += "comment-author vcard";


        var elementAvatar = document.createElement("div"); 
        elementAvatar.className += "avatar_color"; 
        elementAvatar.style.background =  listComments['wp_comments_list'][key]['comment_color_and_letter']['color'];  

        var letterspan =  document.createElement("span");
        letterspan.className += "letter-name";
        
        var textLetter =  document.createTextNode(listComments['wp_comments_list'][key]['comment_color_and_letter']['letter']);   
        letterspan.appendChild(textLetter); 
        elementAvatar.appendChild(letterspan);     
    
    
        var imgAvatar = document.createElement("img");
        imgAvatar.className = "avatarImg";
        imgAvatar.src = listComments['wp_comments_list'][key]['url_avatar_email'];


      var nameAuthornode = document.createElement("span");
      nameAuthornode.className = "author-name";
      var textNameAuthor = document.createTextNode(listComments['wp_comments_list'][key]['comment_author']);

      var commentDatenode = document.createElement("div");
      commentDatenode.className += "comment-metadata";
      var spandatenode = document.createElement("span");
      var textDatenode =  document.createTextNode(listComments['wp_comments_list'][key]['comment_human_date']);//comment_date_gmt

      spandatenode.appendChild(textDatenode);
      commentDatenode.appendChild(spandatenode);
      
      nameAuthornode.appendChild(textNameAuthor);
      

     
     divMetanode.appendChild(elementAvatar);


      divMetanode.appendChild(nameAuthornode);
      divMetanode.appendChild(commentDatenode);

      headernode.appendChild(divMetanode);

      // create div body content comment      
      var divnode = document.createElement("div");
      divnode.className += "comment-content";
      var textnode = document.createTextNode(listComments['wp_comments_list'][key]['comment_content']);         
      divnode.appendChild(textnode);                              
      

      articlenode.appendChild(headernode);
      articlenode.appendChild(divnode);
      nodeli.appendChild(articlenode);                    
      olChildren.appendChild(nodeli);


      if( add_reply ){
        // create div reply message       
        var divReplynode = document.createElement("div");
        divReplynode.className += "div-reply-comment";
        var spanReply = document.createElement("span");
        spanReply.className += "comment-reply-link";
        spanReply.setAttribute("id", "reply-"+listComments['wp_comments_list'][key]['comment_ID']);
        var textReply = document.createTextNode(literal_text_reply);

        spanReply.appendChild(textReply);
        divReplynode.appendChild(spanReply);
        articlenode.appendChild(divReplynode);
        // feenback reply
        var feedbackDivNode = document.createElement("div");
        feedbackDivNode.className += "reply-item";
        feedbackDivNode.setAttribute("id", "reply-to-"+listComments['wp_comments_list'][key]['comment_ID']);
        articlenode.appendChild(feedbackDivNode);
        
      }

      
      // add a children to array. Remove it from listComments
      // Add children in inverse order. First the recent child      
      hijos.unshift(listComments['wp_comments_list'][key]['comment_ID']);
      //hijos.push(listComments['wp_comments_list'][key]['comment_ID']);
      index_to_delete.push(key);
    } 
  }

  //Remove the added children
  for( j=0; j<index_to_delete.length; j++){      
    // remove element from obj
    delete listComments['wp_comments_list'][index_to_delete[j]];       
  }   

return {"list_children_html": olChildren,
        "children" : hijos
       }
}


/*
* function to draw origin/principal nodes/comments
*/
function pintar_comentarios_raiz(listComments,max_comments_raiz){

  var nodos_raiz = '<ol>';
  var index_nodos = [];
  var father_comments = [];
  var combo = {};
  var max = 0;
  var limite = Object.keys(listComments['wp_comments_list']).length;

  
  if( max_comments_raiz != 0 ){ limite = max_comments_raiz; }

  var i=0;
  
  while( max<limite &&  (i<Object.keys(listComments['wp_comments_list']).length) ){

    // if is a comment/principal node
    if(listComments['wp_comments_list'][i]['comment_parent'] === "0"){ 
      
    max++;
    nodos_raiz += '<li id="comment-'+listComments['wp_comments_list'][i]['comment_ID']+'">'+        
        '<article id="div-comment-'+listComments['wp_comments_list'][i]['comment_ID']+'" class="comment-body">'+
          '<footer class="comment-meta">'+
            '<div class="comment-author vcard">';

               nodos_raiz += '<div class="avatar_color" style="background:'+listComments['wp_comments_list'][i]['comment_color_and_letter']['color']+';" ><img class="avatarImg" src="'+listComments['wp_comments_list'][i]['url_avatar_email']+'" /><span class="letter-name">'+listComments['wp_comments_list'][i]['comment_color_and_letter']['letter']+'</span></div>';
               nodos_raiz += '<span class="author-name">'+listComments['wp_comments_list'][i]['comment_author']+'</span>'+

             
              '<div class="comment-metadata"><span class="comment-date">'+listComments['wp_comments_list'][i]['comment_human_date']+'</span></div>'+
            '</div>'+
          '</footer>'+      
          '<div class="comment-content">'+listComments['wp_comments_list'][i]['comment_content']+'</div>'+
          '<div class="div-reply-comment"><span class="comment-reply-link" id="reply-'+listComments['wp_comments_list'][i]['comment_ID']+'">'+literal_text_reply+'</span></div>'+// añadir esta línea si no se ha alcanzado el límite de comentarios
          '<div class="reply-item" id="reply-to-'+listComments['wp_comments_list'][i]['comment_ID']+'"></div>'+
        '</article></li>';

    father_comments.push(listComments['wp_comments_list'][i]['comment_ID']);

    index_nodos.push(i);
    }
  i++;
  }
  nodos_raiz += '</ol>';
  
  for( j=0; j<index_nodos.length; j++ ){   
   var key = index_nodos[j];
   delete listComments['wp_comments_list'][key];
  }

  combo = { "list_html": nodos_raiz,
            "father_comments": father_comments
          }
  
return combo;
}

/*
* function called when ajax request is comleted. get comments list
* 
*/
function callbackResponseCommentsList(myJSON_comments){

  if ( myJSON_comments ){

    var tamano_json = Object.keys(myJSON_comments).length;
    
    if( myJSON_comments['wp_page_exist'] && myJSON_comments['wp_page_exist'] != -1 ){
      //console.log('The post exist in WP.  post_id in WP ='+myJSON_comments['wp_page_exist']);
      return myJSON_comments;
    }
    else{
      //console.log('The page/post not exist in WP yet');
      return false;
    }

  }

}

/** 
*
* function to control comment form events before sending data
*/
function addEventsListenerForm(){


      var email_comment = document.getElementById("comment-fromWP-to-external-site-email-user").value;
      
      var email_validated = false;

      var coment_content = document.getElementById("comment-fromWP-to-external-site").value;
      var nombre_autor = document.getElementById("comment-fromWP-to-external-site-name-user").value;
            

      var author_validated = false;
      var coment_content_validated = false;

      
      
      if( email_comment && email_comment.length > 0 )  email_validated = validateEmail(email_comment);
      
      if ( !email_validated )  show_messaje_validation('email-validation');
      else hide_messaje_validation('email-validation');

      if( coment_content.length == 0 || coment_content.replace(/\s/g, "") == '' ) show_messaje_validation('textarea-validation');
      else{
       hide_messaje_validation('textarea-validation');
       coment_content_validated = true;

      }

      if( nombre_autor.length == 0 || nombre_autor.replace(/\s/g, "") == '' ) show_messaje_validation('nombre-validation');
      else{
        hide_messaje_validation('nombre-validation');
        author_validated = true;
      } 
                  
      if(!tokenCaptcha) show_messaje_validation('captcha-validation');
      else  hide_messaje_validation('captcha-validation');      

      

      // send
      if( email_validated && author_validated && coment_content_validated && tokenCaptcha != ""){          
          check_comment_added_to_send(coment_content,nombre_autor,email_comment);          
      }
    return false;   
}


/**
* function to prepare and send insertion request to  WP
*/
function check_comment_added_to_send(comment, name, email){

  var post_id = document.getElementById("comment_fromWP-to-external-site_post_ID").value;
  var comment_parent = document.getElementById("comment_fromWP-to-external-site_parent").value;
  
  
  var xmlhttp = new XMLHttpRequest();
  var url = url_insert_comment; 

  
  var params = "jrname="+name+"&jremail="+email+"&post_id="+post_id+"&jrcomment_parent="+comment_parent+"&jrcomment="+comment+"&tokenReCaptcha="+tokenCaptcha;
  

   //  put form comment in initial position
   var containerForm = document.getElementById("container-form-comment");
   var form = document.getElementById("respond-comment-fromWP-to-external-site");
   containerForm.appendChild(form);

  /* 
  xmlhttp.readyState:

  0 UNSENT  Client has been created. open() not called yet.
  1 OPENED  open() has been called.
  2 HEADERS_RECEIVED  send() has been called, and headers and status are available.
  3 LOADING Downloading; responseText holds partial data.
  4 DONE  The operation is complete.
  */
  
  xmlhttp.onreadystatechange = function() {//Call a function when the state changes.
    if(xmlhttp.readyState == 4 && xmlhttp.status == 200) {
                
     
      if( xmlhttp.responseText ){
        var response_inserted_comment_json = JSON.parse(xmlhttp.responseText);        
                  
        grecaptcha.reset();// reset the recaptcha.  The token is for just one use
        
        if( response_inserted_comment_json['result_insert_comment_text'] === 'success'){
         
          var status_inserted = response_inserted_comment_json['status_inserted_comment'];
          
          // refresh comment list with the new insertion. Add status comment insertion to fuction refresh.
          callOutsideComments(id_page_original,status_inserted, name, comment, comment_parent);


        }
        else{
          // can't insert the comment
          
          var feedback_error_insercion = document.getElementById('feedback_insert_comment');
          feedback_error_insercion.innerHTML = 'No se ha podido insertar el comentario, por favor, inténtelo de nuevo y si el problema persiste, póngase en contacto con el administrador';
        }  
      }
    }
    else{
     
      if ( xmlhttp.status == 404 ){
          var error_messaje = document.getElementById('feedback_insert_comment');
          error_messaje.innerHTML = 'No se ha podido recuperar registrar el comentario. Por favor, inténtelo de nuevo y si el problema persiste, póngase en contacto con el administrador';
          
      } 
    }
  }
  xmlhttp.open("POST", url, true);

  //Send the proper header information along with the request
  xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
   
  xmlhttp.send(params);
  
  
  
  
}

/*
* show feedback comment status moderation
* 
*/

function show_feedback_inserted(status_comment, name, comment, comment_parent_id){


  if ( name && comment && comment_parent_id && status_comment === 0 ){
    
    var el = document.getElementById("reply-to-"+comment_parent_id);
    if ( el ){
     el.innerHTML = name+", your comment:"+comment+" is waiting for moderation<br />";
     
    }
    
  } 
  if( comment_parent_id == "0" && status_comment === 0 ){  
    var feedback_comment = document.getElementById("feedback_insert_comment");
    if ( feedback_comment ){

      feedback_comment.innerHTML = name+", your comment:"+comment+" <br />is waiting for moderation";
     

    }
  }

  // clean textarea comment
  var textarea_comment = document.getElementById('comment-fromWP-to-external-site');
  textarea_comment.value = '';

}

