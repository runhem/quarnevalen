<?

function getAllTeams() {
  return mysqlFetchAll("SELECT t.*, GROUP_CONCAT(b.name, \" (\", b.title, \")\" ORDER BY b.title DESC SEPARATOR \", \") as vc FROM team t NATURAL JOIN builder b GROUP BY teamId");
}

function addTeam($teamName, $description, $toolLimit) {
  mysql_query("INSERT INTO team (teamId, description, toolLimit) VALUES(\"".$teamName."\", \"".$description."\", $toolLimit);");
}

function getTeam($teamId) {
  return mysqlFetchOne("SELECT * FROM team WHERE teamId = \"".$teamId."\"");
}

function updateToolLimit($teamId, $toolLimit) {
  mysql_query("UPDATE team SET toolLimit = ".$toolLimit." WHERE teamId = \"".$teamId."\"");
}

function getBuilderTeam($builderId) {
  return mysqlFetchOne("SELECT t.* FROM team t NATURAL JOIN builder b WHERE b.builderId = \"".$builderId."\"");
}

?>
