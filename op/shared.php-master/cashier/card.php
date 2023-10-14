<?php

    if (defined('engine_site'))
    {
        if ($transactions_system && $transactions_type && $transactions_systems[$transactions_system] && $transactions_types[$transactions_type] && $transactions_systems[$transactions_system]['methods'][$transactions_type] && $transactions_states[$transactions_state])
        {
            if (user_logged())
            {
                if ($image)
                {
                    srand ();
                    $code_plain = hash_with (5, 'abcdefghjkmnpqrst23456789');
                    setcookie ('card_deposit_code', md5(md5(strtolower($code_plain))));
                    image_flood ($code_plain);
                    exit;
                };

                if (transactions_type_name($transactions_type)=='deposit' && $transactions_state=='init')
                {
                    if ($card_deposit)
                    {
                        if (!$card_deposit['code'] || $_COOKIE['card_deposit_code']!=md5(md5(strtolower($card_deposit['code']))))
                        {
                            form_set_error ('insert[image]');
                            if (!$card_deposit['code'])
                            {
                                form_set_error ('card_deposit[code]', 'მიუთითეთ სურათზე მოცემული სიმბოლოები');
                            }
                            else
                            {
                                form_set_error ('card_deposit[code]', 'სიმბოლოები არ ემთხვევა');
                            };
                        }
                        elseif ($card_deposit['number'])
                        {
                            $card = $database->tables->cards->find ($card_deposit['number']);
                            if (!$card)
                            {
                                form_set_error ('card_deposit[number]', 'ლიტბარათი არ მოიძებნა');
                            }
                        }
                        elseif (!$card_deposit['number'])
                        {
                            form_set_error ('card_deposit[number]', 'მიუთითეთ ლიტბარათის კოდი');
                        }
                    };

                    if ($card_deposit && $card_deposit['number'] && !$html_errors)
                    {
                        if ($database->tables->cards->deposit ($card_deposit['number'], $user))
                        {
                            if (is_object($state) && $state->avalible())
                            {
                                html_add_message ("თქვენ ჩაგერიცხათ ".$card->amount." ლარი", 'page', 'transaction');
                                $state->restore();
                            }
                            else
                            {
                                html_add_message ("თქვენ ჩაგერიცხათ ".$card->amount." ლარი");
                            }
                        }
                        elseif ($transactions_error)
                        {
                            html_add_message ($transactions_error);
                        }
                        else
                        {
                            html_add_message ('მოხდა შეცდომა ლიტბარათის გააქტიურების დროს');
                        }
                    }
                    else
                    {
                        form_open ('deposit_submit', $project_url.seo_encode(array('apage'=>$pages_settings['page_transaction'],'transactions_system'=>$transactions_system,'transactions_type'=>$transactions_type, 'transactions_state'=>'init')));
                        form_add_label ('<div style="clear:both;width:535px;font-weight:bold;color:red;margin-bottom:15px;">10 და 20 ლარიანი ლიტბარათების შეძენა შეგიძლიათ დიოგენეს წიგნის მაღაზიაში. მის: ქ. თბილისი, ა. აფაქიძის ქ. #9 (სპორტის სასახლის გვერდით).</div>');
                        form_add_label ('<b>ბალანსის შევსება</b>');
                        form_add_hidden ('apage', $apage);
                        form_add_spacer ();
                        form_add_edit ('card_deposit[number]', 'ლიტბარათის კოდი', $card_deposit['number']);
                        form_add_spacer ();
                        form_add_image ('', 'შეიყვანეთ სურათზე მოცემული სიმბოლოები', $redirect_url.'?apage='.$apage.'&transactions_system='.$transactions_system.'&transactions_type='.$transactions_type.'&transactions_state=init&image='.md5(time()));
                        form_add_edit ('card_deposit[code]', '');
                        form_add_submit ('გაგრძელება');
                        form_close ();
                    }
                };
            };
        };
    };

    if (defined('engine_admin'))
    {
        $transactions_module_result = true;
    };

?>