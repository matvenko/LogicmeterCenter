<?php

    mysql_query ("delete from tables_cache");

    tables_save_cache ();

    html_add_message (t('libs.tables.cache_successfuly_saved'));

?>