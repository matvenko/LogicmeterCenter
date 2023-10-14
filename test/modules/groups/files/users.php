<?php
$result = $query->select_sql("f_members", "*", "", "id");

echo " <table border=\"1\" width=\"100%\" class=\"user_table\" cellspacing=\"0\" cellpadding=\"0\"> ";
echo "         <tr> ";
echo "                 <td align=\"center\" class=\"reg_table_title\"><b>"._USER."</b></td> ";
echo "                 <td align=\"center\" width=\"100\" class=\"reg_table_title\"><b>"._NAME."</b></td> ";
echo "                 <td align=\"center\" class=\"reg_table_title\"><b>"._SURNAME."</b></td> ";
echo "                 <td align=\"center\" class=\"reg_table_title\"><b>"._GROUP."</b></td> ";
echo "                 <td align=\"center\" width=\"70\" class=\"reg_table_title\"><b>"._EDIT."</b></td> ";
echo "         </tr> ";

while($row = mysql_fetch_object($result)){
        if($row->id == 1){
                $if_admin_link = '</a>';
                $if_admin = "<font color=\"#800000\"><b>root</b></font>";
        }
        else{
                $if_admin_link = '';
                $if_admin = '';
        }
        echo "         <tr> ";
        echo "                 <td class=\"reg_cell\" height=\"25\">&nbsp;".$row->name."</td> ";
        echo "                 <td align=\"center\" width=\"100\" class=\"reg_cell\">&nbsp;".$row->firstname."</td> ";
        echo "                 <td class=\"reg_cell\">&nbsp;".$row->surname."</td> ";
        echo "                 <td class=\"reg_cell\">&nbsp;".$if_admin."&nbsp;".$query->group_name($row->gt_group)."</td> ";
        echo "                 <td align=\"center\" width=\"70\" class=\"reg_cell\">
                                   <a href=\"admin.php?module=".$_GET['module']."&page=user_edit&id=".$row->id."\"><img src=\"images/edit.png\" border=0></a>
                               </td> ";
         echo "         </tr> ";
}

echo " </table> ";
?>
