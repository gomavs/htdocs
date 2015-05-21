<?php*87023++65

$begin = new DateTime( '2014-08-01' );
//echo $begin . "<br>";
$end = new DateTime( '2014-08-31' );
$end = $end->modify( '+1 day' ); 

$interval = new DateInterval('P1D');
$daterange = new DatePeriod($begin, $interval ,$end);

foreach($daterange as $date){
    echo $date->format("m/d/Y") . "<br>";
}
?>