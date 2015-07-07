<?php
//Proteje contra MySQL injection
function DBEscape($dados) {
  $link = DBConnection();
  if(!is_array($dados)) {
    $dados = mysqli_real_escape_string($link, $dados);
  } else {
    $arr = $dados;
    foreach($arr as $key => $value) {
      $key = mysqli_real_escape_string($link, $key);
      $value = mysqli_real_escape_string($link, $value);

      $dados[$key] = $value;
    }
  }
  DBClose($link);
  return $dados;
}

//Fecha Conexão
function DBClose($link) {
  mysqli_close($link) or die (mysqli_error($link));
}
//Abre conexão com Mysql
function DBConnection() {
  $link = mysqli_connect(DB_HOSTNAME, DB_USERNAME, DB_PASSWORD, DB_DATABASE) or die(mysqli_connect_error());
  mysqli_set_charset($link, DB_CHARSET) or die(mysqli_error($link));

  return $link;
}

//Executa Query

function DBExecution($query) {
  $link = DBConnection();
  $result = mysqli_query($link, $query) or die(mysqli_error($link));
  DBClose($link);
  return($result);

}

//Gravar registros
function DBCreate($table,array $data) {
  $table = DB_PREFIX.'_'. $table;
  $data = DBEscape($data);
  $fields = implode(', ' ,array_keys($data));
  $values = "'".implode("', '", $data)."'";

  $query = "INSERT INTO {$table} ({$fields}) VALUES ({$values})";
  return DBExecution($query);
}

//ler registros
function DBRead($table, $params = null, $fields = '*') {
  $table = DB_PREFIX.'_'. $table;
  $params = ($params) ? " {$params}" : null;

  $query = "SELECT {$fields} FROM {$table}{$params}";
  $result = DBExecution($query);

  if(!mysqli_num_rows($result)){
    return false;
  } else {
    while($res = mysqli_fetch_array($result)) {
      $data[] = $res;
    }
  }
  return $data;
}


//Atualizar Registros
function DBUpDate($table, array $data, $where = null) {
  foreach($data as $key => $value) {
    $fields[] = "{$key} = '{$value}'";
  }
  $fields = implode(", ", $fields);

  $table = $table = DB_PREFIX.'_'. $table;
  $where = ($where) ? " WHERE {$where}" : null;

  $query = "UPDATE {$table} SET {$fields}{$where}";
  return DBExecution($query);
}


//Deletar Registros
function DBDelete($table, $where = null) {
  $table = $table = DB_PREFIX.'_'. $table;
  $where = ($where) ? " WHERE {$where}" : null;

  $query = "DELETE FROM {$table}{$where}";
  return DBExecution($query);
}

?>