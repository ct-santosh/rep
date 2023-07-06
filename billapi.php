<?php
    error_reporting( E_ALL & ~E_WARNING );

    $con = mysqli_connect("localhost", "root", "", "practice" );
    
    mysqli_options($con, MYSQLI_OPT_INT_AND_FLOAT_NATIVE, true);

    if( $_GET['action'] == "load_bills" ){
        $query = "select * from bills order by id";
        $res = mysqli_query($con, $query);
        $bills = [];
        while( $row = mysqli_fetch_assoc($res) ){
            $bills[] = $row;
        }
        echo json_encode([  
			"status"=>"success",
			"bills"=> $bills
		]);
	    exit;
    }


    if( $_GET['action'] == "delete_bill" ){
        $query = "delete from bills where id = " . $_GET['id'];
        $res = mysqli_query($con, $query);
        $query2 = "delete from billitems where bill_id = " . $_GET['id'];
        $res = mysqli_query($con, $query2);
        echo json_encode([
                "status"=>"success",
            ]);
        exit;
    }

    if( $_POST['action'] == "save_bill" ){
        $bill = json_decode($_POST['bill'], true );  
        $bill_items = json_decode($_POST['bill_items'], true ); 

        $res = mysqli_query($con, "insert into bills set date='".$bill['date']."',
        c_name='".$bill['c_name']."',
        amount='".$bill['amount']."',
        tax='".$bill['tax']."',
        net='".$bill['net']."'");

        $res2 = mysqli_query($con,"select * from bills where c_name= '".$bill['c_name']."' and amount='".$bill['amount']."'");
        $row=mysqli_fetch_assoc($res2);

        foreach( $bill_items as $i=>$j ){
            $res2 = mysqli_query($con, "insert into billitems set bill_id='".$row['id']."',
            product_name='".$j['product_name']."',
            quantity='".$j['quantity']."',
            price='".$j['price']."',
            total='".$j['total']."'"); 
        }

        if( mysqli_error($con) ){
            echo json_encode([
                "status"=>"fail",
                "error"=> mysqli_error($con)
            ],  JSON_PRETTY_PRINT );
        }else{
            echo json_encode([
                "status"=>"success",
                "bills"=>$bill,
                "bill_items"=>$bill_items
            ]);
        }
    }
    if( isset($_GET['action'] ) ){
    if( $_GET['action'] == "read_bill" ){
	$query = "select * from bills where id=". $_GET['id'];
	$res = mysqli_query($con, $query);
	$bill= mysqli_fetch_assoc($res);

    $query2 = "select * from billitems where bill_id=". $_GET['id'];
	$res2 = mysqli_query($con, $query2);
    $bill_items=[];
    while($row=mysqli_fetch_assoc($res2)){
        $bill_items[]=$row;
    }

	echo json_encode([
			"status"=>"success",
			"bill"=> $bill,
            "bill_items"=> $bill_items
		]);
	exit;
    }
    }
    ?>
