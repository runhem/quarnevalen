<?

include("../inc/func.php");

if (isset($_POST['category'])) {
	$tools = getAllToolsWithCategory($_POST['category']);
} else {
	$tools = getAllToolsWithoutCategory();
}

$entries = array();

foreach ($tools as $t) {
  array_push($entries, array("barcodeData" => "T".$t['toolId'], "caption" => $t['name']));
}

$barcodePDF = generateBarcodePDF($entries);

header('Content-Type: application/pdf');
header('Content-Disposition: inline; filename="barcodes.pdf"');

echo($barcodePDF);

?>
