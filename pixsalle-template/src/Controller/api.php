<?php
include "config.php";


$dbConn =  connect($db);

/*
  listar todos los posts o solo uno
 */
if ($_SERVER['REQUEST_METHOD'] == 'GET')
{
    if (isset($_GET['id']))
    {
      //Mostrar un post
      $sql = $dbConn->prepare("SELECT * FROM posts where id=:id");
      $sql->bindValue(':id', $_GET['id']);
      $sql->execute();

      $content = $sql->fetch(PDO::FETCH_ASSOC);
      if (!isset($content)){
        header("HTTP/1.1 404 Blog entry with id {id} does not exist");
        exit();
      }

      header("HTTP/1.1 200 OK");
      echo json_encode($content);
      exit();
	  }
    else {
      //Mostrar lista de post
      $sql = $dbConn->prepare("SELECT * FROM posts");
      $sql->execute();
      $sql->setFetchMode(PDO::FETCH_ASSOC);
      header("HTTP/1.1 200 OK");
      echo json_encode( $sql->fetchAll()  );
      exit();
	}
}

// Crear un nuevo post
if ($_SERVER['REQUEST_METHOD'] == 'POST')
{
    if(!isset($_GET['title']) || !isset($_GET['content']) || !isset($_GET['userId'])){
        header("HTTP/1.1 400 'title' and/or 'content' and/or 'userId' key missing");
        exit();
    }

    $input = $_POST;
    $sql = "INSERT INTO posts
          (title, content, user_id)
          VALUES
          (:title, :content, :user_id)";
    $statement = $dbConn->prepare($sql);
    bindAllValues($statement, $input);
    $statement->execute();
    $postId = $dbConn->lastInsertId();
    if($postId)
    {
      $input['id'] = $postId;
      header("HTTP/1.1 200 OK");
      echo json_encode($input);
      exit();
	 }
}

//Borrar
if ($_SERVER['REQUEST_METHOD'] == 'DELETE')
{
	$id = $_GET['id'];
  $statement = $dbConn->prepare("DELETE FROM posts where id=:id");
  $statement->bindValue(':id', $id);
  $statement->execute();
	header("HTTP/1.1 200 OK");
	exit();
}

//Actualizar
if ($_SERVER['REQUEST_METHOD'] == 'PUT')
{
    if(!isset($_GET['title']) || !isset($_GET['content'])){
        header("HTTP/1.1 400 The title and/or content cannot be empty");
        exit();
    }else if(!isset($_GET['id'])){
        header("HTTP/1.1 404 Blog entry with id {id} does not exist");
        exit();
    }

    $input = $_GET;
    $postId = $input['id'];
    
    $fields = getParams($input);

    $sql = "
          UPDATE posts
          SET $fields
          WHERE id='$postId'
           ";

    $statement = $dbConn->prepare($sql);
    bindAllValues($statement, $input);

    $statement->execute();
    header("HTTP/1.1 200 OK");
    exit();
}

function getParams($input)
{
   $filterParams = [];
   foreach($input as $param => $value)
   {
           $filterParams[] = "$param=:$param";
   }
   return implode(", ", $filterParams);
   }

 //Asociar todos los parametros a un sql
   function bindAllValues($statement, $params)
 {
       foreach($params as $param => $value)
   {
               $statement->bindValue(':'.$param, $value);
       }
       return $statement;
  }

//En caso de que ninguna de las opciones anteriores se haya ejecutado
header("HTTP/1.1 400 Bad Request");

?>