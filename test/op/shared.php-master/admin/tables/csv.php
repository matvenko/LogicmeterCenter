<?php

//    #### APPEND COUNTRYS TO REGIONS ####
//    $countrys = mysql_array ('locations_countrys','country_code','country_id');
//    $regions = mysql_array ('locations_regions','region_id','region_country_code');
//    foreach ($regions as $region_id => $region_country)
//    {
//        mysql_debug_query ("update locations_regions set region_country='".$countrys[$region_country]."' where region_id='".$region_id."'");
//    };


      #### APPLY COUNTRY TO COUNTRY IP
//    $countrys = mysql_array ('locations_countrys','country_id','country_code');
//    foreach ($countrys as $country_id => $country_code)
//    {
//        mysql_debug_query ("update locations_countrys_ips set ip_country='".$country_id."' where ip_join='".$country_code."'");
//    };
//

//
//    #### DELETE REGIONS WITHOUT CITYS ####
//    $regions = array();
//    $result = mysql_debug_query ("select region_id,region_name from locations_regions");
//    while (list($region_id,$region_name)=mysql_fetch_row($result))
//    {
//        if (!mysql_value_query ("select city_id from locations_citys where city_region='".$region_id."' limit 1"))
//        {
//            $regions[$region_id] = $region_id;
//        }
//        //if (count($regions)>10) break;
//    };
//
//    foreach ($regions as $region_id)
//    {
//        mysql_debug_query ("delete from locations_regions where region_id='".$region_id."' limit 1");
//    }
//
//    //debug_var ($regions);
//    debug_echo (count($regions));

//    #### DELETE DUBLICATE CITYS ####
//    $citys = array();
//    $result = mysql_debug_query ("select city_name,city_country,city_region,city_id,city_latitude,city_longitude from locations_citys");
//    while (list($city_name,$city_country,$city_region,$city_id,$city_latitude,$city_longitude)=mysql_fetch_row($result))
//    {
//        $city_hash = md5 ($city_country.'|'.$city_latitude.'|'.$city_longitude);
//        if (!isset($citys[$city_hash]))
//        {
//            $citys[$city_hash]['count'] = 1;
//        }
//        else
//        {
//            $citys[$city_hash]['count']++;
//        }
//        $citys[$city_hash]['id'] = $city_id;
//        $citys[$city_hash]['country'] = $city_country;
//        $citys[$city_hash]['latitude'] = $city_latitude;
//        $citys[$city_hash]['longitude'] = $city_longitude;
//        //$citys[$city_hash]['name'] = str_replace("'","\'",$city_name);
//    };
//
//    debug_echo (count($citys).' unique in '.mysql_num_rows($result));


//    foreach ($citys as $city)
//    {
//        if ($city['count']>1)
//        {
//            mysql_debug_query ("delete from locations_citys where city_country=".$city['country']." and city_longitude=".$city['longitude']." and city_latitude=".$city['latitude']." and city_id!=".$city['id']);
//        };
//    }

//ძვირფასო სანტა, ყოველ ახალ წელს ჩვენ მეგობრებს ასეთი ტრადიცია გვაქვს - ვიცვამთ თბილად. შევდივართ მაღაზიაში. და ვყიდულობთ არაყს.


//    #### APPEND REGIONS AND COUNTRYS TO CITYS ####
//    $countrys = mysql_array ('locations_countrys','country_code','country_id');
//    $regions = array ();
//    $result = mysql_debug_query ("select region_code,region_country_code,region_id from locations_regions");
//    while (list($region_code,$region_country,$region_id)=mysql_fetch_row($result))
//    {
//      $regions[$region_country][$region_code] = $region_id;
//    }
//    $result = mysql_debug_query ("select city_id,city_country_code,city_region_code from locations_citys");
//    while (list($city_id,$city_country,$city_region)=mysql_fetch_row($result))
//    {
//        mysql_debug_query ("update locations_citys set city_country='".$countrys[$city_country]."', city_region='".$regions[$city_country][$city_region]."' where city_id='".$city_id."'");
//    };

//    $count = 1000;
//    $amount = 0;
//    for ($i=1; $i<=$count; $i++)
//    {
//        $number = hash_with (10, '123456789');
//        while (mysql_value_exists ('cards', 'card_number', "'".$number."'"))
//        {
//            $number = hash_with (10, '0123456789');
//        }
//        echo $number."<br>";
//        mysql_debug_query ("insert into cards set card_number='".$number."', card_amount='".$amount."', card_insert='".to_nulldate()."', card_type=10");
//    }

