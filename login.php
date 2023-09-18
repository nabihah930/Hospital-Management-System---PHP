<?php
session_start();
/**
  * Function to query information based on
  * a parameter: in this case, location.
  *
  */
#  $_SESSION["current_id"] = "";
#  $_SESSION["current_password"] = "";

  if (isset($_POST['submit'])) {
    try {
        require "database_config.php";
        require "convert_to_HTML.php";

        $connection = new PDO($dsn, $username, $password, $options);
        
        $id = $_POST['id'];
        $email = $_POST['email'];                                           #Should we make an array here?
        $password = $_POST['password'];
        
        if (strstr($id,"PID")) {
            $sql = "SELECT * FROM patients WHERE patient_id = :id AND patient_email = :email AND patient_password = :password";
        }
        elseif (strstr($id,"DID")) {
            $sql = "SELECT * FROM doctors WHERE doctor_id = :id AND dr_email = :email AND dr_password = :password";
            echo "You are a doctor.";
        }
        elseif (strstr($id,"NID")) {
            $sql = "SELECT * FROM nurses WHERE nurse_id = :id AND nurse_email = :email AND nurse_password = :password";
            echo "You are a nurse.";
        }
        else {
            echo "INCORRECT ID ENTERED";
        }

        $statement = $connection->prepare($sql);
        $statement->bindParam(':id', $id, PDO::PARAM_STR);
        $statement->bindParam(':email', $email, PDO::PARAM_STR);
        $statement->bindParam(':password', $password, PDO::PARAM_STR);
        $statement->execute();
        $result = $statement->fetchAll();

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
        <title>Newlife Hospital-Login</title>
        <link rel="stylesheet" href="style.css" />
    </head>
    <body>
    <h1>Login</h1>
        <!-- If a submission has been made and account verified -->
        <?php
        if (isset($_POST['submit'])) {
            if ($result && $statement->rowCount() > 0) { 
                #echo "Submission has been made and account has been verified!";
                $sql2 = "INSERT INTO users(user_id, user_email, user_password) values (:id, :email, :password)"; 
                $state2 = $connection->prepare($sql2);
                $state2->bindParam(':id', $id, PDO::PARAM_STR);
                $state2->bindParam(':email', $email, PDO::PARAM_STR);
                $state2->bindParam(':password', $password, PDO::PARAM_STR);
                $state2->execute();
                foreach ($result as $row) { 
                    #$id = $row["user_id"];
                    echo "<br>" . $id;
                    $_SESSION["current_id"] = $id;
                    if (strstr($id,"DID")) {
                        header('location: homepage_doctor.php');                           
                        die;
                    } 
                    elseif (strstr($id,"NID")) {
                        header('location: homepage_nurse.php');                           
                        die;
                    } 
                    elseif (strstr($id,"PID")) {
                        header('location: homepage_patient.php');                           
                        die;
                    }
                    else {
                        echo "Error occured.";
                        die;
                    }
                }
            }
            else {
                    echo "No existing account. Would you like to <a href='signup.php' target='_blank'><strong>Sign up?</strong></a>"; 
            }
        }
        ?>
    <form method="post">
        <label for="id">ID</label>
        <input type="text" id="id" name="id">
        <label for="email">Email</label>
        <input type="text" id="email" name="email">
        <label for="password">Password</label>
        <input type="password" id="password" name="password">
        <input type="submit" name="submit" value="Enter">
    </form>
    <a href="main_homepage.php">Back to homepage</a>            <!--ADD REDIRECT INSTEAD-->
</html>