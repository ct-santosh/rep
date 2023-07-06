<?php 

error_reporting( E_ALL & ~E_WARNING );

$con = mysqli_connect("localhost", "root", "", "practice" );

mysqli_options($con, MYSQLI_OPT_INT_AND_FLOAT_NATIVE, true);

if( isset($_GET['action'] ) ){
if( $_GET['action'] == "load_products" ){
	$query = "select * from products order by id";
	$res = mysqli_query($con, $query);
	$products = [];
	while( $row = mysqli_fetch_assoc($res) ){
		$products[] = $row;
	}
//	$mysqli->options(MYSQLI_OPT_INT_AND_FLOAT_NATIVE, 1);

//	var_dump( $products );exit;
	echo json_encode([
			"status"=>"success",
			"products"=> $products
		]);
	exit;
}
if( $_GET['action'] == "delete_product" ){
	$query = "delete from products where id = " . $_GET['id'];
	$res = mysqli_query($con, $query);
	echo json_encode([
			"status"=>"success",
		]);
	exit;
}
}
if( $_POST['action'] == "add_product" ){
	$data = json_decode($_POST['data'], true );
	$query = "insert into products set 
	name = '" . $data['name'] . "',
	category = '" . $data['category'] . "',
	price = '" . $data['price'] . "' ,
    quantity='".$data['quantity']."'";

	$res = mysqli_query($con, $query);
	if( mysqli_error($con) ){
		echo json_encode([
			"status"=>"fail",
			"error"=> mysqli_error($con)
		],  JSON_PRETTY_PRINT );
	}else{
		echo json_encode([
			"status"=>"success",
			"new_id"=> mysqli_insert_id($con)
		]);
	}
	exit;
}
if( $_POST['action'] == "update_product" ){
	$data = json_decode($_POST['data'], true );
	$query = "update products set 
	name = '" . $data['name'] . "',
	category = '" . $data['category'] . "',
	price = '" . $data['price'] . "' ,
    quantity = '" . $data['quantity'] . "'
	where id = ". $_POST['id'];
	$res = mysqli_query($con, $query);
	if( mysqli_error($con) ){
		echo json_encode([
			"status"=>"fail",
			"error"=> mysqli_error($con)
		],  JSON_PRETTY_PRINT );
	}else{
		echo json_encode([
			"status"=>"success",
		]);
	}
	exit;
}
?>