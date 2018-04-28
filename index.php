<!DOCTYPE html>
<html>
    <head>
        <title>Chat - Customer Module</title>
        <link type="text/css" rel="stylesheet" href="style.css" />
        <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.0.10/css/all.css" integrity="sha384-+d0P83n9kaQMCwj8F4RJB66tzIwOKmrdb46+porD/OvrJ+37WqIM7UoBtwHO6Nlg" crossorigin="anonymous">
        <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.0/css/bootstrap.min.css" integrity="sha384-9gVQ4dYFwwWSjIDZnLEWnxCjeSWFphJiwGPXr1jddIhOegiu1FwO5qRGvFXOdJZ4" crossorigin="anonymous">
    </head>
    <body>
    
        <?php
            
            session_start();
            
            if(!isset($_SESSION['name'])){
                loginForm();
            }
            else{
        ?>

                <div class="container m-4 p-2 bg-info">
                    <div class="row">
                        <div class="col-sm-9">
                            <p class="m-2">Hello, <b><?php echo $_SESSION['name']; ?></b></p>
                        </div>
                        <div class="col-sm-3">
                            <a id="exit" href="#" class="m-2 text-danger float-right"><i class="far fa-times-circle fa-2x"></i></a>
                        </div>
                    </div>
                    
                    <div class="container-fluid bg-light p-2 mb-2">
                        <div class="container border border-info p-1">
                            <div id="chatbox">
                                <?php
                                    
                                    if(file_exists("logs/log.html") && filesize("logs/log.html") > 0){
                                        $handle = fopen("logs/log.html", "r");
                                        $contents = fread($handle, filesize("logs/log.html"));
                                        fclose($handle);
                                        
                                        echo $contents;
                                    }
                                    
                                ?>
                            </div>
                        </div>
                    </div>
                    
                    <div class="container-fluid mt-2 p-2">
                        <form name="message" action="" class="form-inline">
                            <div class="form-group col-md-10 bg-light border rounded">
                                <input name="usermsg" type="text" id="usermsg" size="63" placeholder="type a message..." class="form-control-plaintext" />
                            </div>
                            
                            <div class="form-group col-md-2">
                                <button name="submitmsg" type="submit"  id="submitmsg" class="col btn ml-auto">Send <i class="fas fa-share"></i></button>
                            </div>
                            
                        </form>
                    </div>
                </div>

                <script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.3/jquery.min.js"></script>
                <script type="text/javascript">
                    
                    // jQuery

                    $(document).ready(function(){

                        //If user wants to end session
                        $("#exit").click(function(){
                            
                            var exit = confirm("Are you sure you want to quit?");
                            
                            if(exit == true){
                                window.location = 'index.php?logout=true';
                            }		
                        });

                        //If user clicks the 'Send' button on the form to submit the message:
                        $("#submitmsg").click(function(e){	
                            e.preventDefault();
                            var clientmsg = $("#usermsg").val();
                            $.post("post.php", {text: clientmsg});				
                            $("#usermsg").attr("value", "");
                            return false;
                        });

                        setInterval (loadLog, 2500);	
                        
                        //Load the file containing the chat log
                        function loadLog(){		

                            var oldscrollHeight = $("#chatbox").attr("scrollHeight") - 20;

                            $.ajax({
                                url: "logs/log.html",
                                cache: false,
                                success: function(html){		
                                    $("#chatbox").html(html); //Insert chat log into the #chatbox div				
                                    
                                    //Auto-scroll			
                                    var newscrollHeight = $("#chatbox").attr("scrollHeight") - 20; //Scroll height after the request
                                    if(newscrollHeight > oldscrollHeight){
                                        $("#chatbox").animate({ scrollTop: newscrollHeight }, 'normal'); //Autoscroll to bottom of div
				                },
                            });
                        }
                    });
                </script>
                
        <?php
            }
        ?>

    </body>
</html>

<?php
 
    function loginForm(){
        echo'
        <div class="container p-4">

            <div id="loginform" class="m-2 p-2 border border-info">

                <form action="index.php" method="post">
                    
                    <div class="container bg-info text-center p-2">
                        
                        <h3 class="text-light">Please enter your name to chat</h3>
                        
                        <div class="form-row">
                            
                            <div class="form-group col-md-9">
                                <input type="text" placeholder="Enter your name here" name="name" id="name" class="form-control"/>
                            </div>
                            
                            <div class="form-group col-md-3">
                                <input type="submit" name="enter" id="enter" class="btn col" value="Enter Chat" />
                            </div>
                            
                        </div>
                        
                    </div>
                    
                </form>
            </div>
        </div>
        ';
    }
?>

<?php

if( isset($_POST['enter']) ){
    
    if($_POST['name'] != ""){
        $_SESSION['name'] = stripslashes(htmlspecialchars($_POST['name']));
    }
    else{
        echo '<span class="error">Please type in a name</span>';
    }
    header("Location: index.php"); //Redirect the user
}

if(isset($_GET['logout'])){ 

    //Simple exit message
    $fp = fopen("logs/log.html", 'a');
    
    fwrite($fp, "<div class='msgln'><i>User ". $_SESSION['name'] ." has left the chat session.</i><br></div>");
    fclose($fp);
    
    session_destroy();
    header("Location: index.php"); //Redirect the user
}
?>
