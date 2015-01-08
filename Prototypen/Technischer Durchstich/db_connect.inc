<?php

// Verbindungsvariable samt Zugangsdaten festlegen
@$GLOBALS['db'] = mysqli_connect("localhost", "root", "", "s3");

// Verbindung überprüfen
if (mysqli_connect_errno()) {
  printf("Verbindung fehlgeschlagen: %s\n", mysqli_connect_error());
  exit();
}

?>