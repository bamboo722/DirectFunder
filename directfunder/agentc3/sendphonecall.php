<?php
    if (isset($_REQUEST['addr']))
    {
         // Include the Twilio PHP library
        require 'twilio-php/Services/Twilio.php';

        // Twilio REST API version
        $version = "2010-04-01";

        // Set our Account SID and AuthToken
        $sid = 'AC118582b68fad40fa1b5451d3a9038b79';
        $token = 'ea120d640e8cfd0a615c7cd650f5013a';

        // A phone number you have previously validated with Twilio
        $phonenumber = '17149084596';

        // Instantiate a new Twilio Rest Client
        $client = new Services_Twilio($sid, $token, $version);

        try {
            // Initiate a new outbound call
            $call = $client->account->calls->create(
                $phonenumber, // The number of the phone initiating the call
                $_REQUEST['addr'], // The number of the phone receiving call
                'https://demo.twilio.com/welcome/voice/' // The URL Twilio will request when the call is answered
            );
            echo 'Started call: ' . $call->sid;
            echo '<br>';
            echo 'Phone call is successed!';
        } catch (Exception $e) {
            echo 'Error: ' . $e->getMessage();
        }
    }
?>