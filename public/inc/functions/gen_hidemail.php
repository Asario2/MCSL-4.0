<?php
if(!function_exists("gen_Hidemail"))
{
function gen_hidemail($ad)
{
    if(empty($ad))
    {
        return;
    }
$ad2 = str_replace("@","[at]",$ad);
 return "<a class='l".@$_GET['page']."' href='mailto:$ad'>". htmlentities($ad2) ."</a>";
}
}
?>
