<?php
$row = $query->select_obj_sql("f_members", "*" ,"id = '".$_GET['id']."'");

$select[$row->sex] = "selected";
$selected_gr[$row->gt_group] = "selected";
$error[0] = "egeti useri ukve arsebobs";

echo $error[$_GET['error']];

function group_select($sel_num){
        global $query, $groups;
        $groups = '';
        $selected_gr[$sel_num] = 'selected';
        $result_select = $query->select_sql("gov_group", "*", "", "name ASC");
        while($row_group = mysql_fetch_object($result_select)){
                $groups .=  " <option value=\"".$row_group->id."\" ".$selected_gr[$row_group->id].">".$row_group->name."</option> \n";
        }
        return $groups;
}


echo " <form action=\"post.php?module=".$_GET['module']."&id=".$_GET['id']."\" method=\"post\"> ";
echo " <table border=\"0\" width=\"500\" id=\"table1\"> ";
echo "         <tr> ";
echo "                 <td valign=\"top\"> <b>"._ADD_USER."</b><br> ";
echo "                    <input type=\"text\" name=\"user_name\" size=\"30\" value=".$row->name."> ";
echo "                 <br><br></td> ";
echo "                 <td valign=\"top\"> <b>"._SEX."</b><br>";
echo "                         <select size=\"1\" name=\"sex\"> ";
echo "                         <option value=\"0\">---------------</option> ";
echo "                         <option value=\"1\" ".$select[1].">"._FEMALE."</option> ";
echo "                         <option value=\"2\" ".$select[2].">"._MALE."</option> ";
echo "                 </select> ";
echo "                 </td> ";
echo "         </tr> ";
echo "         <tr> ";
echo "                 <td valign=\"top\"> <b>"._ADD_NAME."</b><br> ";
echo "                 <input type=\"text\" name=\"name\" size=\"30\" value=".$row->firstname."><br><br> ";
echo "                </td> ";
echo "                 <td valign=\"top\"> <b>"._ADD_SURNAME."</b><br> ";
echo "                 <input type=\"text\" name=\"surname\" size=\"30\" value=".$row->surname."><br> ";
echo "                 </td> ";
echo "         </tr> ";
echo "         <tr> ";
echo "                 <td valign=\"top\"> <b>"._ADD_MAIL."</b><br> ";
echo "                 <input type=\"text\" name=\"email\" size=\"30\" value=".$row->email."><br><br> ";
echo "                 </td> ";
echo "                 <td valign=\"top\">  </td> ";
echo "         </tr> ";
echo "         <tr> ";
echo "                 <td colspan=\"2\" align=\"center\"> ";
echo "                   <table border=\"0\" width=\"100%\" id=\"table1\"> \n";
echo "                         <tr> \n";
echo "                            <td valign=\"top\"> <b>"._GROUP."1</b><br> ";
echo "                                <select size=\"1\" name=\"group1\"> \n";
echo "                                  <option value=\"0\">------------</option> \n";
echo                                     group_select($row->gt_group1);
echo "                                 </select> \n";
echo "                            </td> ";
echo "                            <td valign=\"top\"> <b>"._GROUP."2</b><br> ";
echo "                                <select size=\"1\" name=\"group2\"> \n";
echo "                                  <option value=\"0\">------------</option> \n";
echo                                     group_select($row->gt_group2);
echo "                                 </select> \n";
echo "                            </td> ";
echo "                            <td valign=\"top\"> <b>"._GROUP."3</b><br> ";
echo "                                <select size=\"1\" name=\"group3\"> \n";
echo "                                  <option value=\"0\">------------</option> \n";
echo                                     group_select($row->gt_group3);
echo "                                 </select> \n";
echo "                            </td> ";
echo "                         </tr> \n";
echo "                   </table> \n";
echo "                 </td> ";
echo "         </tr> ";
echo "         <tr> ";
echo "                 <td colspan=\"2\" align=\"center\"> <br><br>";
echo "                       <input type=\"submit\" value=\""._EDIT."\" name=\"edit_user\"> ";
echo "                 </td> ";
echo "         </tr> ";
echo " </table> ";
echo " </form> ";
?>
