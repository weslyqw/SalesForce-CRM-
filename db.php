<?php 
    $servername = "localhost";
    $username = "alpahsco_whatcon";
    $password = "123456";
    $dbname = "alpahsco_whatconverts";
    
    // Create connection
    $conn = new mysqli($servername, 
        $username, $password, $dbname);
    
    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " 
            . $conn->connect_error);
    }
    
    $client_name =  $_REQUEST['name'];
    $client_email = $_REQUEST['email'];
    $description = $_REQUEST['description'];
    $final_priority = "";

    if (!empty($_REQUEST['priority_a'])){
        $final_priority = $_REQUEST['priority_a'];
    } elseif (!empty($_REQUEST['priority_b'])) {
        $final_priority = $_REQUEST['priority_b'];
    } elseif (!empty($_REQUEST['priority_c'])) {
        $final_priority = $_REQUEST['priority_c'];
    } 
    
    $sql = "INSERT INTO clients (Client_Name,Email,Website_Priority,Client_Description)  VALUES ('$client_name', 
      '$client_email','$final_priority','$description')";
    
    
    if ($conn->query($sql) === TRUE) {
?> 
        <html>
            <div class="sakura-falling"></div> 
            <div class="wrap">
                <div class="title">
                    <h1>Awesome </h1>
                    <h1>Thanks for signing up</h1> 
                    <h1>We will keep intouch</h1>

                </div>
            </div>
        </html>
        <style>
            <?php 
                include 'css/response.css';
            ?>
        </style>
<?php
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
?>
<script src="https://code.jquery.com/jquery-3.4.1.js" integrity="sha256-WpOohJOqMqqyKL9FccASB9O0KwACQJpFTUBLTYOVvVU=" crossorigin="anonymous"></script>   

<script>

$.ajax({
   url: 'salesforceContactWebhook.php',
   type: 'POST',
   dataType : 'json',
   success : function (result) 
   {
      console.log (result) // Here, you need to use response by PHP file.
   },
   error : function () 
   {
      console.log ('error')
   }

});

</script>

<script>

$.ajax({
   url: 'salesforceOpportunityWebhook.php',
   type: 'POST',
   dataType : 'json',
   success : function (result) 
   {
      console.log (result) // Here, you need to use response by PHP file.
   },
   error : function () 
   {
      console.log ('error')
   }

});

</script>