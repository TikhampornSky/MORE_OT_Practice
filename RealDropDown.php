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
  
    $sql = "select fullname, lastname, id from person ORDER BY fullname, lastname, id" ;
    $result = ($conn->query($sql));
    //declare array to store the data of database
    $row = []; 
  
    if ($result->num_rows > 0) 
    {
        // fetch all data from db into array 
        $row = $result->fetch_all(MYSQLI_ASSOC);  
    }   
?>

<!DOCTYPE html>
<html>
<head>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script>
$(document).ready(function(){
    var input = document.getElementById("myInput").value;
    if (input != "" && document.getElementById("myCheck").checked == true) {
        document.getElementsByClassName("dropdown-content")[0].style.display = "block";
        document.getElementById("myInput").style.display = "block";
        document.getElementById("listTochoose").style.display = "none";
    }
    console.log( "ready!" );
    console.log(input) ;
    console.log(document.getElementById("myCheck").checked) ;
/*
  $("button").click(function(){
    $("p").slideToggle();
  });
  */
});
</script>
<meta http-equiv="Content-Language" content="th" />
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta charset="UTF-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">   
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="stylesheet" href="./style.css">
</head>
<body>

<h2>Search/Filter Dropdown</h2>
<p>Demo of drag and drop for select approval</p>

<div>
    <label for="SelectTypeOfWork">โปรดเลือกรูปแบบการทำงานของคุณ :</label>
    <select name="typeWork" id="IDtypeWork">
        <option value="none" selected disabled hidden>รูปแบบการทำงาน</option>
        <option value="ปกติ"> ปกติ </option>
        <option value="ทำงานนอกสถานที่"> ทำงานนอกสถานที่ </option>
    </select>
</div>

<div class="dropdown" id="alldropdown">
    <div>
        <input type="checkbox" id="myCheck" onchange="myFunction(this.checked)">
        <label for="myCheck">โปรดเลือกผู้อนุมัติของท่าน</label> 
    </div>

    <div>
        <h1> Hiiiiiiiiiiiiiii </h1>
    </div>

    <div id="myDropdown" class="dropdown-content" >
        <div id="searchBox">
            <input type="text" placeholder="โปรดใส่ชื่อผู้อนุมัติของท่าน" class = "myInputClass" id="myInput" onkeyup="filterFunction()">
        </div>
        <div class="listTochoose" id="listTochoose">
        <!-- <a onclick="mySelect('ทิฆัมพร เทพสุต')">ทิฆัมพร เทพสุต <br> 1111111111 </a> -->
            <?php
            if(!empty($row))
                foreach($row as $rows) {
            ?>
                <a onclick="mySelect( '<?php echo $rows['fullname'] . ' ' . $rows['lastname']; ?>' , '<?php echo $rows['id']; ?>' )"> <?php echo $rows['fullname'] . ' ' . $rows['lastname']; ?> <br> <?php echo $rows['id']; ?> </a>
            <?php
                }
            ?>
        </div>
    </div>
</div>
<div>
    <h1> Helloooooooooo </h1>
</div>

<div>
    <a href="./test.html"> LINK </a>
</div>


<script type="text/javascript" src="./dropdownFunction.js"></script>

</body>
</html>