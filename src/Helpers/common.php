<?php

use Illuminate\Support\Facades\Log;

if (!function_exists('get_server_url')) {
    /*
     * 获取服务器网址
     */
    function get_server_url($full = true)
    {
        $http_type = ((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') || (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https')) ? 'https://' : 'http://';
        $http_url = isset($_SERVER['HTTP_X_FORWARDED_HOST']) ? $_SERVER['HTTP_X_FORWARDED_HOST'] : (isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : '');
        if ($full) {
            return $http_type . $http_url;
        } else {
            return $http_url;
        }
    }
}

if (!function_exists('get_request_full')) {
    /*
     * http://example.test/example?id=1
     */
    function get_request_full()
    {
        $pageURL = 'http';

        if (isset($_SERVER["HTTPS"]) && $_SERVER["HTTPS"] == "on") {
            $pageURL .= "s";
        }
        $pageURL .= "://";

        if ($_SERVER["SERVER_PORT"] != "80") {
            $pageURL .= $_SERVER["SERVER_NAME"] . ":" . $_SERVER["SERVER_PORT"] . $_SERVER["REQUEST_URI"];
        } else {
            $pageURL .= $_SERVER["SERVER_NAME"] . $_SERVER["REQUEST_URI"];
        }
        return $pageURL;
    }
}

if (!function_exists('get_request_current')) {
    /*
     * http://example.test/example
     */
    function get_request_current()
    {
        $pageURL = 'http';

        if (isset($_SERVER["HTTPS"]) && $_SERVER["HTTPS"] == "on") {
            $pageURL .= "s";
        }
        $pageURL .= "://";

        $this_page = $_SERVER["REQUEST_URI"];

        // 只取 ? 前面的内容
        if (strpos($this_page, "?") !== false) {
            $this_pages = explode("?", $this_page);
            $this_page = reset($this_pages);
        }

        if ($_SERVER["SERVER_PORT"] != "80") {
            $pageURL .= $_SERVER["SERVER_NAME"] . ":" . $_SERVER["SERVER_PORT"] . $this_page;
        } else {
            $pageURL .= $_SERVER["SERVER_NAME"] . $this_page;
        }
        return $pageURL;
    }
}

if (!function_exists('get_request_previous')) {
    function get_request_previous()
    {
        if (isset($_SERVER['HTTP_REFERER'])) {
            return $_SERVER['HTTP_REFERER'];
        } else {
            return '';
        }
    }
}

if (!function_exists('get_request_uri')) {
    /*
     * /example
     */
    function get_request_uri()
    {
        if (isset($_SERVER['REQUEST_URI'])) {
            $uri = $_SERVER['REQUEST_URI'];
        } else {
            if (isset($_SERVER['argv'])) {
                $uri = $_SERVER['PHP_SELF'] . '?' . $_SERVER['argv'][0];
            } else {
                $uri = $_SERVER['PHP_SELF'] . '?' . $_SERVER['QUERY_STRING'];
            }
        }
        return $uri;
    }
}

if (!function_exists('get_client_ip')) {
    /*
     * 获取当前服务器的IP
     */
    function get_client_ip()
    {
        if (!empty($_SERVER["HTTP_CLIENT_IP"])) {
            $cip = $_SERVER["HTTP_CLIENT_IP"];
        } else if (!empty($_SERVER["HTTP_X_FORWARDED_FOR"])) {
            $cip = $_SERVER["HTTP_X_FORWARDED_FOR"];
        } else if (!empty($_SERVER["REMOTE_ADDR"])) {
            $cip = $_SERVER["REMOTE_ADDR"];
        } else {
            $cip = '';
        }

        preg_match("/[\d\.]{7,15}/", $cip, $cips);
        $cip = isset($cips[0]) ? $cips[0] : 'unknown';
        unset($cips);

        return $cip;
    }
}

if (!function_exists('url_pluck')) {
    /*
     * 提取网址去掉http://|https:// 和 ?之后 部分
     */
    function url_pluck($url)
    {
        $domain = $url;
        if (preg_match('/(http:\/\/)|(https:\/\/)/i', $url)) {
            $domain = preg_replace('/(http:\/\/)|(https:\/\/)/i', '', $url);
        }
        if (strstr($domain, '?')) {
            $domain = substr($domain, 0, strrpos($domain, "?"));
        }
        if (strstr($domain, '/')) {
            $domain = substr($domain, 0, strrpos($domain, "/"));
        }
        return $domain;
    }
}
if (!function_exists('get_dir_files')) {
    /**
     * 获取目录下所有文件
     *
     * @param $dir
     * @return array
     */
    function get_dir_files($dir)
    {
        return array_diff(scandir($dir), ['.', '..']);
    }
}