//    $count = 1000;
//    $amount = 0;
//    for ($i=1; $i<=$count; $i++)
//    {
//        $number = '121738'.int_zeros_right ($i, 4);
//        while (mysql_value_exists ('cards', 'card_number', "'".$number."'"))
//        {
//            $number = '121738'.int_zeros_right ($i, 4);
//        }
//        //echo $number."<br>";
//        mysql_debug_query ("insert into cards set card_number='".$number."', card_amount='".$amount."', card_insert='".to_nulldate()."', card_type=10");
//    }
//
//    $count = 100;
//    $amount = 20;
//    for ($i=1; $i<=$count; $i++)
//    {
//        $number = hash_with (12, '123456789');
//        while (mysql_value_exists ('cards', 'card_number', "'".$number."'"))
//        {
//            $number = hash_with (12, '0123456789');
//        }
//        mysql_debug_query ("insert into cards set card_number='".$number."', card_amount='".$amount."', card_insert='".to_nulldate()."', card_type=1");
//    }

//    $count = 100;
//    $amount = 20;
//    for ($i=1; $i<=$count; $i++)
//    {
//        $number = hash_with (12, '123456789');
//        while (mysql_value_exists ('cards', 'card_number', "'".$number."'"))
//        {
//            $number = hash_with (12, '123456789');
//        }
//        mysql_debug_query ("insert into cards set card_number='".$number."', card_amount='0', card_insert='".to_nulldate()."', card_type=2");
//    }


