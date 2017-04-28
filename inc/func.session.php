<?

session_start("tooltracker");

if (!isset($_SESSION['state'])) {
  $_SESSION['state'] = "inactive";
}

?>
