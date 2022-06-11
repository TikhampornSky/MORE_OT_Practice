<?php
    $servername = "localhost:3307";
    $username = "root";
    $password = ""; 
    $dbname = "trydatabase";
      
    // connect the database with the server
    $conn = new mysqli($servername,$username,$password,$dbname);
      
     // if error occurs 
     if ($conn -> connect_errno)
     {
        echo "Failed to connect to MySQL: " . $conn -> connect_error;
        exit();
     }
   
     $sql = "select * from person ORDER BY fullname, lastname, id" ;
     $result = ($conn->query($sql));
     //declare array to store the data of database
   
     if ($result->num_rows > 0) 
     {
         // fetch all data from db into array 
         $row = $result->fetch_all(MYSQLI_ASSOC);  
     }

    if (isset($_POST["save"])) {
        $myname = $_POST['myname'];
        header("Location: RealDropDown.php?type=demo&myname=$myname");
    }
?>

<!DOCTYPE html>
<html>
<head>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<meta http-equiv="Content-Language" content="th" />
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta charset="UTF-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">   
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="stylesheet" href="./style.css">
</head>
<body>

<h2>First Page [Demo]</h2>

<form name="myForm" onsubmit=" return validateForm()" method="post" required>

<!-- log in demo -->
<label for="myname">Choose your name:</label>
<select name="myname" id="IDmyname">
    <div>
        <!-- <a onclick="mySelect('ทิฆัมพร เทพสุต')">ทิฆัมพร เทพสุต <br> 1111111111 </a> -->
            <?php
            if(!empty($row))
                foreach($row as $rows) {
            ?>
                <option> <?php echo $rows['fullname'] . ' ' . $rows['lastname']; ?> <br> <?php echo $rows['id']; ?> </option>
            <?php
                }
            ?>
    </div>
</select>
<!-- --------- -->

<button type="summit" name="save">NEXT</button>
</form>
</body>
</html>