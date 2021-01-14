<?php
/*
 * PHP代码反混淆
 */
header("Content-Type: text/html;charset=UTF-8");
date_default_timezone_set('Asia/Shanghai');

$path = "../application/job";
//获取目录下所有php文件
$files = getFiles($path);

//$files = ['../application/common/model/WebUrl.php'];
foreach ($files as $file)
{
    antiAliasing($file);
}

//根据目录获取下面所有php文件
function getFiles($path)
{
    $list = [];
    $files = scandir($path);
    foreach ($files as $file)
    {
        if (strstr($file,'.php'))
        {
            $list[] = $path.'/'.$file;
        }
        elseif (strstr($file,'.'))
        {
            continue;
        }
        else
        {
            $list = array_merge($list,getFiles($path.'/'.$file));
        }
    }
    return $list;
}

//反混淆
function antiAliasing($file)
{
    //获取文件内容
    $str = file_get_contents($file);
    //记录源文件内容
//    $myfile = fopen(str_replace('.php','bak.php',$file), "w") or die("Unable to open file!");
//    fwrite($myfile, $str);
//    fclose($myfile);

    //换行替换
    $str = preg_replace("/\;([^\|\"0-9])/",';'."\r\n".'$1',$str);
    $str = preg_replace("/([a-z\)])\{/",'$1'."\r\n{\r\n",$str);
    $str = preg_replace("/([^\|])\}([^\)\,\|\(])/",'$1'."\r\n}\r\n".'$2',$str);
    $str = preg_replace("/([^\|])\}([^\)\,\|\(\r\n])/",'$1'."\r\n}\r\n".'$2',$str);
    $str = preg_replace("/\}[\r\n]\}/","}\r\n}\r\n",$str);

    //记录换行后的内容
//    $myfile = fopen(str_replace('.php','line.php',$file), "w") or die("Unable to open file!");
//    fwrite($myfile, $str);
//    fclose($myfile);

    //按行转成数组
    $arr = explode("\r\n",$str);
    $content = '';
    //反混淆
    foreach ($arr as $one)
    {
        if (strstr($one,'defined') || (strstr($one,'$GLOBALS')&&(strstr($one,'explode')||strstr($one,'call_user_func'))) || preg_match('/^\\$GLOBALS/',$one))
        {
            eval($one);
        }
        elseif (preg_match_all("/(\\$[A-Z]+\[pack\([A-Za-z0-9$\[\]\{\}\,]+\)\])|(pack\([A-Za-z0-9$\[\]\{\}\,]+\))/",$one,$matchs) || strstr($one,'$GLOBALS'))
        {
            if ($matchs[0])
            {
                $matchs[0] = is_array($matchs[0]) ? $matchs[0] : [$matchs[0]];
                foreach ($matchs[0] as $match)
                {
                    if (preg_match("/^(\\$[A-Z])/",$match))
                    {
                        $one = str_replace($match,eval('return '.$match.';'),$one);
                    }
                    else
                    {
                        $one = str_replace($match,"'".eval('return '.$match.';')."'",$one);
                    }
                }
            }
            if (preg_match_all("/(\\$[A-Z]+((\[|\{)[a-zA-Z0-9\-\+\*\(\)]+(\]|\}))+)|([A-Z_]{5,})|((A|0){6,})/",$one,$matchs))
            {
                $matchs[0] = is_array($matchs[0]) ? $matchs[0] : [$matchs[0]];
                foreach ($matchs[0] as $match)
                {
                    $one = str_replace($match,eval('return '.$match.';'),$one);
                }
            }
            $content .= "{$one}\r\n";
        }
        elseif (preg_match_all("/([\-\+\*]((A|0){6,}|[A-Z_]{5,}))|(((A|0){6,}|[A-Z_]{5,})[\-\+\*])/",$one,$matchs))
        {
            $matchs[0] = is_array($matchs[0]) ? $matchs[0] : [$matchs[0]];
            foreach ($matchs[0] as $match)
            {
                $one = str_replace(preg_replace("/[\-\+\*]/",'',$match),eval('return '.preg_replace("/[\-\+\*]/",'',$match).';'),$one);
            }
            $content .= "{$one}\r\n";
        }
        else
        {
            $content .=  "{$one}\r\n";
        }
    }

    //反混淆后的内容
//    $myfile = fopen(str_replace('.php','new.php',$file), "w") or die("Unable to open file!");
//    fwrite($myfile, $str);
//    fclose($myfile);

    //数字运算结果
    while (true)
    {
        if (!preg_match_all("/\(([0-9]|\+|\-|\*)+\)/",$content,$matchs))
        {
            break;
        }
        $matchs[0] = is_array($matchs[0]) ? $matchs[0] : [$matchs[0]];
        foreach ($matchs[0] as $match)
        {
            $content = str_replace($match,eval('return '.$match.';'),$content);
        }
    }
    $myfile = fopen($file, "w") or die("Unable to open file!");
    fwrite($myfile, $content);
    fclose($myfile);
}
exit;