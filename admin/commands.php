<?

include("../inc/func.php");

$commands = getAllCommands();

$entries = array();

foreach ($commands as $c) {
  array_push($entries, array("barcodeData" => "C".$c['barcodeData'], "caption" => $c['description']));
}

$barcodePDF = generateBarcodePDF($entries);

header('Content-Type: application/pdf');
header('Content-Disposition: inline; filename="barcodes.pdf"');

echo($barcodePDF);

?>
