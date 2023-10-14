<?php

    if ($tables_locales['modules_texts']['text'] && $lang_postfix)
    {
        list ($text_text,$text_desc) = mysql_fetch_row (mysql_debug_query ("select text_text".$lang_postfix.",text_desc".$lang_postfix." from modules_texts where text_page='".$apage."'"));
    }
    else
    {
        list ($text_text,$text_desc) = mysql_fetch_row (mysql_debug_query ("select text_text,text_desc from modules_texts where text_page='".$apage."'"));
    };
    
    $text_desc = html_replace ($text_desc, $html_globals);
    $text_text = html_replace ($text_text, $html_globals);
    
    if (isset($skins['body_text']))
    {
        $skins['body'] = str_replace('{page}', $skins['body_text'], $skins['body']);
    }
    $html['page'] .= $text_text;
    $html['page_text'] = $text_text;
    $html['page_desc'] = $text_desc;

?>