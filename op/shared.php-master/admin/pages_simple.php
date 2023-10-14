<?php

    tables_load_table ('modules_texts');

    tables_delete_case ();

    tables_delete_case_multi ();

    if (is_array($insert) && $insert)
    {
        $insert['text_page'] = intval(mysql_value('site_pages','page_name','page_name',"'".caption_to_name($insert['text_page_'])."'"));
        if ($insert['text_page_'] && !$insert['text_page'])
        {
            if ($languages && field_localized('page_caption','site_pages'))
            {
                $lang_query = '';
                foreach ($languages as $lang_name => $lang_data)
                {
                    $lang_query .= "page_caption_".$lang_name."='".$insert['text_page_']."',";
                };
            }
            else
            {
                $lang_query = "team_name='".$insert['text_page_']."',";
            };
            mysql_debug_query ("insert into site_pages set ".$lang_query." page_name='".caption_to_name($insert['text_page_'])."'");
            if (!mysql_errno())
            {
                $insert['text_page'] = mysql_insert_id();
                $pages_page_inserted = $insert['text_page'];
                if (!mysql_errno())
                {
                    $site_widgets = mysql_array ("site_widgets","widget_id","widget_name","",false);
                    foreach ($site_widgets as $key => $value)
                    {
                        tables_relations_insert (tables_relations_id("site_pages","id","site_widgets","id"), $key, $insert['text_page']);
                    };
                };
            };
        };
        if (tables_insert_case())
        {
            $pages_module_text = mysql_value_query ("select unit_id from site_units where unit_name='text' and unit_type=1");
            mysql_debug_query ("insert into site_pages_parts set part_unit='".$pages_module_text."', part_page='".$pages_page_inserted."'");
        };
    };

    tables_update_case ();

    tables_update_case_multi ();

    tables_position_case ();

    if (tables_edit_case())
    {
        tabs_open ();

        tables_edit_form ();

        tables_relations_byfield_slave ();

        tables_relations_byrow_master ();

        tables_relations_byrow_slave ();

        tabs_close ();
    }
    else
    {

        tables_order_prepare ();

        tables_search_prepare ();

        tables_select_query ();

        tabs_open ();
        tables_search_form ();
        tables_add_form ();
        tabs_close ();

        tables_result_table ();

    };

?>