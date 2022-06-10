function myFunction(isCheck) {  //show dropdown content
    //console.log(isCheck) ;
    if (isCheck) {
        document.getElementsByClassName("dropdown-content")[0].style.display = "block";
        var name = document.getElementById("myInput").value ;
        if (name != "") {
            console.log("here") ;
            document.getElementById("myInput").style.display = "block";
            document.getElementById("listTochoose").style.display = "none";
        }
    } else {
        document.getElementsByClassName("dropdown-content")[0].style.display = "none";
    }     
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
    document.getElementById("myInput").style.display = "block";
    document.getElementById("listTochoose").style.display = "none";
    var Approve1ID = id ;
    console.log(Approve1ID) ;
}
