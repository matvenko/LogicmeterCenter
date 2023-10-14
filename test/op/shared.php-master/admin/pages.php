<?php


    #### TODO ####
    # 4. REPLACE INCOMING EDIT FIELD IN MENU_SIMPLE WITH $EWIDGET
    # 5. MAKE INDEX.PHP

    if (!$pages_settings['default_master'])
    {
        $pages_settings['default_master'] = 'main';
    };

    if ($pages_insert[page_name])
    {
        mysql_debug_query ("insert into site_pages set page_name='".$pages_insert['page_name']."', ".field_set_localized('page_caption',$pages_insert,'site_pages').", ".field_set_localized('page_description',$pages_insert,'site_pages').", page_tags='".$pages_insert['page_tags']."', page_display='".$pages_insert['page_display']."', page_width='".$pages_insert['page_width']."', page_master='".$pages_insert['page_master']."', page_link='".$pages_insert['page_link']."'");
        if (!mysql_errno())
        {
            $page_inserted = mysql_insert_id();
            $site_widgets = mysql_array ("site_widgets","widget_id","widget_name","",false);
            foreach ($site_widgets as $key => $value)
            {
                tables_relations_insert (tables_relations_id("site_pages","id","site_widgets","id"), $key, $page_inserted);
            };
        };

    };

    if (intval($pages_delete))
    {
        mysql_debug_query ("delete from site_pages where page_id='".intval($pages_delete)."'");
        mysql_debug_query ("delete from site_pages_parts where part_page='".intval($pages_delete)."'");
        tables_relations_delete (tables_relations_id("site_pages","id","site_widgets","id"), false, $pages_delete);
    };

    if (intval($pages_update['page_id']))
    {
        mysql_debug_query ("update site_pages set page_name='".$pages_update['page_name']."', ".field_set_localized('page_caption',$pages_update,'site_pages').",  ".field_set_localized('page_description',$pages_update,'site_pages').", page_tags='".$pages_update['page_tags']."', page_display='".$pages_update['page_display']."', page_width='".$pages_update['page_width']."', page_link='".$pages_update['page_link']."', page_master='".$pages_update['page_master']."' where page_id='".$pages_update['page_id']."'");
        $site_widgets = mysql_array ('site_widgets','widget_id','widget_name','order by widget_order',false);
        $pages_widgets_relation = tables_relations_id ("site_pages","id","site_widgets","id");
        //debug_var ($pages_update_widgets);
        foreach ($site_widgets as $key => $value)
        {
            tables_relations_delete ($pages_widgets_relation, $key, $pages_update['page_id']);
            if ($pages_update_widgets[$key])
            {
                tables_relations_insert ($pages_widgets_relation, $key, $pages_update['page_id']);
            };
        };
    };

    if (intval($pages_edit))
    {
        $pages_edit = intval ($pages_edit);

        $result = mysql_debug_query ("select * from site_pages where page_id='".$pages_edit."'");
        if (mysql_num_rows($result))
        {
            $row = mysql_fetch_assoc ($result);

            if (intval($pages_parts_insert))
            {
                mysql_debug_query ("insert into site_pages_parts set part_unit=".intval($pages_parts_insert).", part_page='".$row['page_id']."'");
            };

            if (intval($pages_parts_delete))
            {
                mysql_debug_query ("delete from site_pages_parts where part_id=".intval($pages_parts_delete)."");
            };

            form_open ();
            form_add_hidden ('apage',$apage);
            form_add_hidden ('pages_update[page_id]', $row['page_id']);
            form_add_edit ('pages_update[page_name]', t('name'), $row['page_name']);
            form_add_edit_localized ('pages_update[page_caption]', t('caption'), $row, 'site_pages');
            form_add_edit ('pages_update[page_master]', t('admin.pages.master'), $row['page_master']);
            form_add_edit ('pages_update[page_width]', t('admin.pages.width'), $row['page_width']);
            form_add_edit ('pages_update[page_link]', t('admin.pages.link'), $row['page_link']);
            form_add_edit ('pages_update[page_tags]', t('admin.pages.tags'), $row['page_tags']);
            form_add_edit_localized ('pages_update[page_description]', t('admin.pages.description'), $row, 'site_pages');
            form_add_select ('pages_update[page_display]', t('admin.pages.display'), $row['page_display'], $displays);

            form_add_label (t('admin.pages.widgets'),"<img id='collapse_img' src='./images/plus.gif' onClick=\"collapse('collapse_div','collapse_img','./images/plus.gif','./images/minus.gif')\">");
            $site_widgets = mysql_array ('site_widgets','widget_id','widget_name','order by widget_order',false);
            $pages_widgets_relation = tables_relations_id ("site_pages","id","site_widgets","id");
            form_add_custom('<tr><td></td><td><div style="display:none" id="collapse_div"><table>');
            foreach ($site_widgets as $key => $value)
            {
                form_add_checkbox ('pages_update_widgets['.$key.']',name_to_caption($value),$relations[$pages_widgets_relation][$key][$pages_edit]);
            };
            form_add_custom('</table></div></td></tr>');

            form_add_submit (t('save'),t('cancel'));
            form_close ();

            $units = mysql_array ('site_units','unit_id','unit_name','where unit_type!=2', false);

            foreach ($units as $key => $value)
            {
                if (mysql_value('site_pages_parts','part_unit','part_unit',$key." and part_page=$pages_edit"))
                {
                    unset ($units[$key]);
                };
            };

            if ($units)
            {
                form_open ();
                form_add_hidden ('apage', $apage);
                form_add_hidden ('pages_edit', $row['page_id']);
                form_add_select ('pages_parts_insert',t('unit'),'0',$units);
                form_add_submit (t('add'));
                form_close ();
            };

            $units = mysql_array ('site_units','unit_id','unit_name','where unit_type!=2', false);

            $result_parts = mysql_debug_query ("select * from site_pages_parts where part_page='".$row['page_id']."'");
            if (mysql_num_rows($result_parts))
            {
                table_open ();
                table_open_record_header ();
                table_add_cell_header ('#');
                table_add_cell_header (t('unit'));
                table_close_record ();

                while ($row_part=mysql_fetch_assoc($result_parts))
                {
                    table_open_record ();
                    table_add_cell ($row_part['part_unit']);
                    table_add_cell ($units[$row_part['part_unit']]);
                    table_add_cell_delete_ ('delete.png', $redirect_url."?apage=$apage&pages_edit=$pages_edit&pages_parts_delete=".$row_part['part_id']);
                    table_add_cell_button_ ('edit.png', $redirect_url."?apage=$apage&pages_edit=$pages_edit&part=".$row_part['part_id']);
                    table_close_record ();
                };

                table_close ();
            };

            if (intval($part))
            {
                $epage = $pages_edit; ## ??
                $unit_id = mysql_value ('site_pages_parts','part_unit','part_id',$part);
                $unit_type = mysql_value ('site_units','unit_type','unit_id',$unit_id);
                $unit_name = mysql_value ('site_units','unit_name','unit_id',$unit_id);

                html_add ('<p>&nbsp;<p>');

                if ($unit_type==3)
                {
                    if (file_exists($units_dir.'custom/'.$unit_name.$units_ext))
                    {
                        include $units_dir.'custom/'.$unit_name.$units_ext;
                    }
                    elseif (file_exists($shared_dir.'custom/'.$unit_name.$units_ext))
                    {
                        include $shared_dir.'custom/'.$unit_name.$units_ext;
                    };
                }
                elseif ($unit_type==1)
                {
                    if (file_exists($units_dir.'modules/'.$unit_name.$units_ext))
                    {
                         include $units_dir.'modules/'.$unit_name.$units_ext;
                    }
                    elseif (file_exists($shared_dir.'modules/'.$unit_name.$units_ext))
                    {
                        include $shared_dir.'modules/'.$unit_name.$units_ext;
                    };
                };
            };
        };

    }
    else
    {

        form_open ();
        form_add_edit ('pages_insert[page_name]',t('name'));
        form_add_edit_localized ('pages_insert[page_caption]',t('caption'), array(), 'site_pages');
        form_add_edit ('pages_insert[page_master]', t('admin.pages.master'), '0');
        form_add_edit ('pages_insert[page_width]', t('admin.pages.width'), '0');
        form_add_edit ('pages_insert[page_link]', t('admin.pages.link'), '');
        form_add_select ('pages_insert[page_display]', t('admin.pages.display'), 0, $displays);
        form_add_edit ('pages_insert[page_tags]', t('admin.pages.tags'), '');
        form_add_edit_localized ('pages_insert[page_description]',t('admin.pages.description'), array(), 'site_pages');
        form_add_submit (t('add'));
        form_close ();

        $result = mysql_debug_query ("select * from site_pages order by page_name asc");
        if (mysql_num_rows($result))
        {
            table_open ();
            table_open_record_header ();
            table_add_cell_header ('#');
            table_add_cell_header (t('admin.pages.page'));
            table_add_cell_header (t('admin.pages.caption'));
            table_add_cell_header (t('admin.pages.width'));
            table_add_cell_header (t('admin.pages.master'));
            table_add_cell_header (t('admin.pages.display'));
            table_close_record ();
            while ($row=mysql_fetch_assoc($result))
            {
                table_open_record ();
                table_add_cell ($row['page_id']);
                table_add_cell ($row['page_name']);
                table_add_cell ($row[field_name_localized('page_caption','site_pages')]);
                if ($row['page_width'])
                {
                    table_add_cell ($row['page_width']);
                }
                else
                {
                    table_add_cell ($pages_settings['default_width']);
                };
                if ($row['page_master'])
                {
                    table_add_cell ($row['page_master']);
                }
                else
                {
                    table_add_cell ($pages_settings['default_master']);
                };
                table_add_cell ($displays[$row['page_display']]);
                table_add_cell_delete_ ('delete.png', $redirect_url."?apage=$apage&pages_delete=".$row['page_id']);
                table_add_cell_button_ ('edit.png', $redirect_url."?apage=$apage&pages_edit=".$row['page_id']);
                table_close_record ();
            };
            table_close ();
        };
    };

?>