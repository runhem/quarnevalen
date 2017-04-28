<?

include("../inc/func.php");

$users = getAllUsers();

$entries = array();

foreach ($users as $b) {
  array_push($entries, array("barcodeData" => $b['barcodeData'], "name" => $b['name']));
}

$IDPDF = generateIDPDF($entries);

header('Content-Type: application/pdf');
header('Content-Disposition: inline; filename="id.pdf"');

echo($IDPDF);

?>
