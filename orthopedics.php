<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8" />
        <meta http-equiv="x-ua-compatible" content="ie=edge" />
        <meta name="viewport" content="width=device-width, initial-scale=1" />
        <title>
            "NewLife Hospital/Departments/Orthopedics"</title>
			<link rel="stylesheet" href="style.css" />
		</head>
        <body>
		<div>
		<?php
      require "database_config.php";
      require "convert_to_HTML.php";
    try {
        $connection = new PDO($dsn, $username, $password, $options);
        $connection->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
		echo "<h2>Orthopedics</h2>";
	    $sql = $connection->prepare("SELECT fname,lname,start_time,end_time from doctors INNER JOIN shifts 
		on doctors.shift_num = shifts.shift_num where dept_id='ODP01'");
		$sql->execute();
		$statement = $sql->setFetchMode(PDO::FETCH_ASSOC);
		$rows = $sql->fetchAll(PDO::FETCH_ASSOC);
		foreach($rows as $row){
        echo  '<br>' . $row['fname'] . " " . $row['lname'];
		echo "<br> General Physician";
		echo "<br> Schedule <br>";
		echo 'Timings: ' . $row['start_time'] . '  ' . $row['end_time'] . '<br>';
		#echo "<a href='appointment.php' target='_blank'><strong>Book an appointment</strong></a> ";
		echo "<br>";
	    }
	    }
	   catch(PDOException $error) {
       echo $sql . "<br>" . $error->getMessage();
         }
        ?>
		<br><strong><a href="homepage_patient.php">Back to homepage</a></strong></break>
		<!--<br><strong><a href="appointment.php">Book an appointment</a></strong></br>-->
		</div>
		</body>
</html>
