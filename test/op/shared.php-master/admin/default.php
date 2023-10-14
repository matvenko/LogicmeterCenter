<?php

    function table_setting_add ($field, $value)
    {
        table_open_record ();
        table_add_cell ($field);
        table_add_cell ($value);
        table_close_record ();
    };

    function table_setting_header ($field)
    {
        table_open_record ();
        table_add_cell ('<b>'.strtoupper($field).'</b>', 'colspan=2 align=right');
        table_close_record ();
    };

    tabs_open('stats');
    tabs_open_panel ('PHP');
    table_open ();
    table_open_record_header ();
    table_add_cell_header ('Setting');
    table_add_cell_header ('Value');
    table_close_record (0);
    table_setting_add ('version', phpversion());
    table_setting_add ('register_globals',ini_get('register_globals'));
    table_setting_add ('variables_order',ini_get('variables_order'));
    table_setting_add ('post_max_size',ini_get('post_max_size'));
    table_setting_add ('magic_quotes_gpc',ini_get('magic_quotes_gpc'));
    table_setting_add ('open_basedir',ini_get('open_basedir'));
    table_setting_add ('safe_mode',ini_get('safe_mode'));
    table_setting_add ('safe_mode_include_dir',ini_get('safe_mode_include_dir'));
    table_setting_add ('open_basedir',ini_get('open_basedir'));
    table_setting_add ('disable_functions',ini_get('disable_functions'));
    table_setting_add ('max_execution_time',ini_get('max_execution_time'));
    table_setting_add ('max_input_time',ini_get('max_input_time'));
    table_setting_add ('memory_limit',ini_get('memory_limit'));
    table_setting_add ('display_errors',ini_get('display_errors'));
    table_setting_add ('error_reporting',ini_get('error_reporting'));
    table_setting_add ('log_errors',ini_get('log_errors'));
    table_setting_add ('error_log',ini_get('error_log'));
    table_setting_add ('include_path',ini_get('include_path'));
    table_setting_add ('user_dir',ini_get('user_dir'));
    table_setting_add ('auto_prepend_file',ini_get('auto_prepend_file'));
    table_setting_add ('auto_append_file',ini_get('auto_append_file'));
    table_setting_add ('file_uploads',ini_get('file_uploads'));
    table_setting_add ('upload_tmp_dir',ini_get('upload_tmp_dir'));
    table_setting_add ('upload_max_filesize',ini_get('upload_max_filesize'));
    table_setting_add ('allow_url_fopen',ini_get('allow_url_fopen'));
    table_setting_add ('session.save_path',ini_get('session.save_path'));
    table_setting_add ('default_socket_timeout',ini_get('default_socket_timeout'));
    table_close ();
    tabs_close_panel ();
    tabs_open_panel ('MYSQL');
    table_open ();
    table_open_record_header ();
    table_add_cell_header ('Setting');
    table_add_cell_header ('Value');
    table_close_record (0);
    $result = mysql_debug_query ("show variables");
    if (mysql_num_rows($result))
    {
        while (list($field,$value)=mysql_fetch_row($result))
        {
            table_setting_add ($field,$value);
        };
    };
    table_close ();
    tabs_close_panel ();

    tabs_open_panel ('APACHE');
    table_open ();
    table_open_record_header ();
    table_add_cell_header ('Setting');
    table_add_cell_header ('Value');
    table_close_record (0);
    //table_setting_header ('Modules');
    foreach ($_SERVER as $key => $value)
    {
        table_setting_add ($key, $value);
    };
    //table_setting_header ('Modules');
    if (function_exists ('apache_get_modules'))
    {
        $apache_modules = apache_get_modules ();
        foreach ($apache_modules as $value)
        {
            $apache_modules_plain .= '; '.$value;
        };
        table_setting_add ('Modules', after(';',$apache_modules_plain));
    };
    table_close ();
    tabs_close_panel ();
    tabs_close ();

?>
