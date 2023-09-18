<?php
session_start();
/**
  * Use an HTML form to create a new entry in the
  * users table.
  *
  */
  $_SESSION["current_id"] = "";
  $new_id = "";
if (isset($_POST['submit'])) {                                              #Just checks if form has been submitted
    require "database_config.php";
    require "convert_to_HTML.php";
    try {
        $connection = new PDO($dsn, $username, $password, $options);
        $new_patient = array(                                                  #Getting 9 inputs from form 
            "id" => $new_id,
            "fname"  => $_POST['fname'],
            "lname" => $_POST['lname'],
            "age"  => $_POST['age'],
            "gender"  => $_POST['gender'],
            "phone"  => $_POST['phone'],
            "address"  => $_POST['address'],
            "bloodG"  => $_POST['bloodG'],
            "email"  => $_POST['email'],
            "password"  => $_POST['password'],
        );
        $sql1 = "SELECT MAX(patient_id) FROM patients";
        $statement = $connection->prepare($sql1);
        $statement->execute();
        $result = $statement->fetch();
        $new_id = $result["MAX(patient_id)"];
        $new_id = substr($new_id,3,2);                                        #Creating a new ID
        $new_numID = (int)$new_id;
        $new_numID = $new_numID+1;
        $new_id = (string)$new_numID;
        $new_id = "PID".$new_id;
        $new_patient['id'] = $new_id;
        $new_user = array (
            "user_id" => $new_patient['id'],
            "user_email" => $new_patient['email'],
            "user_password" => $new_patient['password'],
        );
        $_SESSION["current_id"] = $new_id;
        $sql2 = "INSERT INTO patients(patient_id, fname, lname, age, gender, phone, current_address, blood_type, patient_email, patient_password) 
                VALUES (:id, :fname, :lname, :age, :gender, :phone, :address, :bloodG, :email, :password)";
        
        $sql3 = "INSERT INTO users(user_id, user_email, user_password) values (:user_id, :user_email, :user_password)"; 
        
        $statement = $connection->prepare($sql2);                            #Prepare and execute the SQL codes
        $statement->execute($new_patient);

        $statement2 = $connection->prepare($sql3);
        $statement2->execute($new_user);

    } catch(PDOException $error) {
    echo $sql . "<br>" . $error->getMessage();
    }

}
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8" />
        <meta http-equiv="x-ua-compatible" content="ie=edge" />
        <meta name="viewport" content="width=device-width, initial-scale=1" />
        <title>Newlife Hospital-SignUp</title>
        <link rel="stylesheet" href="style.css" />
    </head>
    <body>

    <!-- Redirected to appropriate page once account is created -->
    <?php
    if (isset($_POST['submit']) && $statement) {
        echo "<br>This is your ID: " .  $new_id . "<br>Please use it the next time you login.<br><a href='homepage_patient.php' target='_blank'><strong>Homepage</strong></a>";
        
    }?>

    <h1>Create free account</h1>
    <p style="color: red;">(* means required field)</p>
    <form method="post">
        <strong>
        <label for="fname">First name* :</label>
        <input type="text" id="fname" name="fname">
        <label for="lname">Last name* :</label>
        <input type="text" id="lname" name="lname">
        <label for="age">Age* :</label>
        <input type="number" id="age" name="age" min="18">
        <br>Gender* :<br>
        <label for="male">Male</label>
        <input type="radio" id="male" name="gender" value="male">
        <label for="female">Female</label>
        <input type="radio" id="female" name="gender" value="female">

        <label for="phone">Phone number* :</label>
        <input type="text" id="phone" name="phone">
        <label for="address">Current Address* :</label>
        <input type="text" id="address" name="address">
        <label for="bloodG">Blood Group* :</label>
        <input type="text" id="bloodG" name="bloodG">
        <label for="email">Email* :</label>
        <input type="text" id="email" name="email">
        <label for="password">Password* :</label>
        <input type="password" id="password" name="password">           <!--Should we add a re-type request? -->
        <input type="submit" name="submit" value="Sign Up">
    </form>
    <br><a href="main_homepage.php">Back to homepage</a>
    </strong>
    </body>
</html>