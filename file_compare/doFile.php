<?php
set_time_limit(0);
$url_one=$_POST['url_one'];
$url_tow=$_POST['url_two'];
$filter=array();
if($_POST['filter'])
    $filter=explode(',',$_POST['filter']);
$isFile1=file_exists($url_one);
$isFile2=file_exists($url_tow);
if(empty($isFile1) || empty($isFile2)){
	echo json_encode(array('success'=>false,'message'=>'目标文件夹不存在'));
	exit;
}
$dataOne=read_all_dir($url_one,$filter);
$dataTwo=read_all_dir($url_tow,$filter);
$dataOne=multi2array($dataOne,true);
$dataTwo=multi2array($dataTwo,false);
$caji1=array_diff($dataOne,$dataTwo);
$caji2=array_diff($dataTwo,$dataOne);
unset($dataOne);unset($dataTwo);
$html1="";
$html2="";
if(!empty($caji1)){
    foreach($caji1 as $key=>$val){
        $html1.="<span class='html1 add1'>{$key}</span></br>";
    }
}
if(!empty($caji2)){
    foreach($caji2 as $key=>$val){
        $html2.="<span class='html1 add2'>{$key}</span></br>";
    }
}
$res=json_encode(array('success'=>true,'message'=>'yes','html1'=>$html1,'count1'=>count($caji1),'html2'=>$html2,'count2'=>count($caji2)),JSON_UNESCAPED_UNICODE); //JSON_UNESCAPED_SLASHES JSON_UNESCAPED_UNICODE
echo $res;
exit;

function read_all_dir($dir,$filter)
{
    $result=array();
    $handle = opendir($dir);
    if ($handle)
    {
        while ( ( $file = readdir ( $handle ) ) !== false )
        {
            $utf8togb2312=iconv("gb2312","utf-8",$file);
            if ( $file != '.' && $file != '..' && !in_array($utf8togb2312,$filter))
            {
                $cur_path = $dir.DIRECTORY_SEPARATOR .$file;
                if ( is_dir ( $cur_path ) )
                {
                    $result['dir'][] = read_all_dir($cur_path,$filter);
                }
                else
                {
                    $result['file'][] = iconv("gb2312","utf-8",$cur_path);
                }
            }
        }
        closedir($handle);
    }
    return $result;
}
function multi2array($array,$start) {
    if($start)
        static $result_array1 = array();
    else
        static $result_array2 = array();
    foreach ($array as $key => $value) {
        if (is_array($value)) {
            multi2array($value,$start);
        }else if(!empty($value)){
            $basename=basename($value);
            if($start){
                if($_POST['status'])
                    @$result_array1[str_replace($_POST['url_one'],'',$value)] = $basename.(filesize($value)?filesize($value):filesize(iconv("utf-8","gb2312",$value)));
                else
                    @$result_array1[str_replace($_POST['url_one'],'',$value)] = file_get_contents($value);
            }else{
                if($_POST['status'])
                    @$result_array2[str_replace($_POST['url_two'],'',$value)] = $basename.(filesize($value)?filesize($value):filesize(iconv("utf-8","gb2312",$value)));
                else
                    @$result_array2[str_replace($_POST['url_two'],'',$value)] = file_get_contents($value);
            }
        }
    }
    if($start)
        return $result_array1;
    else{
        return $result_array2;
    }
}