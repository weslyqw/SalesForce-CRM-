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
            curl_setopt($Curl, CURLOPT_CONNECTTIMEOUT, 10);
            curl_setopt($Curl, CURLOPT_TIMEOUT, 10);

            $headers = array(
               "Content-Type: application/x-www-form-urlencoded",
            );
            curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        
            $data = "grant_type=password&client_id=3MVG9FMtW0XJDLd1.cTdglQBYPhD3K830hSZAOZAwienjuQ9zf5qzdHUIMR5l9vpuLa7nlDRDr1O9MTNYSvBb&client_secret=C10939DC5AE49B1B07874B6231A2FC5E5932D6904C5454AB40B6F52B87753A51&username=akelqw@gmail.com&password=Brotherblood#1";
        
            curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
        
            $resp = curl_exec($curl);
            $resp = json_decode($resp, true);
            $salesForceAccessToken = ($resp['access_token']);
            curl_close($curl);
            return $salesForceAccessToken;
            
        
            
        }

        public function getSalesForceLeadSearch($email,$leadId,$salesForceAccessToken){
            $url = "https://whatconverts-dev-ed.my.salesforce.com/services/data/v54.0/sobjects/Lead/Email/$email";
            $salesForce = new SalesForce;
            $curl = curl_init($url);
            curl_setopt($curl, CURLOPT_URL, $url);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($Curl, CURLOPT_CONNECTTIMEOUT, 10);
            curl_setopt($Curl, CURLOPT_TIMEOUT, 10);

            $headers = array(
               "Authorization: Bearer ".$salesForceAccessToken,
            );
            curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
            
            
            $resp = curl_exec($curl);
            $resp = json_decode($resp, true);
            curl_close($curl);

            if (!($resp[0]['errorCode'])){
                $this->_lead_found = true;
                echo "lead found in sales Force \n";
            } else {
                echo "lead not found ". "\n";
                echo "Searching for WC lead in SF contacts ". "\n";
                $contactFound = $this->getSalesForceContactSearch($email,$salesForceAccessToken);
            }

            if (!($this->_lead_found) && (!($this->_contact_found)) && (!($this->_oppertunity_found))){
                $addational_field = "Pending";
                $this->setWcSalesForceField($leadId,$addational_field);

            } else if(($this->_lead_found) || ($this->_contact_found)){
                $addational_field = "Connected";
                
                $this->setWcSalesForceField($leadId,$addational_field);
                $this->setSalesForeceLeadNumber($leadId,$email,$salesForceAccessToken);

                if($this->_contact_found){
                    $wcQuotable = "Yes";
                    $this->setWcQuotable($wcQuotable,$leadId);

                }
            }
        }
        
        private function getSalesForceContactSearch($email,$salesForceAccessToken){

            $url = "https://whatconverts-dev-ed.my.salesforce.com/services/data/v54.0/sobjects/Contact/Email/$email";

            $curl = curl_init($url);
            curl_setopt($curl, CURLOPT_URL, $url);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($Curl, CURLOPT_CONNECTTIMEOUT, 10);
            curl_setopt($Curl, CURLOPT_TIMEOUT, 10);

            $headers = array(
               "Authorization: Bearer ".$salesForceAccessToken,
            );
            curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
            
            $resp = curl_exec($curl);
            $resp = json_decode($resp, true);
            curl_close($curl);

            if (!($resp[0]['errorCode'])){
                $this->_contact_found = true;
                echo "Contact found in sales Force \n";
            } else {
                echo "Contact not found ". '\n';
                echo "Searching for WC lead in SF Opportunities ". "\n";
                $opportunityFound = $this->getSalesForceOpportunitySearch($email,$salesForceAccessToken);
                
            }


            
            

        }
        
        private function getSalesForceOpportunitySearch($email,$salesForceAccessToken){

            $url = "https://whatconverts-dev-ed.my.salesforce.com/services/data/v54.0/sobjects/Opportunity/Email/$email";

            $curl = curl_init($url);
            curl_setopt($curl, CURLOPT_URL, $url);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($Curl, CURLOPT_CONNECTTIMEOUT, 10);
            curl_setopt($Curl, CURLOPT_TIMEOUT, 10);

            $headers = array(
               "Authorization: Bearer ".$salesForceAccessToken,
            );
            curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
           
            $resp = curl_exec($curl);
            $resp = json_decode($resp, true);
            curl_close($curl);

            if (!($resp[0]['errorCode'])){
                $this->$_oppertunity_found = true;
                echo "Opportunity found in sales Force \n";
            } else {
                echo "Opportunity not found ". '\n';
            }

           
        }

        public function getSalesForceContactByAccountId($email,$salesForceAccessToken){

            $url = "https://whatconverts-dev-ed.my.salesforce.com/services/data/v54.0/sobjects/Contact/Email/$email";

            $curl = curl_init($url);
            curl_setopt($curl, CURLOPT_URL, $url);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($Curl, CURLOPT_CONNECTTIMEOUT, 10);
            curl_setopt($Curl, CURLOPT_TIMEOUT, 10);

            $headers = array(
               "Authorization: Bearer ".$salesForceAccessToken,
            );
            curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
          
            $resp = curl_exec($curl);
            $resp = json_decode($resp, true);
            curl_close($curl);

            $wcLeadId = $resp['Fax'];
            
            return $wcLeadId;
        }

        private function setWcSalesForceField($leadId,$addational_field){
            $url = "https://app.whatconverts.com/api/v1/leads/$leadId";

            $curl = curl_init($url);
            curl_setopt($curl, CURLOPT_URL, $url);
            curl_setopt($curl, CURLOPT_POST, true);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($Curl, CURLOPT_CONNECTTIMEOUT, 10);
            curl_setopt($Curl, CURLOPT_TIMEOUT, 10);
            
            $headers = array(
               "Authorization: Basic OTc2NDktZDBiMTI0NzUxNzU0NDhiZDo3NjE5ZmIyNjIxNDdjNzI0NWI5ZDM2YzRlYTE0MDE0OQ==",
               "Content-Type: application/x-www-form-urlencoded",
            );
            curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
            
            $data = "additional_fields[SalesForce]=$addational_field";
            
            curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
            
            $resp = curl_exec($curl);
            curl_close($curl);
            
        }
        
        public function setWcQuotable($wcQuotable,$leadId){
            $url = "https://app.whatconverts.com/api/v1/leads/$leadId";

            $curl = curl_init($url);
            curl_setopt($curl, CURLOPT_URL, $url);
            curl_setopt($curl, CURLOPT_POST, true);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($Curl, CURLOPT_CONNECTTIMEOUT, 10);
            curl_setopt($Curl, CURLOPT_TIMEOUT, 10);
            
            $headers = array(
               "Authorization: Basic OTc2NDktZDBiMTI0NzUxNzU0NDhiZDo3NjE5ZmIyNjIxNDdjNzI0NWI5ZDM2YzRlYTE0MDE0OQ==",
               "Content-Type: application/x-www-form-urlencoded",
            );
            curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
            
            $data = "quotable=$wcQuotable";
            
            curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
            
            $resp = curl_exec($curl);
            curl_close($curl);
            
        }
        
        public function setWcQuoteValue($leadId,$opportunityAmount){
            $url = "https://app.whatconverts.com/api/v1/leads/$leadId";

            $curl = curl_init($url);
            curl_setopt($curl, CURLOPT_URL, $url);
            curl_setopt($curl, CURLOPT_POST, true);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($Curl, CURLOPT_CONNECTTIMEOUT, 10);
            curl_setopt($Curl, CURLOPT_TIMEOUT, 10);
            
            $headers = array(
               "Authorization: Basic OTc2NDktZDBiMTI0NzUxNzU0NDhiZDo3NjE5ZmIyNjIxNDdjNzI0NWI5ZDM2YzRlYTE0MDE0OQ==",
               "Content-Type: application/x-www-form-urlencoded",
            );
            curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
            
            $data = "quote_value=$opportunityAmount";
            
            curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
            
            
            $resp = curl_exec($curl);
            curl_close($curl);
            
        }
        
        public function setWcSalesVlaue($leadId,$opportunityAmount){
            $url = "https://app.whatconverts.com/api/v1/leads/$leadId";

            $curl = curl_init($url);
            curl_setopt($curl, CURLOPT_URL, $url);
            curl_setopt($curl, CURLOPT_POST, true);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($Curl, CURLOPT_CONNECTTIMEOUT, 10);
            curl_setopt($Curl, CURLOPT_TIMEOUT, 10);
            
            $headers = array(
               "Authorization: Basic OTc2NDktZDBiMTI0NzUxNzU0NDhiZDo3NjE5ZmIyNjIxNDdjNzI0NWI5ZDM2YzRlYTE0MDE0OQ==",
               "Content-Type: application/x-www-form-urlencoded",
            );
            curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
            
            $data = "sales_value=$opportunityAmount";
            
            curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
            
            $resp = curl_exec($curl);
            curl_close($curl);
        }

        private function setSalesForeceLeadNumber($leadId,$email,$salesForceAccessToken){
            $url = "https://whatconverts-dev-ed.my.salesforce.com/services/data/v54.0/sobjects/Lead/Email/$email";

            $curl = curl_init($url);
            curl_setopt($curl, CURLOPT_URL, $url);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($Curl, CURLOPT_CONNECTTIMEOUT, 10);
            curl_setopt($Curl, CURLOPT_TIMEOUT, 10);

            $headers = array(
               "Authorization: Bearer .$salesForceAccessToken",
               "Content-Type: application/json",
            );
            curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);

            $data = <<<DATA
            {
              "Fax":$leadId
            }
            DATA;

            curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
            curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'PATCH');

            $resp = curl_exec($curl);
            curl_close($curl);

        }
        
        public function setContactEmailIdentifier($email,$salesForceAccessToken){
            $url = "https://whatconverts-dev-ed.my.salesforce.com/services/data/v54.0/sobjects/Contact/Email/$email";

            $curl = curl_init($url);
            curl_setopt($curl, CURLOPT_URL, $url);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($Curl, CURLOPT_CONNECTTIMEOUT, 10);
            curl_setopt($Curl, CURLOPT_TIMEOUT, 10);

            $headers = array(
               "Authorization: Bearer .$salesForceAccessToken",
               "Content-Type: application/json",
            );
            curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);

            $data = <<<DATA
            {
              "Description":"$email"
            }
            DATA;

            curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
            curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'PATCH');

            $resp = curl_exec($curl);
            curl_close($curl);

        }

    }


    
?>
