<?

function numberPad($number,$n) {
  return str_pad((int) $number,$n,"0",STR_PAD_LEFT);
}

function printTable($data, $headers, $numbering, $sortLinks = false, $action = "") {
  $keys = array_keys($headers);
  echo("<table>\n");
  echo("<tr style=\"background-color: #CCCCCC\">");

  if ($numbering) {
    echo("<td> </td>");
  }
  foreach ($keys as $k) {
    if ($sortLinks)
      echo("<td><a href=\"index.php?action=".$action."&sort=".$k."\">$headers[$k]</a></td>");
    else
      echo("<td><b>$headers[$k]</b></td>");
  }
  echo("</tr>\n");

  $i = 1;
  foreach ($data as $d) {
    $color = "#F0F0F0";
    if ($i % 2 == 0)
      $color = "#E0E0E0";
    if (isset($d['rowColor']))
      $color = $d['rowColor'];

    echo("<tr style=\"background-color: ".$color."\">");

    if ($numbering) {
      echo("<td>".$i."</td>");
    }
    foreach ($keys as $k) {
      echo("<td>$d[$k]</td>");
    }
    echo("</tr>\n");
    $i++;
  }
  echo("</table>\n");
}

?>
