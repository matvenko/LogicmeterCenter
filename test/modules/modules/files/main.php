<?php
$result = $query->select_sql("gov_modules", "*", "parent_id = '0'", "id ASC");
echo " <center> \n";
echo "<table border=\"1\" width=\"500\" class=\"result\" cellspacing=\"0\" cellpadding=\"0\" bordercolorlight=\"#BFB99B\" bordercolordark=\"#BFB99B\" > \n";
       echo " <tr>";
       echo "     <td align=\"center\" width=\"340\" height=\"20\" class=\"dep\" background=\"modules/".$_GET['module']."/images/dep.jpg\"> "._GROUP_NAME." </td> \n";
       echo "   <td align=\"center\" width=\"80\" height=\"20\" class=\"dep\" background=\"modules/".$_GET['module']."/images/dep.jpg\">"._EDIT." </td> \n";
       echo "   <td align=\"center\" width=\"80\" height=\"20\" class=\"dep\" background=\"modules/".$_GET['module']."/images/dep.jpg\">"._DELETE." </td> \n";
       echo " </tr>";
       while($row = mysql_fetch_object($result)){
               echo " <tr>";
               echo "   <td width=\"340\"> &nbsp;".$row->module." </td>";
               echo "   <td align=\"center\" width=\"80\"> <a href=\"admin.php?module=".$_GET['module']."&action=edit&id=".$row->id."\"><img src=\"images/edit.png\" border=0></a> </td> \n";
               echo "   <td align=\"center\" width=\"80\"> <a href=\"javascript: yes_no('post.php?module=".$_GET['module']."&action=delete&option=".$_GET['option']."&id=".$row->id."')\"><img src=\"images/drop.png\" border=0></a> </td> \n";
               echo " </tr>";
               $result_sub = $query->select_sql("gov_modules", "*", "parent_id = '".$row->id."'", "id ASC");
               while($row_sub = mysql_fetch_object($result_sub)){
                       echo " <tr>";
                       echo "   <td width=\"340\"> &nbsp;---".$row_sub->module." </td>";
                       echo "   <td align=\"center\" width=\"80\"> <a href=\"admin.php?module=".$_GET['module']."&action=edit&id=".$row_sub->id."\"><img src=\"images/edit.png\" border=0></a> </td> \n";
                       echo "   <td align=\"center\" width=\"80\"> <a href=\"javascript: yes_no('post.php?module=".$_GET['module']."&action=delete&option=".$_GET['option']."&id=".$row->id."')\"><img src=\"images/drop.png\" border=0></a> </td> \n";
                       echo " </tr>";
               }
       }
echo " </table>";

if($_GET['action'] == 'edit'){
        $result = $query->select_sql("gov_modules", "*", "id = '".$_GET['id']."'");
        $row = mysql_fetch_object($result);
        $module_value = $row->module;
        $action = 'edit';
}
else{
        $action = 'add';
}

echo " <form action=\"post.php?module=".$_GET['module']."&id=".$_GET['id']."\" method=\"post\"> \n";
$result_select = $query->select_sql("gov_modules", "*", "parent_id = '0'", "id ASC");
echo " <br /><br /> ";
echo " <table border=\"1\" width=\"500\" class=\"add_edit_menu\" cellspacing=\"0\" cellpadding=\"3\"> \n";
echo "         <tr> \n";
echo "                 <td width=\"300\"> \n";
echo "                      &nbsp;<input type=\"text\" name=\"".$action."_module_name\" size=\"40\" value=\"".$module_value."\"></td> \n";
echo "                 <td width=\"200\"> \n";
echo "                      &nbsp;<select size=\"1\" name=\"in_module\"> \n";
echo "                                 <option value=\"0\">------------</option> \n";
while($row = mysql_fetch_object($result_select)){
        echo "    <option value=\"".$row->id."\" ".$blank_opt[0].">".$row->module."</option> \n";
}
echo "                                 </select></td> \n";
echo "         </tr> \n";
echo " </table> <br /> \n";
echo " <input type=\"submit\" value=\"Send\" name=\"".$action."_module\"> \n";
echo " </form> \n";

?>
