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

$sql = "INSERT INTO person (fullname, lastname, id)
VALUES ('Ariana', 'Lonly', '213456779')";
$sql2 = "INSERT INTO person (fullname, lastname, id)
VALUES ('Babara', 'Caos', '113456978')";
$sql3 = "INSERT INTO person (fullname, lastname, id)
VALUES ('Banana', 'Apple', '21355689')";
$sql4 = "INSERT INTO person (fullname, lastname, id)
VALUES ('Cerene', 'Bobby', '21345888')";
$sql5 = "INSERT INTO person (fullname, lastname, id)
VALUES ('Coro', 'Jeff', '555638999')";
$sql6 = "INSERT INTO person (fullname, lastname, id)
VALUES ('Susan', 'Jeffrey', '313459889')";
$sql7 = "INSERT INTO person (fullname, lastname, id)
VALUES ('Tortilla', 'Jenet', '315659889')";

if ($conn->query($sql2) === TRUE) {
  echo "New record created successfully";
} else {
  echo "Error: " . $sql . "<br>" . $conn->error;
}

if ($conn->query($sql3) === TRUE) {
  echo "New record created successfully";
} else {
  echo "Error: " . $sql . "<br>" . $conn->error;
}

if ($conn->query($sql4) === TRUE) {
  echo "New record created successfully";
} else {
  echo "Error: " . $sql . "<br>" . $conn->error;
}

if ($conn->query($sql5) === TRUE) {
  echo "New record created successfully";
} else {
  echo "Error: " . $sql . "<br>" . $conn->error;
}

if ($conn->query($sql6) === TRUE) {
  echo "New record created successfully";
} else {
  echo "Error: " . $sql . "<br>" . $conn->error;
}

if ($conn->query($sql7) === TRUE) {
  echo "New record created successfully";
} else {
  echo "Error: " . $sql . "<br>" . $conn->error;
}

$conn->close();
?>