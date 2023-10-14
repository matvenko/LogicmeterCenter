<?php

    if (!user_logged())
    {
        $html[$widget['name'].'_message'] = '';
        switch (user_exit())
        {
            case user_error_inactive:
                $html[$widget['name'].'_message'] = html_parse ($skins[$widget['name'].'_inactive']);
            break;
            case user_error_disabled:
                $html[$widget['name'].'_message'] = html_parse ($skins[$widget['name'].'_disabled']);
            break;
            case user_error_failed:
                $html[$widget['name'].'_message'] = html_parse ($skins[$widget['name'].'_failed']);
            break;
            case user_error_required:
                $html[$widget['name'].'_message'] = html_parse ($skins[$widget['name'].'_required']);
            break;
            case user_error_notfound:
                $html[$widget['name'].'_message'] = html_parse ($skins[$widget['name'].'_notfound']);
            break;
        }
        $html[$widget['name']] .= html_parse ($skins[$widget['name']], $values + array('user_login_message'=>$html[$widget['name'].'_message']));
    };

?>