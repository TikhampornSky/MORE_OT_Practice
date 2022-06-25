<?php
    $ddate = "2022-07-22 07:55:00" ;
    //$ddate = "2012-6-21";
    $date = new DateTime($ddate);
    $week = $date->format("W");
    $year = $ddate[0]*1000 + $ddate[1]*100 + $ddate[2]*10 + $ddate[3] ;
    $code = $week + (int)$year;
    echo "Weeknummer: $week   ";               //Get same number (althought difference year)
    echo "Year: $year     "; 
    echo "my code: $code   "; 
    echo "-------------" ;

    $ddate = "2023-07-22 07:55:00" ;
    //$ddate = "2012-6-21";
    $date = new DateTime($ddate);
    $week = $date->format("W");
    echo "Weeknummer: $week ";

    $day = date('w');
    $week_start = date('m-d-Y', strtotime('-'.$day.' days'));
    $week_end = date('m-d-Y', strtotime('+'.(6-$day).' days'));
    echo $week_start ;
    echo $week_end ;
?>