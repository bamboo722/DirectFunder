<?php
  // if the caller pressed anything but 1 send them back
  if($_REQUEST['Digits'] != '12345') {
      header("Location: fail.php");
      die;
  }

  header("content-type: text/xml");
  echo "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n"
?>
<Response>
  <Say>Very good.</Say>
  <Say>Now for the PIN.</Say>
  <Gather numDigits="4" action="magic2.php" method="POST">
  </Gather>
</Response>
