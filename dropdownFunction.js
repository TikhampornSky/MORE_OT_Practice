function myFunction(isCheck) {  //show dropdown content
    //console.log(isCheck) ;
    if (isCheck) {
        document.getElementsByClassName("dropdown-content")[0].style.display = "block";
        document.getElementById("myInput").style.display = "block";
        document.getElementById("listTochoose").style.display = "none";
    } else {
        document.getElementsByClassName("dropdown-content")[0].style.display = "none";
    }     
}

function filterFunction() {
  var name = document.getElementById("myInput").value ;
  if (name == "") {
    document.getElementById("listTochoose").style.display = "none";
    return ;
  }
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
        if (fname[j] != input.value[j]) {
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
        if (lname[j] != input.value[j]) {
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
        if (id[j] != input.value[j]) {
            isOk3 = false ;
            break ;
        }
    }
    var stringCheck = fname + " " + lname ;
    if (isOk) {
        for (j = 0; j < InputLength; j++) {
            if (j > stringCheck.length) break ;
            if (stringCheck[j] != input.value[j]) {
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

function mySelect(valueSelect, id) {
    var arr = valueSelect.split(" ") ;
    document.getElementById("myInput").value = arr[0] + " " + arr[1] ;
    console.log(arr[0] + " " + arr[1]) ;
    document.getElementById("myInput").style.display = "block";         /* difference */
    document.getElementById("listTochoose").style.display = "none";
    var Approve1ID = id ;
    console.log(Approve1ID) ;
}

function validateForm(ok) {
    var search = document.forms["myForm"]["searchBox"].value;
    var radios = document.getElementsByName('yesno');
    var choice1 = radios[0].checked ;
    var choice2 = radios[1].checked ;
    var typeworkk = document.getElementById('IDtypeWork').value ;
    //console.log(typeworkk) ;
    var yn ;
    if (choice1) {
        yn = radios[0].value ;
    } else {
        yn = radios[1].value ;
    }
    //console.log(yn) ;
    var isOk1 = true ;
    var isOk2 = true ;
    var isOk3 = true ;
    if (yn == "y" && (search == "" || search == null)) {
        isOk1 = false ;
    }
    if (!choice1 && !choice2) {
        isOk2 = false ;
    }
    if (typeworkk == "none") {
        isOk3 = false ;
    }

    if (!isOk1) {
        document.getElementById("warn-approve1").style.display = "block";
    } else {
        document.getElementById("warn-approve1").style.display = "none";
    }
    if (!isOk2) {
        document.getElementById("warn-approve2").style.display = "block";
    } else {
        document.getElementById("warn-approve2").style.display = "none";
    }
    if (!isOk3) {
        document.getElementById("warn-type").style.display = "block";
    } else {
        document.getElementById("warn-type").style.display = "none";
    }

    console.log(ok) ;
    if ((isOk1 && isOk2 && isOk3) == false) {
        return false && ok ;
    } else {
        var x = true && ok ;
        console.log("-->" + x) ;
        return true && ok;
    }
}
