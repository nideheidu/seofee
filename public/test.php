<?php 

header("Content-type:text/html; charset=utf-8"); 

/*参数1:请求的URL;参数2:以CURL方式设置http的请求头;参数3:要提交的数据包*/ 

function doCurlPostRequest($url,$header,$data){   

    $ch = curl_init(); 

    /*请求地址*/ 

    curl_setopt($ch, CURLOPT_URL, $url); 

    /*以CURL方式设置http的请求头*/ 

    curl_setopt($ch, CURLOPT_HTTPHEADER,$header); 

    /*文件流形式*/ 

    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);  

    /*发送一个常规的Post请求*/ 

    curl_setopt($ch, CURLOPT_POST, 1);  

    /*Post提交的数据包*/ 

    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);  

    return curl_exec($ch);  

} 

/*请求的URL*/ 

$url = "http://apis.5118.com/morerank/baidupc"; 

/*要提交的数据包*/ 
$data = "url=www.safehoo.com&keywords=安全";  
// $data['url'] = "www.safehoo.com";  
// $data['keywords'] = "安全";  

/*以CURL方式设置http的请求头*/ 

$header[] = "Content-type:application/x-www-form-urlencoded"; 

/*输入你要调用API的APIKEY*/ 

$header[] = "Authorization: APIKEY 891FA8529A5C4C8789E0AF908716999D"; 

/*调用CURL POST函数*/ 

$result=doCurlPostRequest($url,$header,$data); 
/*对 JSON 格式的字符串进行解码，并转换为 PHP 变量*/ 

$json = json_decode($result); 

/*errcode=0 正确返回 errcode=200104 数据采集中*/ 

$errcode = $json->errcode;  

$errmsg = $json->errmsg; 

// if ($errcode != "0" && $errmsg != "") { 

//     /*非正确返回*/ 

//     print_r($result); 

//     exit; 

// } 

/*提交查询请求后获取提取结果的任务ID*/ 

$taskid = $json->data->taskid; 
//$taskid =2084364;
if ($taskid != "") {       

    /*要提交的数据包*/ 

    $data = "taskid=".$taskid; 

    /*调用CURL POST函数*/ 

    // $result = doCurlPostRequest($url,$header,$data); 

    // $json = json_decode($result); 

    // $errcode = $json->errcode; 

    // $errmsg = $json->errmsg;     

    // /*errcode=200104 数据采集中*/ 

    // if ($errcode == "200104" && $errmsg =="数据采集中") {      

        while(true){ 
        sleep(5);

        /*调用CURL POST函数*/         

            $result = doCurlPostRequest($url,$header,$data); 
            $json = json_decode($result);
            $errcode = $json->errcode; 

            $errmsg = $json->errmsg;             

            /*errcode=0 正确返回*/  

            if ($errcode == "0" && $errmsg == "") {                 
                file_put_contents("text.txt", $result);
                print_r($result);                 

                /*退出当前的脚本*/ 
                exit; 
            } 
        } 

    // }else { 
    //  /*返回数据结果*/ 
    //     file_put_contents("text.txt", $result);
    //     print_r($result); 
    // } 
} 

else { 

    /*返回异常或其它信息*/ 
                file_put_contents("text.txt", $result);

    print_r($result); 

} 

/*退出当前的脚本*/ 

exit; 

?> 

  

