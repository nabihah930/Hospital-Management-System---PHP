<?php
session_start();
$item_name = array();
//session_destroy();
$_SESSION["current_id"] = "";
$new_id = "";
  
//Submission check
if(filter_input(INPUT_POST,'add_to_cart')){
	if(isset($_SESSION['food_items'])){
	 //keep track of products that are in cart;
	$count = count($_SESSION["food_items"]);
	//create sequential array
	 $item_name = array_column($_SESSION['food_items'],'food_name');
	 if(!in_array(filter_input(INPUT_GET,'food_name'),$item_name))
	 {
		$_SESSION['food_items'][$count] = array(
			'food_name' => filter_input(INPUT_GET,'food_name'),
			'food_type' => filter_input(INPUT_POST,'food_type'),
			'price' => filter_input(INPUT_POST,'price'),
			'quantity' => filter_input(INPUT_POST,'quantity')
            );
	 }
	 else{ //item id exist,increase the quantity
    for($i = 0; $i < count($item_name); $i++){
		if($item_name[$i]==filter_input(INPUT_GET,'food_name')){
         $_SESSION['food_items'][$i]['quantity'] += filter_input(INPUT_POST,'quantity');      
        }
	}

	 }
	}
	else{ //no item exist, create first
		//create an array using submit button, start from key value
		$_SESSION['food_items'][0] = array(
		'food_name' => filter_input(INPUT_GET,'food_name'),
		'food_type' => filter_input(INPUT_POST,'food_type'),
		'price' => filter_input(INPUT_POST,'price'),
		'quantity' => filter_input(INPUT_POST,'quantity')
        );
	}
}
if(isset($_GET['action']))
{
	if($_GET['action']=='delete'){
		foreach($_SESSION["food_items"] as $key => $item){
			if($item["food_name"] == $_GET["food_name"]){
				unset ($_SESSION["food_items"][$key]);
			}
			
		}
		
	}
}
?>
<!Doctype html>
<html>
<head>
<title>"Newlife Hospital/Cafeteria"</title>
<link rel="stylesheet"href="cafe.css"/>
</head>
<body>
<div class="container">
    <?php
    require "database_config.php";
	require "convert_to_HTML.php";
   try{
       $connection = new PDO ($dsn,$username,$password,$options);
	   $connection->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
       $sql = $connection->prepare("Select food_name,food_type,price from cafeteria");
	   $result =$sql->execute();
	   $statement = $sql->setFetchMode(PDO::FETCH_ASSOC);
	   $rows = $sql->fetchAll(PDO::FETCH_ASSOC);
	           if ($result){
				foreach($rows as $item){
		       ?>
			<form method="post"action="cafe_order.php?action=add&food_name=<?php echo $item['food_name']?>">
			<div class="item">
			<h4 class="text-info"><?php echo $item ['food_name'];?></h4>
			<h4 class="text-info"><?php echo $item ['food_type'];?></h4>
	        <h4 class="text-info">Rs:<?php echo $item ['price'];?></h4>
			<input type="text" name="quantity"class="form-control" value=1>
			<input type="hidden" name="food_name" value=<?php echo $item['food_name']?>>
			<input type="hidden" name="food_type" value=<?php echo $item['food_type']?>>
			<input type="hidden" name="price" value=<?php echo $item['price']?>>
			<input type="submit" name="add_to_cart" style="margin-top:5px;" value="Add to Cart"/>
			</div>
			</form>
			   <?php
			   }
		  }
		  ?>       
         <div style="clear:both"></div>
         <br />
         <div class="table-responsive">
         <table class ="table">
         <tr><h3>Order Details</h3></th></tr>
        </tr>
		<tr>
		    <th width="30%">Food Name</th>
			<th width="13%">Quantity</th>
			<th width="10%">Price</th>
			<th width="10%">Total</th>
		</tr>
       <?php

           if(!empty($_SESSION['food_items'])){
			
			$total = 0;
			
			foreach($_SESSION['food_items'] as $key => $item) {
		?>
		<tr>
		<td><?php echo $item['food_name'];?></td>
		<td><?php echo $item['quantity'];?></td>
		<td><?php echo $item['price'];?></td>
		<td><?php echo number_format($item['quantity'] * $item['price'],2);?></td>
		<td><a href="cafe_order.php?action=delete&food_name=<?php echo $item["food_name"];?>"><span class="text-danger">Remove</span></a></td>
		</tr>
		 <?php
             $total = $total + ($item ['quantity'] * $item ['price']);
             $charges = $total;
			 }
          ?>
   <tr>
   <td colspan="3" align="right">Total</td>
   <td align="right">Rs:<?php echo number_format($total,2);?></td>
   <td></td>
   </tr>

   <tr>
       <td colspan="5">
	   <?php
	 if(isset($_SESSION['food_items'])){
	 if(count($_SESSION['food_items'])>0){
	 ?>
    <h1>Fill In The Order Details</h1>
    <form method="post">
        <strong>
        <label for="ordered_by">ID :</label>
        <input type="text" id="ordered_by" name="ordered_by">
        <label for="to_room">Room No :</label>
        <input type="text" id="to_room" name="to_room">
        <?php  $time = date('Y-m-d H:i:s'); ?>
        <label for="order_time"> Order Time:</label>
        <input for="text" id="order_time" value ="<?=$time;?>">
        <?php $charges = $total ;?>
        <label for="charges"> Charges:</label>      
        <input for ="text" id="charges" value ="<?=$charges;?>">
        <input type="submit" name="submit" value="Confirm Order">
    </form>
    <?php echo "<br>" ;?>
    <?php
	 }
	 }
	?>
     </td>
	 </tr>
	<?php } ?>
	  <?php
    if (isset($_POST['submit'])) 
    {                                         
    $new_order = array(                                                 
    "id" => $new_id,
    "ordered_by" => $_POST['ordered_by'],
    "to_room" => $_POST['to_room'],
   );
   $sql = "SELECT MAX(order_id) FROM cafe_order";
   $statement = $connection->prepare($sql);
   $statement->execute();
   $result = $statement->fetch();
   $new_id = $result["MAX(order_id)"];
   $new_id = substr($new_id,3,2);                                        
   $new_numID = (int)$new_id;
   $new_numID = $new_numID+1;
   $new_id = (string)$new_numID;
   $new_id = "ORD".$new_id;
   $new_order['id'] = $new_id;
   $time = date('Y-m-d H:i:s');
   
   $order="INSERT INTO cafe_order(order_ID,ordered_by,to_room,order_time,charges) 
   VALUES(:id,:ordered_by,:to_room,'$time','$charges')";

   $statement = $connection->prepare($order);                             
   $statement->execute($new_order);

}
   
        }
		catch(PDOException $error){
        echo $sql . "<br>" .$error->getMessage();
         } ?>
</div>
 </body>
</html>









