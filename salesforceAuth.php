<?php

    class SalesForce {
        private $_lead_found = false;
        private $_contact_found = false;
        private $_oppertunity_found = false;
        private $_client_exists_in_sales_force = false;

        public function getSalesForceAccessToken(){
            $url = "https://whatconverts-dev-ed.my.salesforce.com/services/oauth2/token";
            $curl = curl_init($url);
            curl_setopt($curl, CURLOPT_URL, $url);
            curl_setopt($curl, CURLOPT_POST, true);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

            $headers = array(
               "Content-Type: application/x-www-form-urlencoded",
            );
            curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        
            $data = "grant_type=password&client_id=3MVG9FMtW0XJDLd1.cTdglQBYPhD3K830hSZAOZAwienjuQ9zf5qzdHUIMR5l9vpuLa7nlDRDr1O9MTNYSvBb&client_secret=C10939DC5AE49B1B07874B6231A2FC5E5932D6904C5454AB40B6F52B87753A51&username=akelqw@gmail.com&password=Brotherblood#1";
        
            curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
        
            //for debug only!
            curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        
            $resp = curl_exec($curl);
            $resp = json_decode($resp, true);
            $salesForceAccessToken = ($resp['access_token']);
            return $salesForceAccessToken;
            curl_close($curl);
        
            
        }

        public function getSalesForceLeadSearch($email,$leadId,$salesForceAccessToken){
            $url = "https://whatconverts-dev-ed.my.salesforce.com/services/data/v54.0/sobjects/Lead/Email/$email";
            $salesForce = new SalesForce;
            $curl = curl_init($url);
            curl_setopt($curl, CURLOPT_URL, $url);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

            $headers = array(
               "Authorization: Bearer ".$salesForceAccessToken,
            );
            curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
            //for debug only!
            curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);

            $resp = curl_exec($curl);
            $resp = json_decode($resp, true);

            if (!($resp[0]['errorCode'])){
                $this->$_lead_found = true;
                echo "lead found in sales Force \n";
            } else {
                echo "lead not found ". "\n";
                echo "Searching for WC lead in SF contacts ". "\n";
                $contactFound = $this->getSalesForceContactSearch($email,$salesForceAccessToken);
            }

            if (!($this->_lead_found) && (!($this->_contact_found)) && (!($this->_oppertunity_found))){
                $addational_field = "Pending";
                setWcSalesForceField($leadId,$addational_field);
                $this->setSalesForeceLeadNumber($leadId,$email,$salesForceAccessToken);

            } else if(($this->_lead_found) || ($this->_contact_found)){
                $addational_field = "Connected";
                
                setWcSalesForceField($leadId,$addational_field);

                if($this->_contact_found){
                    $wcQuotable = "Yes";
                    $this->setWcQuotable($wcQuotable,$leadId);

                }
            } else if($this->_oppertunity_found){

            }

            curl_close($curl);
            

        }
        
        private function getSalesForceContactSearch($email,$salesForceAccessToken){

            $url = "https://whatconverts-dev-ed.my.salesforce.com/services/data/v54.0/sobjects/Contact/Email/$email";

            $curl = curl_init($url);
            curl_setopt($curl, CURLOPT_URL, $url);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

            $headers = array(
               "Authorization: Bearer ".$salesForceAccessToken,
            );
            curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
            //for debug only!
            curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);

            $resp = curl_exec($curl);
            $resp = json_decode($resp, true);

            if (!($resp[0]['errorCode'])){
                $this->_contact_found = true;
                echo "Contact found in sales Force \n";
            } else {
                echo "Contact not found ". '\n';
                echo "Searching for WC lead in SF Opportunities ". "\n";
                $opportunityFound = $this->getSalesForceOpportunitySearch($email,$salesForceAccessToken);
                
            }


            curl_close($curl);
            

        }
        
        private function getSalesForceOpportunitySearch($email,$salesForceAccessToken){

            $url = "https://whatconverts-dev-ed.my.salesforce.com/services/data/v54.0/sobjects/Opportunity/Email/$email";

            $curl = curl_init($url);
            curl_setopt($curl, CURLOPT_URL, $url);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

            $headers = array(
               "Authorization: Bearer ".$salesForceAccessToken,
            );
            curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
            //for debug only!
            curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);

            $resp = curl_exec($curl);
            $resp = json_decode($resp, true);
            if (!($resp[0]['errorCode'])){
                $this->$_oppertunity_found = true;
                echo "Opportunity found in sales Force \n";
            } else {
                echo "Opportunity not found ". '\n';
            }

            curl_close($curl);
           

        }

        public function setWcSalesForceField($leadId,$addational_field){
            $url = "https://app.whatconverts.com/api/v1/leads/$leadId";

            $curl = curl_init($url);
            curl_setopt($curl, CURLOPT_URL, $url);
            curl_setopt($curl, CURLOPT_POST, true);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
            
            $headers = array(
               "Authorization: Basic OTc2NDktNTgxMjY5ZjFlNWM1OTg3YzoyNWUwZDU3Y2RkMGVjYWQyMjFmZjNhOGY3ZDMxYmI2NA==",
               "Content-Type: application/x-www-form-urlencoded",
            );
            curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
            
            $data = "additional_fields[SalesForce]=$addational_field";
            
            curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
            
            //for debug only!
            curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
            
            $resp = curl_exec($curl);
            curl_close($curl);
            
        }
        
        private function setWcQuotable($wcQuotable,$leadId){
            $url = "https://app.whatconverts.com/api/v1/leads/$leadId";

            $curl = curl_init($url);
            curl_setopt($curl, CURLOPT_URL, $url);
            curl_setopt($curl, CURLOPT_POST, true);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
            
            $headers = array(
               "Authorization: Basic OTc2NDktNTgxMjY5ZjFlNWM1OTg3YzoyNWUwZDU3Y2RkMGVjYWQyMjFmZjNhOGY3ZDMxYmI2NA==",
               "Content-Type: application/x-www-form-urlencoded",
            );
            curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
            
            $data = "quotable=$wcQuotable";
            
            curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
            
            //for debug only!
            curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
            
            $resp = curl_exec($curl);
            curl_close($curl);
            
        }

        private function setSalesForeceLeadNumber($leadId,$email,$salesForceAccessToken){
            $url = "https://whatconverts-dev-ed.my.salesforce.com/services/data/v54.0/sobjects/Lead/Email/$email";

            $curl = curl_init($url);
            curl_setopt($curl, CURLOPT_URL, $url);
            curl_setopt($curl, CURLOPT_PATCH, true);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

            $headers = array(
               "Authorization: Bearer .$salesForceAccessToken",
               "Content-Type: application/json",
            );
            curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);

            $data = <<<DATA
            {
              "Description":$leadId
            }
            DATA;

            curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
            curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'PATCH');

            //for debug only!
            curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);

            $resp = curl_exec($curl);
            curl_close($curl);

        }

    }


    
?>
