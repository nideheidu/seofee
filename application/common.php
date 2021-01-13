<?php

if (function_exists('errors')) {
    if (1) {
        function errors($msg = '', $code = 0)
        {
            Result([], $msg, $code, true);

        }

    }
}
if (function_exists('Result')) {
    if (1) {
        function Result($Data, $Msg = '', $HttpCode = 200, $Over = false)
        {
            if (is_array($Data)) {
                $D = true;
                if (!empty($Data['count'])) {
                    $Result['count'] = $Data['count'];
                    unset($Data['count']);
                    $D = false;

                }
                if (!empty($Data['msg'])) {
                    $Result['msg'] = $Data['msg'];
                    unset($Data['msg']);
                    $D = false;

                }
                if (isset($Data['code'])) {
                    $Result['code'] = $Data['code'];
                    unset($Data['code']);
                    $D = false;

                }
                if (!empty($Data['access_token'])) {
                    $Result['data']['access_token'] = $Data['access_token'];
                    unset($Data['access_token']);
                    $D = false;

                }
                if (empty($Data['data'])) {
                    if ($D) {
                        if (!empty($Data)) {
                            $Result['code'] = 0;
                            $Result['msg'] = '成功';
                            $Result['data'] = $Data;

                        }

                    }
                } else {
                    $Result['code'] = 0;
                    $Result['msg'] = '成功';
                    $Result['data'] = $Data['data'];

                }

            } else {
                if ($Data) {
                    $Result['code'] = 0;
                    $Result['data'] = $Data;
                    $Result['msg'] = '操作成功';

                } else {
                    $Result['code'] = 1;
                    $Result['msg'] = '操作失败';

                }

            }
            if ($Msg != '') {
                $Result['msg'] = $Msg;

            }
            if (!isset($Result)) {
                $Result = '';
            }
            (int)$HttpCode;
            if ($Over == true) {
                die(json_encode($Result));

            }
            return json($Result, $HttpCode);

        }

    }
}
if (function_exists('isWeixin')) {
    if (1) {
        function isWeixin()
        {
            if (strpos($_SERVER['HTTP_USER_AGENT'], 'MicroMessenger') !== false) {
                return true;

            } else {
                return false;

            }

        }
    }
}
if (function_exists('isMobile')) {
    if (1) {
        function isMobile()
        {
            if (isset($_SERVER['HTTP_X_WAP_PROFILE'])) {
                return true;

            }
            if (isset($_SERVER['HTTP_VIA'])) {
                return stristr($_SERVER['HTTP_VIA'], 'wap') ? true : false;

            }
            if (isset($_SERVER['HTTP_USER_AGENT'])) {
                $clientkeywords = array('nokia', 'sony', 'ericsson', 'mot', 'samsung', 'htc', 'sgh', 'lg', 'sharp', 'sie-', 'philips', 'panasonic', 'alcatel', 'lenovo', 'iphone', 'ipod', 'blackberry', 'meizu', 'android', 'netfront', 'symbian', 'ucweb', 'windowsce', 'palm', 'operamini', 'operamobi', 'openwave', 'nexusone', 'cldc', 'midp', 'wap', 'mobile', 'MicroMessenger');
                if (preg_match('/(' . implode('|', $clientkeywords) . ')/i', strtolower($_SERVER['HTTP_USER_AGENT']))) {
                    return true;

                }

            }
            if (isset($_SERVER['HTTP_ACCEPT'])) {
                if ((strpos($_SERVER['HTTP_ACCEPT'], 'vnd.wap.wml') !== false) && (strpos($_SERVER['HTTP_ACCEPT'], 'text/html') === false || (strpos($_SERVER['HTTP_ACCEPT'], 'vnd.wap.wml') < strpos($_SERVER['HTTP_ACCEPT'], 'text/html')))) {
                    return true;

                }

            }
            return false;

        }

    }
}
if (!function_exists('parse_attr')) {
    if (1) {
        function parse_attr($value = '')
        {
            if (is_array($value)) return $value;
            $array = preg_split('/[,;\r\n]+/', trim($value, ',;
'));
            if (strpos($value, ':')) {
                $value = array();
                foreach ($array as $val) {
                    list($k, $v) = explode(':', $val);
                    $value[$k] = $v;

                }

            } else {
                $value = $array;

            }
            return $value;

        }

    }
}
function formartUrl($url)
{
    preg_match('/[\w][\w-]*\.[\w][\w-]*\.[\w][\w-]*\.(?:com\.cn|com|cn|co|net|org|gov|cc|biz|info)(\/|$)/isU', $url, $domain);
    return count($domain) > 0 ? str_replace('/', '', $domain[0]) : $url;

}