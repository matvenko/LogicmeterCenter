<?php

    if (intval($widgets_update['widget_id']))
    {
        mysql_debug_query ("update site_widgets set widget_display='".$widgets_update['widget_display']."' where widget_id='".$widgets_update['widget_id']."'");
        if (!mysql_errno())
        {
            tables_relations_update (tables_relations_id("site_pages","id","site_widgets","id"), $widgets_update['widget_id'], $widgets_update_pages);
        };
    };

    if (intval($widgets_edit))
    {
        $widgets_edit = intval ($widgets_edit);

        $result = mysql_debug_query ("select * from site_widgets where widget_id='".$widgets_edit."'");
        if (mysql_num_rows($result))
        {
            $row = mysql_fetch_assoc ($result);

            form_open ();
            form_add_hidden ('apage',$apage);
            form_add_hidden ('widgets_update[widget_id]', $row['widget_id']);
            form_add_label (name_to_caption($row['widget_name']));
            form_add_spacer ();
            form_add_select ('widgets_update[widget_display]',t('admin.widgets.display'),$row['widget_display'],$displays);
            form_add_label (t('admin.widgets.pages'),"<img id='collapse_img' src='./images/plus.gif' onClick=\"collapse('collapse_div','collapse_img','./images/plus.gif','./images/minus.gif')\">");
            $site_pages = mysql_array ('site_pages','page_id','page_name','',false);
            $widgets_pages = tables_relations_select ("site_pages","id","site_widgets","id",$row['widget_id']);
            form_add_custom('<tr><td></td><td><div style="display:none" id="collapse_div"><table>');
            foreach ($site_pages as $key => $value)
            {
                form_add_checkbox ('widgets_update_pages['.$key.']',name_to_caption($value),$widgets_pages[$key]);
            };
            form_add_custom('</table></div></td></tr>');
            form_add_submit (t('save'),t('cancel'));
            form_close ();

            $ewidget = $widgets_edit;
            $unit_id = $row['widget_unit'];
            $unit_type = 3;
            $unit_name = mysql_value ('site_units','unit_name','unit_id',$row['widget_unit']);

            html_add ('<p>&nbsp;<p>');

            if (file_exists($units_dir.'widgets/'.$unit_name.$units_ext))
            {
                include $units_dir.'widgets/'.$unit_name.$units_ext;
            }
            elseif (file_exists($shared_dir.'widgets/'.$unit_name.$shared_ext))
            {
                include $shared_dir.'widgets/'.$unit_name.$shared_ext;
            };

        };

    }
    else
    {

        $result = mysql_debug_query ("select * from site_widgets where widget_unit='".intval(mysql_value_query("select unit_id from site_units where unit_name='menu' and unit_type=2"))."' order by widget_id asc");
        if (mysql_num_rows($result))
        {
            table_open ();
            table_open_record_header ();
            table_add_cell_header ('#');
            table_add_cell_header (t('admin.widgets.name'));
            table_close_record (1);
            while ($row=mysql_fetch_assoc($result))
            {
                table_open_record ();
                table_add_cell ($row['widget_id']);
                table_add_cell (name_to_caption($row['widget_name']));
                table_add_cell_button_ ('edit.png', $redirect_url."?apage=$apage&widgets_edit=".$row['widget_id']);
                table_close_record ();
            };
            table_close ();
        };
    };

?>