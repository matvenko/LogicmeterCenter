<?php

    $units_dirs = array ('modules'=>t('admin.units.module'), 'widgets'=>t('admin.units.widgets'), 'units'=>t('admin.units.custom'));
    $units_types = array ('modules'=>1, 'widgets'=>2, 'units'=>3);
    $units_types_ = array (1=>t('admin.units.module'), 2=>t('admin.units.widget'), 3=>t('admin.units.custom'));

    if (intval($delete))
    {
        mysql_debug_query ("delete from site_units where unit_id='".intval($delete)."'");
    };

    if (before('/',$insert['unit_name']) && after('/',$insert['unit_name']))
    {
        mysql_debug_query ("insert site_units set unit_name='".after('/',$insert['unit_name'])."', unit_type='".$units_types[before('/',$insert['unit_name'])]."'");
    };

    function after_file_found ($path, $basename)
    {
        global $units_files, $units_dirs, $units_types, $shared_dir, $units_dir;
        if (strtolower(between_last('/','/',$path))==strtolower(between_last('/','/',before_last('/',$shared_dir))))
        {
            $path = str_replace (between_last('/','/',$path), between_last('/','/',before_last('/',$units_dir)), $path);
        };
        if ($units_dirs[between_last('/','/',$path)])
        {
            if ($units_types[between_last('/','/',$path)])
            {
                if (!mysql_value('site_units','unit_id','unit_type',$units_types[between_last('/','/',$path)]." and unit_name='".before_last('.',$basename)."'"))
                {
                    $units_files [between_last('/','/',$path)][between_last('/','/',$path).'/'.before_last('.',$basename)] = strtoupper(before_last('.',$basename));
                };
            };
        };
        //debug_echo ($path.$basename);;
        return $basename;
    };

    function after_dir_found ($path, $basename)
    {
        //debug_echo ($path.$basename);
        return $basename;
    };

    //explore_dir (before_last ('/', before_last('/',$units_dir)).'/modules', after_last('.',$units_ext), false);
    explore_dir (str_replace('admin/units/','site/units/',$units_dir).'modules', after_last('.',$units_ext), false);
    explore_dir (before_last ('/', before_last('/',$shared_dir)).'/modules', after_last('.',$shared_ext), false);

    //explore_dir (before_last ('/', before_last('/',$units_dir)).'/widgets', after_last('.',$units_ext), false);
    explore_dir (str_replace('admin/units/','site/units/',$units_dir).'widgets', after_last('.',$units_ext), false);
    explore_dir (before_last ('/', before_last('/',$shared_dir)).'/widgets', after_last('.',$shared_ext), false);

    //explore_dir (before_last ('/', before_last('/',$units_dir)).'/', after_last('.',$units_ext), false);
    explore_dir (str_replace('admin/units/','site/units/',$units_dir), after_last('.',$units_ext), false);
    explore_dir (before_last ('/', before_last('/',$shared_dir)).'/', after_last('.',$shared_ext), false);

    //debug_echo (before_last ('/', before_last('/',$shared_dir)).'/', after_last('.',$shared_ext));
    //debug_var ($units_files);
    //debug_var ($units_dirs);

    if (tables_edit_case())
    {
        tables_edit_form ();

        if (file_exists($units_dir.'widgets/'.mysql_value('site_units','unit_name','unit_id',$row['widget_unit']).$units_ext))
        {
            include $units_dir.'widgets/'.mysql_value('site_units','unit_name','unit_id',$row['widget_unit']).$units_ext;
        }
        elseif (file_exists($shared_dir.'widgets/'.mysql_value('site_units','unit_name','unit_id',$row['widget_unit']).$shared_ext))
        {
            include $shared_dir.'widgets/'.mysql_value('site_units','unit_name','unit_id',$row['widget_unit']).$shared_ext;
        };
    }
    else
    {
        if (is_array($units_files))
        {
            form_open ();
            form_add_hidden ('apage',$apage);
            form_add_select_grouped ('insert[unit_name]', t('admin.units.unit'), '', $units_files, '', '', '', '', $units_dirs);
            form_add_submit (t('admin.units.add'));
            form_close ();
        };

        $result = mysql_debug_query ("select * from site_units order by unit_id asc");
        if (mysql_num_rows($result))
        {
            table_open ();
            table_open_record_header ();
            table_add_cell_header ('#');
            table_add_cell_header (t('admin.units.unit'));
            table_add_cell_header (t('admin.units.type'));
            table_close_record (1);

            while ($row=mysql_fetch_assoc($result))
            {
                table_open_record ();
                table_add_cell ($row['unit_id']);
                table_add_cell ($row['unit_name']);
                table_add_cell ($units_types_[$row['unit_type']]);
                table_add_cell_delete_ ('delete.png',$redirect_url."?apage=$apage&delete=".$row['unit_id']);
                //table_add_cell_button_ ('edit.png',$redirect_url."?apage=$apage&edit=".$row['unit_id']);
                table_close_record ();
            };

            table_close ();
        };
    };


?>