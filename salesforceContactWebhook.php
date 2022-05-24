<?php
    require_once './salesforceMainClass.php';
    
    $salesForce = new SalesForce();
    $data = json_decode(file_get_contents('https://echo-webhook.herokuapp.com/testWebhookTrigger'), true);

    try {
        var_dump($data);
        $leadId = ($data['new'][0]['Description']);
        $wcQuotable = "yes";
        $getSalesForceToken = $salesForce->getSalesForceAccessToken();
        $salesForce->setWcQuotable($wcQuotable,$leadId);
        } catch (\Throwable $th) {
            echo "Error / " . $th->getMessage();
        } 
?>