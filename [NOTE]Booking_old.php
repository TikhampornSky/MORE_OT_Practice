<?php
include("../dbconnect.php");
include("../test.php");
session_start();
$user_id = $_SESSION["user_id"];
$typepe = $_SESSION["user_type"];


$sql = mysqli_query($con, "SELECT * FROM employee a , employeeInfo b WHERE a.employee_id = '$user_id' AND a.employee_id = b.employee_id");
$rs = $sql->fetch_object();
$name = $rs->employee_name;
$lastname = $rs->employee_lastname;

//Connect to approverinfo table to get name, id to create drop down
$sql2 = "select approver_name, approver_lastname, approver_id from approverInfo ORDER BY approver_name, approver_lastname, approver_id" ;
$result = ($con->query($sql2));
$row = [];              //declare array to store the data of database
  
if ($result->num_rows > 0)  {                       // fetch all data from db into array 
    $row = $result->fetch_all(MYSQLI_ASSOC);  
}

if ($typepe == "normal"){
    $type = "(พนักงานปกติ)";
    $in = '07:30';
    $out = '07:29';
}else {
    $type = "(พนักงานกะ)";
    $in = '08:00';
    $out = '08:00';
}

$number = 0;
$normal = 0;
$lunch = 0;
$range = "";

//-------------ดึงชื่อ reviewer (ถ้ามี)ออกมา--------------
$OLD_approve_firstname = "";
$OLD_approve_lastname = "";
$OLD_approve_id ;
$approve = $con->query("SELECT reviewer FROM employeeInfo WHERE employee_name='$name' AND employee_lastname='$lastname'") ;   //return as object ;
while ($row2 = $approve->fetch_assoc()) {
    $OLD_approve_id =  $row2['reviewer'] ;
}
//find name and lastname of old approver
if ($OLD_approve_id != "") {
    $approve2 = $con->query("SELECT approver_name, approver_lastname FROM approverInfo WHERE approver_id='$OLD_approve_id'") ;   //return as object ;
    while ($row3 = $approve2->fetch_assoc()) {
        $OLD_approve_firstname = $row3['approver_name'] ;
        $OLD_approve_lastname = $row3['approver_lastname'];
    }
}
//------------------------------------------------------

