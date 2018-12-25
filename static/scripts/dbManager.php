<?php 

function is_row_exists($table, $row_id){
	$pdo = _dbConnect();
	$stmt = $pdo->prepare('SELECT COUNT(*) FROM '.$table.' WHERE id=?');
	$stmt->execute(array($row_id));
	$exists = $stmt->fetchColumn();
	$pdo = null;
	$stmt = null;
	if($exists)
		return true;
	else 
		return false;
}
function get_limit_range(&$rows_per_page,&$page){
	global $defaultRowsPerPage;

	if(isset($_COOKIE['rows_per_page']) && is_numeric($_COOKIE['rows_per_page']) && $_COOKIE['rows_per_page'] > 0)
		$rows_per_page = $_COOKIE['rows_per_page'];
	else
		$rows_per_page = $defaultRowsPerPage;
	if(isset($_GET['page']) && is_numeric($_GET['page']))
		$page = $_GET['page']-1;
	else 
		$page = 0;

	return ($rows_per_page * $page).','.$rows_per_page;
}
function get_links_range($rows_per_page, $rows_count, $page){
	$max_page = ceil($rows_count / $rows_per_page);
	$page++;
	$start_page = $page > 5 ? $page - 5 : 1;
	$end_page = $page <= $max_page - 5 ? $page + 5 : $max_page;
	return array($start_page, $end_page);
}
function settle_search($fields, &$values, $is_without_prefix = false){
	$search_clause = "";
	$values = array();

	foreach ($fields as $getName => $dbName) {
		if(!empty($_GET["$getName"])){
			$search_clause .= " $dbName LIKE :$getName AND";
			$values["$getName"] = "%".$_GET["$getName"]."%";
		}
	}
	if(!$is_without_prefix && !empty($search_clause))
		$search_clause = "AND ".$search_clause;

	$search_clause = substr($search_clause, 0, -3); 

	return $search_clause;
}
function select_rows($preliminary_query, &$rows_count, $values=array()){
	$pdo = _dbConnect();
	$stmt = $pdo->prepare($preliminary_query);
	$stmt->execute($values);
	$rows_count = $pdo->query('SELECT FOUND_ROWS()')->fetchColumn();
	$result = $stmt->fetchAll();
	$pdo = null;
	$stmt = null;
	return $result;
}

function insert_row($table, $allowed_fields, &$error, $return_to=false){
	$pdo = _dbConnect();
	$stmt = $pdo->prepare("INSERT INTO $table SET"._pdoSet($allowed_fields, $values));

	if(_checkErrors($stmt, $allowed_fields, $error))
		return false;

	$stmt->execute($values);
	$pdo = null;
	$stmt = null;
	if($return_to)
		header("Location: $return_to");
}
function update_row($table, $row_id, $allowed_fields, $return_to=false, &$error){
	$pdo = _dbConnect();
	$stmt = $pdo->prepare("UPDATE $table SET"._pdoSet($allowed_fields, $values).' WHERE id=:id');

	if(_checkErrors($stmt, $allowed_fields, $error))
		return false;

	$values['id'] = $row_id;
	$stmt->execute($values);
	$pdo = null;
	$stmt = null;
	if($return_to)
		header("Location: $return_to");
}
function eliminate_row($table, $row_id, $return_to=false){
	$pdo = _dbConnect();
	$stmt = $pdo->prepare("UPDATE $table SET is_active=false WHERE id=?");
	$stmt->execute(array($row_id));
	$pdo = null;
	$stmt = null;
	if($return_to)
		header("Location: $return_to");
}

function _dbConnect(){
	global $opt;
	return new PDO("mysql:dbname=building materials;host=127.0.0.1;charset=utf8", 'root', '', $opt);
}
function _pdoSet($allowed, &$values, $source = array()) {
	$set = '';
	$values = array();
	if (!$source) $source = &$_POST;
	foreach ($allowed as $field) {
		if (isset($source[$field])) {
			$set.="`".str_replace("`","``",$field)."`". "=:$field, ";
			$values[$field] = $source[$field];
		}
	}
	return substr($set, 0, -2); 
}
function _checkErrors($stmt, $allowed_fields, &$error){
	foreach ($allowed_fields as $field) {
		if($_POST[$field] === ''){
			$error = 'Заполните все поля!';
			return true;
		}
	}
	if(!isset($stmt)){
		$error = 'Возникла ошибка. Попробуйте еще раз.';
		return true;
	}
}
?>