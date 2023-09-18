<?php
session_start();
#echo $_SESSION["current_id"] . "<br>"      #you can see that the session is the same since the id will be the same
  require "database_config.php";											    #file needed in order to connect to our database
  require "convert_to_HTML.php";                                              #file needed in order to convert to HTML characters that will be displayed
  require "functions.php";

  $connection = new PDO($dsn, $username, $password, $options);
  $get_patient_info = "SELECT * FROM patients WHERE patient_id= :id";
  $statement = $connection->prepare($get_patient_info);
  $statement->bindParam(':id', $_SESSION["current_id"], PDO::PARAM_STR);
  $statement->execute();
  $patient_info = $statement->fetch();

?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8" />
    <meta http-equiv="x-ua-compatible" content="ie=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />

    <title>Newlife Hospital-Patient Homepage</title>

    <link rel="stylesheet" href="style.css" />
  </head>
  <body>
    <h1>Newlife Hospital</h1>
    <h2>Hello <?php echo $patient_info['fname']?> !</h2>
    <h2 style="text-align:left;">
    <ul>
        <li>
            <a href="appointment.php"><strong>Appoinments</strong></a>     <!--To book appointments-->
        </li>
        <li>
            <a href="sub_bank.php"><strong>Blood Bank</strong></a>     <!--Patient view of blood bank-->
        </li>
        <li>
        <a href="departments.php"><strong>Departments</strong></a>     <!--To book appointments-->
        </li>
        <li>
        <a href="view_appointments.php"><strong>View your appointments</strong></a>     <!--To view appointments made-->
        </li>
    </ul>
    </h2>
  </body>
</html>