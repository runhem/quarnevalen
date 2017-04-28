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

    <table cellspacing="10">
      <tr>
        <td valign="top">
          <h3>Meny</h3>
          <ul>
            <li><a href="index.php?action=showTools">Visa verktyg</a></li>
            <li><a href="index.php?action=addTool">Lägg till verktyg</a></li>
            <li><a href="tools.php">Generera verktygslista (verktyg utan kategori)</a></li>
			<li><a href="index.php?action=generateToolCategory">Generera verktygslista från kategori</a></li>
            <li><a href="commonTools.php">Vanliga verktyg</a></li>

            <br />
            <li><a href="index.php?action=showTeams">Visa vagnar</a></li>
            <li><a href="index.php?action=addTeam">Lägg till vagn</a></li>
            <li><a href="id.php">Generera id-kort</a></li>

            <br />
            <li><a href="index.php?action=showCommands">Visa kommandon</a></li>
            <li><a href="index.php?action=addCommand">Lägg till kommando</a></li>
            <li><a href="commands.php">Generera kommandolista</a></li>

            <br />
            <li><a href="index.php?action=showToolStatistics">Visa verktugsstatistik</a></li>
            <li><a href="index.php?action=showTeamStatistics">Visa Vagnsstatistik</a></li>
          </ul>
        </td>
        <td valign="top">

<?

