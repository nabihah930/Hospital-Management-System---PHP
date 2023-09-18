
<!Doctype html>
<html>
<head>
<title>"Newlife Hospital/Blood-Bank"</title>
</head>
<body>
<?php
require "database_config.php";
require "convert_to_HTML.php";
try {
$connection = new PDO($dsn, $username, $password, $options);
$connection->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
//session_start();
?>
        <form method="post">
        <h5>Fill In The Details</h5>
        <label for="donorID">Donor ID :</label>
        <input type="text" id="donorID" name="donorID">
        <label for="bloodType"> Blood Type :</label>
        <input type="text" id="bloodType" name="bloodType">
        <label for="quantity"> Quantity :</label>
        <input type="text" id="quantity" name="quantity">
        <input type="submit" name="donate" value="donate">
    </form>
    <?php echo "<br>" ;?>
    
<?php
if (isset($_POST['donate'])) 
{                                        
$new_donor = array(                                                 
"donorID" => $_POST['donorID'],
"bloodType" => $_POST['bloodType'],
);

          $quantity = $_POST['quantity'];
          $blood_type = $_POST['bloodType'];

          function getSingleValue($connection,$sql,$parameters)
          {
            $val1 = $connection->prepare($sql);
            $val1 ->execute($parameters);
            return $val1->fetchColumn();
          }
           $rows = getSingleValue($connection,"SELECT units_remaining FROM blood_bank where blood_type =?",[$blood_type]);
           
           
       $units = $rows + $quantity; 
       $sql1 = $connection->prepare("Select blood_type from blood_bank");
       $result =$sql1->execute();
	   $statement = $sql1->setFetchMode(PDO::FETCH_ASSOC);
       $rows = $sql1->fetchAll(PDO::FETCH_ASSOC);
       
        $update = "UPDATE blood_bank SET units_remaining = '$units' where blood_type = '$blood_type' ";
        $statement = $connection->prepare($update);
        $statement->execute();
       

$date= date('Y/m/d');

   $donor="INSERT INTO donations(donor_ID,donation_date,blood_type) 
   VALUES(:donorID,'$date',:bloodType)";

   $statement = $connection->prepare($donor);                             
   $statement->execute($new_donor);

}}
catch(PDOException $error){
echo  $donor . "<br>" .$error->getMessage();
}

?>
 </body>
</html>