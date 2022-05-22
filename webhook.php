<?php
    require_once './salesforceAuth.php';
    // $headers = getallheaders();

    $salesForce = new SalesForce();
    $data = json_decode(file_get_contents('php://input'), true);
    $dataFile = fopen("data.txt", "w");

    try {
            $leadId = intval($data["lead_id"]);
            $email = $data["email_address"];
            $getSalesForceToken = $salesForce->getSalesForceAccessToken();
            $salesForceSearch = $salesForce->getSalesForceLeadSearch("12345sdfsf6@me.com",$leadId,$getSalesForceToken,$url);
            
            echo "Sales Force Token: " . $getSalesForceToken;

        } catch (\Throwable $th) {
            echo "Error / " . $th->getMessage();
        } 
?>