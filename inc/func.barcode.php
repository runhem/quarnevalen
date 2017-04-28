<?

function ps2pdf($ps) {
  file_put_contents("/tmp/tmp.ps", utf8_decode($ps));
  $convertCommand = "/usr/bin/gs -dSAFER -dCompatibilityLevel=1.2 -q -dNOPAUSE -dBATCH -sDEVICE=pdfwrite -sOutputFile=/tmp/tmp.pdf -dSAFER -dCompatibilityLevel=1.2 -c .setpdfwrite -f /tmp/tmp.ps";
  exec($convertCommand);
  return file_get_contents("/tmp/tmp.pdf");
}

function generateIDPDF($entries) {
  $ps = generateIDPostScript($entries);
  return ps2pdf($ps);
}

function generateBarcodePDF($entries) {
  $ps = generateBarcodePostScript($entries);
  return ps2pdf($ps);
}

function generateIDPostScript($entries) {
        $psFile = file_get_contents("/var/www/tooltracker/inc/barcode.ps");

        $procedureCall = generateIDProcedure($entries);

        return $psFile . $procedureCall;
}

function generateBarcodePostScript($entries) {
	$psFile = file_get_contents("/var/www/tooltracker/inc/barcode.ps");
	
	$procedureCall = generateBarcodeProcedure($entries);

	return $psFile . $procedureCall;
}

//:::::::::::::::::::::::::::::::::::

function generateCutEdgeProcedure($startX, $startY, $sizeX, $sizeY, $length, $width) {
	$proc = "";

	$proc .= $startX." ".$startY." moveto\n";
        $proc .= "-".$length." 0 rlineto\n";
        $proc .= "0 ".$width." rlineto\n";
        $proc .= ($length - $width)." 0 rlineto\n";
        $proc .= "0 ".($length - $width)." rlineto\n";
        $proc .= $width." 0 rlineto\n";
        $proc .= "0 -".$length." rlineto\n";
        $proc .= "closepath fill\n";

        $proc .= ($startX + $sizeX)." ".$startY." moveto\n";
        $proc .= $length." 0 rlineto\n";
        $proc .= "0 ".$width." rlineto\n";
        $proc .= "-".($length - $width)." 0 rlineto\n";
        $proc .= "0 ".($length - $width)." rlineto\n";
        $proc .= "-".$width." 0 rlineto\n";
        $proc .= "0 -".$length." rlineto\n";
        $proc .= "closepath fill\n";

        $proc .= $startX." ".($startY - $sizeY)." moveto\n";
        $proc .= "-".$length." 0 rlineto\n";
        $proc .= "0 -".$width." rlineto\n";
        $proc .= ($length - $width)." 0 rlineto\n";
        $proc .= "0 -".($length - $width)." rlineto\n";
        $proc .= $width." 0 rlineto\n";
        $proc .= "0 ".$length." rlineto\n";
        $proc .= "closepath fill\n";

        $proc .= ($startX + $sizeX)." ".($startY - $sizeY)." moveto\n";
        $proc .= $length." 0 rlineto\n";
        $proc .= "0 -".$width." rlineto\n";
        $proc .= "-".($length - $width)." 0 rlineto\n";
        $proc .= "0 -".($length - $width)." rlineto\n";
        $proc .= "-".$width." 0 rlineto\n";
        $proc .= "0 ".$length." rlineto\n";
        $proc .= "closepath fill\n";

	return $proc;
}