switch ($action) {

//Tool handling

case "showTools":
  $tools = getAllTools();
  foreach (array_keys($tools) as $k) {
    $tools[$k]['stock'] = "<a href=\"index.php?action=updateToolStock&toolId=".$tools[$k]['toolId']."\">".$tools[$k]['stock']."</a>";
  }
  echo("<h3>Alla verktyg</h3><br />");
  printTable($tools, array("name" => "Verktyg", "stock" => "Antal", "category" => "Kategori"), true);
  echo("<br /><a href=\"index.php?action=addTool\">Lägg till verktyg</a>");
  break;
 
case "generateToolCategory":
  ?>
	<form id="generateToolCategoryForm" action="tools.php" method="post" />
		För vilken kategori vill du generera verktygslista?<br />
		<select name="category">
			<?
				$categories = getAllToolCategories();
				foreach ($categories as $category) {
					echo("<option>" . $category[0] . "</option>");
				}
			?>
		</select><br />
		<input type="submit" value="Generera" />
	</form>
  <?
  break;

case "addTool":
  ?>
      <h3>Lägg till verktyg</h3><br />
      <form id="toolForm" action="index.php?action=doAddTool" method="post" />
        Verktygets namn:<br />
        <input type="text" name="name" /><br /><br />
        Antal:<br />
        <input type="text" name="stock" /><br /><br />
		Kategori:<br />
        <input type="text" name="category" /><br /><br />
        <input type="submit" value="Lägg till" />
      </form>
      <script language="javascript">document.getElementById('toolForm').name.focus()</script>
  <?
  break;

case "doAddTool":
  if (isset($_POST['name']) && isset($_POST['stock']) && is_numeric($_POST['stock']) && isset($_POST['category'])) {
    addTool($_POST['name'], $_POST['stock'], $_POST['category']);
    echo("<h2>Verktyg tillagt!</h2>");
  } else {
    echo("<h2>Du misslyckades!</h2>");
  }

  echo("<meta http-equiv=\"refresh\" content=\"2;url=index.php?action=addTool\" />");
  break;

case "updateToolStock":
  if (isset($_GET['toolId'])) {
    $tool = getTool($_GET['toolId']);
?>
      <h3>Uppdatera antal - <?=$tool['name'];?></h3><br />
      <form id="toolForm" action="index.php?action=doUpdateToolStock" method="post" />
        Antal:<br />
        <input type="text" name="stock" value="<?=$tool['stock'];?>"/><br /><br />
        <input type="hidden" name="toolId" value="<?=$tool['toolId'];?>" />
        <input type="submit" value="Spara" />
      </form>
      <script language="javascript">document.getElementById('toolForm').stock.focus()</script>
<?
  }
  break;

case "doUpdateToolStock":
  if (isset($_POST['toolId'])) {
    updateToolStock($_POST['toolId'], $_POST['stock']);
    echo("<h2>Antal uppdaterat!</h2>\n");
    echo("<meta http-equiv=\"refresh\" content=\"2;url=index.php?action=showTools\" />");
  }
  break;

//Team handling

case "showTeams":
  $teams = getAllTeams();
  foreach (array_keys($teams) as $k) {
    $teams[$k]['toolLimit'] = "<a href=\"index.php?action=updateToolLimit&teamId=".$teams[$k]['teamId']."\">".$teams[$k]['toolLimit']."</a>";
    $teams[$k]['addBuilder'] = "<a href=\"index.php?action=addBuilder&teamId=".$teams[$k]['teamId']."\">Lägg till VC</a>";
  }

  echo("<h3>Alla vagnar</h3><br />");
  printTable($teams, array("teamId" => "Vagn", "description" => "Bygger", "vc" => "Vagnschefer", "toolLimit" => "Max antal verktyg", "addBuilder" => "Lägg till VC"), true);
  echo("<br /><a href=\"index.php?action=addTeam\">Lägg till vagn</a>");
  break;

case "addTeam":
  ?>
      <h3>Lägg till vagn</h3><br />
      <form id="teamForm" action="index.php?action=doAddTeam" method="post" />
        Vagnens namn:<br />
        <input type="text" name="name" /><br /><br />
        Bygger:<br />
        <input type="text" name="description" /><br /><br />
        Vagnschefer:<br />
        Namn: 
        <input type="text" name="builderName[]" />
        <select name="builderTitle[]"><option value="VC" selected>VC</option><option value="vVC">vVC</option></select><br />
        Personnummer: 
        <input type="text" name="builderId[]" />
        <br /><br />
        Namn:
        <input type="text" name="builderName[]" />
        <select name="builderTitle[]"><option value="VC">VC</option><option value="vVC" selected>vVC</option></select><br />
        Personnummer:
        <input type="text" name="builderId[]" />
        <br /><br />
        Max antal verktyg:<br />
        <input type="text" name="toolLimit" value="5" /><br /><br />
        <input type="submit" value="Lägg till" />
      </form>
      <script language="javascript">document.getElementById('teamForm').name.focus()</script>
  <?
  break;

case "doAddTeam":
  addTeam($_POST['name'], $_POST['description'], $_POST['toolLimit']);
  foreach(array_keys($_POST['builderName']) as $k) {
    addBuilder($_POST['builderId'][$k], $_POST['name'], $_POST['builderTitle'][$k], $_POST['builderName'][$k]);
  }
  echo("<h2>Vagn tillagd!</h2>");
  echo("<meta http-equiv=\"refresh\" content=\"2;url=index.php?action=addTeam\" />");

  break;

case "addBuilder":
  ?>
      <h3>Lägg till extra VC</h3><br />
      <form id="teamForm" action="index.php?action=doAddBuilder" method="post" />
        Namn:
        <input type="text" name="builderName" />
        <select name="builderTitle"><option value="VC">VC</option><option value="vVC">vVC</option><option value="extra" selected>extra</option></select><br />
        Personnummer:
        <input type="text" name="builderId" />
        <br /><br />
        <input type="submit" value="Lägg till" />
        <input type="hidden" name="teamId" value="<?=$_GET['teamId'];?>" />
      </form>
      <script language="javascript">document.getElementById('teamForm').builderName.focus()</script>
<?
  break;

case "doAddBuilder":
  addBuilder($_POST['builderId'], $_POST['teamId'], $_POST['builderTitle'], $_POST['builderName']);
  echo("<h2>Extra VC tillagd!</h2>");
  echo("<meta http-equiv=\"refresh\" content=\"2;url=index.php?action=showTeams\" />");
  break;

case "updateToolLimit":
  if (isset($_GET['teamId'])) {
    $team = getTeam($_GET['teamId']);
?>
      <h3>Uppdatera verktygsgräns - <?=$team['teamId'];?></h3><br />
      <form id="teamForm" action="index.php?action=doUpdateToolLimit" method="post" />
        Antal:<br />
        <input type="text" name="toolLimit" value="<?=$team['toolLimit'];?>"/><br /><br />
        <input type="hidden" name="teamId" value="<?=$team['teamId'];?>" />
        <input type="submit" value="Spara" />
      </form>
      <script language="javascript">document.getElementById('teamForm').toolLimit.focus()</script>
<?
  }
  break;

case "doUpdateToolLimit":
  if (isset($_POST['teamId'])) {
    updateToolLimit($_POST['teamId'], $_POST['toolLimit']);
    echo("<h2>Verktygsgräns uppdaterad!</h2>\n");
    echo("<meta http-equiv=\"refresh\" content=\"2;url=index.php?action=showTeams\" />");
  }
  break;




//Command-handling
case "showCommands":
  $commands = getAllCommands();

  echo("<h3>Alla kommandon</h3><br />");
  printTable($commands, array("barcodeData" => "ID", "command" => "Kommando", "description" => "Förklaring"), false);
  echo("<br /><a href=\"index.php?action=addCommand\">Lägg till kommando</a>");
  break;

case "addCommand":
  ?>
      <h3>Lägg till kommando</h3><br />
      <form id="commandForm" action="index.php?action=doAddCommand" method="post" />
        Kommando:<br />
        <input type="text" name="command" /><br /><br />
        Beskrivning:<br />
        <input type="text" name="description" /><br /><br />
        <input type="submit" value="Lägg till" />
      </form>
      <script language="javascript">document.getElementById('commandForm').command.focus()</script>
  <?
  break;

case "doAddCommand":
  addCommand($_POST['command'], $_POST['description']);
  echo("<h2>Kommando tillagt!</h2>");
  echo("<meta http-equiv=\"refresh\" content=\"2;url=index.php?action=showCommands\" />");
  break;

//-----------
// statistik
//-----------

case "showToolStatistics":

  break;

//---------
// default
//---------

default:
?>
      <h3>Admin</h3><br />
<?
  echo("DEFAULT");
  break;

}

?>
        </td>
      </tr>
    </table>
  </body>
</html>

