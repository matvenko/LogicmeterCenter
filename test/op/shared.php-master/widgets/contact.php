<?php

    if (is_array($contact))
    {
        if (!$contact['name'])
        {
            form_set_error ('contact[name]', t('site.contact.name_required','Please specify name'));
        };

        if (!$contact['subject'])
        {
            form_set_error ('contact[subject]', t('site.contact.subject_required','Please specify subject'));
        };

        if (!$contact['body'])
        {
            form_set_error ('contact[body]', t('site.contact.body_required','Please specify body'));
        };

        if (!$contact['email'] || !preg_match("/^[^@]+@[a-zA-Z0-9._-]+.[a-zA-Z]+$/", $contact['email']))
        {
            form_set_error ('contact[email]', t('site.contact.email_required','Please specify email correctly'));
        };
    };

    if (!$html_errors && is_array($contact))
    {
        @mail_utf8 ($contact_settings['to'], '"'.$contact['name'].'" <>', $contact['subject'], $contact['name'].' (<b>'.$contact['email'].'</b>) wrote:'."\n\n<p>".nl2br($contact['body']));
        html_add_p (t('site.contact.sent','Your message was sent seccessfuly.'));
        html_add_redirect ($redirect_url.'?apage='.$pages_settings['page_contact_send'], 0, true);
        $apage = $pages_settings['page_contact_send'];
    };

    if ($html_errors)
    {
        html_add_p (t('site.contact.fields_required','Please fill all fields.'));
    };
    form_open ();
    form_add_edit ('contact[name]', t('site.contact.name'), $contact['name']);
    form_add_edit ('contact[email]', t('site.contact.email'), $contact['email']);
    form_add_edit ('contact[subject]', t('site.contact.subject'), $contact['subject']);
    form_add_memo ('contact[body]', t('site.contact.body'), $contact['body']);
    form_add_submit (t('site.contact.send'));
    form_close (false, true, $widget_name);

    //$apage = $pages_settings['home'];

?>