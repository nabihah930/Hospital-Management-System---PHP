<?php

/**
  * Open a connection via PDO*(can use mysqli_connect() however parameter list will vary) to create a
  * new database and table with structure.
  *
  */

require "database_config.php";

try {
  $connection = new PDO("mysql:host=$host", $username, $password, $options);
  $sql = file_get_contents("hospital_management.sql");
  $connection->exec($sql);

  echo "Hospital database, tables created successfully.";
} catch(PDOException $error) {
  echo "<br>" . $sql . "<br>" . $error->getMessage();
}