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
            "NewLife Hospital-Book Appointment"</title>
			<link rel="stylesheet" href="style.css" />
		</head>
        <body>
			<h1>Book an appointment online</h1>
			<h2 style="text-align:left;">Follow these 5 easy steps to easily book an appointment online:</h2>
			<h3>
				Step 1: Choose the type of appointment to book<br>
				We offer four different types of appointments:
				<br><a href="consultation.php">Consultation</a>	<a href="follow_up.php">Follow-up</a>	<a href="physical.php">Physicals</a>
				<a href="routine_app.php">Routine check-up</a><br>
				<br>Step 2: Choose the doctor</br>
				Choose from our selection of experts. Browse through the doctors we have for each of our departments.
				<br>To browse <a href="departments.php"><strong>click here</strong></a>.</br>
				<br>Step 3: Choose the time</br>
				The system will present the available timings for the doctor chosen.<br>
				<br>Step 4: Choose a room</br>
				We offer 3 different types of rooms per department.<br> 
				You can opt for a sharing ward that accomodates 6 patients and is priced at Rs.1000<br>
				Or if you'd prefer a little privacy choose a special ward that accomodates 3 patients and is priced at Rs.1500<br>
				We also offer a cozy private room just for you which is priced at Rs.2500<br>
				<br>Step 5: Verify appointment</br>
				Finally a reciept of your appointment will be displayed. If you accept just click "Book Appointment". <br>
			
			<br><strong><a href="homepage_patient.php">Back to homepage</a></strong>
			<br><strong><a href="view_appointments.php">View previous appointments</a></strong></br>
			</h3>
		</body>
</html>