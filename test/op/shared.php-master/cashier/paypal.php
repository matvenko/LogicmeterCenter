<?php

    //CANCEL
    //CANCEL DOUBLE REFRESH
    //CANCEL LACKS PAYER-ID AND TOKEN CANT BE EXTRACTED
    //CURRENCY
    //DOUBLE REFRESH

    include $lib_dir.'paypal.php';

    if (!$paypal_deposit_settings['url_success'])
    {
        $paypal_deposit_settings['url_success'] = 'http://lit.ge/transaction/paypal/deposit/complete';
    }
    if (!$paypal_deposit_settings['url_fail'])
    {
        $paypal_deposit_settings['url_fail'] = 'http://lit.ge/transaction/paypal/deposit/complete';
    }

    $skins['paypal_body'] = "
    <!-- Add Digital goods in-context experience. Ensure that this script is added before the closing of html body tag -->
    <script src='https://www.paypalobjects.com/js/external/dg.js' type='text/javascript'></script>
    <script>
        var dg = new PAYPAL.apps.DGFlow(
        {
            trigger: 'paypal_submit_x',
            expType: 'popup'
             //PayPal will decide the experience type for the buyer based on his/her 'Remember me on your computer' option.
        });
        {paypal_redirect}
    </script>
    {page}
    ";

    $skins['paypal_success'] = "
    <html>
    <head></head>
    <body>
    <!-- Add Digital goods in-context experience. Ensure that this script is added before the closing of html body tag -->
    <script src='https://www.paypalobjects.com/js/external/dg.js' type='text/javascript'></script>
    <script>
        var dg = new PAYPAL.apps.DGFlow(
        {
            trigger: 'paypal_submit_x',
            expType: 'popup'
             //PayPal will decide the experience type for the buyer based on his/her 'Remember me on your computer' option.
        });
		if (window.opener)
        {
            window.opener.location = ('http://lit.ge/transaction_success/');
			window.close();
		}
		else if (top.dg.isOpen() == true)
        {
			top.dg.closeFlow();
            top.location = ('http://lit.ge/transaction_success/');
		}
	</script>
    {page}
    </body>
    </html>
    ";

    $skins['paypal_fail'] = "
    <html>
    <head></head>
    <body>
    <!-- Add Digital goods in-context experience. Ensure that this script is added before the closing of html body tag -->
    <script src='https://www.paypalobjects.com/js/external/dg.js' type='text/javascript'></script>
    <script>
        var dg = new PAYPAL.apps.DGFlow(
        {
            trigger: 'paypal_submit_x',
            expType: 'popup'
             //PayPal will decide the experience type for the buyer based on his/her 'Remember me on your computer' option.
        });
		if (window.opener)
        {
            window.opener.location = ('http://lit.ge/transaction_fail/');
			window.close();
		}
		else if (top.dg.isOpen() == true)
        {
			top.dg.closeFlow();
            top.location = ('http://lit.ge/transaction_fail/');
		}
	</script>
    {page}
    </body>
    </html>
    ";

    if ($skins['paypal_body'])
    {
        $skins['body'] = $skins['paypal_body'];
    }

    //debug_var ($_REQUEST);
    //debug_var ($_SERVER);
    if (defined('engine_site'))
    {
        if ($transactions_system && $transactions_type && $transactions_systems[$transactions_system] && $transactions_types[$transactions_type] && $transactions_systems[$transactions_system]['methods'][$transactions_type] && $transactions_states[$transactions_state])
        {
            if (user_logged())
            {
                if (transactions_type_name($transactions_type)=='deposit' && $transactions_state=='init')
                {
                    $forms_settings ['name_hints'] = true;

                    $skins['cashier_transaction_submit'] = "
                    <tr>
                    <td colspan=2 align=right><input type=submit title='{hint}' value='{submit_caption}' class='button' onClick=\"{submit_url}\" id='paypal_submit'>
                    {submit_cancel}
                    </td>
                    </tr>
                    ";
                    if ($paypal_deposit && $paypal_deposit['amount'] && !$html_errors)
                    {
                        $transactions_object = transactions_transaction_object (user_id(), $transactions_settings['main_balance'], $transactions_system, $transactions_type, $paypal_deposit['amount']);
                        if (!$transactions_error)
                        {
                            $transactions_id = transactions_transaction_add (user_id(), $transactions_settings['main_balance'], $transactions_system, $transactions_type, $paypal_deposit['amount']);
                            if (!$transactions_error)
                            {
                                //$paypal_state = SetExpressCheckoutDG (\db\round($paypal_deposit['amount']/1.63), 'USD', 'Sale', $paypal_deposit_settings['url_success'], $paypal_deposit_settings['url_fail'], array(array ('name'=>'ბალანსის შევსება', 'amt'=>\db\round($paypal_deposit['amount']/1.63), 'qty'=>1)));
                                //debug_var ($transactions_object);
                                $paypal_state = SetExpressCheckoutDG (\db\round($transactions_object['transfer_amount']['sum']), $transactions_object['trasfer_currency']['name'], 'Sale', $paypal_deposit_settings['url_success'], $paypal_deposit_settings['url_fail'], array(array ('name'=>'ბალანსის შევსება', 'amt'=>\db\round(\db\round($transactions_object['transfer_amount']['sum'])), 'qty'=>1)));
                                $paypal_status = strtoupper ($paypal_state["ACK"]);
//                                debug_var ($paypal_state);
//                                exit;
                                if ($paypal_status=="SUCCESS"||$paypal_status=="SUCCESSWITHWARNING")
                                {
                                    $transactions_post['token'] = urldecode ($paypal_state["TOKEN"]);
                                    $html['paypal_redirect'] = "dg.startFlow('".RedirectToPayPalDG ($transactions_post['token'])."');";
                                    $transactions_object['saved'] = true;
                                    mysql_debug_query ("update transactions set transaction_key='".md5($transactions_post['token'])."' where transaction_id='".$transactions_id."'");
                                }
                                else
                                {
                                    $ErrorCode = urldecode ($paypal_state["L_ERRORCODE0"]);
                                    $ErrorShortMsg = urldecode ($paypal_state["L_SHORTMESSAGE0"]);
                                    $ErrorLongMsg = urldecode ($paypal_state["L_LONGMESSAGE0"]);
                                    $ErrorSeverityCode = urldecode ($paypal_state["L_SEVERITYCODE0"]);
                                    echo "SetExpressCheckout API call failed. ";
                                    echo "Detailed Error Message: ".$ErrorLongMsg;
                                    echo "Short Error Message: ".$ErrorShortMsg;
                                    echo "Error Code: ".$ErrorCode;
                                    echo "Error Severity Code: ".$ErrorSeverityCode;
                                    transactions_transaction_fail ($transactions_id);
                                }
                            };
                        };
                    };
                    if ($transactions_error)
                    {
                        html_add_message ($transactions_error);
                        if ($transactions_id)
                        {
                            transactions_transaction_fail ($transactions_id);
                        };
                    };
                    if ($html_errors || !$transactions_object['saved'])
                    {
                        form_open ('paypal_deposit', $project_url.seo_encode(array('apage'=>$pages_settings['page_transaction'],'transactions_system'=>$transactions_system,'transactions_type'=>$transactions_type, 'transactions_state'=>'init')));
                        form_add_label ('<b>ბალანსის შევსება PayPal-ის ანგარიშით</b>');
                        form_add_hidden ('apage', $apage);
                        form_add_spacer ();
                        form_add_edit ('paypal_deposit[amount]', 'მიუთითეთ თანხა ლარებში', $paypal_deposit['amount']);
                        form_add_submit ('გაგრძელება');
                        form_close ();
                    }
                    else
                    {
                        html_parse_text ('cashier_processing');
                    };
                };
            };

            if (transactions_type_name($transactions_type)=='deposit' && $transactions_state=='complete')
            {
                $transactions_token = between('token=','&',$_SERVER['REQUEST_URI']);
                if (!$transactions_token)
                {
                    $transactions_token = after('token=',$_SERVER['REQUEST_URI']);
                }
                $transactions_payer = after('PayerID=',$_SERVER['REQUEST_URI']);

                $transactions_post['key'] = md5($transactions_token);
                $transactions_post['success'] = false;
                $transactions_post['fail'] = false;

                $paypal_result = GetExpressCheckoutDetails ($transactions_token);
                $transactions_post['amount'] = $paypal_result["AMT"];
                $transactions_post['token'] = $transactions_token;
                $transactions_post['payer'] = $transactions_payer;
                $transactions_post['type'] = 'Sale';
                $transactions_post['currency'] = $paypal_result['CURRENCYCODE'];

                $paypal_state = ConfirmPayment ($transactions_post['token'], $transactions_post['type'], $transactions_post['currency'], $transactions_post['payer'], $transactions_post['amount']);
                $paypal_status = strtoupper ($paypal_state["ACK"]);
                if ($paypal_status=="SUCCESS"||$paypal_status=="SUCCESSWITHWARNING")
                {
                    $transactions_post['id'] = $paypal_state["PAYMENTINFO_0_TRANSACTIONID"]; // Unique transaction ID of the payment.
                    $transactions_post['method'] = $paypal_state["PAYMENTINFO_0_TRANSACTIONTYPE"]; // The type of transaction Possible values: l  cart l  express-checkout
                    $transactions_post['type'] = $paypal_state["PAYMENTINFO_0_PAYMENTTYPE"];  // Indicates whether the payment is instant or delayed. Possible values: l  none l  echeck l  instant
                    $transactions_post['time'] = $paypal_state["PAYMENTINFO_0_ORDERTIME"];  // Time/date stamp of payment
                    $transactions_post['sum'] = $paypal_state["PAYMENTINFO_0_AMT"];  // The final amount charged, including any  taxes from your Merchant Profile.
                    $transactions_post['currency'] = $paypal_state["PAYMENTINFO_0_CURRENCYCODE"];  // A three-character currency code for one of the currencies listed in PayPay-Supported Transactional Currencies. Default: USD.
                    $transactions_post['free'] = $paypal_state["PAYMENTINFO_0_FEEAMT"];  // PayPal fee amount charged for the transaction
                    //	$settleAmt			= $paypal_state["PAYMENTINFO_0_SETTLEAMT"];  // Amount deposited in your PayPal account after a currency conversion.
                    $transactions_post['tax'] = $paypal_state["PAYMENTINFO_0_TAXAMT"];  // Tax charged on the transaction.
                    //	$exchangeRate		= $paypal_state["PAYMENTINFO_0_EXCHANGERATE"];  // Exchange rate if a currency conversion occurred. Relevant only if your are billing in their non-primary currency. If the customer chooses to pay with a currency other than the non-primary currency, the conversion occurs in the customer's account.

                    /*
                      ' Status of the payment:
                      'Completed: The payment has been completed, and the funds have been added successfully to your account balance.
                      'Pending: The payment is pending. See the PendingReason element for more information.
                     */

                    $transactions_post['status'] = $paypal_state["PAYMENTINFO_0_PAYMENTSTATUS"];

                    /*
                      'The reason the payment is pending:
                      '  none: No pending reason
                      '  address: The payment is pending because your customer did not include a confirmed shipping address and your Payment Receiving Preferences is set such that you want to manually accept or deny each of these payments. To change your preference, go to the Preferences section of your Profile.
                      '  echeck: The payment is pending because it was made by an eCheck that has not yet cleared.
                      '  intl: The payment is pending because you hold a non-U.S. account and do not have a withdrawal mechanism. You must manually accept or deny this payment from your Account Overview.
                      '  multi-currency: You do not have a balance in the currency sent, and you do not have your Payment Receiving Preferences set to automatically convert and accept this payment. You must manually accept or deny this payment.
                      '  verify: The payment is pending because you are not yet verified. You must verify your account before you can accept this payment.
                      '  other: The payment is pending for a reason other than those listed above. For more information, contact PayPal customer service.
                     */

                    $transactions_post['pending'] = $paypal_state["PAYMENTINFO_0_PENDINGREASON"];

                    /*
                      'The reason for a reversal if TransactionType is reversal:
                      '  none: No reason code
                      '  chargeback: A reversal has occurred on this transaction due to a chargeback by your customer.
                      '  guarantee: A reversal has occurred on this transaction due to your customer triggering a money-back guarantee.
                      '  buyer-complaint: A reversal has occurred on this transaction due to a complaint about the transaction from your customer.
                      '  refund: A reversal has occurred on this transaction because you have given the customer a refund.
                      '  other: A reversal has occurred on this transaction due to a reason not listed above.
                     */

                    $transactions_post['reversal'] = $paypal_state["PAYMENTINFO_0_REASONCODE"];

                    if (strtolower($transactions_post['status'])=='completed')
                    {
                        $transactions_post['success'] = true;
                    }
                    else if (strtolower ($transactions_post['pending']))
                    {
                        $transactions_post['pending'] = true;
                    }
                    else if (strtolower($transactions_post['reversal']))
                    {
                        $transactions_post['fail'] = true;
                    }

                }
                else
                {
//                    //Display a user friendly Error on the page using any of the following error information returned by PayPal
//                    $ErrorCode = urldecode ($paypal_state["L_ERRORCODE0"]);
//                    $ErrorShortMsg = urldecode ($paypal_state["L_SHORTMESSAGE0"]);
//                    $ErrorLongMsg = urldecode ($paypal_state["L_LONGMESSAGE0"]);
//                    $ErrorSeverityCode = urldecode ($paypal_state["L_SEVERITYCODE0"]);
//
//                    echo "DoExpressCheckoutDetails API call failed. ";
//                    echo "Detailed Error Message: ".$ErrorLongMsg;
//                    echo "Short Error Message: ".$ErrorShortMsg;
//                    echo "Error Code: ".$ErrorCode;
//                    echo "Error Severity Code: ".$ErrorSeverityCode;
                }



                if ($transactions_post['key'])
                {
                    $transactions_id = mysql_value_query ("select transaction_id from transactions where transaction_key='".escape_string($transactions_post['key'])."' and transaction_status=1");

                    if ($transactions_id)
                    {
                        if ($transactions_post['success']==true)
                        {
                            transactions_transaction_complete ($transactions_id);

                            if (!$transactions_error)
                            {
                                echo html_parse($skins['paypal_success']);
                                exit;
                            }
                        }
                        else
                        {
                            if ($transactions_post['pending'])
                            {
                                transactions_transaction_log ("paypal: transaction with key ".$transactions_post['key']." is pending", 2);
                            }
                            else
                            {
                                transactions_transaction_fail ($transactions_id);
                                transactions_transaction_log ("paypal: transaction with key ".$transactions_post['key']." not avalible", 2);
                            }
                        }
                    }
                    else
                    {
                        transactions_transaction_log ("paypal: transaction with key ".$transactions_post['key']." cant be found", 2);
                    };
                }
                else
                {
                    transactions_transaction_log ("paypal: deposit has not post info", 2);
                }
                echo html_parse($skins['paypal_fail']);
                exit;
            }

        }
    }
?>
