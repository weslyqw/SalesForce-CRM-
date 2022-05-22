<?php
    require_once './salesforceMainClass.php';
    
    $salesForce = new SalesForce();
    $data = json_decode(file_get_contents('https://echo-webhook.herokuapp.com/testWebhookTrigger'), true);

    try {
        $leadId = ($data['new'][0]['Description']);
        $addational_field = "Connected";
        $salesForceSearch = $salesForce->setWcSalesForceField($leadId,$addational_field);
        } catch (\Throwable $th) {
            echo "Error / " . $th->getMessage();
        } 
?>