if (isset($_POST["save"])) {
    $date = $_POST['date'];
    $time_start = $_POST['time_start'];
    $time_end = $_POST['time_end'];
    $request_msg = $_POST['msg'];
    $reason = $_POST['reason'];
    $detail = $_POST['detail'];
    $typeWork = $_POST['typeWork'];
    $approve1 = $_POST['searchBox'] ;
    $yesno = $_POST['yesno'] ;
    $result2 = true;
    $rows=0;
    
    //------------ตรวจสอบการทับกันของวัน ----------------
    $Datas = array();
    $sql3 = mysqli_query($con, "SELECT transaction_id,time_start,time_end,(time_start>time_end) as Nextday FROM transaction WHERE user_id = '$rs->employee_id' AND transaction_id != '$transaction_id' AND date = '$date' AND approve_status not in('cancle','reject')");
    if (mysqli_affected_rows($con) >= '1') {
        $x = decimalHours($time_start); //วันเวลาเริ่มใหม่
        $y = decimalHours($time_end); //วันเวลาสิ้นสุดใหม่
        $DatetimeStartInput = $date." ".$time_start;
        $DatetimeStartInput = strtotime($DatetimeStartInput);
        $DatetimeStartInput = date('Y-m-d H:i:s',$DatetimeStartInput);

        if($x>$y){
            $date1 = str_replace('-', '/', $date);
            $DateEnd = date('Y-m-d',strtotime($date1 . "+1 days"));
            $DateEnd= $DateEnd." ".$time_end;
            $DatetimeEndInput = strtotime($DateEnd);
            $DatetimeEndInput=date('Y-m-d H:i:s',$DatetimeEndInput);
            $IsNextdayinput=1;
        }else{
            $DatetimeEndInput= $date." ".$time_end;
            $DatetimeEndInput = strtotime($DatetimeEndInput);
            $DatetimeEndInput = date('Y-m-d H:i:s',$DatetimeEndInput);
            $IsNextdayinput=0;
        }

        while($row5 = $sql3->fetch_object()){
            $Datas[$rows][0] = $row5->transaction_id;
            $Datas[$rows][1] = $row5->time_start;
            $DBDateStart=date_create($row5->time_start);
            $DatetimeStartOld=date_format($DBDateStart,"Y-m-d H:i:s");
            $Datas[$rows][2] = $row5->time_end;
            if($row->time_start>$row5->time_end){
                $DBDateEnd=date_create($row5->time_end);
                $DBDateEnd->modify('+1 day');
                $DatetimeEndOld=date_format($DBDateEnd,"Y-m-d H:i:s");
                $Datas[$rows][4] =$DatetimeEndOld;
                $IsNextday=1; 
            }else{
                $DBDateEnd=date_create($row5->time_end);
                $DatetimeEndOld=date_format($DBDateEnd,"Y-m-d H:i:s");
                $Datas[$rows][4] =$DatetimeEndOld;
                $IsNextday=0;
            }
            $Datas[$rows][3] = $row5->Nextday;
            $rows=$rows+1;
            
            if($DatetimeStartInput>$DatetimeStartOld && $DatetimeEndInput<$DatetimeEndOld){
                //$alertmsg="ไม่สามารถบันทึกได้ เนื่องจากวันเวลาที่ input อยู่ในช่วงเวลาเก่า";
                $alertmsg="ช่วงเวลาที่ท่านเลือกตรงกับเวลาที่เคยขออนุมัติไปแล้ว โปรดเลือกช่วงเวลาใหม่";
                $result2 = false;
                break;
            }else if($DatetimeStartInput<=$DatetimeStartOld && $DatetimeEndInput>=$DatetimeEndOld){
                //$alertmsg="ไม่สามารถบันทึกได้ เนื่องจากวันเวลาที่ input ครอบเวลาที่มีอยู่";
                $alertmsg="ช่วงเวลาที่ท่านเลือกตรงกับเวลาที่เคยขออนุมัติไปแล้ว โปรดเลือกช่วงเวลาใหม่";
                $result2 = false;
                break;
            }else if($DatetimeStartInput<=$DatetimeStartOld && $DatetimeEndInput>$DatetimeStartOld && $DatetimeEndInput<=$DatetimeEndOld){
                //$alertmsg="ไม่สามารถบันทึกได้ เนื่องจากวันเวลาที่ input อยู่ในช่วงเหลื่อมข้างหน้า";
                $alertmsg="ช่วงเวลาที่ท่านเลือกตรงกับเวลาที่เคยขออนุมัติไปแล้ว โปรดเลือกช่วงเวลาใหม่";
                $result2 = false;
                break;
            }else if($DatetimeEndInput>=$DatetimeEndOld && $DatetimeStartInput>=$DatetimeStartOld && $DatetimeStartInput<$DatetimeEndOld){
                //$alertmsg="ไม่สามารถบันทึกได้ เนื่องจากวันเวลาที่ input อยู่ในช่วงเหลื่อมข้างหลัง";
                $alertmsg="ช่วงเวลาที่ท่านเลือกตรงกับเวลาที่เคยขออนุมัติไปแล้ว โปรดเลือกช่วงเวลาใหม่";
                $result2 = false;
                break;
            }else{
                //กรณีไม่ซ้ำ ไม่เหลื่อม ไม่ครอบ เวลาที่เคยขอไว้ก่อนหน้านี้
                //$alertmsg=$DatetimeStartInput.",".$DatetimeEndInput."/".$DatetimeStartOld.",".$DatetimeEndOld;
                //$alertmsg="OK";
                $result2 = true;
            }
        }
        
        if($result2==true){                //action save
            //header("Location: edit.php?transaction=$transaction_id&date=$date&time_start=$time_start&time_end=$time_end&msg=$msg&ot_type=$ot_type");
        }
        
    }else{//หากไม่มีข้อมูลในวันนั้นๆ ให้บันทึกได้เลย
        //action save
        //header("Location: edit.php?transaction=$transaction_id&date=$date&time_start=$time_start&time_end=$time_end&msg=$msg&ot_type=$ot_type");
    }
    //--------------------------------------------------------
    if ($yesno == 'n') {
        $approve1 = "" ;
    } else {
        $approve1 = $_POST['searchBox'] ;
    }
    $range = $time_end - $time_start;
    
    $stamp = strtotime($date);
    $ot = $_GET['ot'];
    if($type == "(พนักงานกะ)"){
        $ot = "normal";
    }
    
    if($ot == "lunch"){
        if((decimalHours($time_start) >= decimalHours('12:00')) && (decimalHours($time_start) <= decimalHours('13:00'))){
            if((decimalHours($time_end) >= decimalHours('12:00')) && (decimalHours($time_end) <= decimalHours('13:00')) && $result2==true){
                header("Location: temp.php?ot=lunch&date=$date&time_start=$time_start&time_end=$time_end&msg=$request_msg&reason=$reason&detail=$detail&typeWork=$typeWork&approve1=$approve1");
                //header("Location: temp.php?ot=lunch&date=$date&time_start=$time_start&time_end=$time_end&msg=$request_msg&reason=$reason&detail=$detail&typeWork=$typeWork&approve1=$approve1&DatetimeStartInput=$DatetimeStartInput&DatetimeEndInput=$DatetimeEndInput&alertmsg=$alertmsg");
            }
            else {
                $lunch = 1;
            }
        }else{
            $lunch = 1;
        }
    }if ($ot == "normal"){
        $checkDate = mysqli_query($con, "SELECT * FROM transaction WHERE user_id = '$user_id' AND date = '$date' AND approve_status != 'reject' AND approve_status != 'cancle'");
        if (mysqli_affected_rows($con) >= '1') {
            $checkSTime = mysqli_query($con, "SELECT * FROM transaction WHERE user_id = '$user_id' AND '$date' = date AND approve_status != 'reject' AND approve_status != 'cancle' AND '$time_start' BETWEEN time_start AND time_end");
            if (mysqli_affected_rows($con) >= '1') {
                $number = 1;
            } else {
                if((decimalHours($time_start) >= decimalHours('12:00')) && (decimalHours($time_start) <= decimalHours('13:00')) && (decimalHours($time_end) >= decimalHours('12:00')) && (decimalHours($time_end) <= decimalHours('13:00'))){
                    $normal = 1;
                }else if ($result2==true){
                    header("Location: temp.php?ot=$typepe&date=$date&time_start=$time_start&time_end=$time_end&msg=$request_msg&reason=$reason&detail=$detail&typeWork=$typeWork&approve1=$approve1");
                    //header("Location: temp.php?ot=$typepe&date=$date&time_start=$time_start&time_end=$time_end&msg=$request_msg&reason=$reason&detail=$detail&typeWork=$typeWork&approve1=$approve1&DatetimeStartInput=$DatetimeStartInput&DatetimeEndInput=$DatetimeEndInput&alertmsg=$alertmsg");
                } else {
                    echo '<script type="text/JavaScript"> 
                        console.log("here1");
                    </script>' ;
                    
                }
            }
        } else {
            if((decimalHours($time_start) >= decimalHours('12:00')) && (decimalHours($time_start) <= decimalHours('13:00')) && (decimalHours($time_end) >= decimalHours('12:00')) && (decimalHours($time_end) <= decimalHours('13:00'))){
                    $normal = 1;
            }else if ($result2==true) {
                header("Location: temp.php?ot=$typepe&date=$date&time_start=$time_start&time_end=$time_end&msg=$request_msg&reason=$reason&detail=$detail&typeWork=$typeWork&approve1=$approve1");
                //header("Location: temp.php?ot=$typepe&date=$date&time_start=$time_start&time_end=$time_end&msg=$request_msg&reason=$reason&detail=$detail&typeWork=$typeWork&approve1=$approve1&DatetimeStartInput=$DatetimeStartInput&DatetimeEndInput=$DatetimeEndInput&alertmsg=$alertmsg");

            } else {
                echo '<script type="text/JavaScript"> 
                        console.log("here1");
                    </script>' ;
                
            }
        }
        
    }
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    
    <!-- Link to jquery-->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script>
        $(document).ready(function(){
            if (document.querySelector('input[name="yesno"]:checked') != null) {
                var data = document.querySelector('input[name="yesno"]:checked').value;
                console.log(data) ;
                if (data == "y") {
                    console.log("hiiiiiiiiii") ;
                    document.getElementsByClassName("dropdown-content")[0].style.display = "block";
                    document.getElementById("myInput").style.display = "inline-block";
                    document.getElementById("listTochoose").style.display = "none";
                }
            }
            console.log( "ready!" );
            //------- initialize value in search box
            var old_id = '<?php echo $OLD_approve_id;?>';
            if (old_id != '') {
                var old_name = '<?php echo $OLD_approve_firstname . " ". $OLD_approve_lastname;?>';
                document.getElementById("myInput").value = old_name ;
                console.log(old_id) ;
            }
        });
    </script>
    
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>แบบฟอร์มกรอก</title>
    <!-- plugins:css -->
    <link rel="stylesheet" href="../../vendors/feather/feather.css">
    <link rel="stylesheet" href="../../vendors/ti-icons/css/themify-icons.css">
    <link rel="stylesheet" href="../../vendors/css/vendor.bundle.base.css">
    <!-- endinject -->
    <!-- Plugin css for this page -->
    <link rel="stylesheet" href="../../vendors/select2/select2.min.css">
    <link rel="stylesheet" href="../../vendors/select2-bootstrap-theme/select2-bootstrap.min.css">
    <!-- End plugin css for this page -->
    <!-- css for dropdown -->
    <link rel="stylesheet" href="./dropdown.css">
    <!-- inject:css -->
    <link rel="stylesheet" href="../../css/vertical-layout-light/style.css">
    <!-- endinject -->
    <link rel="shortcut icon" href="../../images/favicon.png" />
</head>

<body>
    <div class="container-scroller">
        <nav class="navbar col-lg-12 col-12 p-0 fixed-top d-flex flex-row" >
            <div class="text-center navbar-brand-wrapper d-flex align-items-center justify-content-center" style="width: 80px;" >
                <a class="navbar-brand brand-logo mr-5"><img src="../../images/1.jpeg" class="mr-2" alt="logo" /></a>
                <a class="navbar-brand brand-logo-mini"><img src="../../images/img.png" class="mr-2" style="width: 75px; height: 50px;" /></a>
            </div>
            <div class="navbar-menu-wrapper d-flex align-items-center justify-content-end" style="width: calc(100% - 80px);">
            <span class="text-dark">คุณ <?=$name ?> <?=$lastname?><?=$ot?></span>
            </div>
        </nav>
        <div class="container-fluid page-body-wrapper">
            <div class="theme-setting-wrapper">
                <div id="settings-trigger"><i class="ti-settings"></i></div>
                <div id="theme-settings" class="settings-panel">
                    <i class="settings-close ti-close"></i>
                    <p class="settings-heading">SIDEBAR SKINS</p>
                    <div class="sidebar-bg-options selected" id="sidebar-light-theme">
                        <div class="img-ss rounded-circle bg-light border mr-3"></div>Light
                    </div>
                    <div class="sidebar-bg-options" id="sidebar-dark-theme">
                        <div class="img-ss rounded-circle bg-dark border mr-3"></div>Dark
                    </div>
                    <p class="settings-heading mt-2">HEADER SKINS</p>
                    <div class="color-tiles mx-0 px-4">
                        <div class="tiles success"></div>
                        <div class="tiles warning"></div>
                        <div class="tiles danger"></div>
                        <div class="tiles info"></div>
                        <div class="tiles dark"></div>
                        <div class="tiles default"></div>
                    </div>
                </div>
            </div>
            <!--<div class="main-panel">-->
            <div class="content-wrapper">
                <div class="row">
                    <div class="col-md-12">
                        <div class="row">
                            <div class="col-md-12  stretch-card">
                                <div class="card">
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-12">
                                                <div class="row">
                                                    <div class="col-12  stretch-card">
                                                        <div class="card">
                                                            <font color="red"><?= $alertmsg?></font>
                                                            <div class="text-dark " style="text-align: center; font-size: 150%; ">แบบฟอร์มขออนุมัติ OT</div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div><br>
                                        <div class="row">
                                            <div class="col-12 grid-margin stretch-card">
                                                <div class="card">
                                                    <p style="color: blue;">หมายเหตุ :</p>
                                                            <p style="color: blue; font-size: 85%">1. กรณีพนักงานทำ OT ควบ 2 กะ ห้ามขอ OT ควบกะ ให้ขอแยกเป็นกะ 2 ครั้ง</p>
                                                            <p style="color: blue; font-size: 85%">2. กรณีทำ OT ช่วงพักเที่ยง ให้ขอแยกอีกครั้งหนึ่ง โดยเลือกประเภทเป็น OT พักเที่ยง</p>
                                                    <div class="card-body">
                                                        <form name="myForm" class="forms-sample" onsubmit="return validateForm()" method="POST">
                                                            <div class="row">
                                                                <div class="col-12">
                                                                    <label for="exampleInputPassword4">วันที่ขอ OT</label>
                                                                    <div class="form-group row">
                                                                        <div class="col-12">
                                                                            <input type="date" name="date" class="form-control col-12" placeholder="dd/mm/YYYY" style="-webkit-appearance: none; -moz-appearance: none;" />
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                        
                        
                                                            <div class="row">
                                                                <div class="col-6">
                                                                    <label for="exampleInputPassword4">เวลาเริ่ม</label>
                                                                    <div class="form-group row">
                                                                        <div class="col-12">
                                                                            <input type="time" id="Text1" oninput="add_number()" name="time_start" class="form-control" style="-webkit-appearance: none; -moz-appearance: none;" />
                                                                        </div>
                                                                    </div>
                                                                </div>
                        
                                                                <div class="col-6">
                                                                    <label for="exampleInputPassword4">เวลาสิ้นสุด</label>
                                                                    <div class="form-group row">
                                                                        <div class="col-12">
                                                                            <input type="time" id="Text2" oninput="add_number()" name="time_end" class="form-control" style="-webkit-appearance: none; -moz-appearance: none;" />
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="row">
                                                                <div class="col-12">
                                                                    <p id="demo2" style="color: red;"></p>
                                                                    <center>
                                                                    <a href="otType.php"><span id="demo3" style="color: blue; display:none;">คลิ๊กที่นี้</span></a>
                                                                    </center>
                                                                </div>
                                                            </div>
                                                            <div class="col-12">
                                                                <p id="demo1" style="color: red;"></p>
                                                                
                                                                <!--<a href="otType.php" style="color: red;"><p id="demo2"><span>คลิ๊ก</span></p></a>-->
                                                                
                                                                <?php
                                                                if($normal>0){
																	echo '<script>',
                                                                    'document.getElementById("demo2").innerHTML = "แบบฟอร์มนี้เป็นแบบฟอร์มขอ OT ปกติ กรณีขอ OT คาบเกี่ยวในช่วงเวลา 12:00-13:00 ให้ขออนุมัติในแบบฟอร์มขอ OT พักเที่ยงอีกครั้งหนึ่ง";',
                                                                    '</script>';
                                                                    
                                                                    echo '<script>',
                                                                    'document.getElementById("demo3").style.display = "block";',
                                                                    '</script>';
																}
                                                                if ($number > 0) {
                                                                    echo '<script>',
                                                                    'document.getElementById("demo1").innerHTML = "ช่วงเวลานี้คุณได้มีการขอแล้ว กรุณาเลือกช่วงเวลาใหม่";',
                                                                    '</script>';
                                                                }
                                                                if ($lunch > 0){
                                                                    echo '<script>',
                                                                    'document.getElementById("demo2").innerHTML = "แบบฟอร์มนี้เป็นแบบฟอร์มขอ OT ช่วงพักเที่ยง กรณีขอ OT นอกเหนือจากช่วงเวลา 12:00-13:00 ให้ขออนุมัติในแบบฟอร์มขอ OT ปกติ";',
                                                                    '</script>';
                                                                    
                                                                    echo '<script>',
                                                                    'document.getElementById("demo3").style.display = "block";',
                                                                    '</script>';
                                                                }
                                                                ?>
                                                            </div>
                                                            <!--<div class="row">-->
                                                            <!--    <div class="col-6">-->
                                                            <!--        <label for="exampleInputPassword4">จำนวนชั่วโมง</label>-->
                                                            <!--        <div class="form-group row">-->
                                                            <!--            <div class="col-12">-->
                                                            <!--                <input type="text" id="txtresult3" oninput="add_number()" class="form-control" style="-webkit-appearance: none; -moz-appearance: none;" />-->
                                                            <!--            </div>-->
                                                            <!--        </div>-->
                                                            <!--    </div>-->
                                                            <!--</div>-->
                                                            <script>
                                                                var text1 = document.getElementById("Text1");
                                                                var text2 = document.getElementById("Text2");
                        
                                                                function add_number() {
                                                                    var t1 = text1.value;
                                                                    var hours = t1.split(":")[0];
                                                                    var minutes = t1.split(":")[1];
                                                                    var displayTime = hours + "." + minutes;
                                                                    var first_number = parseFloat(displayTime); //txt to float
                                                                    if (isNaN(first_number)) first_number = 0;
                        
                                                                    var t2 = text2.value;
                                                                    var hours2 = t2.split(":")[0];
                                                                    var minutes2 = t2.split(":")[1];
                                                                    var displayTime2 = hours2 + "." + minutes2;
                                                                    var second_number = parseFloat(displayTime2);
                                                                    if (isNaN(second_number)) second_number = 0;
                                                                    
                        
                                                                    var result = (second_number - first_number).toFixed(2);
                                                                    document.getElementById("txtresult3").value = result;
                        
                                                                }
                                                            </script>
                                                            <div class="form-group">
                                                                <p id="warn-reason"> โปรดระบุเหตุผล </p>
                                                                <label for="exampleSelectGender">เหตุผล</label>
                                                                <select name="msg" class="form-control" onchange="yesnoCheck(this);" id="exampleSelectGender">
                                                                    <option value="none" selected hidden>กรุณาเลือกเหตุผล</option>
                                                                    <option value="งานต่อเนื่อง">งานต่อเนื่อง</option>
                                                                    <option value="Project">Project</option>
                                                                    <option value="งานเร่งด่วน">งานเร่งด่วน</option>
                                                                    <option value="วันหยุดประเพณี">วันหยุดประเพณี</option>
                                                                    <option value="งาน PM">งาน PM</option>
                                                                    <option value="TPM/AM">TPM/AM</option>
                                                                    <option value="ปฏิบัติงานแทนเพื่อนร่วมงาน">ปฏิบัติงานแทนเพื่อนร่วมงาน</option>
                                                                    <option value="OT ช่วงพัก/พักเที่ยง">OT ช่วงพัก/พักเที่ยง</option>
                                                                    <option value="other">อื่นๆ</option>
                                                                </select>
                                                                <div id="ifYes" style="display: none;">
                                                                    <label for="reason">เหตุผลอื่นๆ</label>
                                                                    <input type="text" id="car" name="reason" class="form-control" /><br />
                                                                </div>
                                                                <script>
                                                                    function yesnoCheck(that) {
                                                                        if (that.value == "other") {
                                                                            document.getElementById("ifYes").style.display = "block";
                                                                        } else {
                                                                            document.getElementById("ifYes").style.display = "none";
                                                                        }
                                                                    }
                                                                </script>
                                                            </div>
                                                            <div class="row">
                                                                <div class="col-12">
                                                                    <label for="exampleInputPassword4">รายละเอียดเพิ่มเติม</label>
                                                                    <div class="form-group row">
                                                                        <div class="col-12">
                                                                            <input type="text" name="detail" oninput="add_number()" class="form-control" style="-webkit-appearance: none; -moz-appearance: none;" />
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            
                                                            <!-- Adding what type of work (dropdown) -->
                                                             <div class="row">
                                                                <div class="col-12">
                                                                    
                                                                    <p id="showErrorWork" style="color:red;"> </p>
                                                                    <div class="form-group">
                                                                        <p id="warn-type"> กรุณาเลือกรูปแบบการบันทึกเวลา  </p>
                                                                        <label for="SelectTypeOfWork">เลือกรูปแบบการบันทึกเวลา :</label>
                                                                        <select name="typeWork" class="form-control" id="IDtypeWork">
                                                                            <option value="none" selected hidden>รูปแบบการทำงาน</option>
                                                                            <option value="แตะบัตร"> แตะบัตร </option> </option>
                                                                            <option value="ทำงานนอกสถานที่"> ทำงานนอกสถานที่ </option>
                                                                        </select>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            
                                                            <!-- Adding your approve1 (If user have...) -->
                                                            <div class="row">
                                                                <div class="col-12">
                                                                    <div class="form-group">
                                                                        <p id="warn-approve1"> กรุณาระบุชื่อผู้ตรวจสอบในระดับผู้ช่วยผู้จัดการ/ผู้ชำนาญการ </p>
                                                                        <p id="warn-approve2"> กรุณาเลือกว่าท่านมีผู้ตรวจสอบในระดับผู้ช่วยผู้จัดการ/ผู้ชำนาญการหรือไม่ </p>
                                                                        <p>ท่านมีผู้ตรวจสอบในระดับผู้ช่วยผู้จัดการ/ผู้ชำนาญการหรือไม่</p>
                                                                        <input type="radio" name="yesno" value="y" onchange="myFunction(true)"> มี
                                                                        <input type="radio" name="yesno" value="n" onchange="myFunction(false)"> ไม่มี
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            
                                                            
                                                            <div class="row">
                                                                <div class="col-12">
                                                                    <div class="dropdown" id="alldropdown"> 
                                                                        <div id="myDropdown" class="dropdown-content" >
                                                                            <div class="searchbox">
                                                                                <input type="text" placeholder="โปรดใส่ชื่อผู้อนุมัติของท่าน" id="myInput" onkeyup="filterFunction()" name="searchBox">
                                                                                <button onclick="document.getElementById('myInput').value = ''" id="clear_button">
                                                                                    <img src="./delete-icon.png" id='bin'/>
                                                                                </button>
                                                                                <!-- <button onclick="document.getElementById('myInput').value = ''" id="clear_button"><i class="clear_input"><img src="./delete-icon.png" id='bin'/></i></button> -->
                                                                            </div>
                                                                            <div id="listTochoose">
                                                                                <?php
                                                                                    if(!empty($row))
                                                                                        foreach($row as $rows) {
                                                                                ?>
                                                                                <a onclick="mySelect( '<?php echo $rows['approver_name'] . ' ' . $rows['approver_lastname']; ?>' , '<?php echo $rows['approver_id']; ?>' )"> <?php echo $rows['approver_name'] . ' ' . $rows['approver_lastname']; ?> <br> <?php echo $rows['approver_id']; ?> </a>
                                                                                <?php
                                                                                    }
                                                                                ?>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>

                                                            <center>
                                                                <button type="submit" name="save" class="btn btn-primary mr-2 col-12">สรุปข้อมูล</button>
                                                            </center>
                        
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div> 
                            </div>
                        </div>
                    </div>
                </div>    
            <!--    <footer class="footer">-->
            <!--    <div class="d-sm-flex justify-content-center justify-content-sm-between">-->
            <!--        <span class="text-muted text-center text-sm-left d-block d-sm-inline-block">Copyright © 2021. Premium <a href="https://www.bootstrapdash.com/" target="_blank">Bootstrap admin template</a> from BootstrapDash. All rights reserved.</span>-->
            <!--        <span class="float-none float-sm-right d-block mt-1 mt-sm-0 text-center">Hand-crafted & made with <i class="ti-heart text-danger ml-1"></i></span>-->
            <!--    </div>-->
            <!--</footer>-->
            </div>
        <!-- main-panel ends -->
    </div>
    <!-- page-body-wrapper ends -->
    </div>
    <!-- container-scroller -->
    <!-- plugins:js -->
    <script src="../../vendors/js/vendor.bundle.base.js"></script>
    <!-- endinject -->
    <!-- Plugin js for this page -->
    <script src="../../vendors/typeahead.js/typeahead.bundle.min.js"></script>
    <script src="../../vendors/select2/select2.min.js"></script>
    <!-- End plugin js for this page -->
    <!-- inject:js -->
    <script src="../../js/off-canvas.js"></script>
    <script src="../../js/hoverable-collapse.js"></script>
    <script src="../../js/template.js"></script>
    <script src="../../js/settings.js"></script>
    <script src="../../js/todolist.js"></script>
    <!-- endinject -->
    <!-- Custom js for this page-->
    <script src="../../js/file-upload.js"></script>
    <script src="../../js/typeahead.js"></script>
    <script src="../../js/select2.js"></script>
    <!-- js for search approve1-->
    <script type="text/javascript" src="./dropdownFunction.js"></script>
    <!-- End custom js for this page-->
</body>

</html>

<?php
mysqli_close($con);
?>