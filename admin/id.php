<?

include("../inc/func.php");

$builders = getAllBuildersWithoutId();

$entries = array();

foreach ($builders as $b) {
  array_push($entries, array("barcodeData" => $b['builderId'], "name" => $b['name'], "teamId" => $b['teamId'], "title" => $b['title']));
}

$IDPDF = generateIDPDF($entries);

header('Content-Type: application/pdf');
header('Content-Disposition: inline; filename="id.pdf"');

echo($IDPDF);

?>
