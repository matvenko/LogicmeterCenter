<?php
chdir('..');
include "init.php";

$holidays = [
    '2020-11-23',
    '2020-12-31',
    '2021-01-01',
    '2021-01-02',
    '2021-01-03',
    '2021-01-04',
    '2021-01-05',
    '2021-01-06',
    '2021-01-07',
    '2021-01-08',
    '2021-01-09',
    '2021-01-10',
    '2021-01-11',
    '2021-01-19',
    '2021-03-03',
    '2021-03-08',
    '2021-04-09',
    '2021-04-30',
    '2021-05-01',
    '2021-05-02',
    '2021-05-03',
    '2021-05-26',
];

$start_date = "2020-09-14";
$year = 2020;
$day_id = 26;
$day_1 = 3;
$day_2 = 3;
$lesson = 72;

$lesson_n = 0;
for($i = 1; $i <= 400; $i++){
    if(in_array($start_date, $holidays)){
        $start_date = current_date(strtotime($start_date." +1 day"));
        continue;
    }

    $unix_time = date_to_unix($start_date);
    if((int)date("N", $unix_time) == $day_1){
        $lesson_n ++;
        echo '"'.$day_id.'","'.$year.'","'.$lesson_n.'","'.$start_date.'"<br>';
    }
    if((int)date("N", $unix_time) == $day_2){
        $lesson_n ++;
        echo '"'.$day_id.'","'.$year.'","'.$lesson_n.'","'.$start_date.'"<br>';
    }

    if($lesson_n == $lesson){
        break;
    }
    $start_date = current_date(strtotime($start_date." +1 day"));
}