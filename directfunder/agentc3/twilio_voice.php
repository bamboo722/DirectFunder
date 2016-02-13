<?php
header('Content-type: text/xml');

// phone number you've verified with Twilio
$callerId =  $_REQUEST['callerid'];

// custom parameter from Twilio.Device.connect
$tocall   = $_REQUEST['tocall'];
?>
<Response>
    <Dial callerId="<?php echo $callerId ?>">
        <Number><?php echo $tocall ?></Number>
    </Dial>
</Response>