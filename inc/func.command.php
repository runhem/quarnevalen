<?

function getCommand($barcode) {
  if (substr($barcode, 0, 1) == "C") {
    $barcodeData = (int)(substr($barcode, 1));
    return mysqlFetchOne("SELECT * FROM command WHERE barcodeData = ".$barcodeData);
  }
  return false;
}

function getAllCommands() {
  return mysqlFetchAll("SELECT * FROM command");
}

function addCommand($command, $description) {
  mysql_query("INSERT INTO command (command, description) VALUES(\"".$command."\", \"".$description."\");");
}

function getCommandDescription($command) {
  $command = mysqlFetchOne("SELECT * FROM command WHERE command = \"".$command."\"");
  return $command['description'];
}

?>
