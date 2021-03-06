<?php
  // small php script that calls our db with chat logs
  // and returns it through the echo (this is called in chat.js with ajax)

  // Set the content type.
  header('Content-Type: text/plain');

  $errors = array();
  try {
    $conn = new PDO(/* this should include connection details to database with chat logs */);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
  } catch(PDOException $e) {
    $errors[] = "Connection failed: " . $e->getMessage();
  }

  // only do all our stuff on a successful connection!!!
  if (sizeof($errors) === 0) {
    $method = $_SERVER['REQUEST_METHOD'];
    if ($method === 'GET') {
      // get means, get the shit! and then display
      $query = $conn->prepare("SELECT id, name, message FROM logs;");
      $result = $query->execute();
      if (!$result) {
        $errors[] = "Failed select!";
      }
      $data = $query->fetchAll(PDO::FETCH_ASSOC);
      if ($data === false) {
        $errors[] = "Failed fetch!";
      }
      $results = array();
      foreach ($data as $row) {
        $results[] = array('name' => $row['name'], 'message' => $row['message']);
      }
      echo json_encode($results);
    } elseif ($method === 'POST') {
      // post means! add the comment to the db
      $name = $_POST['name'];
      $message = $_POST['message'];
      $pub_date = $_POST['date'];
      $query = $conn->prepare("INSERT INTO logs (name, message, pub_date) VALUES (:name, :message, :pub_date);");
      $result = $query->execute(array('name' => $name, 'message' => $message, 'pub_date' => $pub_date));
      if (!$result) {
        $errors[] = "Failed insert!";
      }
    }
  } else {
    $results = array();
    $results[0] = array('name' => 'hi', 'message' => 'yeano');
    echo json_encode($results);
  }
?>