<?php

    ### LOAD TABLES LOCALES IN ADMIN.PHP AND INDEX.PHP

    $table_name = 'widgets_menus';

    $menus_captions = array ();
    $menus_booleans = array ('0'=>t('no'),'1'=>t('yes'));
    $menus_pages = mysql_array ('site_pages','page_id','page_name');

    if ($menus_up) mysql_move_up ('widgets_menus','menu_id','menu_order',$menus_up,'menu_widget');
    if ($menus_down) mysql_move_down ('widgets_menus','menu_id','menu_order',$menus_down,'menu_widget');
    if ($menus_delete) mysql_debug_query ("delete from widgets_menus where menu_id='".intval($menus_delete)."' and menu_widget='".$widgets_edit."' limit 1");
    if ($menus_insert['menu_name']) mysql_debug_query ("insert into widgets_menus set ".field_set_localized('menu_caption',$menus_insert).", menu_name='$menus_insert[menu_name]', menu_url='$menus_insert[menu_url]', menu_target='$menus_insert[menu_target]', menu_onclick='$menus_insert[menu_onclick]', menu_page='$menus_insert[menu_page]', menu_icon='$menus_insert[menu_icon]', menu_spacer='$menus_insert[menu_spacer]', menu_expanded='$menus_insert[menu_expanded]', menu_parent='$menus_insert[menu_parent]', menu_hidden='$menus_insert[menu_hidden]', menu_display='$menus_insert[menu_display]', menu_order='".intval(mysql_value('widgets_menus','max(menu_order)+1'))."', menu_widget='".$widgets_edit."'");
    if ($menus_update['menu_id'] && $menus_update['menu_name']) mysql_debug_query ("update widgets_menus set ".field_set_localized('menu_caption',$menus_update).", menu_name='$menus_update[menu_name]', menu_url='$menus_update[menu_url]', menu_target='$menus_update[menu_target]', menu_onclick='$menus_update[menu_onclick]', menu_page='$menus_update[menu_page]', menu_icon='$menus_update[menu_icon]', menu_spacer='$menus_update[menu_spacer]', menu_expanded='$menus_update[menu_expanded]', menu_parent='$menus_update[menu_parent]', menu_hidden='$menus_update[menu_hidden]', menu_display='$menus_update[menu_display]' where menu_id='".intval($menus_update[menu_id])."' and menu_widget='".$widgets_edit."' limit 1");

    if ($menu_edit)
    {
        $menu_edit = intval ($menu_edit);
        $result = mysql_debug_query ("select * from widgets_menus where menu_id='$menu_edit' and menu_widget='".$widgets_edit."'");
        $row = mysql_fetch_assoc ($result);

        form_open_widget ('menu_edit');
        form_add_hidden ('menus_update[menu_id]', $menu_edit);
        form_add_edit ('menus_update[menu_name]', t('name'), $row[menu_name]);
        form_add_edit_localized ('menus_update[menu_caption]', t('caption'), $row);
        form_add_select ('menus_update[menu_page]', t('admin.menus.page'), $row[menu_page], 'site_pages', 'page_id', 'page_name', 'order by page_name');
        form_add_select ('menus_update[menu_parent]', t('admin.menus.parent'), $row[menu_parent], 'widgets_menus', 'menu_id', 'menu_name', "where menu_widget='".$widgets_edit."'");
        form_add_edit ('menus_update[menu_icon]', t('admin.menus.icon'), $row[menu_icon]);
        form_add_select ('menus_update[menu_display]', t('admin.menus.display'), $row[menu_display], $displays);
        form_add_spacer ();
        form_add_select ('menus_update[menu_hidden]', t('admin.menus.hidden'), $row[menu_hidden], $menus_booleans);
        form_add_spacer ();
        form_add_edit ('menus_update[menu_url]', t('admin.menus.link'), $row[menu_url]);
        form_add_edit ('menus_update[menu_target]', t('admin.menus.target'), $row[menu_target]);
        form_add_edit ('menus_update[menu_onclick]', t('admin.menus.javascript'), $row[menu_onclick]);
        form_add_spacer ();
        form_add_select ('menus_update[menu_spacer]', t('admin.menus.spacer'), $row[menu_spacer], $menus_booleans);
        form_add_select ('menus_update[menu_expanded]', t('admin.menus.expanded'), $row[menu_expanded], $menus_booleans);
        form_add_submit_widget (t('save'), t('cancel'));
        form_close ();

    }
    else
    {

        form_open_widget ('menus_insert');
        form_add_edit ('menus_insert[menu_name]', t('name'), '', '');
        form_add_edit_localized ('menus_insert[menu_caption]', t('caption'), '');
        form_add_select ('menus_insert[menu_page]', t('admin.menus.page'), '0', 'site_pages', 'page_id', 'page_name', 'order by page_name');
        form_add_select ('menus_insert[menu_parent]', t('admin.menus.parent'), '0', 'widgets_menus', 'menu_id', 'menu_name', "where menu_widget='".$widgets_edit."'");
        form_add_edit ('menus_insert[menu_icon]', t('admin.menus.icon'), '', '');
        form_add_select ('menus_insert[menu_display]', t('admin.menus.display'), '0', $displays);
        form_add_spacer ();
        form_add_select ('menus_insert[menu_hidden]', t('admin.menus.hidden'), '0', $menus_booleans);
        form_add_spacer ();
        form_add_edit ('menus_insert[menu_url]', t('admin.menus.link'), '', '');
        form_add_edit ('menus_insert[menu_target]', t('admin.menus.target'), '', '');
        form_add_edit ('menus_insert[menu_click]', t('admin.menus.javascript'), '', '');
        form_add_spacer ();
        form_add_select ('menus_insert[menu_spacer]', t('admin.menus.spacer'), '0', $menus_booleans);
        form_add_select ('menus_insert[menu_expanded]', t('admin.menus.expanded'), '0', $menus_booleans);
        form_add_submit (t('add'));
        form_close ();

        $menus_captions = mysql_array ('widgets_menus', 'menu_id', "menu_name");

        $menus_table = false;


        function mysql_while_function ($row, $level)
        {
            global $displays, $menus_pages, $menus_captions, $widgets_edit, $menus_table, $apage, $widgets_edit, $l;

            if (!$menus_table)
            {
                table_open ();
                table_open_record_header ();
                table_add_cell_header ('#');
                //table_add_cell_header ('<b>სორტირება</b>');
                table_add_cell_header (t('name'));
                table_add_cell_header (t('caption'));
                table_add_cell_header (t('admin.menus.page'));
                table_add_cell_header (t('admin.menus.parent'));
                table_add_cell_header (t('admin.menus.display'));
                table_close_record (4);
                $menus_table = true;
            };

            table_open_record ();
            table_add_cell ($row[menu_id]);
            //table_add_cell ($row[menu_order]);
            table_add_cell ($row[menu_name]);
            table_add_cell ($row['menu_caption'.$l]);
            //table_add_cell ($level);
            //table_add_cell_button ($handlers_captions[$row[menu_page]], $redirect_url."?apage=".$handlers_names[$row[menu_page]]."&menu_widget=".$row[menu_id], 'align=middle');
            table_add_cell ($menus_pages[$row[menu_page]]);
            table_add_cell ($menus_captions[$row[menu_parent]]);
            table_add_cell ($displays[$row[menu_display]]);
            table_add_cell_delete_widget ("menus_delete=".$row[menu_id]);
            table_add_cell_button_widget ("menu_edit=".$row[menu_id]);
            table_add_cell_button_widget ("menus_up=".$row[menu_id], 'up.png');
            table_add_cell_button_widget ("menus_down=".$row[menu_id], 'down.png');
            table_close_record ();
        };

        mysql_recursive_query ("select * from widgets_menus where menu_parent='0' and menu_widget='".$widgets_edit."' order by menu_order asc", 'menu_id', 'menu_parent', 'mysql_while_function');

        if ($menus_table)
        {
            table_close();
        };

    };

?>