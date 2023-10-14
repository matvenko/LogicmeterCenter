<?php

    $skins['dshop_votes_questions'] = "
        <div id='dshop_votes_container'>
            <div class='top_books' style='margin-top:0px'>
            	<div class='tops_title' id='votes'>გამოკითხვა</div>
                <form style='margin-left:17px;margin-top:10px;' method='post' action='{redirect_url}#votes' id='dshop_votes_form'>
                <div style='margin-bottom:10px;font-weight:bold;padding-right:10px;'>{caption}</div>
                <input type='hidden' name='apage' value='{apage}'>
                {questions}
                <input type='button' class='button' style='margin-top:6px;margin-left:135px;' value='პასუხი' onclick='dshop_votes_widget.process()'>
                </form>
                <div class='space'></div>
            </div>
            <div class='see_all' style='margin-bottom:30px;'></div>
        </div>
    ";
    
    $skins['dshop_votes_questions_item'] = "

        <div style='padding-right:10px;padding-bottom:5px;'><input type='radio' name='dshop_vote' value='{id}'> {caption}<br></div>
            
    ";
    
    $skins['dshop_votes_answers'] = "
            <div class='top_books' style='margin-top:0px'>
            	<div class='tops_title' id='votes'>გამოკითხვა</div>
                <form style='margin-left:17px;margin-top:10px;' method='post' action='{redirect_url}#votes'>
                <div style='margin-bottom:10px;font-weight:bold;padding-right:10px;'>{caption}</div>           
                {answers}
                </form>
                <div class='space'></div>
            </div>
            <div class='see_all' style='margin-bottom:30px;'></div>        
    ";
  
    $skins['dshop_votes_answers_item'] = "
        <div style='padding-right:10px;'>{caption} ({percent}%)</div>
        <div style='margin-top:5px;margin-bottom:10px;height:10px;width:{width}px;border:1px solid #8faaaa;background:url({image_dir}/px_link.gif) repeat-x scroll 0 0 transparent'></div>
            
    ";
    
    $result = mysql_debug_query ("select vote_id,vote_caption from widgets_votes where 
        vote_enabled and ((vote_start=0) or (vote_start>0 and vote_start>=now())) and ((vote_end=0) or (vote_end>0 and vote_end<=now()))
        order by vote_start desc,vote_id desc limit 1");
    
    if (mysql_num_rows($result))
    {
        list ($dshop_vote_id, $dshop_vote_caption) = mysql_fetch_row ($result);
        $result = mysql_debug_query ("select answer_id,answer_answer,answer_count from widgets_votes_answers where answer_vote='".$dshop_vote_id."' order by rand()");
        if (mysql_num_rows($result))
        {
            $values['caption'] = $dshop_vote_caption;
            if ($_COOKIE['dshop_votes_'.$dshop_vote_id] || intval($dshop_vote))
            {
                $dshop_vote_answer_sum = 0;
                while (list($dshop_vote_answer_id,,$dshop_vote_answer_count)=mysql_fetch_row($result))
                {
                    $dshop_vote_answer_sum += $dshop_vote_answer_count;
                    if ($dshop_vote && !$_COOKIE['dshop_votes_'.$dshop_vote_id] && $dshop_vote==$dshop_vote_answer_id )
                    {
                        $dshop_vote_answer_sum ++;
                    }
                }
                mysql_data_seek($result, 0);
                while (list($dshop_vote_answer_id,$dshop_vote_answer_caption,$dshop_vote_answer_count)=mysql_fetch_row($result))
                {

                    if (($_COOKIE['dshop_votes_'.$dshop_vote_id] && $_COOKIE['dshop_votes_'.$dshop_vote_id]==$dshop_vote_answer_id) || $dshop_vote && $dshop_vote==$dshop_vote_answer_id)
                    {
                        $dshop_vote_found = true;
                        if ($dshop_vote && !$_COOKIE['dshop_votes_'.$dshop_vote_id])
                        {
                            setcookie ('dshop_votes_'.$dshop_vote_id, $dshop_vote, time()+24*3600+12);
                            mysql_query ("update widgets_votes_answers set answer_count=answer_count+1 where answer_id='".$dshop_vote_answer_id."'");
                            if (user_logged())
                            {
                                mysql_query ("insert into widgets_votes_voters set voter_user='".user_id()."', voter_vote='".$dshop_vote_id."', voter_answer='".$dshop_vote_answer_id."', voter_ip='".$_SERVER['REMOTE_ADDR']."', voter_date='".to_nulldate()."'");
                            }                            
                            $dshop_vote_answer_count++;
                        }
                    }
                    $value = array ();
                    $value['id'] = $dshop_vote_answer_id;
                    $value['caption'] = $dshop_vote_answer_caption;
                    $value['count'] = $dshop_vote_answer_count;
                    $value['percent'] = intval(@($dshop_vote_answer_count/$dshop_vote_answer_sum)*100); 
                    $value['width'] = $value['percent']*1.8;
                    $values['answers'] .= html_parse ($skins[$widget_name."_answers_item"], $value+$html_globals);
                }
            }            
            if (!$dshop_vote_found)
            {
                while (list($dshop_vote_answer_id,$dshop_vote_answer_caption,$dshop_vote_answer_count)=mysql_fetch_row($result))
                {
                    $value = array ();
                    $value['id'] = $dshop_vote_answer_id;
                    $value['caption'] = $dshop_vote_answer_caption;
                    $value['count'] = $dshop_vote_answer_count;
                    $values['questions'] .= html_parse ($skins[$widget_name."_questions_item"], $value+$html_globals);
                }
            }
            if ($dshop_vote_found)
            {
                $html[$widget_name] = html_parse ($skins[$widget_name.'_answers'], $values);
            }
            else
            {
                $html[$widget_name] = html_parse ($skins[$widget_name.'_questions'], $values);
            }
        }
    }
?>
