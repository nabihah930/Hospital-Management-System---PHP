<?php
session_start();
?>
<!DOCTYPE html>
    <head>
        <meta charset="utf-8" />
        <meta http-equiv="x-ua-compatible" content="ie=edge" />
        <meta name="viewport" content="width=device-width, initial-scale=1" />
        <title>
            "NewLife Hospital-Book a consultation"</title>
		    <link rel="stylesheet" href="style.css" />
    </head>
    <body>
        <h1>Booking a Consulation</h1>
        <h1 style="text-align:left;">Appointment Details:</h1>
        <h3>
        <?php
            require "database_config.php";											    #file needed in order to connect to our database
            require "convert_to_HTML.php";                                              #file needed in order to convert to HTML characters that will be displayed
            require "functions.php";
            #echo "<br>Room chosen: " . $_SESSION['room_chosen'] . "<br>For: " . $_SESSION["current_id"];
            
            #echo "<br>" . $x . " " . $_SESSION['rooms'][$_SESSION['room_chosen']];
            #foreach($_SESSION['rooms'] as $rooms => $capacity){
            #    echo "<br>" . $rooms . "= " . $capacity;
            #}
            $connection = new PDO($dsn, $username, $password, $options);
            #for room charges and type
            $room_info = "SELECT room_type, charges FROM rooms WHERE room_id = :room_num";
            $statement4 = $connection->prepare($room_info);
            $statement4->bindParam(':room_num', $_SESSION['room_chosen'], PDO::PARAM_STR);
            $statement4->execute();
            $room_result = $statement4->fetch();
            #add appointment charges, doctor's charges and room charges
            $total_app_charges = $_SESSION['charges'] + $_SESSION['dr_charges'] + $room_result['charges'];

            #to check if table is empty, this way is better than rowCount() uncomment line 95 to check
            $check_first = "SELECT COUNT(*) AS existing FROM appointments";
            $statement1 = $connection->prepare($check_first);
            $statement1->execute();
            $check_first_result = $statement1->fetch();
            $app_num="";

            switch($check_first_result['existing']){
                case 0:
                    $app_num = create_appt_num("0");
                    break;
                default:
                    $get_app_num = "SELECT MAX(appointment_num) as max_num FROM appointments";
                    $statement2 = $connection->prepare($get_app_num);
                    $statement2->execute();
                    $max_appNum = $statement2->fetch();
                    $app_num = create_appt_num($max_appNum['max_num']);
                    break;
            }
            #echo "<br>Appointment num: " . $app_num;
            #echo "<br>Row Count: " . ($max_appNum && $statement2->rowCount()) . "<br>Using count(*): " . $check_first_result['existing'];

            #to get patient details
            $get_patient = "SELECT * FROM patients WHERE patient_id = :id";
            $statement3 = $connection->prepare($get_patient);
            $statement3->bindParam(':id', $_SESSION["current_id"], PDO::PARAM_STR);
            $statement3->execute();
            $patient_details = $statement3->fetch();

            if(isset($_POST['book_appointment'])){
                try{
                    echo "<br>Booking a Rs." . $total_app_charges . " appointment";
                    $new_appointment = array(
                        "appointment_num" => $app_num,
                        "appointment_type" => $_SESSION['app_type'],
                        "patient_id" => $_SESSION["current_id"],
                        "doctor_id" => $_SESSION['dr_id'],
                        "nurse_id" => NULL,
                        "room_id" => $_SESSION['room_chosen'],
                        "start_time" => $_SESSION['start_time'],
                        "end_time" => $_SESSION['end_time'],
                        "app_day" => $_SESSION['day'],
                        "charges" => $total_app_charges,
                    );
                    $book_appointment = "INSERT INTO appointments(appointment_num, appointment_type, patient_id, doctor_id, nurse_id, room_id, start_time, end_time, app_day, charges) 
                                        VALUES (:appointment_num, :appointment_type, :patient_id, :doctor_id, :nurse_id, :room_id, :start_time, :end_time, :app_day, :charges)";
                    $statement5 = $connection->prepare($book_appointment);
                    $statement5->execute($new_appointment);
                    header('location: view_appointments.php');
                }
                catch(PDOException $error){
                    echo "<br>" . $error->getMessage();
                }

            }
            if(isset($_POST['cancel_appointment'])){
                try{
                    $total_app_charges = 0;
                    #emptying the time slot and room
                    $_SESSION['dates'][$_SESSION['day']] = "null";
                    if($_SESSION['rooms'][$_SESSION['room_chosen']]==1){                    #check if that was the only reservation for the room
                        $_SESSION['rooms'][$_SESSION['room_chosen']]=-1;
                    }
                    elseif($_SESSION['rooms'][$_SESSION['room_chosen']]>1){                 #if other reservations for other patients just subtract 1 from capacity
                        $_SESSION['rooms'][$_SESSION['room_chosen']] = $_SESSION['rooms'][$_SESSION['room_chosen']]-1;
                    }
                    $_SESSION['charges'] = 0;
                    $_SESSION['dr_charges'] =0;

                    header('location: appointment.php');
                    #echo "<br>Appointment cancelled!";
                }
                catch(PDOException $error){
                    echo "<br>" . $error->getMessage();
                }
            }
        ?>
        </h3>
        <br><h2 style="text-align:left;">Patient Information</h2>
        <h3>
        Patient ID: <?php echo $_SESSION["current_id"] . "<br>"?>
        First name: <?php echo $patient_details['fname'] . "<br>"?>
        Last name: <?php echo $patient_details['lname'] . "<br>"?>
        Gender: <?php echo $patient_details['gender'] . "<br>"?>
        Age: <?php echo $patient_details['age'] . "<br>"?>
        ------------------------------------------------------------------------<br>
        </h3>
        <h2 style="text-align:left;">Appointment Information</h2>
        <h3>
        Appointment number: <?php echo $app_num . "<br>"?>
        <!--Appointment type: <?php echo $_SESSION['app_type'] . "<br>"?>-->
        Doctor: <?php echo "Dr. " . $_SESSION['dr'] . "<br>"?>
        Room number: <?php echo $_SESSION['room_chosen'] . "<br>"?>
        Room type: <?php echo $room_result['room_type'] . "<br>"?>
        Day: <?php echo $_SESSION['day'] . "<br>"?>
        Time Slot: <?php echo $_SESSION['start_time'] . "-" . $_SESSION['end_time'] . "<br>"?>
        Charges: <?php echo $total_app_charges . "<br>"?><br>
        <br>
        <form method="post">
            <input type="submit" value="Book appointment" name="book_appointment" id="book_appointment">
            <input type="submit" value="Cancel appointment" name="cancel_appointment" id="cancel_appointment">
        </form>
        </h3>
        <footer>
            <script>
                if ( window.history.replaceState ) {
                    window.history.replaceState( null, null, window.location.href );
                }
            </script>
        </footer>
    </body>
</html>