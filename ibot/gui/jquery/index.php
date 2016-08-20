<?php

  $botId = 1;
  $convo_id = 1; // user id will be here
  $bot_id = 1;

  // Experimental code
  $base_URL  = 'http://' . $_SERVER['HTTP_HOST'];                                   // set domain name for the script
  $this_path = str_replace(DIRECTORY_SEPARATOR, '/', realpath(dirname(__FILE__)));  // The current location of this file, normalized to use forward slashes
  $this_path = str_replace($_SERVER['DOCUMENT_ROOT'], $base_URL, $this_path);       // transform it from a file path to a URL
  $this_path = str_replace('var/www/release0/', '', $this_path);
  $url = str_replace('gui/jquery', 'chatbot/conversation_start.php', $this_path);   // and set it to the correct script location

?>
<!DOCTYPE html>
<html>
  <head>
    <link rel="stylesheet" type="text/css" href="main.css" media="all" />
    <style type="text/css">
      h3 {
        text-align: center;
      }
      hr {
        width: 80%;
        color: green;
        margin-left: 0;
      }

      .user_name {
        color: rgb(16, 45, 178);
      }
      .bot_name {
        color: rgb(204, 0, 0);
      }
      .leftside {
        text-align: right;
        float: left;
        width: 48%;
      }
      .rightside {
        text-align: left;
        float: right;
        width: 48%;
      }
      .centerthis {
        width: 90%;
      }
      #chatdiv {
        margin-top: 20px;
        text-align: center;
        width: 100%;
      }
      p.center {
        text-align: center;
      }
      hr.center {
        margin: 0 auto;
      }

    </style>
  </head>
  <body>
    <div class="" >
        <div id="chatwin" style="height:600px; overflow: hidden; bottom:0px;"></div>
      <form method="post" style="position: relative; bottom: 0px;" name="talkform" id="talkform" action="index.php">
        <div id="chatdiv">
          <label for="submit">Say:</label>
          <input type="text" name="say" id="say" size="60"/>
          <input type="submit" name="submit" id="submit" class="submit"  value="say" />
          <input type="hidden" name="convo_id" id="convo_id" value="<?php echo $convo_id;?>" />
          <input type="hidden" name="bot_id" id="bot_id" value="<?php echo $bot_id;?>" />
          <input type="hidden" name="format" id="format" value="json" />
        </div>
      </form>
    </div>
    <script type="text/javascript" src="jquery-1.9.1.min.js"></script>
    <script type="text/javascript" >
     $(document).ready(function() {
      // put all your jQuery goodness in here.
        $('#talkform').submit(function(e) {
          e.preventDefault();
          var user = $('#say').val();
          $('.usersay').text(user);
          var formdata = $("#talkform").serialize();
          $('#say').val('');
          $('#say').focus();
          $.get('<?php echo $url ?>', formdata, function(data){
            if(!data.botsay) data.botsay = 'Sorry, I have nothing to say here.';
            var b = data.botsay;
            if (b.indexOf('[img]') >= 0) {
              b = showImg(b);
            }
            if (b.indexOf('[link') >= 0) {
              b = makeLink(b);
            }
            var usersay = data.usersay;
            if (user != usersay) {
              $('.usersay').text(usersay);
            }
            $('.botsay').html(b);

            $('#chatwin').append("<div><label class='username'>You:</label> " + usersay + "</div>");
            $('#chatwin').append("<div><label class='botname'>" + data.bot_name + ":</label> " + b + "</div>");

          }, 'json').fail(function(xhr, textStatus, errorThrown){
            //$('#urlwarning').html("Something went wrong! Error = " + errorThrown);
          });
          return false;
        });
      });
      function showImg(input) {
        var regEx = /\[img\](.*?)\[\/img\]/;
        var repl = '<br><a href="$1" target="_blank"><img src="$1" alt="$1" width="150" /></a>';
        var out = input.replace(regEx, repl);
        console.log('out = ' + out);
        return out
      }
      function makeLink(input) {
        var regEx = /\[link=(.*?)\](.*?)\[\/link\]/;
        var repl = '<a href="$1" target="_blank">$2</a>';
        var out = input.replace(regEx, repl);
        console.log('out = ' + out);
        return out;
      }
    </script>
  </body>
</html>