if (!function_exists('build_number_no')) {
    function build_number_no()
    {
        return date('ymd') . substr(implode(NULL, array_map('ord', str_split(substr(uniqid(), 7, 13), 1))), 0, 8);
    }
}

if (!function_exists('create_order_no')) {
    function create_order_no($prefix = "")
    {
        $order_no = date('Ymd') . substr(time(), -5) . substr(microtime(), 2, 5) . sprintf('%02d', rand(1000, 9999));
        return $prefix . $order_no;
    }
}

if (!function_exists('rm_empty_dir')) {
    /**
     * 删除所有空目录
     *
     * @param String $path 目录路径
     * @return bool
     */
    function rm_empty_dir($path)
    {
        try {
            if (is_dir($path) && ($handle = opendir($path)) !== false) {
                while (($file = readdir($handle)) !== false) {// 遍历文件夹
                    if ($file != '.' && $file != '..') {
                        $curfile = $path . '/' . $file;// 当前目录
                        if (is_dir($curfile)) {// 目录
                            rm_empty_dir($curfile);// 如果是目录则继续遍历
                            if (count(scandir($curfile)) == 2) {//目录为空,=2是因为.和..存在
                                rmdir($curfile);// 删除空目录
                            }
                        }
                    }
                }
                closedir($handle);
            }
            return true;
        } catch (Exception $e) {
            return false;
        }
    }
}

if (!function_exists('deep_in_array')) {
    /*
     * 多维数组中查询键值
     */
    function deep_in_array($value, $array)
    {
        foreach ($array as $item) {
            if (!is_array($item)) {
                if ($item == $value) {
                    return true;
                } else {
                    continue;
                }
            }
            if (in_array($value, $item)) {
                return true;
            } else if (deep_in_array($value, $item)) {
                return true;
            }
        }
        return false;
    }
}

if (!function_exists('search_multi_array')) {
    /**
     * 获取PHP多维数组中指定键（key或下标）的值，并以数组格式返回
     *
     * @param array $array
     * @param $search
     * @param string $mode
     * @return array
     */
    function search_multi_array(array $array, $search, $mode = 'key')
    {
        $res = array();
        foreach (new RecursiveIteratorIterator(new RecursiveArrayIterator($array)) as $key => $value) {
            if ($search === ${${"mode"}}) {
                if ($mode == 'key') {
                    $res[] = $value;
                } else {
                    $res[] = $key;
                }
            }
        }
        return $res;
    }
}

if (!function_exists('array_filter_recursive')) {

    /**
     * array_filter_recursive 清除多维数组里面的空值
     * @param array $arr
     * @return array
     * @author   liuml
     * @DateTime 2018/12/3  11:27
     */
    function array_filter_recursive(array &$arr)
    {
        if (count($arr) < 1) {
            return [];
        }
        foreach ($arr as $k => $v) {
            if (is_array($v)) {
                $arr[$k] = array_filter_recursive($v);
            }
            if (is_null($arr[$k]) && $arr[$k] == '') {
                unset($arr[$k]);
            }
        }
        return $arr;
    }
}

if (!function_exists('base64_encode_image')) {
    /**
     * 图片转成base64
     *
     * @param $image
     * @return string
     */
    function base64_encode_image($image)
    {
        try {
            $image_info = getimagesize($image);
            $base64 = "" . chunk_split(base64_encode(file_get_contents($image)));
            return 'data:' . $image_info['mime'] . ';base64,' . chunk_split(base64_encode(file_get_contents($image)));
        } catch (Exception $e) {
            return $image;
        }
    }
}

