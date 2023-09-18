<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8" />
        <meta http-equiv="x-ua-compatible" content="ie=edge" />
        <meta name="viewport" content="width=device-width, initial-scale=1" />
        <title>
            "NewLife Hospital-Departments"</title>
			<link rel="stylesheet" href="style.css" />
		</head>
        <body>
		<h1>NewLife Hospital<br>Departments</h1>
		<div>
		<?php

      require "database_config.php";
      require "convert_to_HTML.php";
    try {
        $connection = new PDO($dsn, $username, $password, $options);
        $connection->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
		echo "<h2>Cardiology</h2>";
		echo "Newlife Hospital is the private sector institute for Cardio Vascular diseases in Karachi, Pakistan. Newlife Heart and Vascular center provides the most advanced and comprehesnive care for cardiac, vascular and pulmonary problems in Pakistan.We provide you with
		specialized and affordable cardiac care to patients. Cardiology is one of the most important component of medicine. Well trained cardiology consultants provide cardiac facilities to patients who are admitted in other medical and surgical disciplines.Check the information about the best cardiologists.";	
	    $sql = $connection->prepare("SELECT dept_number,dept_email FROM departments where dept_ID = 'CDP01'");
		echo "<a href='cardiology.php' target='_blank'><strong>Click here.</strong></a> ";
		$sql->execute();
		$statement = $sql->setFetchMode(PDO::FETCH_ASSOC);
		$rows = $sql->fetchAll(PDO::FETCH_ASSOC);
		echo "<br>For more information contact us";
		foreach($rows as $row){
        echo  '<br>' . $row['dept_number']. '<br>' ;
        echo  $row['dept_email']. '<br>' ;
	    }
        echo "<h2>Orthopedics</h2>";
		echo "Orthopedic is the branch of surgery concerned with conditions involving the musculoskeletal system. We have provided comprehensive
		care for all skeleton conditions. Our surgeons, well known on both national and international level,devotedly and skillfully diagnose and treat incoming trauma cases
        and chronic musculoskeletal diseases. It also coordinates the management and rehabilition of patients after injury.
        A physiotherapist with special training in orthopedic rehabilition has been dedicated to patients postoperative rehabilition.Check the information about the best orthopedics.";	
	    $sql = $connection->prepare("SELECT dept_number,dept_email FROM departments where dept_ID = 'ODP01'");
		echo "<a href='orthopedics.php' target='_blank'><strong>Click here.</strong></a> ";
		$sql->execute();
		$statement = $sql->setFetchMode(PDO::FETCH_ASSOC);
		$rows = $sql->fetchAll(PDO::FETCH_ASSOC);
		echo "<br>For more information contact us";
		foreach($rows as $row){
        echo  '<br>' . $row['dept_number']. '<br>' ;
        echo  $row['dept_email']. '<br>' ;
	    }
        echo "<h2>Hematology</h2>";
		echo " Hematology department offers a full battery of routine and special hematology tests, Body fluid routine analysis including cerebrospinal fluid
		,routine and special coagulation profile,bone marrow biopsies and complete set of Immunohisochemical analysis for accurate
		and final diagnosis of Hematological malignancies.It comprises of experienced surgeons and has modern art equipments.Check the information about the best orthopedics.";	
	    $sql = $connection->prepare("SELECT dept_number,dept_email FROM departments where dept_ID = 'HDP01'");
		echo "<a href='hematology.php' target='_blank'><strong>Click here.</strong></a> ";
		$sql->execute();
		$statement = $sql->setFetchMode(PDO::FETCH_ASSOC);
		$rows = $sql->fetchAll(PDO::FETCH_ASSOC);
		echo "<br>For more information contact us";
		foreach($rows as $row){
        echo  '<br>' . $row['dept_number']. '<br>' ;
        echo  $row['dept_email']. '<br>' ;
	    }
	    }
	   catch(PDOException $error) {
       echo $sql . "<br>" . $error->getMessage();
         }
        ?>
		</div>
        <h3>
		<strong><a href="homepage_patient.php">Back to homepage</a></strong>
		</h3>
		</body>
</html>
