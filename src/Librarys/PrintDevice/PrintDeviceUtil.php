<?php

namespace System\Librarys\PrintDevice;

// 字体大小
const JsonLi_FontSizeSmall = "\x00";
const JsonLi_FontSizeMiddle = "\x11";
const JsonLi_FontSizeBig = "\x22";

// 对齐方式
const JsonLi_Left = "\x00";
const JsonLi_Center = "\x01";
const JsonLi_Right = "\x02";

const JsonLi_WIDTH_PIXEL = 384;
const JsonLi_IMAGE_SIZE = 320;

class PrintDeviceUtil
{
    public $printWhiteString;

    function __construct()
    {
        $this->initPrintDevice();
    }

    /**
     * 初始化打印机
     */
    protected function initPrintDevice()
    {
        //$this->printWhiteString = "\x1b\x40\x1b\x32\x1b\x4d\x00";
        $this->printWhiteString = "\x1b\x40";

    }

    /**
     * 设置字体大小
     */
    protected function setTextFont($size)
    {
        $this->printWhiteString .= "\x1d" . "\x21" . $size;
    }

    /**
     * 设置对齐方式
     */
    protected function setAlignment($alignment)
    {
        $this->printWhiteString .= "\x1b" . "\x61" . $alignment;
    }

    /**
     * 换一行
     */
    protected function addNewLine()
    {
        $this->addMuiltNewLine(1);
    }

    /**
     * 换多行
     */
    protected function addMuiltNewLine($linenum)
    {
        for ($i = 0; $i < $linenum; $i++) {
            $this->printWhiteString .= "\x0A";
        }
    }

    /*
     * 添加分割线
     */
    protected function addDividingLine()
    {
        $this->setAlignment(JsonLi_Center);
        $this->setTextFont(JsonLi_FontSizeSmall);
        $this->printWhiteString .= "- - - - - - - - - - - - - - - -";
        $this->addNewLine();
    }

    /**
     * 设置文本
     */
    protected function setText($text)
    {
        $this->printWhiteString .= iconv("UTF-8", "GB2312//IGNORE", $text);
    }

    /*
     * 获取偏移量
     */
    protected function getOffset($str)
    {
        return JsonLi_WIDTH_PIXEL - mb_strwidth($str);
    }

    /*
     * 设置偏移量
     */
    protected function setOffset($str)
    {
        $offset = $this->getOffset($str);
        $remainder = $offset % 256; // 低位
        $consult = $offset >> 8; // 高位
        $offsetBytes = "\x1B\x24" . chr($remainder) . chr($consult);
        $this->printWhiteString .= $offsetBytes;
    }

    public function testPrint()
    {
        $this->setAlignment(JsonLi_Center);
        $this->setTextFont(JsonLi_FontSizeBig);
        $this->setText("泛客云商");
        $this->addNewLine();

        $this->addDividingLine();

        $this->setAlignment(JsonLi_Left);
        $this->setTextFont(JsonLi_FontSizeSmall);
        $this->setText("时间：");

        $value = "2018-01-01";
        $this->setOffset($value);
        $this->setText($value);
        $this->addNewLine();

        $this->setAlignment(JsonLi_Left);
        $this->setTextFont(JsonLi_FontSizeSmall);
        $this->setText("订单号：");
        $this->setAlignment(JsonLi_Right);
        $this->setTextFont(JsonLi_FontSizeSmall);
        $this->setText("XXXXXXXXXXX");
        $this->addNewLine();

        $this->setAlignment(JsonLi_Left);
        $this->setTextFont(JsonLi_FontSizeSmall);
        $this->setText("付款人：");
        $this->setAlignment(JsonLi_Right);
        $this->setTextFont(JsonLi_FontSizeSmall);
        $this->setText("XXXXXXXXXXX");
        $this->addNewLine();

        $this->addDividingLine();

        $this->addMuiltNewLine(3);

//        $result = $this->printWhiteString . iconv("UTF-8","GB2312//IGNORE","泛客云商") ;
//        $printWhiteString = $printWhiteString . "测试测试";

        //return $this->printWhiteString;
        return base64_encode($this->printWhiteString);

//        return strlen("我爱上海");

//        $hex = '';
//        for ($i = 0; $i < strlen($this->printWhiteString); $i++) {
//            $hex .= sprintf("%02x", ord($this->printWhiteString[$i]));
//        }
//
//        $hex .= PHP_EOL;
//
//        return $hex;

//        return $this->printWhiteString;
    }

}

//$p = new PrintDeviceUtil;
//echo $p->testPrint();
