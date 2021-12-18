<?php
$user = "bauenfreight";
$password = "93bauenSOG;";
$database = "bauenfreight";
$table = "trns_requests";

try {
  $db = new PDO("mysql:host=localhost;dbname=$database", $user, $password);
  echo "<h2>TODO</h2><ol>";
  foreach($db->query("SELECT request_id FROM $table") as $row) {
    echo "<li>" . $row['request_id'] . "</li>";
  }
  echo "</ol>";
} catch (PDOException $e) {
    print "Error!: " . $e->getMessage() . "<br/>";
    die();
}
