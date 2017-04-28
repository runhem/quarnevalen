<?
require_once("inc/func.php");

if (isset($_POST['barcode'])) {
  $userId = validateUser($_POST['barcode']);

  if ($userId === false) {
    header("location: login.php?fail=true");
    exit();
  } else {
    $_SESSION['userId'] = $userId;
    $_SESSION['state'] = "idle";
	
    header("location: index.php");
    exit();
  }
}

echo("<?xml version=\"1.0\" encoding=\"utf-8\"?>\n");

?>

<html>
  <head>
    <title>CCC - Tootracker</title>
    <link href="css/style.css" rel="stylesheet">
  </head>

  <body style="background-color: #E8E;">
    <br /><br /><br /><br /><br /><br /><br /><br /><br />
    <center>
	  <? if (isset($_GET['fail'])) { echo "FAIL"; } ?>
      <h1>CCC - Tooltracker<br /><br /></h1>
      <div class="yellow">
        <h3>Logga in utblippare</h3><br />
        <form id="loginForm" action="login.php" method="post" />
          Legitimation (CCC eller CC):<br />
          <input type="text" name="barcode" /><br /><br />
          <input type="submit" value="Logga in" />
        </form>
        <script language="javascript">document.getElementById('loginForm').barcode.focus()</script>
      </div>
    </center>
  </body>
</html>



