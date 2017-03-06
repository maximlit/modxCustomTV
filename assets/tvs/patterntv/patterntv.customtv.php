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

global $modx;

include_once(MODX_BASE_PATH . 'assets/lib/Helpers/FS.php');
use Helpers\FS;

$patPath = 'assets/images/pattern/';
$path = MODX_BASE_PATH.$patPath;
$patOpt ='';
$base_url = $modx->config['base_url'];
$tvName = $modx->getTemplateVar($field_id, 'name');
$tvName = $tvName['name'];

$dlm = '||';

if (!empty($field_value)){
    $field_value = str_replace($dlm.' ', $dlm, $field_value );
    $curArray = explode($dlm, $field_value);
    $curPat = true;
}

if(FS::getInstance()->checkDir($path)){
    $patImgArr = scandir(iconv("WINDOWS-1251","UTF-8",$path));
    $patImgArr = array_splice($patImgArr,2);
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

if(!empty($patAll)){


if (!function_exists('cleanExt')) {
    function cleanExt($a){
        return  str_replace(".jpg","",$a);
    
   }
 }
    

    $patImgArr = array_map('cleanExt', $patImgArr);
    $patImgArr = array_diff($patImgArr,$patAll);

    foreach ( $patImgArr as $patImg ){
        //$patImg = str_replace(".jpg","",$patImg);
        $patOpt .='<option value="'.$patImg.'">'.$patImg.'</option>';
    }

    foreach ( $patAll  as $pattern ){
        $curr = ($curArray&&in_array($pattern, $curArray)) ?  ' current' : '';
        $patItem .='<li class="pattern-item'.$curr.'" data-fvalue="'.$pattern.'" onclick="setD(this,'.$field_id.',\'||\')"><img src="'.$base_url.$patPath.$pattern.'.jpg" title="'.$pattern.'"/></li>';
    }
}

$output .='
<style>	
.patternWr{
    display: block; margin: 24px 0; width: 100%;}
.patternWr:after{content: \'\';display:table;position: relative;clear: both;}
.patternWr .pattern-item img{
	width: 100%;}
.patternWr .pattern-item{
    display: block; float: left; cursor: pointer; margin:0 4px 4px 0; padding:4px; border-radius: 6px; background: white; position: relative; color: #555; width: 50px;}
.patternWr .pattern-item span{
    width: 50px; font-size: 12px; line-height: 1em;}
.patternWr .pattern-item:hover{
    background: #999; color:white;}
.patternWr .pattern-item.current{
    background: #555; color:white;}
input.patterntv{margin-top:24px;}
</style>
<span><input class="patterntv" type="text" name="tv'.$field_id.'" id="tv'.$field_id.'" value="'.$field_value.'" /></span>
<span><select id="patternAdd'.$field_id.'" onchange="addD(this,'.$field_id.')"><option></option>'.$patOpt.'</select></span>   
<ul id="#pattern'.$field_id.'" class="patternWr">
'.$patItem.'</ul>';

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


   window.addD = function(el,id){
    var addField = document.getElementById("tv"+id)
        ,dlm =  addField.value !== "" ? "||" : "";
    addField.value = addField.value + dlm + el.options[ el.selectedIndex ].text;
    el.removeChild(el[el.selectedIndex]);
   }

</script>';

echo $output;
