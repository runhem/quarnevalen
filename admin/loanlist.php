<?
require_once("../inc/func.php");

$action = "";
if (isset($_GET['action'])) {
  $action = $_GET['action'];
}

?>

<html>
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>CCC - Tooltracker admin</title>
    <link href="../css/style.css" rel="stylesheet" />
  </head>

  <body style="margin: 20px;">

<?

$team = getAllTeams();

foreach ($team as $t) {
  $loan = getCurrentTeamLoans($t['teamId']);
  echo("<h3>".$t['teamId']."</h3>");
  printTable($loan, array("toolName" => "Verktyg", "loanDate" => "Dag", "loanTime" => "Tid"), true);
  echo("<br /><br />");
}

?>

  </body>
</html>

