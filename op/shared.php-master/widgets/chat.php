<?php

    $database->debug = false;
    if ($database->tables->messages->session())
    {
        if (!defined('engine_parts'))
        {
            $skins[$widget_name] = "
                <script type='text/javascript'>
                chat = new chat ('chat', '".$project_url."parts.php?apage=".$apage."&awidget=".$widget_id."&');
                {items}
                </script>
            ";
            $threads = $database->tables->messages->threads ($database->tables->messages->session());
            if (is_array($threads) && $threads)
            {
                foreach ($threads as $thread)
                {
                    $values['items'] .= "chat.thread(".json_encode($thread).");";
                }
            }
            $html[$widget_name] = html_parse ($skins[$widget_name], $values);
        }
        else
        {
            if ($action=='messages')
            {
                if ($thread)
                {
                    echo json_encode($database->tables->messages->thread ($database->tables->messages->session(), $thread, $last));
                    exit;
                }
            }
            else if ($action=='send')
            {
                if ($thread && $to && $message)
                {
                    $database->tables->messages->send ($database->tables->messages->session(), $to, $message);
                    echo json_encode($database->tables->messages->thread ($database->tables->messages->session(), $thread, $last));
                    exit;
                }
            }
            else if ($action=='check')
            {
                while (true)
                {
                    $result = $database->tables->messages->updates ($database->tables->messages->session(), $command);
                    if ($result)
                    {
                        echo json_encode($result);
                        exit;
                    }
                    else
                    {
                        sleep(3);
                    }
                }
            }
            exit;
        }
    }
?>
