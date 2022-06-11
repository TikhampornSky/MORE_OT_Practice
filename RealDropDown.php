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

    $myname = $_GET['myname'] ;
    $arr = explode(" ", $myname);
    $fullname = $arr[0] ; 
    $lastname = $arr[1] ;

    $OLD_approve_firstname = "";
    $OLD_approve_lastname = "";
    $OLD_approve_id ;
    $approve = $conn->query("SELECT reviewer FROM person WHERE fullname='$fullname' AND lastname='$lastname'") ;   //return as object ;
    while ($row2 = $approve->fetch_assoc()) {
        $OLD_approve_id =  $row2['reviewer'] ;
    }
    //find name and lastname of old approver
    if ($OLD_approve_id != "") {
        $approve2 = $conn->query("SELECT fullname, lastname FROM person WHERE id='$OLD_approve_id'") ;   //return as object ;
        while ($row3 = $approve2->fetch_assoc()) {
            $OLD_approve_firstname = $row3['fullname'] ;
            $OLD_approve_lastname = $row3['lastname'];
        }
    }

    if (isset($_POST["save"])) {
        $approve1 = $_POST['searchBox'];
        header("Location: test.php?type=demo&approve1=$approve1&myname=$myname");
    }
?>

<!DOCTYPE html>
<html>
<head>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script>
$(document).ready(function(){
    console.log( "ready!" );
    if (document.querySelector('input[name="yesno"]:checked') != null) {
        var data = document.querySelector('input[name="yesno"]:checked').value;
        if (data == "y") {
            document.getElementsByClassName("dropdown-content")[0].style.display = "block";
            document.getElementById("myInput").style.display = "block";
            document.getElementById("listTochoose").style.display = "none";
        }
    }
    
    //------- initialize value in search box----------
    var old_id = '<?php echo $OLD_approve_id;?>';
    if (old_id != '') {
        var old_name = '<?php echo $OLD_approve_firstname . " ". $OLD_approve_lastname;?>';
        document.getElementById("myInput").value = old_name ;
        console.log(old_id) ;
    }
    
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
<p> <?php echo $OLD_approve_id;?> </p>
<p> <?php echo $OLD_approve_firstname . "  " . $OLD_approve_lastname;?> </p>

<form name="myForm" onsubmit=" return validateForm()" method="post" required>

<div>
    <p id="warn-type"> กรุณาเลือกรูปแบบการบันทึกเวลา  </p>
    <label for="SelectTypeOfWork">โปรดเลือกรูปแบบการทำงานของคุณ :</label>
    <select name="typeWork" id="IDtypeWork">
        <option value="none" selected hidden>รูปแบบการทำงาน</option>
        <option value="แตะบัตร"> แตะบัตร </option>
        <option value="ทำงานนอกสถานที่"> ทำงานนอกสถานที่ </option>
    </select>
</div>

<div>
    <p id="warn-approve1"> กรุณาระบุชื่อผู้ตรวจสอบในระดับผู้ช่วยผู้จัดการ/ผู้ชำนาญการ </p>
    <p id="warn-approve2"> กรุณาเลือกว่าท่านมีผู้ตรวจสอบในระดับผู้ช่วยผู้จัดการ/ผู้ชำนาญการหรือไม่ </p>
    <p>ท่านมีผู้ตรวจสอบในระดับผู้ช่วยผู้จัดการ/ผู้ชำนาญการหรือไม่</p>
    <input type="radio" name="yesno" value="y" onchange="myFunction(true)"> มี
    <input type="radio" name="yesno" value="n" onchange="myFunction(false)"> ไม่มี
</div>


<div class="dropdown" id="alldropdown">
    
    <div id="myDropdown" class="dropdown-content" >
        <div id="searchBox">
            <input type="text" placeholder="โปรดใส่ชื่อผู้อนุมัติของท่าน" class = "myInputClass" id="myInput" onkeyup="filterFunction()" name="searchBox">
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

<!-- <input type="submit" value="Submit"> -->
<button type="summit" name="save">Click Me!</button>

</form>

<div>
    <a href="./test.html"> LINK </a>
</div>


<script type="text/javascript" src="./dropdownFunction.js"></script>

</body>
</html>