<?

@mysql_connect("localhost", "tooltracker", "password");
@mysql_select_db("tooltracker");

function mysqlFetchAll($sql) {
  $result = mysql_query($sql);
  $values = array();

  while ($v = mysql_fetch_array($result)) {
    array_push($values, $v);
  }

  return $values;
}

function mysqlFetchOne($sql) {
  $result = mysql_query($sql);
  if (mysql_num_rows($result) == 0)
    return false;

  return mysql_fetch_array($result);
}


?>
