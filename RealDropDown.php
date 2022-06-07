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
<meta http-equiv="Content-Language" content="th" />
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta charset="UTF-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">   
<meta name="viewport" content="width=device-width, initial-scale=1">
<style>
#myInput {
  box-sizing: border-box;
  background-position: 14px 12px;
  background-repeat: no-repeat;
  font-size: 16px;
  padding: 14px 20px 12px 45px;
  border: none;
  border-bottom: 1px solid #ddd;
}

#myInput:focus {outline: 3px solid #ddd;}

.dropdown {
  position: relative;
  display: inline-block;
}

.dropdown-content {
  display: none;
  position: absolute;
  background-color: #f6f6f6;
  min-width: 230px;
  overflow: auto;
  border: 1px solid #ddd;
  z-index: 1;
}

.dropdown-content a {
  color: black;
  padding: 12px 16px;
  text-decoration: none;
  display: block;
}

.dropdown a:hover {background-color: #ddd;}

.show {display: block;}
</style>
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

<div class="dropdown">
  <input type="checkbox" id="myCheck" onclick="myFunction()">
  <label for="myCheck">โปรดเลือกผู้อนุมัติของท่าน</label> 
    <div id="myDropdown" class="dropdown-content" >
    <input type="text" placeholder="โปรดใส่ชื่อผู้อนุมัติของท่าน" id="myInput" onkeyup="filterFunction()">
    <div id="listTochoose">
        <!-- <a onclick="mySelect('ทิฆัมพร เทพสุต')">ทิฆัมพร เทพสุต <br> 1111111111 </a> -->
        <?php
        if(!empty($row))
            foreach($row as $rows) {
        ?>
            <a onclick="mySelect( '<?php echo $rows['fullname'] . ' ' . $rows['lastname']; ?>' )"> <?php echo $rows['fullname'] . ' ' . $rows['lastname']; ?> <br> <?php echo $rows['id']; ?> </a>
        <?php
            }
        ?>
    </div>
    </div>
</div>


<script>

function myFunction() {                                                                                //show dropdown content
    document.getElementById("myDropdown").classList.toggle("show");
}

function filterFunction() {
  var input, filter, ul, li, a, i;
  input = document.getElementById("myInput");
  filter = input.value.toUpperCase();
  div = document.getElementById("myDropdown");
  a = div.getElementsByTagName("a");
  var InputLength = input.value.length ;
  for (i = 0; i < a.length; i++) {
    txtValue = a[i].textContent ;
    var arr = txtValue.split(" ") ;     //['', 'Susan', 'Jeffrey', '', '2147483647', '']
    var fname = arr[1] ;
    var lname = arr[2] ;
    var id = arr[4] ;
    //console.log(txtValue) ;
    //console.log(arr) ;
    //console.log(fname) ;
    //console.log(lname) ;
    //console.log(id) ;
    var isOk = true ;
    var isOk2 = true ;
    var isOk3 = true ;
    var isOk4 = true ;
    for (j = 0; j < InputLength; j++) {         //search by NAME
        if (j >= fname.length) {
            break ;
        }
        if (!(fname[j] == input.value[j])) {
            isOk = false ;
            break ;
        }
    }
    for (j = 0; j < InputLength; j++) {         //search by LASTNAME
        if (j >= lname.length) {
            if (InputLength > lname.length) {
                isOk2 = false ;
            }
            break ;
        } 
        if (!(lname[j] == input.value[j])) {
            isOk2 = false ;
            break ;
        }
    }
    for (j = 0; j < InputLength; j++) {         ////search by ID
        if (j >= id.length) {
            if (InputLength > id.length) {
                isOk3 = false ;
            }
            break ;
        }
        if (!(id[j] == input.value[j])) {
            isOk3 = false ;
            break ;
        }
    }
    var stringCheck = fname + " " + lname ;
    if (isOk) {
        for (j = 0; j < InputLength; j++) {
            if (j > stringCheck.length) break ;
            if (!(stringCheck[j] == input.value[j])) {
                isOk4 = false ;
                break ;
            }
        }
    }

    if ((isOk && isOk4) || isOk2 || isOk3) {
        //console.log(input.value + " <match with> " + txtValue) ;
        if (document.getElementById("listTochoose").style.display == "none") {
            document.getElementById("listTochoose").style.display = "";
        }
        a[i].style.display = "";
    } else {
        a[i].style.display = "none";
    }
  }
}

function mySelect(valueSelect) {
    var arr = valueSelect.split(" ") ;
    document.getElementById("myInput").value = arr[0] + " " + arr[1] ;
    console.log(arr[0] + " " + arr[1]) ;
    document.getElementById("listTochoose").style.display = "none";
}

</script>

</body>
</html>