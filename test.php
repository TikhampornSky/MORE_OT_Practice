<?php
  $servername = "localhost:3307";
  $username = "root";
  $password = "";
  $dbname = "trydatabase";

  // Create connection
  $conn = new mysqli($servername, $username, $password, $dbname);
  // Check connection
  if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
  }
  //header("Location: test.html?type=demo&approve1=$approve1&myname=$myname");
  $type = $_GET['type']; 
  $approve1 = $_GET['approve1']; 
  $myname = $_GET['myname']; 

  $arr = explode(" ", $myname);
  //echo $arr[0] . "++" . $arr[1] ;
  $fullname = $arr[0] ; 
  $lastname = $arr[1] ;
  $id ;

  //get the value with specific condition
  $ID = $conn->query("SELECT id FROM person WHERE fullname='$fullname' AND lastname='$lastname'") ;   //return as object ;
  while ($row = $ID->fetch_assoc()) {
    $id =  $row['id'] ;
  }
  //--------------------------------------

  echo "user : " . $fullname . " " . $lastname . " " . $id . " <br> ";

  $arr2 = explode(" ", $approve1);
  $approve1_fullname = $arr2[0] ;
  $approve1_lastname = $arr2[1] ;
  $approve1_id ;

  $ID2 = $conn->query("SELECT id FROM person WHERE fullname='$approve1_fullname' AND lastname='$approve1_lastname'") ;   //return as object ;
  while ($row = $ID2->fetch_assoc()) {
    $approve1_id =  $row['id'] ;
  }

  echo "approve1 : " . $approve1_fullname . " " . $approve1_lastname . " " . $approve1_id . " <br> " ;

  $sql = "UPDATE person SET reviewer='$approve1_id' WHERE id='$id'";

  if ($conn->query($sql) === TRUE) {
    echo "Record updated successfully";
  } else {
    echo "Error updating record: " . $conn->error;
  }

  /*
  $sql = "INSERT INTO person (fullname, lastname, id)
  VALUES ('Ariana', 'Lonly', '213456779')";

  if ($conn->query($sql) === TRUE) {
    echo "New record created successfully";
  } else {
    echo "Error: " . $sql . "<br>" . $conn->error;
  }
  */

  $conn->close();
?>
<!DOCTYPE html>
<html>
<head>
<meta http-equiv="Content-Language" content="th" />
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta charset="UTF-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">   
<meta name="viewport" content="width=device-width, initial-scale=1">
</head>

<body>

<h2>GO GO</h2>
</body>
</html>