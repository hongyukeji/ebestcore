<?php

namespace System\Utils\Util\Libs;

class ShortUrl
{
    /**
     * 长网址转短网址
     *
     * @param $url
     * @return string
     */
    public function longToShortUrl($url)
    {
        if ($this->isUrl($url)) {
            return $this->generate($url);
        } else {
            return false;
        }
    }

    public function generate($url)
    {
        $url = crc32($url);
        $result = sprintf("%u", $url);
        return code62($result);
    }

    public function code62($x)
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

    public function isUrl($url)
    {
        $preg = "/^http(s)?:\\/\\/.+/";
        if (preg_match($preg, $url)) {
            return true;
        } else {
            return false;
        }

    }
}