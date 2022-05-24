<?php
    require_once './salesforceMainClass.php';

    $salesForce = new SalesForce();
    $data = json_decode(file_get_contents('https://echo-webhook.herokuapp.com/OpportunityWebhook'), true);

    try {
            $salesForceAccountEmail = $data['new'][0]['Description'];
            $opportunityAmount = $data['new'][0]['Amount'];
            $stageName = $data['new'][0]['StageName'];
            $salesForceAccessToken = $salesForce->getSalesForceAccessToken();

            $wcLeadId = $salesForce->getSalesForceContactByAccountId($salesForceAccountEmail,$salesForceAccessToken);
            $salesForce->setWcQuoteValue($wcLeadId,$opportunityAmount);
            if ($stageName == "Closed Won"){
                $result=$salesForce->setWcSalesVlaue($wcLeadId,$opportunityAmount);
            }
        } catch (\Throwable $th) {
            echo "Error / " . $th->getMessage();
        } 
?>