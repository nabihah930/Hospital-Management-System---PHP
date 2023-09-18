<?php
session_start();
?>
<!DOCTYPE html>
    <head>
        <meta charset="utf-8" />
        <meta http-equiv="x-ua-compatible" content="ie=edge" />
        <meta name="viewport" content="width=device-width, initial-scale=1" />
        <title>
            "NewLife Hospital-Book a physical"</title>
		    <link rel="stylesheet" href="style.css" />
    </head>
    <body>
        <h1>Booking a Physical</h1>
        <h3>
        <?php

            try {
                require "database_config.php";											    #file needed in order to connect to our database
                require "convert_to_HTML.php";                                              #file needed in order to convert to HTML characters that will be displayed
                require "functions.php";
                
                $_SESSION['app_type'] = "PHY01";
                $connection = new PDO($dsn, $username, $password, $options);                #establish the connection to the db
                
                #Doctors retrieved via views made in view.php
                $get_dr_cardio = "SELECT * FROM dr_cardio";                                 #get all the doctors from cardiology
                $statement1 = $connection->prepare($get_dr_cardio);
                $statement1->execute();
                $dr_cardio = $statement1->fetchAll();
                
                $get_dr_ortho = "SELECT * FROM dr_ortho";                                   #get all the doctors from orthopedics
                $statement2 = $connection->prepare($get_dr_ortho);
                $statement2->execute();
                $dr_ortho = $statement2->fetchAll();

                $get_dr_hem = "SELECT * FROM dr_hem";                                       #get all the doctors from hematology
                $statement3 = $connection->prepare($get_dr_hem);
                $statement3->execute();
                $dr_hem = $statement3->fetchAll();

                #this info. will be used in the form below
            }
            catch(PDOException $error) {
                echo "<br>Error before submission" .  $error->getMessage();
            }

            #now we'll check if the form for doctor has been submitted and process the input
            if(isset($_POST['dr'])) {
                try {
                    $_SESSION['dr'] = $_POST['dr'];
                    $_SESSION['first_name'] = "";
                    $_SESSION['last_name'] = "";
                    $_SESSION['dates'] = array();

                    #echo "<br> You have chosen Dr. " . $_SESSION['dr'];
                    
                    get_names($_SESSION['dr'],$_SESSION['first_name'],$_SESSION['last_name']);
                    #echo "<br>" . $_SESSION['last_name'] . " to " . $_SESSION['first_name'];
                    $get_id = "SELECT doctor_id, dept_id, charges FROM doctors WHERE fname = :first_name AND lname = :last_name";
                    $statement4 = $connection->prepare($get_id);
                    $statement4->bindParam(':first_name', $_SESSION['first_name'], PDO::PARAM_STR);     #we need to bind the values to the query
                    $statement4->bindParam(':last_name', $_SESSION['last_name'], PDO::PARAM_STR);
                    $statement4->execute();
                    $id_result = $statement4->fetch();

                    $_SESSION['dept_id'] = $id_result['dept_id'];
                    $_SESSION['dr_id'] = $id_result['doctor_id'];
                    $_SESSION['dr_charges'] = $id_result['charges'];

                    #echo "<br>ID: " . $id_result['doctor_id'] . "<br>Dept_ID: " . $id_result['dept_id'];

                    if(CDP($id_result['dept_id'])){                                         #for cardiology department
                        #echo "<br>Consultation for Cardiology with Dr. " . $_SESSION['first_name'] . " " . $_SESSION['last_name']; 
                        $check_clash = "SELECT start_time, end_time, app_day FROM appointments WHERE doctor_id = :id";
                        $statement5 = $connection->prepare($check_clash);
                        $statement5->bindParam(':id', $id_result['doctor_id'], PDO::PARAM_STR);
                        $statement5->execute();
                        $clash_result = $statement5->fetchAll();
                        
                        $get_timings = "SELECT charges, start_time, end_time, day_one, day_two, day_three FROM dr_cardio WHERE doctor_id = :id";
                        $statement6 = $connection->prepare($get_timings);
                        $statement6->bindParam(':id', $id_result['doctor_id'], PDO::PARAM_STR);
                        $statement6->execute();
                        $timings_result = $statement6->fetch();
                        #echo "<br>" . $timings_result['start_time'] . "-" . $timings_result['end_time'];
                        $_SESSION['day_one'] = $timings_result['day_one'];
                        $_SESSION['day_two'] = $timings_result['day_two'];
                        $_SESSION['day_three'] = $timings_result['day_three'];

                        $_SESSION['dates'] = array($timings_result['day_one']=>"null",$timings_result['day_two']=>"null",$timings_result['day_three']=>"null");
                        
                        #$dates = array($timings_result['day_one']=>"null",$timings_result['day_two']=>"null",$timings_result['day_three']=>"null");
                        #echo "<br>" . $dates[$timings_result['day_one']];

                        if($clash_result && $statement5->rowCount() > 0){                   #there may be clashes
                            foreach($clash_result as $row){
                                #echo "<br>" . $row['start_time'] . " " . $row['end_time'] . " " . $row['app_day'];
                                $_SESSION['dates'][$row['app_day']] = $row['end_time'];     #update the rray we will use to check availability
                            }
                            foreach($_SESSION['dates'] as $day => $end){
                                #echo "<br>" . $day . "= " . $end;
                            }

                            $available_days = array();
                            $i =0;
                            
                            #retrieving this doctor's start and end time
                            $get_dr_timings = "SELECT * FROM dr_cardio WHERE doctor_id = :id";
                            $statement9 = $connection->prepare($get_dr_timings);
                            $statement9->bindParam(':id', $id_result['doctor_id'], PDO::PARAM_STR);
                            $statement9->execute();
                            $dr_timings = $statement9->fetch();

                            #retrieving appointment_duration
                            $get_duration = "SELECT * FROM appointment_info WHERE appointment_type = 'PHY01'";
                            $statement7 = $connection->prepare($get_duration);
                            $statement7->execute();
                            $appointment_info = $statement7->fetch();

                            #echo "<br>" . $dr_timings['start_time'] . " " . $dr_timings['end_time'];
                            #echo "<br>" . $appointment_info['duration'] . " " . $appointment_info['appointment_name'];

                            $dr_end = AM_PM($dr_timings['end_time']);
                            ?>
                            <form method="post">
			                    <label for="timing">Choose from availabe slots:</label>
			                    <select id="timing" name="timing">
                                    <option value="">--- Select a slot ---</option>							<!--Default value-->
                            <?php
                            foreach($_SESSION['dates'] as $day => $end){
                                if($end=="null"){                                           #meaning no prior engagements on these days
                                    #echo "<br>Added " . $day . " to the array available_days<br>";
                                    $available_days[$i] = $day;
                                    $i = $i+1;
                                    $new_end = add_times($dr_timings['start_time'],$appointment_info['duration'],0);
                                    #echo "<br>New Time: " . $new_end;
                                    ?>
                                    <option value="<?php echo $day. " " . $dr_timings['start_time'] . " " . $new_end?>">
						                <?php echo $day. " " . $dr_timings['start_time'] . "-" . $new_end?>
					                </option>
                                    <?php
                                }
                                else{
                                    #echo "<br>Dr. has another appointment on " . $day;
                                    $new_end = add_times($end,$appointment_info['duration'],1);
                                    #echo " New Time: " . $new_end . "<br>Dr. End: " . $dr_end;
                                    $time1 = strtotime($new_end);
                                    $time2 = strtotime($dr_end);
                                    #echo "<br>Type of T1(" . $time1 . "): " . gettype($time1) . "<br>Type of T2(" . $time2 . "): " . gettype($time2);
                                    if($time1>$time2){
                                        #echo "<br>The doctor no longer has any free slots on " . $day . ".";
                                    }
                                    else{
                                        #echo "<br>The doctor has at least 1 free slot on " . $day;
                                        $new_end = add_times($end,$appointment_info['duration'],0);
                                        ?>
                                        <option value="<?php echo $day. " " . $end . " " . $new_end?>">
						                    <?php echo $day. " " . $end . "-" . $new_end?>
					                    </option>
                                        <?php
                                    }
                                }
                            }
                            ?>
                                </select>
			                    <input type="submit" name="submit" value="Enter">
		                    </form>
                            <?php
                        }
                        else{                                                               #there can be no clashes
                            #echo "<br>This doctor has no appointments";
                            #all slots are free
                            $book_appointment = "SELECT duration, charges FROM appointment_info WHERE appointment_type = 'PHY01'";
                            $statement7 = $connection->prepare($book_appointment);
                            $statement7->execute();
                            $appointment_info = $statement7->fetch();

                            $_SESSION['charges'] =  $appointment_info['charges'];
                            $_SESSION['duration'] =  $appointment_info['duration'];

                            #$end_time = strtotime($timings_result['start_time']);           #convert the string to time
                            #$end_time = $end_time+(20*60);                                  #convert minutes to seconds then add to time
                            #$end_time = date('h:i:s',$end_time);
                            $end_time = add_times($timings_result['start_time'],$appointment_info['duration'],0);
                            #echo "<br>Start_time: " . $timings_result['start_time'] . "<br>End_time: " . $end_time;
                            #echo "<br>" . $timings_result['start_time'] . "-" . $end_time;

                            #now make a form and and display the availabe time slots(I decided on 3 otherwise there would have been 27!)
                            ?>
                            <form method="post">
			                    <label for="timing">Choose from availabe slots:</label>
			                    <select id="timing" name="timing">
                                    <option value="">--- Select a slot ---</option>							<!--Default value-->
				                        <?php
					                    foreach ($_SESSION['dates'] as $day => $time) {                  #display all the options
				                        ?>
					                        <option value="<?php echo $day. " " . $timings_result['start_time'] . " " . $end_time?>">
						                        <?php echo $day. " " . $timings_result['start_time'] . "-" . $end_time?>
					                        </option>
					                    <?php
					                    } 
					                    ?>                    
			                    </select>
			                    <input type="submit" name="submit" value="Enter">
		                    </form>
                            <?php
                            }
                    }  
                    elseif(HDP($id_result['dept_id'])){                                     #for hematology department
                        #echo "<br>Consultation for Hematology with Dr. " . $_SESSION['first_name'] . " " . $_SESSION['last_name'];
                        $check_clash = "SELECT start_time, end_time, app_day FROM appointments WHERE doctor_id = :id";
                        $statement5 = $connection->prepare($check_clash);
                        $statement5->bindParam(':id', $id_result['doctor_id'], PDO::PARAM_STR);
                        $statement5->execute();
                        $clash_result = $statement5->fetchAll();
                        
                        $get_timings = "SELECT charges, start_time, end_time, day_one, day_two, day_three FROM dr_hem WHERE doctor_id = :id";
                        $statement6 = $connection->prepare($get_timings);
                        $statement6->bindParam(':id', $id_result['doctor_id'], PDO::PARAM_STR);
                        $statement6->execute();
                        $timings_result = $statement6->fetch();
                        #echo "<br>" . $timings_result['start_time'] . "-" . $timings_result['end_time'];
                        
                        $_SESSION['dates'] = array($timings_result['day_one']=>"null",$timings_result['day_two']=>"null",$timings_result['day_three']=>"null");
                        
                        #$dates = array($timings_result['day_one']=>"null",$timings_result['day_two']=>"null",$timings_result['day_three']=>"null");
                        #echo "<br>" . $dates[$timings_result['day_one']];

                        if($clash_result && $statement5->rowCount() > 0){                   #there may be clashes
                            #echo "<br>This doctor has other appointments";
                            foreach($clash_result as $row){
                                #echo "<br>" . $row['start_time'] . " " . $row['end_time'] . " " . $row['app_day'];
                                $_SESSION['dates'][$row['app_day']] = $row['end_time'];     #update the rray we will use to check availability
                            }
                            #foreach($_SESSION['dates'] as $day => $end){
                            #    echo "<br>" . $day . "= " . $end;
                            #}

                            $available_days = array();
                            $i =0;
                            
                            #retrieving this doctor's start and end time
                            $get_dr_timings = "SELECT * FROM dr_hem WHERE doctor_id = :id";
                            $statement9 = $connection->prepare($get_dr_timings);
                            $statement9->bindParam(':id', $id_result['doctor_id'], PDO::PARAM_STR);
                            $statement9->execute();
                            $dr_timings = $statement9->fetch();

                            #retrieving appointment_duration
                            $get_duration = "SELECT * FROM appointment_info WHERE appointment_type = 'PHY01'";
                            $statement7 = $connection->prepare($get_duration);
                            $statement7->execute();
                            $appointment_info = $statement7->fetch();

                            #echo "<br>" . $dr_timings['start_time'] . " " . $dr_timings['end_time'];
                            #echo "<br>" . $appointment_info['duration'] . " " . $appointment_info['appointment_name'];

                            $dr_end = AM_PM($dr_timings['end_time']);
                            ?>
                            <form method="post">
			                    <label for="timing">Choose from availabe slots:</label>
			                    <select id="timing" name="timing">
                                    <option value="">--- Select a slot ---</option>							<!--Default value-->
                            <?php
                            foreach($_SESSION['dates'] as $day => $end){
                                if($end=="null"){                                           #meaning no prior engagements on these days
                                    #echo "<br>Added " . $day . " to the array available_days<br>";
                                    $available_days[$i] = $day;
                                    $i = $i+1;
                                    $new_end = add_times($dr_timings['start_time'],$appointment_info['duration'],0);
                                    #echo "<br>New Time: " . $new_end;
                                    ?>
                                    <option value="<?php echo $day. " " . $dr_timings['start_time'] . " " . $new_end?>">
						                <?php echo $day. " " . $dr_timings['start_time'] . "-" . $new_end?>
					                </option>
                                    <?php
                                }
                            }
                        }
                        else{                                                               #there can be no clashes
                            #echo "<br>This doctor has no appointments";
                            #all slots are free
                            $book_appointment = "SELECT duration, charges FROM appointment_info WHERE appointment_type = 'PHY01'";
                            $statement7 = $connection->prepare($book_appointment);
                            $statement7->execute();
                            $appointment_info = $statement7->fetch();

                            $_SESSION['charges'] =  $appointment_info['charges'];
                            $_SESSION['duration'] =  $appointment_info['duration'];

                            #$end_time = strtotime($timings_result['start_time']);           #convert the string to time
                            #$end_time = $end_time+(20*60);                                  #convert seconds to minutes then add to time
                            #$end_time = date('h:i:s',$end_time);
                            $end_time = add_times($timings_result['start_time'],$appointment_info['duration'],0);
                            #echo "<br>Start_time: " . $timings_result['start_time'] . "<br>End_time: " . $end_time;
                            #echo "<br>" . $timings_result['start_time'] . "-" . $end_time;

                            #now make a form and and display the availabe time slots
                            ?>
                            <form method="post">
			                    <label for="timing">Choose from availabe slots:</label>
			                    <select id="timing" name="timing">
                                    <option value="">--- Select a slot ---</option>							<!--Default value-->
				                        <?php
					                    foreach ($_SESSION['dates'] as $day => $time) {                  #display all the options
				                        ?>
					                        <option value="<?php echo $day. " " . $timings_result['start_time'] . " " . $end_time?>">
						                        <?php echo $day. " " . $timings_result['start_time'] . "-" . $end_time?>
					                        </option>
					                    <?php
					                    } 
					                    ?>                    
			                    </select>
			                    <input type="submit" name="submit" value="Enter">
		                    </form>
                            <?php
                        }
                    }
                    else{                                                                   #for orthopedics department
                        #echo "<br>Consultation for Orthopedics with Dr. " . $_SESSION['first_name'] . " " . $_SESSION['last_name'];
                        $check_clash = "SELECT start_time, end_time, app_day FROM appointments WHERE doctor_id = :id";
                        $statement5 = $connection->prepare($check_clash);
                        $statement5->bindParam(':id', $id_result['doctor_id'], PDO::PARAM_STR);
                        $statement5->execute();
                        $clash_result = $statement5->fetchAll();
                        
                        $get_timings = "SELECT charges, start_time, end_time, day_one, day_two, day_three FROM dr_ortho WHERE doctor_id = :id";
                        $statement6 = $connection->prepare($get_timings);
                        $statement6->bindParam(':id', $id_result['doctor_id'], PDO::PARAM_STR);
                        $statement6->execute();
                        $timings_result = $statement6->fetch();
                        #echo "<br>" . $timings_result['start_time'] . "-" . $timings_result['end_time'];
                        
                        $_SESSION['dates'] = array($timings_result['day_one']=>"null",$timings_result['day_two']=>"null",$timings_result['day_three']=>"null");
                        
                        #$dates = array($timings_result['day_one']=>"null",$timings_result['day_two']=>"null",$timings_result['day_three']=>"null");
                        #echo "<br>" . $dates[$timings_result['day_one']];
                        if($clash_result && $statement5->rowCount() > 0){                   #there may be clashes
                            #echo "<br>This doctor has other appointments";
                            foreach($clash_result as $row){
                                #echo "<br>" . $row['start_time'] . " " . $row['end_time'] . " " . $row['app_day'];
                                $_SESSION['dates'][$row['app_day']] = $row['end_time'];     #update the rray we will use to check availability
                            }
                            #foreach($_SESSION['dates'] as $day => $end){
                            #    echo "<br>" . $day . "= " . $end;
                            #}

                            $available_days = array();
                            $i =0;
                            
                            #retrieving this doctor's start and end time
                            $get_dr_timings = "SELECT * FROM dr_ortho WHERE doctor_id = :id";
                            $statement9 = $connection->prepare($get_dr_timings);
                            $statement9->bindParam(':id', $id_result['doctor_id'], PDO::PARAM_STR);
                            $statement9->execute();
                            $dr_timings = $statement9->fetch();

                            #retrieving appointment_duration
                            $get_duration = "SELECT * FROM appointment_info WHERE appointment_type = 'PHY01'";
                            $statement7 = $connection->prepare($get_duration);
                            $statement7->execute();
                            $appointment_info = $statement7->fetch();

                            #echo "<br>" . $dr_timings['start_time'] . " " . $dr_timings['end_time'];
                            #echo "<br>" . $appointment_info['duration'] . " " . $appointment_info['appointment_name'];

                            $dr_end = AM_PM($dr_timings['end_time']);
                            ?>
                            <form method="post">
			                    <label for="timing">Choose from availabe slots:</label>
			                    <select id="timing" name="timing">
                                    <option value="">--- Select a slot ---</option>							<!--Default value-->
                            <?php
                            foreach($_SESSION['dates'] as $day => $end){
                                if($end=="null"){                                           #meaning no prior engagements on these days
                                    #echo "<br>Added " . $day . " to the array available_days<br>";
                                    $available_days[$i] = $day;
                                    $i = $i+1;
                                    $new_end = add_times($dr_timings['start_time'],$appointment_info['duration'],0);
                                    #echo "<br>New Time: " . $new_end;
                                    ?>
                                    <option value="<?php echo $day. " " . $dr_timings['start_time'] . " " . $new_end?>">
						                <?php echo $day. " " . $dr_timings['start_time'] . "-" . $new_end?>
					                </option>
                                    <?php
                                }
                                else{
                                    #echo "<br>Dr. has another appointment on " . $day;
                                    $new_end = add_times($end,$appointment_info['duration'],1);
                                    #echo " New Time: " . $new_end . "<br>Dr. End: " . $dr_end;
                                    $time1 = strtotime($new_end);
                                    $time2 = strtotime($dr_end);
                                    #echo "<br>Type of T1(" . $time1 . "): " . gettype($time1) . "<br>Type of T2(" . $time2 . "): " . gettype($time2);
                                    if($time1>$time2){
                                        #echo "<br>The doctor no longer has any free slots on " . $day . ".";
                                    }
                                    else{
                                        #echo "<br>The doctor has at least 1 free slot on " . $day;
                                        $new_end = add_times($end,$appointment_info['duration'],0);
                                        ?>
                                        <option value="<?php echo $day. " " . $end . " " . $new_end?>">
						                    <?php echo $day. " " . $end . "-" . $new_end?>
					                    </option>
                                        <?php
                                    }
                                }
                            }
                            ?>
                                </select>
			                    <input type="submit" name="submit" value="Enter">
		                    </form>
                            <?php
                        }
                        else{                                                               #there can be no clashes
                            #echo "<br>This doctor has no appointments";
                            #all slots are free
                            $book_appointment = "SELECT duration, charges FROM appointment_info WHERE appointment_type = 'PHY01'";
                            $statement7 = $connection->prepare($book_appointment);
                            $statement7->execute();
                            $appointment_info = $statement7->fetch();

                            $_SESSION['charges'] =  $appointment_info['charges'];
                            $_SESSION['duration'] =  $appointment_info['duration'];

                            #$end_time = strtotime($timings_result['start_time']);           #convert the string to time
                            #$end_time = $end_time+(20*60);                                  #convert seconds to minutes then add to time
                            #$end_time = date('h:i:s',$end_time);
                            $end_time = add_times($timings_result['start_time'],$appointment_info['duration'],0);
                            #echo "<br>Start_time: " . $timings_result['start_time'] . "<br>End_time: " . $end_time;
                            #echo "<br>" . $timings_result['start_time'] . "-" . $end_time;

                            #now make a form and and display the availabe time slots
                            ?>
                            <form method="post">
			                    <label for="timing">Choose from availabe slots:</label>
			                    <select id="timing" name="timing">
                                    <option value="">--- Select a slot ---</option>							<!--Default value-->
				                        <?php
					                    foreach ($_SESSION['dates'] as $day => $time) {                  #display all the options
				                        ?>
					                        <option value="<?php echo $day. " " . $timings_result['start_time'] . " " . $end_time?>">
						                        <?php echo $day. " " . $timings_result['start_time'] . "-" . $end_time?>
					                        </option>
					                    <?php
					                    } 
					                    ?>                    
			                    </select>
			                    <input type="submit" name="submit" value="Enter">
		                    </form>
                            <?php
                        }
                    }
                }
                catch(PDOException $error) {
                    echo "<br>Error after dr submission" .  $error->getMessage();
                }
            }

            #now we'll check if the form for timings has been submitted and process the input
            if(isset($_POST['timing'])) {
                try {
                    #$_SESSION['once'] = 1;
                    #THIS IS FOR NO CLASHES TO DECIDE THIS MAKE A SESSION FLAG VARIABLE IN THE DR. FORM SUBMITTED PORTION
                    $time_selected = $_POST['timing'];
                    echo "<br>Booking Dr. " . $_SESSION['dr'];
                    echo "<br>For: " . $time_selected . "<br>";
                    $_SESSION['day'] ="";
                    $_SESSION['start_time'] ="";
                    $_SESSION['end_time'] ="";
                    get_day_times($time_selected, $_SESSION['day'], $_SESSION['start_time'], $_SESSION['end_time']);
                    $booked_day = $_SESSION['day'];
                    
                    #echo "<br><br>" . $_SESSION['day'] . "   " . $_SESSION['start_time'] . "-" . $_SESSION['end_time'];
                    
                    #now we update our array to keep track of clashes
                    $_SESSION['dates'][$booked_day] = $_SESSION['start_time']. " " .$_SESSION['end_time'];         
                    #foreach($_SESSION['dates'] as $week_day => $timings){
                    #    echo "<br>Day: " . $week_day . " Timings: " . $timings; 
                    #}
                    echo "<br><br>";

                    #Now we have to make a form to ask for choice of room depending on the department chosen
                    #$_SESSION['rooms'] = array();
                    if(CDP($_SESSION['dept_id'])){
                        $num = 0;
                        $count =0;
                        #echo "<br><br>In cardiology";
                        $get_room = "SELECT * FROM rooms WHERE room_id LIKE 'C%' AND room_id NOT LIKE '%MR%'";      #remove common rooms
                        $statement8 = $connection->prepare($get_room);
                        $statement8->execute();
                        $room_result = $statement8->fetchAll();

                        #foreach($room_result as $row){                  #As we made an array for days we'll make 1 for rooms
                        #    $_SESSION['rooms'][$row['room_id']] = -1;
                        #    #echo "<br>In loop";
                        #}

                        #foreach($_SESSION['rooms'] as $room => $capacity){           
                        #    echo "<br>" . $room . "     Capacity: " . $capacity;
                        #}
                        #echo "<br><br>";
                        foreach($room_result as $row){
                            #echo "<br>" . $row['room_id'];
                            $rooms[$count] = $row['room_id'];
                            $count = $count+1;
                        }
                        #echo "<br><br>";
                        #for($j = 0; $j < count($rooms); $j++) {
                        #    echo $rooms[$j];
                        #    echo "<br>";
                        #}
                        echo "<br><br>";
                        for($j=0; $j<count($rooms); $j++){
                            $get_count = "SELECT COUNT(*) AS num FROM appointments WHERE room_id= :room_id";
                            $statement10 = $connection->prepare($get_count);
                            $statement10->bindParam(':room_id', $rooms[$j], PDO::PARAM_STR);
                            $statement10->execute();
                            $room_count = $statement10->fetch();
                            #echo "<br>" . $rooms[$j] . " =>" . $room_count['num'];
                            if($room_count['num']>0){                                                           #meaning room is not empty
                                #echo "<br>Count > 0";
                                $_SESSION['rooms'][$rooms[$j]] = $room_count['num'];                            #update rooms session array
                            }
                        }
                        #echo "<br><br>";
                        #foreach($_SESSION['rooms'] as $room => $capacity){           
                        #    echo "<br>" . $room . "     Capacity: " . $capacity;
                        #}
                        
                        #Now onto creating the actual form
                        ?>
                        <form method="post">
			                <label for="room">Choose from availabe rooms:</label>
			                <select id="room" name="room">
                                <option value="">--- Select a room ---</option>							<!--Default value-->
                        <?php
                        for($index=0; $index<count($rooms); $index++){
                            if(room_type($rooms[$index])=="PR"){                                                         #i.e. private room
                                if($_SESSION['rooms'][$rooms[$index]]==-1){
                                    #give option of private room
                                    #echo "<br>Option of Private Room: " . $rooms[$index] . " Capacity: " . $_SESSION['rooms'][$rooms[$index]];
                                    ?>
                                    <option value="<?php echo $rooms[$index]?>">
                                        <?php echo "Private Room-" . $index+1?>
                                    </option>
                                    <?php
                                }
                            }
                            elseif(room_type($rooms[$index])=="SH"){                                                     #i.e. sharing ward
                                if($_SESSION['rooms'][$rooms[$index]]==-1 || $_SESSION['rooms'][$rooms[$index]]<6){
                                    #give option of sharing ward
                                    #echo "<br>Option of Sharing Ward: " . $rooms[$index] . " Capacity: " . $_SESSION['rooms'][$rooms[$index]];
                                    ?>
                                    <option value="<?php echo $rooms[$index]?>">
                                        <?php echo "Sharing Ward"?>
                                    </option>
                                    <?php
                                }
                            }
                            elseif(room_type($rooms[$index])=="SW"){                                                     #i.e. special ward
                                if($_SESSION['rooms'][$rooms[$index]]==-1 || $_SESSION['rooms'][$rooms[$index]]<3){
                                    #give option of special ward
                                    #echo "<br>Option of Special Ward: " . $rooms[$index] . " Capacity: " . $_SESSION['rooms'][$rooms[$index]];
                                    ?>
                                    <option value="<?php echo $rooms[$index]?>">
                                        <?php echo "Special Ward"?>
                                    </option>
                                    <?php
                                }
                            }
                        }
                        ?>
                            </select>
                            <input type="submit" name="submit" value="Enter">
                        </form>
                        <?php
                    }
                    elseif(HDP($_SESSION['dept_id'])){
                        $num = 0;
                        $count=0;
                        #echo "<br><br>In hematology";
                        $get_room = "SELECT * FROM rooms WHERE room_id LIKE 'H%'";
                        $statement8 = $connection->prepare($get_room);
                        $statement8->execute();
                        $room_result = $statement8->fetchAll();
                        
                        #foreach($room_result as $row){                  #As we made an array for days we'll make 1 for rooms
                            #echo "<br>Room ID: " . $row['room_id'];
                        #    $_SESSION['rooms'][$row['room_id']] = -1;
                        #}
                        
                        #foreach($_SESSION['rooms'] as $room => $capacity){                  
                        #    echo "<br>" . $room . "     Capacity: " . $capacity;
                        #}
                        #echo "<br><br>";
                        foreach($room_result as $row){
                            #echo "<br>" . $row['room_id'];
                            $rooms[$count] = $row['room_id'];
                            $count = $count+1;
                        }
                        #echo "<br><br>";
                        #for($j = 0; $j < count($rooms); $j++) {
                        #    echo $rooms[$j];
                        #    echo "<br>";
                        #}
                        #echo "<br><br>";
                        for($j=0; $j<count($rooms); $j++){
                            $get_count = "SELECT COUNT(*) AS num FROM appointments WHERE room_id= :room_id";
                            $statement10 = $connection->prepare($get_count);
                            $statement10->bindParam(':room_id', $rooms[$j], PDO::PARAM_STR);
                            $statement10->execute();
                            $room_count = $statement10->fetch();
                            #echo "<br>" . $rooms[$j] . " =>" . $room_count['num'];
                            if($room_count['num']>0){                                                           #meaning room is not empty
                                #echo "<br>Count > 0";
                                $_SESSION['rooms'][$rooms[$j]] = $room_count['num'];                            #update rooms session array
                            }
                        }
                        echo "<br><br>";
                        #foreach($_SESSION['rooms'] as $room => $capacity){           
                        #    echo "<br>" . $room . "     Capacity: " . $capacity;
                        #}
                        #Now onto creating the actual form
                        ?>
                        <form method="post">
			                <label for="room">Choose from availabe rooms:</label>
			                <select id="room" name="room">
                                <option value="">--- Select a room ---</option>							<!--Default value-->
                        <?php
                        for($index=0; $index<count($rooms); $index++){
                            if(room_type($rooms[$index])=="PR"){                                                         #i.e. private room
                                if($_SESSION['rooms'][$rooms[$index]]==-1){
                                    #give option of private room
                                    #echo "<br>Option of Private Room: " . $rooms[$index] . " Capacity: " . $_SESSION['rooms'][$rooms[$index]];
                                    ?>
                                    <option value="<?php echo $rooms[$index]?>">
                                        <?php echo "Private Room-" . $index+1?>
                                    </option>
                                    <?php
                                }
                            }
                            elseif(room_type($rooms[$index])=="SH"){                                                     #i.e. sharing ward
                                if($_SESSION['rooms'][$rooms[$index]]==-1 || $_SESSION['rooms'][$rooms[$index]]<6){
                                    #give option of sharing ward
                                    #echo "<br>Option of Sharing Ward: " . $rooms[$index] . " Capacity: " . $_SESSION['rooms'][$rooms[$index]];
                                    ?>
                                    <option value="<?php echo $rooms[$index]?>">
                                        <?php echo "Sharing Ward"?>
                                    </option>
                                    <?php
                                }
                            }
                            elseif(room_type($rooms[$index])=="SW"){                                                     #i.e. special ward
                                if($_SESSION['rooms'][$rooms[$index]]==-1 || $_SESSION['rooms'][$rooms[$index]]<3){
                                    #give option of special ward
                                    #echo "<br>Option of Special Ward: " . $rooms[$index] . " Capacity: " . $_SESSION['rooms'][$rooms[$index]];
                                    ?>
                                    <option value="<?php echo $rooms[$index]?>">
                                        <?php echo "Special Ward"?>
                                    </option>
                                    <?php
                                }
                            }
                        }
                        ?>
                            </select>
                            <input type="submit" name="submit" value="Enter">
                        </form>
                        <?php
                    }
                    else{
                        $num = 0;
                        $count=0;
                        #echo "<br><br>In orthopedics";
                        $get_room = "SELECT * FROM rooms WHERE room_id LIKE 'O%'";
                        $statement8 = $connection->prepare($get_room);
                        $statement8->execute();
                        $room_result = $statement8->fetchAll();
                        #foreach($room_result as $row){                  #As we made an array for days we'll make 1 for rooms
                        #    $_SESSION['rooms'][$row['room_id']] = -1;
                        #}
                        #foreach($_SESSION['rooms'] as $room => $capacity){                  
                        #    echo "<br>" . $room . "     Capacity: " . $capacity;
                        #}
                        #echo "<br><br>";
                        foreach($room_result as $row){
                            #echo "<br>" . $row['room_id'];
                            $rooms[$count] = $row['room_id'];
                            $count = $count+1;
                        }
                        #echo "<br><br>";
                        #for($j = 0; $j < count($rooms); $j++) {
                        #    echo $rooms[$j];
                        #    echo "<br>";
                        #}
                        #echo "<br><br>";
                        for($j=0; $j<count($rooms); $j++){
                            $get_count = "SELECT COUNT(*) AS num FROM appointments WHERE room_id= :room_id";
                            $statement10 = $connection->prepare($get_count);
                            $statement10->bindParam(':room_id', $rooms[$j], PDO::PARAM_STR);
                            $statement10->execute();
                            $room_count = $statement10->fetch();
                            #echo "<br>" . $rooms[$j] . " =>" . $room_count['num'];
                            if($room_count['num']>0){                                                           #meaning room is not empty
                                #echo "<br>Count > 0";
                                $_SESSION['rooms'][$rooms[$j]] = $room_count['num'];                            #update rooms session array
                            }
                        }
                        #echo "<br><br>";
                        #foreach($_SESSION['rooms'] as $room => $capacity){           
                        #    echo "<br>" . $room . "     Capacity: " . $capacity;
                        #}
                        #Now onto creating the actual form
                        ?>
                        <form method="post">
			                <label for="room">Choose from availabe rooms:</label>
			                <select id="room" name="room">
                                <option value="">--- Select a room ---</option>							<!--Default value-->
                        <?php
                        for($index=0; $index<count($rooms); $index++){
                            if(room_type($rooms[$index])=="PR"){                                                         #i.e. private room
                                if($_SESSION['rooms'][$rooms[$index]]==-1){
                                    #give option of private room
                                    #echo "<br>Option of Private Room: " . $rooms[$index] . " Capacity: " . $_SESSION['rooms'][$rooms[$index]];
                                    ?>
                                    <option value="<?php echo $rooms[$index]?>">
                                        <?php echo "Private Room-" . $index+1?>
                                    </option>
                                    <?php
                                }
                            }
                            elseif(room_type($rooms[$index])=="SH"){                                                     #i.e. sharing ward
                                if($_SESSION['rooms'][$rooms[$index]]==-1 || $_SESSION['rooms'][$rooms[$index]]<6){
                                    #give option of sharing ward
                                    #echo "<br>Option of Sharing Ward: " . $rooms[$index] . " Capacity: " . $_SESSION['rooms'][$rooms[$index]];
                                    ?>
                                    <option value="<?php echo $rooms[$index]?>">
                                        <?php echo "Sharing Ward"?>
                                    </option>
                                    <?php
                                }
                            }
                            elseif(room_type($rooms[$index])=="SW"){                                                     #i.e. special ward
                                if($_SESSION['rooms'][$rooms[$index]]==-1 || $_SESSION['rooms'][$rooms[$index]]<3){
                                    #give option of special ward
                                    #echo "<br>Option of Special Ward: " . $rooms[$index] . " Capacity: " . $_SESSION['rooms'][$rooms[$index]];
                                    ?>
                                    <option value="<?php echo $rooms[$index]?>">
                                        <?php echo "Special Ward"?>
                                    </option>
                                    <?php
                                }
                            }
                        }
                        ?>
                            </select>
                            <input type="submit" name="submit" value="Enter">
                        </form>
                        <?php
                    }
                }
                catch(PDOException $error) {
                    echo "<br>Error after timing submission" .  $error->getMessage();
                }
            }
            if(isset($_POST['room'])){
                try {
                    #echo "<br>Room form filled!";
                    #echo "<br>Current user: " . $_SESSION['current_id'];
                    $_SESSION['room_chosen'] = $_POST['room'];
                    $_SESSION['rooms'][$_SESSION['room_chosen']]=-1;
                    #echo "<br>" . $_SESSION['rooms'][$_SESSION['room_chosen']];

                    $x = room_type($_SESSION['room_chosen']);
                    switch($x) {
                        case "PR":
                            #echo "<br>Private Room chosen";
                            if($_SESSION['rooms'][$_SESSION['room_chosen']]==-1){
                                $_SESSION['rooms'][$_SESSION['room_chosen']]=1;
                            }
                            else{
                                #echo "<br>Room is no longer availabe";
                            }
                            break;
                        case "SW":
                            #echo "<br>Special Ward chosen";
                            if($_SESSION['rooms'][$_SESSION['room_chosen']]==-1){
                                $_SESSION['rooms'][$_SESSION['room_chosen']]=1;
                            }
                            elseif($_SESSION['rooms'][$_SESSION['room_chosen']]==3){
                                #echo "<br>Room is no longer availabe";
                                break;
                            }
                            else{
                                $_SESSION['rooms'][$_SESSION['room_chosen']]=$_SESSION['rooms'][$_SESSION['room_chosen']]+1;
                            }
                            break;
                        case "SH":
                            #echo "<br>Sharing Ward chosen";
                            if($_SESSION['rooms'][$_SESSION['room_chosen']]==-1){
                                $_SESSION['rooms'][$_SESSION['room_chosen']]=1;
                            }
                            elseif($_SESSION['rooms'][$_SESSION['room_chosen']]==6){
                                #echo "<br>Room is no longer availabe";
                                break;
                            }
                            else{
                                $_SESSION['rooms'][$_SESSION['room_chosen']]=$_SESSION['rooms'][$_SESSION['room_chosen']]+1;
                            }
                            break;
                        default:
                            echo "<br>Some sort of error has occured during handling of input";    
                    }
                    header('location: appointment_verify.php');                           
                    #die;                    
                }
                catch(PDOException $error) {
                    echo "<br>Error after room submission" .  $error->getMessage();
                }
            }
        ?>
        <form method="post">
			<label for="dr">Choose a doctor:</label>
			<select id="dr" name="dr">
                <option value="">--- Select ---</option>							<!--Default value-->
                <optgroup label="Cardiologists:">
				    <?php
					foreach ($dr_cardio as $row) {                                         #display all the cardiologists from dr_cardio
				    ?>
					    <option value="<?php echo $row['fname']. " " . $row['lname']?>">
						    <?php echo "Dr. " . $row['fname'] . " " . $row['lname']?>
					    </option>
					<?php
					} 
					?>
                </optgroup>
                <optgroup label="Orthopedists:">
                    <?php
					foreach ($dr_ortho as $row) {                                         #display all the orthopedists from dr_ortho
				    ?>
					    <option value="<?php echo $row['fname'] . " " . $row['lname']?>">
						    <?php echo "Dr. " . $row['fname'] . " " . $row['lname']?>
					    </option>
					<?php
					} 
					?>
                </optgroup>
                <optgroup label="Hematologists:">
                <?php
					foreach ($dr_hem as $row) {                                           #display all the orthopedists from dr_hem
				    ?>
					    <option value="<?php echo $row['fname'] . " " . $row['lname']?>">
						    <?php echo "Dr. " . $row['fname'] . " " . $row['lname']?>
					    </option>
					<?php
					} 
					?>
                </optgroup>
			</select>
			<input type="submit" name="submit" value="Enter">
		</form>
        <br><strong><a href="homepage_patient.php">Back to homepage</a></strong></br>
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