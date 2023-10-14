<?php

    ob_start ();
    phpinfo ();
    $html['page'] = str_replace (array('class="e"','class="h"'),array('class="td"','class="tr"'),between('<body>','</body>',ob_get_contents()));
    ob_end_clean ();


?>