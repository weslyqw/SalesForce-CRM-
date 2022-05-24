<?php
    require_once './salesforceMainClass.php';
    // $headers = getallheaders();

    $salesForce = new SalesForce();
    $data = json_decode(file_get_contents('php://input'), true);

    try {
            $leadId = intval($data["lead_id"]);
            $email = $data["email_address"];
            $getSalesForceToken = $salesForce->getSalesForceAccessToken();
            $salesForceSearch = $salesForce->getSalesForceLeadSearch($email,$leadId,$getSalesForceToken);
            
            echo "Sales Force Token: " . $getSalesForceToken;

        } catch (\Throwable $th) {
            echo "Error / " . $th->getMessage();
        } 
?>