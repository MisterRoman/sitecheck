<?php

header("Content-Type: text/html; charset=utf-8");

include_once('simple_html_dom.php');



if (isset ($_POST['site'])) {$site = $_POST['site'];} else {

echo 'Введите адрес сайта';
exit;

}



$site=checkchars($site);




if (strpos($site,'http')===false) {

$site='http://'.$site;

}



$dir=getdir1($site);









echo '<h4>Результат проверки сайта: '.$site.'</h4>';
echo 'Директория страницы: '.$dir.'<br><br>';


$domain=parse_url($site, PHP_URL_HOST);


echo '<table border="1" cellspacing="0" cellpadding="15" width="500"><tr>';



$str=file_get_contents($site);
$arr=(get_headers($site));

//$err='HTTP/1.1 200 OK';


echo '<td>Ответ сервера</td><td>'.$arr[0].'</td></tr>';



$html = new simple_html_dom();

$html->load($str); 



echo '<tr><td>Title</td><td>'.checkelement($html,'title').'</td></tr>';

echo '<tr><td>Description</td><td>'.checkelement($html,'description').'</td></tr>';

echo '<tr><td>H1</td><td>'.checkelement($html,'h1').'</td></tr>';

echo '<tr><td>Favicon</td><td>'.checkfavicon($html,$domain,$dir).'</td></tr>';
echo '<tr><td>Изображения</td><td>'.checkimg($html,$domain,$dir).'</td></tr>';

echo '<tr><td>CSS</td><td>'.checkcss($html,$domain,$dir).'</td></tr>';
echo '<tr><td>JS</td><td>'.checkjs2($html,$domain,$dir).'</td></tr>';


echo '<tr><td>Sitemap</td><td>'.checkfile('http://'.$domain.'/sitemap.xml').'</td></tr>';

echo '<tr><td>Robots</td><td>'.checkfile('http://'.$domain.'/robots.txt').'</td></tr>';

echo '<tr><td>PHP ошибки</td><td>'.checkerrors($html).'</td></tr>';

echo '<tr><td>Проверить скорость в GoogleSpeed</td><td><a href="https://developers.google.com/speed/pagespeed/insights/?url=http%3A%2F%2F'.$domain.'%2F&tab=mobile" target="_blank">Проверить скорость</a></td></tr>';


//echo checktitle($html);





echo '</tr><table>';





exit;












function checktitle($html) {

$title=$html->find("title",0); 


$title1=$title->innertext;


if (strlen($title1)!=0) {
$s1=$title1.'<br>';} else {
$s1='<font color="ff0000">Title не найден!</font><br>';}

return $s1;

}





function checkelement($html,$element) {

$element=$html->find($element,0); 


$element1=$element->innertext;
$element1=checkchars($element1);


if (strlen(trim($element1))!=0) {
$s1=$element1.'<br>';} else {
$s1='<font color="ff0000">'.$element1. ' не найден!<br></font>';}

return $s1;

}


function checkchars($text) {

$text = htmlspecialchars($text);
$text = str_replace("&lt;p&gt;", "<p>", $text);
$text = str_replace("&lt;/p&gt;", "</p>", $text);
return $text;

}




function checkfavicon($html,$domain,$site) {




foreach($html->find('link') as $element){

$rel1=$element->rel;

if ($rel1=='shortcut icon') {

$s1=$element->href;

}
}


if (strlen($s1)==0) {

$s1='Favicon не найден!<br>';} else {




if (strpos($s1,'//')===0) {
$s1='http:'.$s1;

}




if (strpos($s1,'/')==0) 
$s1='http://'.$domain.$s1;



if ((strpos($s1,$site)===false) && (strpos($s1,'http')===false)) {

$s1=$site.$s1;

}




if (file_get_contents($s1)===false) {

$s1='<font color="ff0000">Favicon указан ('.$s1.'), но файл не существует!</font>';

}





}



return $s1;

}












function checkjs2($html,$domain,$site) {


preg_match_all('#<script src="(.+?)"></script>#is', $html, $arr);

//print_r($arr);
   
   
   
   
foreach($arr[1] as $s1)
   
{

//echo $s1."<br>";




if (strpos($s1,'//')===0) {
$s1='http:'.$s1;

}



if (strpos($s1,'/')===0) 
$s1='http://'.$domain.$s1;




if ((strpos($s1,$site)===false) && (strpos($s1,'http')===false) && (strlen(trim($s1))!=0)) {

$s1=$site.$s1;

}


if (file_get_contents($s1)===false) {

$s1='<font color="ff0000">Файл js '.$s1.' не существует!</font>';

} 



if (strlen(trim($s1))!=0) {
$res=$res.$s1.'<br>';}

}



return $res;

}






function checkerrors($html) {

$res='';

$n=substr_count('Notice',$html);
$w=substr_count('Warning',$html);

if (($n==0) && ($w==0)) {
$res=$res.'Ошибок не найдено.';

}

if ($n>0) {
$res=$res.'<font color="ff0000">Notice: найдено '.$n.'!</font><br>';
}

if ($w>0) {
$res=$res.'<font color="ff0000">Warning: найдено '.$w.' !</font><br>';
}

return $res;


}







function checkimg($html,$domain,$site) {

$res='';

foreach($html->find('img') as $element){


$rel1=$element->src;

$s1=$rel1;

if (strpos($s1,'//')===0) {
$s1='http:'.$s1;
//echo $res=$res.'go';
}


if (strpos($s1,'/')===0) {
$s1='http://'.$domain.$s1;
}


if ((strpos($s1,$site)===false) && (strpos($s1,'http')===false) && (strlen(trim($s1))!=0) ) {

$s1=$site.$s1;

}


if (file_get_contents($s1)===false) {

$s1='<font color="ff0000">Изображение '.$s1.' не существует!</font>';

} 


if (strlen(trim($s1))!=0) {
$res=$res.$s1.'<br>';
}

}

return $res;

}



function checkfile($file) {
if (file_get_contents($file)===false) {
$res='<font color="ff0000">Файл '.$file.' не существует!</font>';
} else {
$res='Файл '.$file.' существует!';
}
return $res;

}





function checkcss($html,$domain,$site) {



$res='';

foreach($html->find('link') as $element){




$s1=$element->href;



$rel1=$element->rel;



if ($rel1==='stylesheet') 
{





if (strpos($s1,'//')===0) {
$s1='http:'.$s1;

}



if (strpos($s1,'/')===0) 
$s1='http://'.$domain.$s1;




if ((strpos($s1,$site)===false) && (strpos($s1,'http')===false) && (strlen(trim($s1))!=0)) {

$s1=$site.$s1;

}


if (file_get_contents($s1)===false) {

$s1='<font color="ff0000">Файл css '.$s1.' не существует!</font>';

} 


if (strlen(trim($s1))!=0) {
$res=$res.$s1.'<br>';
}



}}

return $res;

}



function getdir1($str){

$str1=strrpos($str,'/')+1;
if ($str1==7) { $str1=$str.'/';



 } else {
$str1=substr($str,0,$str1);}

return $str1;

}






?> 