//    function mail_spam ($to, $subject, $message)
//    {
//        if (is_array($to))
//        {
//            $to_count = count ($to);
//            $to_current = 0;
//            foreach ($to as $value)
//            {
//                $to_current++;
//                $to_ .= $value;
//                if ($to_count!=$to_current)
//                {
//                    $to_ .= ', ';
//                }
//            }
//            $GLOBALS['spam_sent'] += $to_count;
//            $to = &$to_;
//        }
//        else
//        {
//            $GLOBALS['spam_sent'] ++;
//        }
//        $header = "MIME-Version: 1.0\n";
//        $header .= "Content-type: text/html; charset=UTF-8\n";
//        $header .= "X-Priority: 3\n";
//        $header .= "X-MSmail-Priority: Normal\n";
//        $header .= "X-mailer: php\n";
//        $header .= "From: \"lit.ge\" <info@lit.ge>\n";
//        $header .= "Bcc: $to\n";
//        return mail('info@lit.ge', '=?UTF-8?B?'.base64_encode($subject).'?=', nl2br($message), $header);
//    }
//
//    $result = mysql_debug_query ("select user_id,user_login,user_password,user_email from users where user_referral=1 and user_status=6");
//    if (mysql_num_rows($result))
//    {
//        while ($row=mysql_fetch_assoc($result))
//        {
//            //$row['user_email']
//            mail_spam ($row['user_email'], 'გიწვევთ განახლებული lit.ge - ელექტრონული წიგნების მაღაზია', "ელექტრონული წიგნების ინტერნეტ-საიტი <a href='http://lit.ge'>lit.ge</a> გიწვევთ განახლებულ პორტალზე, როგორც literatura.ge-ს ძველ მომხმარებელს.<br>
//თქვენ არ გჭირდებათ ხელახალი რეგისტრაცია.<br>
//თქვენი სამომხმარებლო სახელი და პაროლი შეგიძლიათ გამოიყენოთ განახლებულ საიტზეც.<br>
//სახელი: <b>".$row['user_login']."</b><br>
//პაროლი: ".$row['user_password']."<p>
//
//მოხარული ვიქნებით, თუ ისარგებლებთ ჩვენი ელექტრონული წიგნების ბიბლიოთეკის მომსახურებით.<br>
//პატივისცემით,<br>
//<a href='http://lit.ge'>lit.ge</a>-ს ადმინისტრაცია<br>
//<a href='http://lit.ge'><img src='http://lit.ge/skins/default/images/logo.png'></a>
//");
//            mysql_debug_query ("update users set user_email_last=user_email, user_status=1, user_password='".md5($row['user_password'].$user_password_salt)."' where user_id='".$row['user_id']."'");
//        }
//    }
//
//    html_add_message ('გაიგზავნა '.intval($spam_sent).' მეილი');


    if (isset($_FILES['insert']['error']['file']) && $_FILES['insert']['error']['file']=='0')
    {
        if (isset($_FILES['insert']['error']['file']) && $_FILES['insert']['error']['file']=='0')
        {
            //file_copy_uploaded (array('tmp_name'=>$_FILES['insert']['tmp_name']['file'],'error'=>$_FILES['insert']['error']['file']), $uploads_files.'file'.'_'.mysql_insert_id().".dat");
            $file_handler = fopen ($_FILES['insert']['tmp_name']['file'], "r");
        };
    }
    elseif ($insert['path'])
    {
        $file_handler = fopen ($insert['path'], "r");
    }

    if ($insert && $file_handler)
    {
        $insert['escape'] = '"';
        $file_position = 0;
        $table_fields = explode (',',$insert['fields']);
        while (!feof($file_handler))
        {
            $file_line = fgets ($file_handler);
            $file_position++;
            if ($insert['break'] && $file_position==$insert['break'])
            {
                break;
            };
            if ($file_position>$insert['skip'])
            {
                if (trim($file_line))
                {
                    if ($insert['escape'])
                    {
                        $file_line = trim ($file_line);
                        $file_line = str_replace (array($insert['delimit'].$insert['escape'],$insert['escape'].$insert['delimit'].$insert['escape'],$insert['escape'].$insert['delimit']),$insert['delimit'],$file_line);
                        //debug_var ($file_line);
                        if ($file_line[0]==$insert['escape'])
                        {
                            $file_line = after($insert['escape'],$file_line);
                        };
                        if ($file_line[strlen($file_line)-1]==$insert['escape'])
                        {
                            $file_line = before_last($insert['escape'],$file_line);
                        };
                        //debug_echo ($file_line);
                    };
                    $file_fields = explode ($insert['delimit'], $file_line);
                    $table_set = '';
                    foreach ($table_fields as $field_position => $field_name)
                    {
                        if ($field_name)
                        {
                            $table_set .= ", `".$field_name."`='".str_replace("'","\'",$file_fields[$field_position])."'";
                        };
                    }
                    $table_set = after (',',$table_set);
                    mysql_debug_query ("insert into `".$insert['table']."` set ".$table_set);
                    if (mysql_errno())
                    {
                        $file_errors++;
                    }
                    else
                    {
                        $file_rows++;
                    };
                };
            };
        }
        fclose ($file_handler);
        html_add_message ("Imported ".intval($file_rows)." rows, with ".intval($file_errors)." errors");
    }
    elseif ($insert)
    {
        html_add_message ("Input file empty");
    };

    if (!$insert)
    {
        $insert['delimit'] = ',';
        $insert['skip'] = '0';
        $insert['break'] = '0';
    }
    else
    {
        $insert_json = serialize ($insert);
        $insert_md5 = md5 ($insert_json);
        if (!isset($_SESSION['csv_imports'][$insert_md5]))
        {
            $_SESSION ['csv_imports'][$insert_md5]['json'] = $insert_json;
            $_SESSION ['csv_imports'][$insert_md5]['name'] = trim ($insert['table']." ".$insert['path']." ".$insert['file']);
        };
    };

    if ($insert_delete && isset($_SESSION['csv_imports'][$insert_delete]))
    {
        unset ($_SESSION['csv_imports'][$insert_delete]);
    };

    if (isset($_SESSION['csv_imports']) && count($_SESSION['csv_imports']))
    {
        table_open();
        table_open_record_header ();
        table_add_cell_header('Previuous imports');
        table_close_record (2);

        foreach ($_SESSION['csv_imports'] as $key => $value)
        {
            table_open_record ();
            table_add_cell ($value['name']);
            table_add_cell_button_ ('edit.png', $redirect_url.'?apage='.$apage.'&insert_previous='.$key);
            table_add_cell_button_ ('delete.png', $redirect_url.'?apage='.$apage.'&insert_delete='.$key);
        };

        table_close ();
    }

    if ($insert_previous)
    {
        $insert = unserialize ($_SESSION['csv_imports'][$insert_previous]['json']);
    };

    form_open ('post', '', 'multipart/form-data', 'new');
    form_add_hidden ('apage',$apage);
    form_add_edit ('insert[table]', 'Table', $insert['table']);
    form_add_edit ('insert[fields]', 'Fields', $insert['fields']);
    form_add_spacer ();
    form_add_edit ('insert[delimit]', 'Delimit', $insert['delimit']);
    //form_add_edit ('insert[escape]', 'Escape', $insert['escape']);
    form_add_edit ('insert[skip]', 'Skip', $insert['skip']);
    form_add_edit ('insert[break]', 'Break', $insert['break']);
    form_add_spacer ();
    form_add_file ('insert[file]', 'File', $insert['file']);
    form_add_edit ('insert[path]', 'Path', $insert['path']);
    form_add_submit ('Process');
    form_add_spacer ();
    form_close ();


?>
