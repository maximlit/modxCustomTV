<?php
/**
 * @copyright 2017 maximlit
 * @version 0.1
 * @license http://opensource.org/licenses/MIT MIT License
 * @author maximlit <maximlit@ya.com>
 * @link http://agency-ml.ru author home page
 */

if (MODX_BASE_PATH == '') {
    die('<h1>ERROR:</h1><p>Please use do not access this file directly.</p>');
}


if (!empty($field_value)){
    $field_value = str_replace(', ', ',', $field_value );
    $curArray = explode(",", $field_value);
}

$sql = 'SELECT DISTINCT `value` FROM '.$modx->getFullTableName('site_tmplvar_contentvalues').' WHERE tmplvarid = '.$field_id.' ORDER BY value ASC';
$result = $modx->db->query( $sql );

while( $row = $modx->db->getRow( $result ) ) {

    $allvalues .= $row['value'].',';
}

$allvalues = str_replace(', ', ',', $allvalues );
$allvalues = explode(",", $allvalues);
$allvalues = array_unique($allvalues);
foreach ( $allvalues  as $elvalue) {
    $curr = ($curArray&&in_array($elvalue, $curArray)) ?  ' current' : '';
    $elem .='<li class="tag-item'.$curr.'" data-fvalue="'.$elvalue.'" onclick="setD(this, '.$field_id.',\',\')">'.$elvalue.'</li>';
}
$output .='<style>	
.tagsWr{
    display: block;margin: 24px 0; width: 100%;}
.tagsWr .tag-item{
    display: inline-block; cursor: pointer; margin:0 4px 4px 0; padding:0 4px; border-radius: 6px; background: white; position: relative; color: #555; text-decoration: underline;}
.tagsWr .tag-item:hover{
    background: #999; color:white;}
.tagsWr .tag-item.current{
    background: #555; color:white;}
</style>
<input class="tagstv" type="text" name="tv'.$field_id.'" id="tv'.$field_id.'" value="'.$field_value.'" style="width:100%;"/>
<ul id="#tags'.$field_id.'" class="tagsWr">
'.$elem.'</ul>';

$output .='<script type="text/javascript">
if(typeof in_array != \'function\'){
   window.in_array = function(j, o){var f = 0;for (var i=0, len=o.length;i<len;i++){if (o[i] == j) return i;f++;}return -1;}
}
if(typeof setD != \'function\'){
   window.setD = function(el,id,dlm){    
        var newD = el.dataset.fvalue.trim(),clN = "current",oldD = document.getElementById("tv"+id).value.split(dlm),spD ="",posD = in_array(newD,oldD);;
    oldD.forEach(function(v,i){
        oldD[i] = v.trim();
        if(oldD[i] == ""){oldD.splice(i, 1);}
    });
    document.getElementById("tv"+id).fireEvent("onchange");   
    if(posD != -1){
        oldD.splice(posD, 1);
        document.getElementById("tv"+id).value = oldD.join(dlm+spD);
        el.classList.remove.apply( el.classList, clN.split( /\s/ ) );
    }else {
        oldD.push(newD);
        document.getElementById("tv"+id).value = oldD.join(dlm+spD);
        el.classList.add.apply( el.classList, clN.split( /\s/ ) );
    }
   }
}
</script>';

echo $output;
