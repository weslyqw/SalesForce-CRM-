<?php
    require_once './salesforceMainClass.php';

    $salesForce = new SalesForce();
    $data = file_get_contents('https://echo-webhook.herokuapp.com/leadTrigger');
    print_r($data);
    $dataFile = fopen("datasf.txt", "w");

    try {
            var_dump($data);
            
            fwrite($dataFile,$data);
        } catch (\Throwable $th) {
            echo "Error / " . $th->getMessage();
        } 
?>