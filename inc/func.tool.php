<?

function getAllTools() {
  return mysqlFetchAll("SELECT * FROM tool ORDER BY name");
}

function getAllToolsWithoutCategory() {
  return mysqlFetchAll("SELECT * FROM tool WHERE category IS NULL OR category = \"\" ORDER BY name");
}

function getAllToolsWithCategory($category) {
  return mysqlFetchAll("SELECT * FROM tool WHERE category = \"" . $category . "\" ORDER BY name");
}

function getCommonTools() {
  return mysqlFetchAll("SELECT * FROM tool WHERE common = \"1\" ORDER BY name");
}

function getTool($toolId) {
  if (!is_numeric($toolId)) {
    if (substr($toolId, 0, 1) == "T")
      $toolId = substr($toolId, 1);
    else
      return false;
  }
  return mysqlFetchOne("SELECT * FROM tool WHERE toolId = ".$toolId);
}

function addTool($name, $stock, $category) {
  if (strlen($category) == 0) {
	$category = null;
  }
  mysql_query("INSERT INTO tool (name, stock, category) VALUES(\"".$name."\", ".$stock.", \"".$category."\");");
}

function updateToolStock($toolId, $stock) {
  mysql_query("UPDATE tool SET stock = ".$stock." WHERE toolId = ".$toolId);
}

function getAllToolCategories() {
  return mysqlFetchAll("SELECT category FROM tool WHERE category IS NOT NULL GROUP BY category ORDER BY name");
}
?>
