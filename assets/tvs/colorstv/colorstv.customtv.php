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

$dlm = ',';

if (!empty($field_value)){
    $field_value = str_replace($dlm.' ', $dlm, $field_value );
    $curArray = explode($dlm, $field_value);
    $curPat = true;
}


$sql = 'SELECT DISTINCT `value` FROM '.$modx->getFullTableName('site_tmplvar_contentvalues').' WHERE tmplvarid = '.$field_id.' ORDER BY value ASC';
$result = $modx->db->query( $sql );
$number = $modx->db->getRecordCount( $result );
$i=1;
while( $row = $modx->db->getRow( $result ) ) {
    $tmpDlm = $i < $number ? $dlm : '';
    $patAll .= $row['value'].$tmpDlm;
    $i++;
}

$patAll = str_replace($dlm.' ', $dlm, $patAll );
$patAll = explode($dlm, $patAll);
$patAll = array_unique($patAll);


$elem = '';

if(!empty($patAll)) {
    foreach ( $patAll  as $item ){
        $curr = ($curArray&&in_array($item, $curArray)) ?  ' current' : '';
        $elem .= '<div id="box-'.$item.'" class="box-item'.$curr.'" data-fvalue="'.$item.'" style="background:'.$item.'" onclick="setD(this, '.$field_id.',\',\')"></div>';
    }

}

$output .='<style>	
.cPikerWr{
    margin: 24px 0;width: 50%;}
.cPikerWr .box-item{
	margin:0 0 2px 0; width: 5%; height: 16px; position: relative; display: inline-block; cursor:pointer;}
.cPikerWr .box-item.current:after{
    content: \'\'; display:inline-block; position:absolute; top:2px; bottom:2px; right:2px; left:2px; border:1px solid white; box-shadow: 0 0 2px 4px #555;}
</style>
<input class="colorstv" type="text" name="tv'.$field_id.'" id="tv'.$field_id.'" value="'.$field_value.'" />
<div id="#colorz'.$field_id.'" class="cPikerWr">
'.$elem.'</div>';

$output .='<script type="text/javascript">	
var clN = "current";
if(typeof in_array != \'function\'){
   window.in_array = function(j, o){var f = 0;for (var i=0, len=o.length;i<len;i++){if (o[i] == j) return i;f++;}return -1;}
}
if(typeof setD != \'function\'){
   window.setD = function(el,id,dlm){
        var newD = el.dataset.fvalue.trim(),oldD = document.getElementById("tv"+id).value.split(dlm),spD ="",posD = in_array(newD,oldD);;
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
