<?php

//        $html[$widget_name] = html_parse("
//        <script>chat.display.threads.hide()</script>
//        <div class='chat_metallogasket'>
//            <span>Talk to our sales representative and get instant online quote</span>
//            <img src='{image_dir}/chat_left.png'></div>
//        </div>
//        ");

    //unset($_SESSION['messages_profile']);
    $skins[$widget_name."_error"] = "
    <div class='chat_guest_warning'>All fields are required !</div>
    ";

    $skins[$widget_name."_guest"] = "
    <script>
    $(document).ready(function() {
      chat.display.threads.hide()
    });
   </script>
    <div class='chat_guest_session'>
        <div class='chat_guest_welcome'>You are now talking<br>to our sales representative</div>
        <div class='chat_guest_close' onclick=\"document.location=('{project_url}chat_logout/');\">End session</div>
    </div>
    ";

    $skins[$widget_name] = "
    <div class='chat_guest_warning'>To serve you better, please answer the following questions:</div>
    <div class='chat_guest'>
    {error}
        <form method='post' action={redirect_url}>
            <input type='hidden' name='apage' value='{apage}'>
            <label>Name</label> <input type='edit' name='chat[name]' value='{name}'>
            <label>E-Mail</label> <input type='edit' name='chat[mail]' value='{mail}'>
            <label>Company</label> <input type='edit' name='chat[company]' value='{company}'>
            <label></label><input type='submit' value='Chat'>
        </form>
    </div>
    ";

    $skins[$widget_name."_welcome"] = "
    Welcome to Metallo Gasket Co, Chat. we appreciate your interest. My name is Rati. How may i assist you today?
    ";

    $skins[$widget_name."_sent"] = "
    <div class='chat_guest_warning'>
    Your message has been successfully sent.
    We will reply to you as soon as possible.
    Thank you!
    </div>
    ";

    $skins[$widget_name."_offline"] = "
        <div class='chat_guest_warning'>Online Support is currently not available. Please leave us a message<br>and a service representative will be in contact with you shortly.</div>
        <div class='chat_guest'>
        {error}
            <form method='post' action={redirect_url}>
                <input type='hidden' name='apage' value='{apage}'>
                <label>Name</label> <input type='edit' name='send[name]' value='{name}'>
                <label>E-Mail</label> <input type='edit' name='send[mail]' value='{mail}'>
                <label>Company</label> <input type='edit' name='send[company]' value='{company}'>
                <label>Message</label> <input type='edit' name='send[message]' value='{message}'>
                <label></label><input type='submit' value='Send'>
            </form>
        </div>
    ";

    if ($chat_settings['administrator'])
    {
        $administrator = $database->tables->message->profiles->load ($chat_settings['administrator']);
    }

    if (!$database->tables->messages->session())
    {
        if (user_logged())
        {
            $database->tables->message->profiles->register ();
        }
        else if (is_array($chat) && $chat['mail'] && $chat['name'] && $chat['company'])
        {
            $profile = $database->tables->message->profiles->register ($chat['name'], $chat['mail'], $chat['company']);
            if ($database->tables->messages->session() && $chat_settings['administrator'] && $skins[$widget_name."_welcome"])
            {
                if ($administrator)
                {
                    mail_utf8 ($administrator->mail, ucwords(\db\string($profile->name))." <".\db\string($profile->mail).">", $profile->name." from ".$profile->company." wants to chat with you", "");
                }
                $database->tables->messages->send ($chat_settings['administrator'], $database->tables->messages->session(), html_parse($skins[$widget_name.'_welcome'],$profile));
            }
        }
        else if (is_array($chat))
        {
            $values['error'] = html_parse ($skins[$widget_name."_error"], $values);
        }
        else if (is_array($send) && $send['mail'] && $send['name'] && $send['company'])
        {
            if ($administrator)
            {
                mail_utf8 ($administrator->mail, ucwords(\db\string($profile->name))." <".\db\string($profile->mail).">", $profile->name." from ".$profile->company." sent you offline message", $send['message']);
            }
        }
        else if (is_array($send))
        {
            $values['error'] = html_parse ($skins[$widget_name."_error"], $values);
        }
    }

    if (!$database->tables->messages->session())
    {
        if ($administrator && user_id()!=$administrator->user && !user_active($administrator->user))
        {
            if (!is_array($send) || $values['error'])
            {
                if (!is_array($send))
                {
                    $send = array ();
                }
                $html[$widget_name] = html_parse ($skins[$widget_name."_offline"], $values+$send);
            }
            else
            {
                $html[$widget_name] = html_parse ($skins[$widget_name."_sent"]);
            }
        }
        else
        {
            if (!is_array($chat))
            {
                $chat = array ();
            }
            $html[$widget_name] = html_parse ($skins[$widget_name], $values+$chat);
        }
    }
    else if (!user_logged())
    {
        $html[$widget_name] = html_parse($skins[$widget_name."_guest"]);
    }
?>
