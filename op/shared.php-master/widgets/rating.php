<?php

    if (user_logged() && intval($rating_type) && intval($rating_object) && intval($rating_value))
    {
        $rating = $database->tables->ratings->load ($rating_type);
        if ($rating)
        {
            $rating->save ($rating_object, $rating_value);
        }
    }
    elseif (!user_logged() && intval($rating_type))
    {
        echo html_parse ($skins['rating_login']);
    }

?>