if (!function_exists('base64_image_content')) {
    /**
     * base64格式编码转换为图片并保存对应文件夹
     *
     * @param $base64_image_content
     * @param $path
     * @return bool|string
     */
    function base64_image_content($base64_image_content, $path)
    {
        //匹配出图片的格式
        if (preg_match('/^(data:\s*image\/(\w+);base64,)/', $base64_image_content, $result)) {
            $type = $result[2];
            $new_file = $path . "/" . date('Ymd', time()) . "/";
            if (!file_exists($new_file)) {
                //检查是否有该文件夹，如果没有就创建，并给予最高权限
                mkdir($new_file, 0700);
            }
            $new_file = $new_file . time() . ".{$type}";
            if (file_put_contents($new_file, base64_decode(str_replace($result[1], '', $base64_image_content)))) {
                return '/' . $new_file;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }
}

if (!function_exists('md5_uniqid')) {
    /**
     * md5 uniqid
     *
     * @return string
     */
    function md5_uniqid()
    {
        return md5(uniqid());
    }
}

if (!function_exists('filter_number_format')) {
    /**
     * 金额默认过滤器
     *
     * @param $number
     * @param int $decimals
     * @param string $dec_point
     * @param string $thousands_sep
     * @return string
     */
    function filter_number_format($number, $decimals = 2, $dec_point = '.', $thousands_sep = '')
    {
        return number_format($number, $decimals, $dec_point, $thousands_sep);
    }
}

if (!function_exists('is_mobile_number')) {
    function is_mobile_number($str)
    {
        $pattern = '/^1\d{10}$/';
        if (preg_match($pattern, $str)) {
            return true;
        } else {
            return false;
        }
    }
}

if (!function_exists('is_email')) {
    function is_email($str, $strict = false)
    {
        if ($strict) {
            // $pattern = '/^\w[-\w.+]*@([A-Za-z0-9][-A-Za-z0-9]+\.)+[A-Za-z]{2,14}$/';
            // @前面的字符可以是英文字母和._- ，._-不能放在开头和结尾，且不能连续出现
            $pattern = '/^[a-z0-9]+([._-][a-z0-9]+)*@([0-9a-z]+\.[a-z]{2,14}(\.[a-z]{2})?)$/i';
            if (preg_match($pattern, $str)) {
                return true;
            } else {
                return false;
            }
        } else {
            return filter_var($str, FILTER_VALIDATE_EMAIL) ? true : false;
        }
    }
}

if (!function_exists('get_client_os')) {
    /**
     * 获取用户设备操作系统类型
     * @return string
     */
    function get_client_os()
    {
        $agent = isset($_SERVER['HTTP_USER_AGENT']) ? strtolower($_SERVER['HTTP_USER_AGENT']) : '';
        switch ($agent) {
            case strpos($agent, 'windows nt') !== false:
                $platform = 'Windows';
                break;
            case strpos($agent, 'macintosh') !== false:
                $platform = 'Mac';
                break;
            case strpos($agent, 'ipod') !== false:
                $platform = 'IPod';
                break;
            case strpos($agent, 'ipad') !== false:
                $platform = 'IPad';
                break;
            case strpos($agent, 'iphone') !== false:
                $platform = 'iPhone';
                break;
            case strpos($agent, 'android') !== false:
                $platform = 'Android';
                break;
            case strpos($agent, 'unix') !== false:
                $platform = 'Unix';
                break;
            case strpos($agent, 'linux') !== false:
                $platform = 'Linux';
                break;
            default:
                $platform = 'Other';
        }
        return $platform;
    }
}

if (!function_exists('get_client_browser')) {
    /**
     * 获取客户端浏览器以及版本号
     * @param string $agent //$_SERVER['HTTP_USER_AGENT']
     * @param string $return_type
     * @return array|mixed
     */
    function get_client_browser($agent = '', $return_type = 'string')
    {
        $browser = '';
        $browser_ver = '';
        if (preg_match('/OmniWeb\/(v*)([^\s|;]+)/i', $agent, $regs)) {
            $browser = 'OmniWeb';
            $browser_ver = $regs[2];
        }
        if (preg_match('/Netscape([\d]*)\/([^\s]+)/i', $agent, $regs)) {
            $browser = 'Netscape';
            $browser_ver = $regs[2];
        }
        if (preg_match('/safari\/([^\s]+)/i', $agent, $regs)) {
            $browser = 'Safari';
            $browser_ver = $regs[1];
        }
        if (preg_match('/MSIE\s([^\s|;]+)/i', $agent, $regs)) {
            $browser = 'Internet Explorer';
            $browser_ver = $regs[1];
        }
        if (preg_match('/Opera[\s|\/]([^\s]+)/i', $agent, $regs)) {
            $browser = 'Opera';
            $browser_ver = $regs[1];
        }
        if (preg_match('/NetCaptor\s([^\s|;]+)/i', $agent, $regs)) {
            $browser = '(Internet Explorer ' . $browser_ver . ') NetCaptor';
            $browser_ver = $regs[1];
        }
        if (preg_match('/Maxthon/i', $agent, $regs)) {
            $browser = '(Internet Explorer ' . $browser_ver . ') Maxthon';
            $browser_ver = '';
        }
        if (preg_match('/360SE/i', $agent, $regs)) {
            $browser = '(Internet Explorer ' . $browser_ver . ') 360SE';
            $browser_ver = '';
        }
        if (preg_match('/SE 2.x/i', $agent, $regs)) {
            $browser = '(Internet Explorer ' . $browser_ver . ') 搜狗';
            $browser_ver = '';
        }
        if (preg_match('/FireFox\/([^\s]+)/i', $agent, $regs)) {
            $browser = 'FireFox';
            $browser_ver = $regs[1];
        }
        if (preg_match('/Lynx\/([^\s]+)/i', $agent, $regs)) {
            $browser = 'Lynx';
            $browser_ver = $regs[1];
        }
        if (preg_match('/Chrome\/([^\s]+)/i', $agent, $regs)) {
            $browser = 'Chrome';
            $browser_ver = $regs[1];
        }
        if (preg_match('/MicroMessenger\/([^\s]+)/i', $agent, $regs)) {
            $browser = 'WeChat';
            $browser_ver = $regs[1];
        }
        if ($browser != '') {
            $result = ['browser' => $browser, 'browser_ver' => $browser_ver];
        } else {
            $result = ['browser' => 'Unknown', 'browser_ver' => ''];
        }
        return $return_type != 'string' ? $result : $result['browser'];
    }
}

if (!function_exists('is_mobile')) {
    /**
     * 判断访问客户端是否是移动端
     *
     * @return bool
     */
    function is_mobile()
    {
        // 如果有HTTP_X_WAP_PROFILE则一定是移动设备
        if (isset($_SERVER['HTTP_X_WAP_PROFILE'])) {
            return true;
        }
        // 如果via信息含有wap则一定是移动设备,部分服务商会屏蔽该信息
        if (isset($_SERVER['HTTP_VIA'])) {
            // 找不到为flase,否则为true
            return stristr($_SERVER['HTTP_VIA'], "wap") ? true : false;
        }
        // 判断手机发送的客户端标志,兼容性有待提高。其中'MicroMessenger'是电脑微信
        if (isset($_SERVER['HTTP_USER_AGENT'])) {
            $clientkeywords = array(
                'nokia', 'sony', 'ericsson', 'mot', 'samsung', 'htc', 'sgh', 'lg', 'sharp', 'sie-', 'philips',
                'panasonic', 'alcatel', 'lenovo', 'iphone', 'ipod', 'blackberry', 'meizu', 'android', 'netfront',
                'symbian', 'ucweb', 'windowsce', 'palm', 'operamini', 'operamobi', 'openwave', 'nexusone', 'cldc',
                'midp', 'wap', 'mobile', 'MicroMessenger'
            );
            // 从HTTP_USER_AGENT中查找手机浏览器的关键字
            if (preg_match("/(" . implode('|', $clientkeywords) . ")/i", strtolower($_SERVER['HTTP_USER_AGENT']))) {
                return true;
            }
        }
        // 协议法，因为有可能不准确，放到最后判断
        if (isset ($_SERVER['HTTP_ACCEPT'])) {
            // 如果只支持wml并且不支持html那一定是移动设备
            // 如果支持wml和html但是wml在html之前则是移动设备
            if ((strpos($_SERVER['HTTP_ACCEPT'], 'vnd.wap.wml') !== false) && (strpos($_SERVER['HTTP_ACCEPT'], 'text/html') === false || (strpos($_SERVER['HTTP_ACCEPT'], 'vnd.wap.wml') < strpos($_SERVER['HTTP_ACCEPT'], 'text/html')))) {
                return true;
            }
        }
        return false;
    }
}

if (!function_exists('browser_detect_is_weixin')) {
    /**
     * 判断访问客户端是否是微信浏览器
     *
     * @return bool
     */
    function browser_detect_is_weixin()
    {
        if (strpos($_SERVER['HTTP_USER_AGENT'], 'MicroMessenger') !== false) {
            return true;
        } else {
            return false;
        }
    }
}
if (!function_exists('array_diff_assoc_recursive')) {

    /**
     * 多维素组差集
     *
     * @param $array1
     * @param $array2
     * @return array
     */
    function array_diff_assoc_recursive($array1, $array2)
    {
        $diff_array = array();
        foreach ($array1 as $key => $value) {
            //判断数组每个元素是否是数组
            if (is_array($value)) {
                //判断第二个数组是否存在key
                if (!isset($array2[$key])) {
                    $diff_array[$key] = $value;
                    //判断第二个数组key是否是一个数组
                } elseif (!is_array($array2[$key])) {
                    $diff_array[$key] = $value;
                } else {
                    $diff = array_diff_assoc_recursive($value, $array2[$key]);
                    if ($diff != false) {
                        $diff_array[$key] = $diff;
                    }
                }
            } elseif (!array_key_exists($key, $array2) || $value !== $array2[$key]) {
                $diff_array[$key] = $value;
            }
        }
        return $diff_array;
    }

}

if (!function_exists('mk_folder')) {
    /*
     * 判断目录是否存在, 不存在则创建目录
     */
    function mk_folder($dir, $mode = 0777)
    {
        return is_dir($dir) or (mk_folder(dirname($dir)) and mkdir($dir, $mode, true));
    }
}

if (!function_exists('str_cut')) {
    /*
     * 截取指定两个字符之间字符串, 没有返回默认值
     * */
    function str_cut($str, $begin, $end)
    {
        $b = mb_strpos($str, $begin) + mb_strlen($begin);
        $e = mb_strpos($str, $end) - $b;
        return mb_substr($str, $b, $e);
    }
}

if (!function_exists('str_between')) {
    /*
     * 截取指定两个字符之间字符串, 有返回默认值
     * */
    function str_between($input, $start, $end, $default = '')
    {
        $substr = substr($input, strlen($start) + strpos($input, $start), (strlen($input) - strpos($input, $end)) * (-1));
        return $substr ?: $default;
    }
}

if (!function_exists('cut_str')) {
    /**
     * 按符号截取字符串的指定部分
     * @param string $str 需要截取的字符串
     * @param string $sign 需要截取的符号
     * @param int $number 如是正数以0为起点从左向右截  负数则从右向左截
     * @return string 返回截取的内容
     */
    function cut_str($str, $sign, $number)
    {
        $array = explode($sign, $str);
        $length = count($array);
        if ($number < 0) {
            $new_array = array_reverse($array);
            $abs_number = abs($number);
            if ($abs_number > $length) {
                return 'error';
            } else {
                return $new_array[$abs_number - 1];
            }
        } else {
            if ($number >= $length) {
                return 'error';
            } else {
                return $array[$number];
            }
        }
    }
}

if (!function_exists('str_n_pos')) {
    /*
     * 查找某字符串在另一个字符串中,第n次出现的位置
     */
    function str_n_pos($str, $find, $count, $offset = 0)
    {
        $pos = stripos($str, $find, $offset);
        $count--;
        if ($count > 0 && $pos !== FALSE) {
            $pos = str_n_pos($str, $find, $count, $pos + 1);
        }
        return $pos;
    }
}

if (!function_exists('file_content_replace')) {

    function file_content_replace($filename, $search, $replace)
    {
        $string = file_get_contents($filename);
        $new_string = str_replace($search, $replace, $string);
        if ($string != $new_string) file_put_contents($filename, $new_string);
    }
}

if (!function_exists('find_all_dir')) {
    /*
     * 批量修改路径下所有文件内容
     */
    function find_all_dir($dir, $research = array(), $replace = array())
    {
        //找到目录下的所有文件：
        $dh = opendir($dir);
        while ($file = readdir($dh)) {
            if ($file != "." && $file != "..") {
                $fullpath = $dir . "/" . $file;
                if (!is_dir($fullpath)) {
                    $f = fopen($fullpath, 'r');
                    $text = fread($f, filesize($fullpath));
                    //对内容进行修改
                    $text = str_replace($research, $replace, $text);
                    //判断结果
                    $result = file_put_contents($fullpath, $text);
                } else {
                    find_all_dir($fullpath);
                }
            }
        }
        closedir($dh);
    }
}

if (!function_exists('scan_dirs')) {
    /**
     * 获取目录中所有文件的路径
     * @param $dir
     * @return array
     */
    function scan_dirs($dir)
    {
        if (is_dir($dir)) {
            $files = array();
            $child_dirs = scandir($dir);
            foreach ($child_dirs as $child_dir) {
                //'.'和'..'是Linux系统中的当前目录和上一级目录，必须排除掉，
                //否则会进入死循环，报segmentation falt 错误
                if ($child_dir != '.' && $child_dir != '..') {
                    if (is_dir($dir . DIRECTORY_SEPARATOR . $child_dir)) {
                        $files[$child_dir] = scan_dirs($dir . DIRECTORY_SEPARATOR . $child_dir);
                    } else {
                        $files[] = $child_dir;
                    }
                }
            }
            return $files;
        } else {
            return $dir;
        }
    }
}

if (!function_exists('filter_array')) {
    /**
     * 去除多维数组中的空值
     * @param $arr $arr
     * @param array $values 去除的值  默认 去除  '',null,false,0,'0',[]
     * @return mixed
     * @author
     */
    function filter_array($arr, $values = ['', null, false, 0, '0', []])
    {
        foreach ($arr as $k => $v) {
            if (is_array($v) && count($v) > 0) {
                $arr[$k] = filter_array($v, $values);
            }
            foreach ($values as $value) {
                if ($v === $value) {
                    unset($arr[$k]);
                    break;
                }
            }
        }
        return $arr;
    }
}

if (!function_exists('str_participle')) {
    /**
     * 字符串分词
     *
     * @param $str
     * @return array
     */
    function str_participle($str)
    {
        //找出字符串中的英文单词和数字
        if (preg_match_all('%[A-Za-z0-9_-]{1,}%', $str, $matches)) {
            $arr = $matches[0];
        }
        //以非中文(中文包括简体和繁体)进行正则分割
        $sections = preg_split('%[^\x{4e00}-\x{9fa5}]{1,}%u', $str);
        foreach ($sections as $v) {
            //注意:foreach中多次正则匹配会降低性能
            switch (true) {
                case ($v === ''):
                    //continue; // TODO: php7.3版本bug
                    break;
                case (mb_strlen($v, 'UTF-8') < 3):
                    $arr[] = $v;
                    break;
                case (preg_match_all('%[\x{4e00}-\x{9fa5}]%u', $v, $matches)):
                    //前后俩俩组合,实现冗余分词.
                    //如"中国好声音"将被分词为: 中国 国好 好声 声音
                    $size = count($matches[0]);
                    for ($i = 0; $i <= $size - 2; $i++) {
                        $word = '';
                        for ($j = 0; $j < 2; $j++) {
                            $word .= $matches[0][$i + $j]; //echo $i.' '.$j.' '.$matches[0][$i+$j]."\n";
                        }
                        $arr[] = $word; //echo "\n";
                    }
                    break;
            }
        }
        return array_unique($arr);
    }
}

if (!function_exists('code62')) {
    function code62($x)
    {
        $show = '';
        while ($x > 0) {
            $s = $x % 62;
            if ($s > 35) {
                $s = chr($s + 61);
            } elseif ($s > 9 && $s <= 35) {
                $s = chr($s + 55);
            }
            $show .= $s;
            $x = floor($x / 62);
        }
        return $show;
    }
}

if (!function_exists('short_url')) {
    /**
     * 短链接生成
     *
     * @param $url
     * @return string
     */
    function short_url($url)
    {
        $url = crc32($url);
        $result = sprintf("%u", $url);
        return code62($result);
    }
}

if (!function_exists('php_run_path')) {
    /*
     * 获取php运行路径
     */
    function php_run_path()
    {
        if (substr(strtolower(PHP_OS), 0, 3) == 'win') {
            $ini = ini_get_all();
            $path = $ini['extension_dir']['local_value'];
            $php_path = str_replace('\\', '/', $path);
            $php_path = str_replace(array('/ext/', '/ext'), array('/', '/'), $php_path);
            $real_path = $php_path . 'php.exe';
        } else {
            $real_path = PHP_BINDIR . '/php';
        }
        if (strpos($real_path, 'ephp.exe') !== FALSE) {
            $real_path = str_replace('ephp.exe', 'php.exe', $real_path);
        }
        return isset($real_path) ? $real_path : 'php';
    }
}

if (!function_exists('number_calc_proportion')) {
    /*
     * 计算占百分比
     */
    function number_calc_proportion($sum, $row, $decimals = 2, $dec_point = '.', $thousands_sep = '')
    {
        return $sum != 0 ? @number_format($row / $sum * 100, $decimals, $dec_point, $thousands_sep) : 0;
    }
}

if (!function_exists('number_calc_percentage')) {
    /*
     * 计算增减百分比
     */
    function number_calc_percentage($sum, $row, $decimals = 2, $dec_point = '.', $thousands_sep = '')
    {
        $percentage = @number_format((($sum - $row) / $sum) * 100, $decimals, $dec_point, $thousands_sep);
        if ($percentage > 0) {
            return '-' . $percentage;
        } elseif ($percentage < 0) {
            return str_replace("-", "", $percentage);
        } else {
            return 0;
        }
    }
}

if (!function_exists('get_ip_lookup')) {
    /*
     * 根据IP地址获取其地理位置（省份,城市等）
     */
    function get_ip_lookup($ip = null, $query = [])
    {
        if (is_null($ip)) {
            return false;
        }

        $base_uri = 'http://ip-api.com/php/';
        $url = $base_uri . $ip;

        $query = array_merge([], $query);
        if ($query = http_build_query($query)) {
            $url .= strpos($url, '?') ? $query : "?{$query}";
        }

        $res = @file_get_contents($url);
        return unserialize($res);
    }
}

if (!function_exists('get_base_url')) {

    function get_base_url()
    {
        $base_url = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') ? "https://" : "http://";
        $base_url .= $_SERVER["SERVER_NAME"];
        $base_url .= ($_SERVER["SERVER_PORT"] == "80") ? "" : (":" . $_SERVER["SERVER_PORT"]);

        return $base_url;
    }
}

if (!function_exists('get_current_url')) {

    function get_current_url()
    {
        return get_base_url() . $_SERVER["REQUEST_URI"];
    }
}

if (!function_exists('in_host')) {
    /**
     * HOST访问限制 支持 IP(单IP,多IP,*通配符,IP段) 域名(单域名,多域名,*通配符)
     * 根据判断实现IP地址 白名单黑名单
     * @param string $host 当前host 127.0.0.2
     * @param string $list 允许的host列表 127.0.0.*,192.168.1.1,192.168.1.70,127.1.1.33-127.1.1.100
     * @return boolean
     */
    function in_host($host, $list)
    {
        $list = ',' . $list . ',';
        $is_in = false;
        // 1.判断最简单的情况
        $is_in = strpos($list, ',' . $host . ',') === false ? false : true;

        // 2.判断通配符情况
        if (!$is_in && strpos($list, '*') !== false) {
            $hosts = array();
            $hosts = explode('.', $host);
            // 组装每个 * 通配符的情况
            foreach ($hosts as $k1 => $v1) {
                $host_now = '';
                foreach ($hosts as $k2 => $v2) {
                    $host_now .= ($k2 == $k1 ? '*' : $v2) . '.';
                }
                // 组装好后进行判断
                if (strpos($list, ',' . substr($host_now, 0, -1) . ',') !== false) {
                    $is_in = true;
                    break;
                }
            }
        }

        // 3.判断IP段限制
        if (!$is_in && strpos($list, '-') !== false) {
            $lists = explode(',', trim($list, ','));
            $host_long = ip2long($host);
            foreach ($lists as $k => $v) {
                if (strpos($v, '-') !== false) {
                    list ($host1, $host2) = explode('-', $v);
                    if ($host_long >= ip2long($host1) && $host_long <= ip2long($host2)) {
                        $is_in = true;
                        break;
                    }
                }
            }
        }
        return $is_in;
    }
}

if (!function_exists('descartes')) {
    /**
     * 计算多个集合的笛卡尔积
     *
     * @param $sets
     * @return array
     */
    function descartes($sets)
    {
        // 保存结果
        $result = [];

        // 循环遍历集合数据
        for ($i = 0, $count = count($sets); $i < $count - 1; $i++) {

            // 初始化
            if ($i == 0) {
                $result = $sets[$i];
            }

            // 保存临时数据
            $tmp = [];

            // 结果与下一个集合计算笛卡尔积
            foreach ($result as $res) {
                foreach ($sets[$i + 1] as $set) {
                    $tmp[] = $res . $set;
                }
            }

            // 将笛卡尔积写入结果
            $result = $tmp;

        }

        return $result;
    }
}
// TODO: 生成树类结构有bug
if (!function_exists('array_tree')) {
    /**
     * 核心函数, 将列表数据转化树形结构
     * 使用前提必须是先有父后有子, 即儿子的id必须小于父亲id
     * 列表数据必须安装id从小到大排序
     * @param $array $array
     * @param string $parentKey
     * @param string $childKey 字段名
     * @return array 返回树形数据
     */
    function array_tree($array, $parentKey = 'parent_id', $childKey = 'children')
    {
        $map = [];
        $res = [];
        foreach ($array as $id => &$item) {
            // 获取出每一条数据的父id
            $parent_id = &$item[$parentKey];
            // 将每一个item的引用保存到$map中
            $map[$item['id']] = &$item;
            // 如果在map中没有设置过他的parent_id, 说明是根节点, parent_id为0,
            if (!isset($map[$parent_id])) {
                // 将parent_id为0的item的引用保存到$res中
                $res[$id] = &$item;
            } else {
                // 如果在map中没有设置过他的parent_id, 则将该item加入到他父亲的叶子节点中
                $pItem = &$map[$parent_id];
                $pItem[$childKey][] = &$item;
            }
        }
        return $res;
    }
}

if (!function_exists('array_tree_parents')) {

    /**
     * 递归实现无限极算法
     *
     * @param $array
     * @param int $pid
     * @param int $level
     * @param string $id_code
     * @param string $parent_code
     * @param string $level_code
     * @return array 二维数组
     */
    function array_tree_parents($array, $pid = 0, $level = null, $id_code = 'id', $parent_code = 'parent_id', $level_code = 'level')
    {
        //声明静态数组,避免递归调用时,多次声明导致数组覆盖
        //static $list = [];
        $subs = []; //如果不适用static，可以和array_merge搭配使用

        foreach ($array as $key => $value) {
            //第一次遍历,找到父节点为根节点的节点 也就是pid=0的节点
            if ($value[$parent_code] == $pid) {
                if (!empty($level)) {
                    //父节点为根节点的节点,级别为0，也就是第一级
                    $value[$level_code] = $level;
                } else {
                    $value[$level_code] = 0;
                }

                //把数组放到list中
                //$list[] = $value;
                $subs[] = $value;

                //把这个节点从数组中移除,减少后续递归消耗
                unset($array[$key]);
                //开始递归,查找父ID为该节点ID的节点,级别则为原级别+1
                //$this->getTree($array, $value[$id_code], $level + 1);
                $subs = array_merge($subs, array_tree_parents($array, $value[$id_code], $level + 1));
            }
        }
        //return $list;
        return $subs;
    }
}

if (!function_exists('array_tree_parents_format')) {
    function array_tree_parents_format($array, $make = '|---', $name_code = 'name')
    {
        $item = [];
        foreach ($array as $value) {
            $cate = [
                'id' => $value['id'],
                'name' => str_repeat($make, $value['level']) . $value[$name_code],
            ];
            array_push($item, $cate);
        }
        return $item;
    }
}

if (!function_exists('array_delete_value')) {
    /*
     * 根据值删除指定元素
     */
    function array_delete_value(&$arr, $value)
    {
        if (!is_array($arr)) {
            return $arr;
        }
        foreach ($arr as $k => $v) {
            if ($v == $value) {
                unset($arr[$k]);
            }
        }
        return true;
    }
}

if (!function_exists('exec_in_background')) {
    /*
     * 后台执行命令
     */
    function exec_in_background($cmd)
    {
        if (substr(php_uname(), 0, 7) == "Windows") {
            pclose(popen("chcp 65001 && start /B " . $cmd, "r"));
        } else {
            exec($cmd . " > /dev/null &");
        }
    }
}

if (!function_exists('class_basename')) {
    /**
     * Get the class "basename" of the given object / class.
     *
     * @param string|object $class
     * @return string
     */
    function class_basename($class)
    {
        $class = is_object($class) ? get_class($class) : $class;

        return basename(str_replace('\\', '/', $class));
    }
}

if (!function_exists('generate_product_code')) {
    /*
     * 生成商品Code
     */
    function generate_product_code($prefix = '', $str = '', $length = 16, $pad_string = '0')
    {
        $pad_length = $length - strlen($prefix);
        return $prefix . str_pad($str, $pad_length, $pad_string, STR_PAD_LEFT);
    }
}

if (!function_exists('parse_version')) {
    /*
     * 解析版本号
     */
    function parse_version($string)
    {
        $version_pattern = '([0-9.]+)';

        if (preg_match("/^\s*$version_pattern\s*\$/x", $string, $regs)) {
            return array('min' => $regs[1] ?: '0.0.0');
        } elseif (preg_match("/^\s*[>=]+\s*$version_pattern\s*\$/x", $string, $regs)) {
            return array('min' => $regs[1] ?: '0.0.0');
        } elseif (preg_match("/^\s*[<=]+\s*$version_pattern\s*\$/x", $string, $regs)) {
            return array('max' => $regs[1]);
        } elseif (preg_match("/^\s*$version_pattern\s*<=>\s*$version_pattern\s*\$/x", $string, $regs)) {
            return array(
                'min' => $regs[1] ?: '0.0.0',
                'max' => $regs[2],
            );
        }

        return null;
    }
}

if (!function_exists('version_require')) {
    /*
     * 版本号依赖检查
     */
    function version_require($current_version, $require_version)
    {
        $parse_version = parse_version($require_version);
        if (isset($parse_version['min'])) {
            return version_compare($current_version, $parse_version['min'], '>=');
        }
        if (isset($parse_version['max'])) {
            return version_compare($current_version, $parse_version['max'], '<=');
        }
        return false;
    }
}
if (!function_exists('array_to_object')) {
    /**
     * 数组 转 对象
     *
     * @param array $arr 数组
     * @return object
     */
    function array_to_object($arr)
    {
        if (gettype($arr) != 'array') {
            return;
        }
        foreach ($arr as $k => $v) {
            if (gettype($v) == 'array' || getType($v) == 'object') {
                $arr[$k] = (object)array_to_object($v);
            }
        }

        return (object)$arr;
    }
}

if (!function_exists('object_to_array')) {
    /**
     * 对象 转 数组
     *
     * @param object $obj 对象
     * @return array
     */
    function object_to_array($obj)
    {
        $obj = (array)$obj;
        foreach ($obj as $k => $v) {
            if (gettype($v) == 'resource') {
                return;
            }
            if (gettype($v) == 'object' || gettype($v) == 'array') {
                $obj[$k] = (array)object_to_array($v);
            }
        }

        return $obj;
    }
}

if (!function_exists('price_format')) {
    /**
     * 金额格式化
     *
     * @param $price
     * @return string
     */
    function price_format($price)
    {
        if (!is_numeric($price)) {
            $price = 0;
        }

        return number_format((string)$price, 2, '.', '');
    }
}

if (!function_exists('file_size_format')) {
    /**
     * 文件大小格式化
     *
     * @param $size
     * @return string
     */
    function file_size_format($size = 0)
    {
        if (empty($size)) {
            return 0;
        }

        if (!is_numeric($size)) {
            return 0;
        }

        if ($size < 1024) {
            return 0;
        }

        $unit = ['B', 'KB', 'MB', 'GB'];

        for ($i = 0; $size >= 1024 && $i <= 4; $i++) {
            $size /= 1024;
        }
        return round($size, 2) . $unit[$i];
    }
}
