<?php
set_time_limit(0);
$file_one=$_POST['url1'];
$file_tow=$_POST['url2'];
//获取文件内容
$content1='';$content2='';
$file_arr1=array();$file_arr2=array();
if(file_exists($file_one))
    $file_arr1 = file($file_one);
if(file_exists($file_tow))
    $file_arr2 = file($file_tow);
if($file_arr1){
    foreach($file_arr1 as $value){
        if(in_array($value,$file_arr2))
            $content1.='<span>'.htmlspecialchars($value).'</span>'."<br/>";
        else
            $content1.='<span class="yellow">'.htmlspecialchars($value).'</span>'."<br/>";
    }
}
if($file_arr2){
    foreach($file_arr2 as $value){
        if(in_array($value,$file_arr1))
            $content2.='<span>'.htmlspecialchars($value).'</span>'."<br/>";
        else
            $content2.='<span class="yellow">'.htmlspecialchars($value).'</span>'."<br/>";
    }
}
if(empty($file_arr1) && empty($file_arr2))
    echo json_encode(array('success'=>false));
else
    echo json_encode(array('success'=>true,'content1'=>$content1,'content2'=>$content2));