<?php

// Connection variable including access details
@$db = mysqli_connect("localhost", "root", "", "s3");

// doublecheck connection
if (mysqli_connect_errno()) {
  printf("Verbindung fehlgeschlagen: %s\n", mysqli_connect_error());
  exit();
}

?>