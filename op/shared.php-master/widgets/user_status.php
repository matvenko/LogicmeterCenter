<?php

   //var clock_date = new Date ({

   $values['clock_start'] = date_ ('F j g:i:s A');
   $values['clock_start'] = str_replace (date_('F'), t(strtolower(date_('F'))), $values['clock_start']);
   $values['clock_year'] = date_ ('Y');
   $values['clock_month'] = date_ ('m');
   $values['clock_day'] = date_ ('j');
   $values['clock_hour'] = date_ ('H');
   $values['clock_minutes'] = date_ ('i');
   $values['clock_seconds'] = date_ ('s');
   //$values['clock_offset'] = intval($timezones[$user['timezone']]['offset']);
   $values['clock_offset'] = '0';

    $values += array ( 'redirect_url'=>$redirect_url,
                      'apage'=>$apage,
                      'page_forgot'=>$project_url.seo_encode(array('apage'=>$pages_settings['page_forgot'])),
                      'page_register'=>$project_url.seo_encode(array('apage'=>$pages_settings['page_register'])),
                      'user_displayname'=>$user->name,
                      'user_code'=>int_zeros_right(user_id(),5),
                      //'user_currency'=>$currencys[$user['currency']]['caption'],
                      'user_balance'=>round($user_balance['balance'],2),
                      'user_bonus'=>round($user_balance['points'],2)
                    );

    if (!user_logged())
    {
        $html[$widget['name']] .= html_parse ($skins[$widget['name']."_out"], $values);
    }
    else
    {
        $html[$widget['name']] .= html_parse ($skins[$widget['name']."_in"], $values);
    };
?>