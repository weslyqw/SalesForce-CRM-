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
 
    // From URL to get webpage contents.
    // $url = "https://app.whatconverts.com/api/v1/";
     
    // Initialize a CURL session.
    // $ch = curl_init();
    // curl -u 97649-a5b2cdb272d759b4:6f55f2dd386a95a0de9a161308eae1bb    
    // // Return Page contents.
    // curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
     
    // //grab URL and pass it to the variable.
    // curl_setopt($ch, CURLOPT_URL, $url);
     
    // $result = curl_exec($ch);
     
    // echo $result;
     
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
?>