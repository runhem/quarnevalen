<?
require_once("inc/func.php");

//INACTIVATE SYSTEM
//header("location: login.php");
//exit();

if (!isset($_SESSION['userId']) || !isset($_SESSION['state'])) {
  header("location: login.php");
  exit();
}

function printFooter() {
?>
    </center>
  </body>
</html>
<?
}

function printHeader() {
  header("Content-Type: text/html; charset=utf-8");
  echo("<?xml version=\"1.0\" encoding=\"utf-8\"?>\n");

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
   "http://www.w3.org/TR/html4/loose.dtd">

<html>
  <head>
    <title>CCC - Tooltracker</title>
    <link href="css/style.css" rel="stylesheet" />
  </head>

  <body>

    <br /><br /><br /><br /><br /><br />
    <center>
      <h1>CCC - Tooltracker<br /><br /></h1>

<?
}

function showCommandForm($caption = "Ange kommando: (DEFAULT)") {
?>
		<center>
      <div class="generic">
        <h3><?=$caption;?></h3><br />
        <form id="scanForm" action="index.php" method="post" />
          <input type="text" name="scan" /><br /><br />
          <input type="submit" value="N&auml;sta" />
        </form>
        <script language="javascript">document.getElementById('scanForm').scan.focus()</script>
      </div>
		</center>
<?
}

function showAbortMessage() {
?>
		<center>
      <div class="abort">
        <h2>Avbrutet</h2>
      </div>
		</center>
<?
}

function showError($error) {
?>
		<center>
      <div class="abort">
        <h2><?=$error;?></h2>
      </div>
		</center>
<?
}

function registerPool() {
	if (isset($_SESSION['return'])) {
		foreach ($_SESSION['return'] as $r) {
			returnTool($r['toolId'], $_SESSION['builderId']);
		}
	}

	if (isset($_SESSION['borrow'])) {
		foreach ($_SESSION['borrow'] as $b) {
			borrowTool($b['toolId'], $_SESSION['builderId']);
		}
	}

	unset($_SESSION['return']);
	unset($_SESSION['borrow']);
}

function showTeamStatus($builderId) {
  $team = getBuilderTeam($builderId);
  $loans = getCurrentTeamLoans($team['teamId']);
  switch ($_SESSION['state']) {
    case "handleBuilder":
			$borrowClass = "borrow";
			$returnClass = "return";
      break;
    case "returnTool":
			$borrowClass = "borrow";
			$returnClass = "returnSelected";
      break;
    case "borrowTool":
			$borrowClass = "borrowSelected";
			$returnClass = "return";
      break;
  }
?>
		<center>
      <table>
        <tr>
					<td colspan="3">
						<div class="generic">
							<h2><?=$team['teamId'];?> - status</h2>
						</div>
					</td>
				</tr>
				<tr >
					<td valign="top">
						<div class="<?=$borrowClass;?>">
							<h3>Verktyg som l&auml;mnas UT:</h3><br />
<?
	if (isset($_SESSION['borrow']))
		printTable($_SESSION['borrow'], array("toolName" => "Verktyg"), true);
?>
						</div>
					</td>
					<td valign="top">
						<div class="using">
							<h3>Verktyg som &auml;r ute:</h3><br />
							<b>Anv&auml;nder <?=(count($loans));?>/<?=$team['toolLimit'];?> verktyg.</b><br /><br />
<?
	printTable($loans, array("builderName" => "L&aring;nat av", "toolName" => "Verktyg", "loanDate" => "Datum", "loanTime" => "Tid"), true);
?>
						</div>
					</td>
					<td valign="top">
						<div class="<?=$returnClass;?>">
							<h3>Verktyg som l&auml;mnas IN:</h3><br />
<?
	if (isset($_SESSION['return']))
		printTable($_SESSION['return'], array("toolName" => "Verktyg"), true);
?>
						</div>
					</td>
				</tr>
			</table>
		</center>
<?
}

