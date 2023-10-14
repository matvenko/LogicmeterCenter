<?php

    if ($delete)
    {
        mysql_debug_query ("kill ".intval($delete));
    };

    $result_fields ['Id'] = t('admin.proccesslist.id');
    $result_fields ['User'] = t('admin.proccesslist.user');
    $result_fields ['Host'] = t('admin.proccesslist.host');
    $result_fields ['db'] = t('admin.proccesslist.db');
    $result_fields ['Command'] = t('admin.proccesslist.command');
    $result_fields ['Time'] = t('admin.proccesslist.time');
    $result_fields ['State'] = t('admin.proccesslist.state');
    $result_fields ['Info'] = t('admin.proccesslist.query');

    $result = mysql_debug_result ("show full processlist", 'Id', $result_fields);

?>