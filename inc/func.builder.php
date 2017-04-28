<?

function addBuilder($builderId, $teamId, $title, $name) {
  mysql_query("INSERT INTO builder (builderId, teamId, title, name) VALUES(\"".$builderId."\", \"".$teamId."\", \"".$title."\", \"".$name."\");");
}

function getAllBuilders() {
  return mysqlFetchAll("SELECT * FROM builder ORDER BY teamId, title");
}

function getAllBuildersWithoutId() {
  return mysqlFetchAll("SELECT * FROM builder Where id = '0' ORDER BY teamId, title");
}

function getBuilder($builderId) {
  return mysqlFetchOne("SELECT * FROM builder WHERE builderId = \"".$builderId."\"");
}

?>
