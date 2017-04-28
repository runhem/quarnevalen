<?

function getAllCurrentToolLoans() {
  return mysqlFetchAll("SELECT l.*, b.name as builderName, t.name as toolName FROM tool t, loan l NATURAL JOIN builder b WHERE t.toolId = l.toolId AND l.returnedDate IS NULL ORDER BY b.teamId, l.loanDate, l.loanTime");
}

function getCurrentToolLoans($toolId) {
  return mysqlFetchAll("SELECT l.*, b.name as builderName FROM loan l NATURAL JOIN builder b WHERE l.toolId = ".$toolId." AND l.returnedDate IS NULL ORDER BY l.loanDate, l.loanTime");
}

function getToolLoans($toolId) {
  return mysqlFetchAll("SELECT l.*, t.name as toolName FROM loan l NATURAL JOIN tool t WHERE l.toolId = ".$toolId." ORDER BY l.loanDate, l.loanTime");
}

function borrowTool($toolId, $builderId) {
  $builder = getBuilder($builderId);
  mysql_query("INSERT INTO loan (teamId, builderId, loanDate, loanTime, toolId) VALUES(\"".$builder['teamId']."\", \"".$builder['builderId']."\", NOW(), NOW(), ".$toolId.");");
}

function hasTool($toolId, $builderId) {
  $builder = getBuilder($builderId);
  $loan = mysqlFetchOne("SELECT * FROM loan WHERE toolId = ".$toolId." AND teamId = \"".$builder['teamId']."\" AND returnedDate IS NULL");
  if ($loan == false)
    return false;
	return true;
}

function returnTool($toolId, $builderId) {
  $builder = getBuilder($builderId);
  $loan = mysqlFetchOne("SELECT * FROM loan WHERE toolId = ".$toolId." AND teamId = \"".$builder['teamId']."\" AND returnedDate IS NULL");
  if ($loan == false)
    return false;

  mysql_query("UPDATE loan SET returnBuilderId = \"".$builder['builderId']."\", returnedDate = NOW(), returnedTime = NOW() WHERE loanId = ".$loan['loanId']);
  return true;
}

function getCurrentTeamLoans($teamId) {
  return mysqlFetchAll("SELECT l.*, b.name as builderName, t.name as toolName FROM tool t, loan l NATURAL JOIN builder b WHERE l.teamId = \"".$teamId."\" AND l.returnedDate IS NULL AND t.toolId = l.toolId ORDER BY l.loanDate, l.loanTime");

}

?>