/*
* The actual "program" starts here...
*/
switch ($_SESSION['state']) {

  case "idle":
		if (isset($_POST['scan'])) {
			if ($command = getCommand($_POST['scan'])) {
				$action = $command['command'];
			} else if ($builder = getBuilder($_POST['scan'])) {
				$_SESSION['builderId'] = $builder['builderId'];
				$_SESSION['state'] = "handleBuilder";
				header("location: index.php");
				exit();
			}
		} else {
			$action = "crap. =(";	//default
		}

		switch ($action) {

			case "logout":
				unset($_SESSION['userId']);
				session_destroy();
				header("location: login.php");
				exit();
				break;

			case "abort":
				printHeader();
				showAbortMessage();
				showCommandForm("Ange kommando / byggare:");
				printFooter();
				break;

			//TODO: this code looks like crap. make function.
			case "showToolStatus":
				printHeader();
				if (isset($_POST['toolId'])) {
					$tool = getTool($_POST['toolId']);
					$loans = getCurrentToolLoans($tool['toolId']);
			?>
					  <center>
							<div class="using">
						    <h3><?=$tool['name'];?> - status</h3><br />
						    <b>Inne:</b> <?=($tool['stock'] - count($loans));?>/<?=$tool['stock'];?><br /><br />
						    <b>Utl&aring;nade:</b><br />
			<?
					printTable($loans, array("builderName" => "L&aring;nat av", "teamId" => "Vagn", "loanDate" => "Datum", "loanTime" => "Tid"), true);
			?>
							</div>
					  </center>
			<?
					showCommandForm("Ange kommando / byggare:");
				} else {
			?>
						<div class="generic">
						  <h3>Visa status f&ouml;r verktyg - Ange verktyg</h3><br />
						  <form id="toolForm" action="index.php" method="post" />
								<input type="hidden" name="scan" value="C3">
						    <input type="text" name="toolId" /><br /><br />
						    <input type="submit" value="N&auml;sta" />
						  </form>
						  <script language="javascript">document.getElementById('toolForm').toolId.focus()</script>
						</div>
			<?
				}
				printFooter();
				break;

			case "showLoans":
				printHeader();
				$loans = getAllCurrentToolLoans();
			?>
						<div class="using">
						  <center>
						    <h3>Alla l&aring;n</h3><br />
						    <b>Utl&aring;nat:</b><br />
			<?
				printTable($loans, array("toolName" => "Verktyg", "builderName" => "L&aring;nat av", "teamId" => "Vagn", "loanDate" => "Datum", "loanTime" => "Tid"), true);
			?>
						  </center>
						</div>
			<?
				showCommandForm("Ange kommando / byggare:");
				printFooter();
				break;

			default:
				printHeader();
				showCommandForm("Ange kommando / byggare:");
				printFooter();
				break;
		}

    break;

  case "handleBuilder":
		if (isset($_POST['scan'])) {
			if ($command = getCommand($_POST['scan'])) {
				if ($command['command'] == "borrowTool") {
					$_SESSION['state'] = "borrowTool";
					header("location: index.php");
					exit();
				} else if ($command['command'] == "returnTool") {
					$_SESSION['state'] = "returnTool";
					header("location: index.php");
					exit();
				} else if ($command['command'] == "abort") {
					$_SESSION['state'] = "idle";
					header("location: index.php");
					exit();
				} else if ($command['command'] == "ok") {
					$_SESSION['state'] = "idle";
					header("location: index.php");
					exit();
				}
			}
		}

		printHeader();
		showTeamStatus($_SESSION['builderId']);
		showCommandForm("Ange kommando");
		printFooter();
    break;

  case "borrowTool":
		$noslots = false;
		if (isset($_POST['scan'])) {
			if ($tool = getTool($_POST['scan'])) {
				$team = getBuilderTeam($_SESSION['builderId']);
			  $nLoans = count(getCurrentTeamLoans($team['teamId']));
				if ($team['toolLimit'] > ($nLoans - count($_SESSION['return']) + count($_SESSION['borrow']))) {
					if (!is_array($_SESSION['borrow'])) {
						$_SESSION['borrow'] = array();
					} 
					array_push($_SESSION['borrow'], array("toolId" => $tool['toolId'], "toolName" => $tool['name']));
				} else {
					$noslots = true;
				}
			} else if ($command = getCommand($_POST['scan'])) {
				if ($command['command'] == "returnTool") {
					$_SESSION['state'] = "returnTool";
					header("location: index.php");
					exit();
				} else if ($command['command'] == "abort") {
					unset($_SESSION['borrow']);
					unset($_SESSION['return']);
					$_SESSION['state'] = "idle";
					header("location: index.php");
					exit();
				} else if ($command['command'] == "ok") {
					registerPool();
					$_SESSION['state'] = "handleBuilder";
					header("location: index.php");
					exit();
				}
			}
		}

		printHeader();
		if ($noslots)	
			showError("Får inte låna fler verktyg.");
		showTeamStatus($_SESSION['builderId']);
		showCommandForm("Ange verktyg att l&auml;mna UT / kommando:");
		printFooter();
		break;

  case "returnTool":
		$notool = false;
		if (isset($_POST['scan'])) {
			if ($tool = getTool($_POST['scan'])) {
				if (hasTool($tool['toolId'], $_SESSION['builderId'])) {	//TODO: kolla om de har ett sånt verktyg när man drar bort det som redan är i poolen
					if (!is_array($_SESSION['return'])) {
						$_SESSION['return'] = array();
					} 
					array_push($_SESSION['return'], array("toolId" => $tool['toolId'], "toolName" => $tool['name']));
				} else {
					$notool = true;
				}
			} else if ($command = getCommand($_POST['scan'])) {
				if ($command['command'] == "borrowTool") {
					$_SESSION['state'] = "borrowTool";
					header("location: index.php");
					exit();
				} else if ($command['command'] == "abort") {
					unset($_SESSION['borrow']);
					unset($_SESSION['return']);
					$_SESSION['state'] = "idle";
					header("location: index.php");
					exit();
				} else if ($command['command'] == "ok") {
					registerPool();
					$_SESSION['state'] = "handleBuilder";
					header("location: index.php");
					exit();
				}
			}
		}

		printHeader();
		if ($notool)	
			showError("Har inget s&aring;dant verktyg.");
		showTeamStatus($_SESSION['builderId']);
		showCommandForm("Ange verktyg att l&auml;mna IN / kommando:");
		printFooter();
		break;
}
?>

