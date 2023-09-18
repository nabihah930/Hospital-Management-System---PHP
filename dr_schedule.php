<?php
session_start();
    require "database_config.php";											    #file needed in order to connect to our database
    require "convert_to_HTML.php";                                              #file needed in order to convert to HTML characters that will be displayed
    require "functions.php";
?>
<!DOCTYPE html>
    <head>
        <meta charset="utf-8" />
        <meta http-equiv="x-ua-compatible" content="ie=edge" />
        <meta name="viewport" content="width=device-width, initial-scale=1" />
        <title>"NewLife Hospital-Dr Schedule"</title>
		<link rel="stylesheet" href="style.css" />
    </head>
    <body>
        <h1>Schedule</h1>
        <?php
            $connection = new PDO($dsn, $username, $password, $options);
            
            $check_first = "SELECT COUNT(*) AS existing FROM appointments WHERE doctor_id = :id";
            $statement1 = $connection->prepare($check_first);
            $statement1->bindParam(':id', $_SESSION["current_id"], PDO::PARAM_STR);
            $statement1->execute();
            $check_first_result = $statement1->fetch();
            if($check_first_result['existing']==0){
                echo "<br>No appointment made.";
            }
            else{
                $get_patient_records = "SELECT appointments.*, appointment_name, room_type, fname, lname, age, gender 
                                        FROM appointments 
                                        INNER JOIN appointment_info USING (appointment_type)
                                        INNER JOIN rooms USING (room_id) 
                                        INNER JOIN patients USING (patient_id)
                                        WHERE doctor_id = :id";
                    #maybe instead of these joins we could take a slower approach i.e. for every record(appointment) in a loop pick up the room
                    #and doctors and appointment_info infotmation seperately
                $statement = $connection->prepare($get_patient_records);
                $statement->bindParam(':id', $_SESSION["current_id"], PDO::PARAM_STR);
                $statement->execute();
                $patient_records = $statement->fetchAll();
                if ($patient_records && $statement->rowCount() > 0){
                    foreach($patient_records as $row){
                        echo "<h3>Appointment Number: " . $row['appointment_num'] . "<br>Appointment Type: " . $row['appointment_name'] . "<br>";
                        echo "Room Number: " . $row['room_id'] . "<br>Room Type: " . $row['room_type'] . "<br>";
                        echo "Patient: " . $row['fname'] . " " . $row['lname'] . "<br>";
                        echo "Gender: " . $row['gender'] . "<br>Age: " . $row['age'] . "<br>";
                        echo "Day: " . $row['app_day'] . "<br>Timing: " . $row['start_time'] . "-" . $row['end_time'] . "<br>";
                        echo "Charges: " . $row['charges'] . "<br>";
                        echo "-----------------------------------------------------------------------------------------------------------------</h3><br>";
                    }
                }
            }
        ?>
        <h3><strong><a href="homepage_doctor.php">Back to homepage</a></strong></h3>
    </body>
</html>