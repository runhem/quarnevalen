<?

function validateUser($barcode) {
  return mysqlFetchOne("SELECT * FROM user WHERE barcodeData = \"".$barcode."\"");
}

function getAllUsers() {
  return mysqlFetchAll("SELECT * FROM user");
}

?>
