<?php
  // if the caller pressed anything but 1 send them back
  if($_REQUEST['Digits'] != '7890') {
      header("Location: fail.php");
      die;
  }

  header("content-type: text/xml");
  echo "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n"
?>
<Response>
  <Say>You just sunk my battleship</Say>
  <Play>http://wheresgus.com/tapi/tada.mp3</Play>
</Response>
