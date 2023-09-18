<?php
    require "database_config.php";                                      #needed to make a connection with our database
    require "convert_to_HTML.php";                                      #to convert to html characters that will be displayed

    try {
        $connection = new PDO($dsn, $username, $password, $options);
        $cardio_view = "CREATE VIEW dr_cardio AS 
                        SELECT doctor_id, fname, lname, charges, start_time, end_time, day_one, day_two, day_three 
                        FROM ((doctors INNER JOIN shifts ON doctors.shift_num = shifts.shift_num) 
                                INNER JOIN working_days ON shifts.working_days_code = working_days.working_days_code)
                        WHERE dept_id = 'CDP01' ";
        $statement = $connection->prepare($cardio_view);
        $statement->execute();

        $ortho_view =  "CREATE VIEW dr_ortho AS 
                        SELECT doctor_id, fname, lname, charges, start_time, end_time, day_one, day_two, day_three 
                        FROM ((doctors INNER JOIN shifts ON doctors.shift_num = shifts.shift_num) 
                               INNER JOIN working_days ON shifts.working_days_code = working_days.working_days_code)
                        WHERE dept_id = 'ODP01' ";
        $statement2 = $connection->prepare($ortho_view);
        $statement2->execute();

        $hem_view = "CREATE VIEW dr_hem AS 
                     SELECT doctor_id, fname, lname, charges, start_time, end_time, day_one, day_two, day_three 
                     FROM ((doctors INNER JOIN shifts ON doctors.shift_num = shifts.shift_num) 
                             INNER JOIN working_days ON shifts.working_days_code = working_days.working_days_code)
                     WHERE dept_id = 'HDP01' ";
        $statement3 = $connection->prepare($hem_view);
        $statement3->execute();
    }
    catch(PDOException $error) {
        echo $ortho_view . "<br>" . $error->getMessage() . "<br>";
    }
?>