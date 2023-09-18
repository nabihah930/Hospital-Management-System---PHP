<?php
session_start();
#echo $_SESSION["current_id"] . "<br>"      #you can see that the session is the same since the id will be the same
?>

<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8" />
    <meta http-equiv="x-ua-compatible" content="ie=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />

    <title>Newlife Hospital-Doctor Homepage</title>

    <link rel="stylesheet" href="style.css" />
  </head>
  <body>
    <h1>Newlife Hospital</h1>
    <h2>Hello Doctor</h2>
    <ul>
        <li>
            <a href="dr_schedule.php"><strong>View Schedule</strong></a>     <!--Their appointments-->
        </li>
        <li>
            <a href="blood_bank.php"><strong>Blood Bank</strong></a>     <!--Priveledged view of blood bank-->
        </li>
        <li>
            <a href="cafe_order.php"><strong>Cafeteria</strong></a>     <!--Place order from cafeteria-->
        </li>
    </ul>
  </body>
</html>