function generateIDProcedure($entries) {
	$pxpmm = 78.5 / 25.4;
        $procedureCall = "";
        $startLeft = 9 * $pxpmm;
        $stepLeft = 95 * $pxpmm;
        $startBottom = 240 * $pxpmm;
        $stepBottom = -54 * $pxpmm;
        $codesPerRow = 2;
        $rowsPerSheet = 4;
        $stepTextX = 10 * $pxpmm;
        $stepTextY = -15 * $pxpmm;
	$stepTeamY = -25 * $pxpmm;
        $codeHeight = 0.5;
	$sizeX = 85 * $pxpmm;
	$sizeY = 54 * $pxpmm;
	$cutLength = 4 * $pxpmm;
	$cutWidth = 0.5 * $pxpmm;
	$bcOffsetX = 30 * $pxpmm;
        $bcOffsetY = -45 * $pxpmm;

        $i = 0;

        $procedureCall .= "/Helvetica findfont\n";
        $procedureCall .= "0 dict copy begin\n";
        $procedureCall .= "/Encoding ISOLatin1Encoding def\n";
        $procedureCall .= "/ISOHelvetica /FontName def\n";
        $procedureCall .= "currentdict end\n";
        $procedureCall .= "dup /FID undef\n";
        $procedureCall .= "/ISOHelvetica exch definefont pop\n";

        foreach ($entries as $e) {

                $posLeft = $startLeft + $stepLeft * ($i % $codesPerRow);
                $posBottom = $startBottom + $stepBottom * round(floor($i / $codesPerRow));

		$procedureCall .= generateCutEdgeProcedure($posLeft, $posBottom, $sizeX, $sizeY, $cutLength, $cutWidth);

                $procedureCall .= ($posLeft + $bcOffsetX)." ".($posBottom + $bcOffsetY)." moveto (".$e['barcodeData'].") (includetext height=".$codeHeight.") interleaved2of5 barcode\n";

		$procedureCall .= "/ISOHelvetica findfont\n";
		$procedureCall .= "14 scalefont setfont\n";
                $procedureCall .= ($posLeft + $stepTextX)." ".($posBottom + $stepTextY)." moveto (".$e['name'].", ".$e['title'].") show\n\n";

                $procedureCall .= "/ISOHelvetica-bold findfont\n";
                $procedureCall .= "20 scalefont setfont\n";
                $procedureCall .= ($posLeft + $stepTextX)." ".($posBottom + $stepTeamY)." moveto (".$e['teamId'].") show\n\n";
		
                $i++;

                if ($i / $codesPerRow >= $rowsPerSheet) {
                        $procedureCall .= "showpage\n\n";
                        $i = 0;
                }

        }
        $procedureCall .= "showpage\n";

        return $procedureCall;
}

function generateBarcodeProcedure($entries) {
        
        $procedureCall = "";
        $startLeft = 90;
        $stepLeft = 200;
        $startBottom = 700;
        $stepBottom = -90;
        $codesPerRow = 3;
        $rowsPerSheet = 8;
        $stepTextX = -10;
        $stepTextY = 45;
        $codeHeight = 0.5;

        $i = 0;

        $procedureCall .= "/Helvetica findfont\n";
        $procedureCall .= "0 dict copy begin\n";
        $procedureCall .= "/Encoding ISOLatin1Encoding def\n";
        $procedureCall .= "/ISOHelvetica /FontName def\n";
        $procedureCall .= "currentdict end\n";
        $procedureCall .= "dup /FID undef\n";
        $procedureCall .= "/ISOHelvetica exch definefont pop\n";

        $procedureCall .= "/ISOHelvetica findfont\n";
        $procedureCall .= "14 scalefont setfont\n";

	foreach ($entries as $e) {

		$posLeft = $startLeft + $stepLeft * ($i % $codesPerRow);
		$posBottom = $startBottom + $stepBottom * round(floor($i / $codesPerRow));
		$procedureCall .= $posLeft . " " . $posBottom . " moveto (^104^102" . $e['barcodeData'] . ") (includetext height=" . $codeHeight . ") code128 barcode\n";
		$procedureCall .= $stepTextX . " " . $stepTextY . " rmoveto (" . $e['caption'] . ") show\n\n";

		$i++;	

		if ($i / $codesPerRow >= $rowsPerSheet) {
                	$procedureCall .= "showpage\n\n";
	                $i = 0;
		}

	}
        $procedureCall .= "showpage\n";

        return $procedureCall;
}

?>
