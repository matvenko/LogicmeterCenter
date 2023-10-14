<?php

    function import_report ($message)
    {
        global $report;
        $report .= $message."<br>\n";
    };

    function import_relation ($master_table, $master_field_id, $slave_table, $slave_field_id)
    {
        global $tables;
        import_report ($master_table.".".$master_field_id." @ ".$slave_table.".<font color=green>".$slave_field_id."</font>");
        $master_field_id = after(tables_table_prefix($master_table),$master_field_id);
        $slave_field_id = after(tables_table_prefix($slave_table),$slave_field_id);
        import_report ($master_table.".".$master_field_id." @ ".$slave_table.".<font color=red>".$slave_field_id."</font>");
        import_report ("---------------------");
        if ($tables[$master_table]['fields']['caption'])
        {
            $master_field_caption = 'caption';
        }
        elseif ($tables[$master_table]['fields']['name'])
        {
            $master_field_caption = 'name';
        }
        elseif ($tables[$master_table]['fields']['login'])
        {
            $master_field_caption = 'login';
        }
        else
        {
            $master_field_caption = 'id';
        };
        if ($tables[$slave_table]['fields']['caption'])
        {
            $slave_field_caption = 'caption';
        }
        elseif ($tables[$slave_table]['fields']['name'])
        {
            $slave_field_caption = 'name';
        }
        elseif ($tables[$slave_table]['fields']['login'])
        {
            $slave_field_caption = 'login';
        }        
        else
        {
            $slave_field_caption = 'id';
        };
        if  (!mysql_num_rows(mysql_debug_query("select relation_id from tables_relations where relation_master_table='$master_table' and relation_master_field_id='$master_field_id' and relation_slave_table='$slave_table' and relation_slave_field_id='$slave_field_id' and relation_type='1' limit 1")))
        {
            mysql_debug_query ("insert into tables_relations set relation_master_table='$master_table', relation_master_field_id='$master_field_id', relation_master_field_caption='$master_field_caption', relation_slave_table='$slave_table',relation_slave_field_id='$slave_field_id',relation_slave_field_caption='$slave_field_caption',relation_type='1'");
        }
        else
        {
            $relation_result = mysql_debug_query("select relation_id from tables_relations where relation_master_table='$master_table' and relation_master_field_id='$master_field_id' and relation_slave_table='$slave_table' and relation_slave_field_id='$slave_field_id' and relation_type='1' limit 1");
            list ($relation_id) = mysql_fetch_row ($relation_result);
            mysql_debug_query ("update tables_relations set relation_master_table='$master_table', relation_master_field_id='$master_field_id', relation_master_field_caption='$master_field_caption', relation_slave_table='$slave_table',relation_slave_field_id='$slave_field_id',relation_slave_field_caption='$slave_field_caption',relation_type='1' where relation_id='".$relation_id."'");
        };
    };

    if (isset($_FILES['insert']['error']['import_file']) && $_FILES['insert']['error']['import_file']=='0')
    {
        $file_handler = fopen ($_FILES['insert']['tmp_name']['import_file'], "r");
        while (!feof($file_handler))
        {
            $file_line = str_replace('  ',' ',strtolower(trim(fgets($file_handler))));
            if ($file_line)
            {
                if ($b=between("use `","`",$file_line)) $d['database'] = $b;
                if ($d['database'] && $b=between("create table if not exists `".$d['database']."`.`","`",$file_line)) $d['master_table'] = $b;
                elseif (!$d['database'] && $b=between("create table if not exists `","`",$file_line)) $d['master_table'] = $b;
                if ($b=between("engine =",";",$file_line))
                {
                    $d['master_table'] = '';
                    $d['master_field_id'] = '';
                    $d['slave_table'] = '';
                    $d['slave_field_id'] = '';
                };
                if ($d['master_table'])
                {
                    if ($b=between("foreign key (`","`",$file_line)) $d['master_field_id'] = $b;
                    if ($d['master_field_id'])
                    {
                        if ($d['database'] && $b=between("references `".$d['database']."`.`","`",$file_line)) $d['slave_table'] = $b;
                        elseif (!$d['database'] && $b=between("references `","`",$file_line)) $d['slave_table'] = $b;
                        if ($d['slave_table'])
                        {
                            if ($b=between_last("(`","`",$file_line))
                            {
                                $d['slave_field_id'] = $b;
                                import_relation ($d['master_table'],$d['master_field_id'],$d['slave_table'],$d['slave_field_id']);
                                $d['master_field_id'] = '';
                                $d['slave_table'] = '';
                                $d['slave_field_id'] = '';
                            };
                        };
                    };
                };
            };
            if ($c!=$d)
            {
                //debug_var ($d);
            };
            $c = $d;
            $file_rows++;

        }
        fclose ($file_handler);
        form_open ();
        form_add_label ("<img src='./images/warn.gif'>", $_FILES['insert']['name']['import_file']);
        form_add_label ("&nbsp;", intval($file_rows)." ".t('admin.relation_import.records_analized'));
        form_add_label ("&nbsp;", intval($file_errors)." ".t('admin.relation_import.errors_fixed'));
        form_add_label ("&nbsp;", $report);
        form_add_hidden ('apage', 'cache');
        form_add_spacer ();
        form_add_submit (t('cache'));
        form_close ();
    }
    else
    {
        form_open ('post', '', 'multipart/form-data', 'form_import');
        form_add_hidden ('apage',$apage);
        form_add_file ('insert[import_file]', t('file'));
        form_add_submit (t('import'));
        form_close (2);
    };

?>