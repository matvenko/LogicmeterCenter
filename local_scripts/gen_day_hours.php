<?php
chdir('..');
include "init.php";

$subject  = "e";

$day_hours = [
    3 => [
        '6;8',
    ],
    4 => [
        '26;8',
    ],
    5 => [
        '18;8',
    ],
    6 => [
        '7;8',
    ],
];

foreach ($day_hours as $grade => $data) {
    $n = 0;
    foreach ($data as $dh) {
        $n ++;
        $data_array = explode(";", $dh);
        $day_id = $data_array[0];
        $hour_id = $data_array[1];

        $day_hours = $query_l->select_ar_sql("center_lesson_hours_to_dayes", "id", "
        subject = '".$subject."' AND 
        grade = ".$grade." AND 
        dayes_id = ".$day_id." AND 
        hours_id = ".$hour_id."
    ");
        if (isset($day_hours['id'])) {
            $query_l->update_sql("center_lesson_hours_to_dayes", ['closed' => 0, 'sort' => $n], "id = ".(int)$day_hours['id']);
        } else {
            $fields = [
                'subject'  => $subject,
                'grade'    => $grade,
                'dayes_id' => $day_id,
                'hours_id' => $hour_id,
                'closed'   => 0,
                'sort'     => $n
            ];

            $query_l->insert_sql("center_lesson_hours_to_dayes", $fields);
        }
    }
}