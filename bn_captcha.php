<?php

/**
 * @author lord_viper
 * @copyright 2013
 */

class bn_captcha
{
    private $font_size    = 5;
    private $fontfilename = '';
    private $bg_color     = array(255,255,255);
    private $text_color   = array(0,0,0);
    private $line         = 0;
    private $noise        = 0;
    private $elipse       = 0;
    private $elfill       = false;
    private $text         = '';
    private $img;
    protected static $instance;

    /**
     * bn_captcha::__construct()
     *
     * @return
     */
    function __construct()
    {
        if (session_status() == PHP_SESSION_NONE)
        //if (!isset($_SESSION))
        session_start();
    }

    /**
     * bn_captcha::instance()
     *
     * @return
     */
    public static function instance()
    {
        if(!isset(self::$instance))
        self::$instance = new self();

        return self::$instance;
    }

    /**
     * bn_captcha::RandomString()
     *
     * @param integer $length
     * @param string $type
     * @param integer $repeat
     * @return
     */
    private function RandomString($length = 10, $type = 'char',$repeat=2)
    {
        $Special = '!@#$%^&*()-_ []{}<>~+=,.;:/?|';
        $number  = '0123456789';
        $chars   = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        switch($type)
        {
            case 'all' :$str = $Special.$number.$chars;break;
            case 'char':$str = $chars;break;
            case 'num' :$str = $number;break;
            default    :$str = $chars.$number;
        }
        return substr(str_shuffle(str_repeat($str,$repeat)),0,$length);
    }

    /**
     * bn_captcha::line()
     *
     * @param mixed $count
     * @return
     */
    public function line($count)
    {
        $this->line = $count;
        return $this;
    }

    /**
     * bn_captcha::noise()
     *
     * @param mixed $count
     * @return
     */
    public function noise($count)
    {
        $this->noise = $count;
        return $this;
    }

    /**
     * bn_captcha::font()
     *
     * @param mixed $fontname
     * @param mixed $fontsize
     * @return
     */
    public function font($fontname,$fontsize)
    {
        $this->fontfilename = $fontname;
        $this->font_size    = $fontsize;
        return $this;
    }

    /**
     * bn_captcha::ellipse()
     *
     * @param mixed $count
     * @param bool $fill
     * @return
     */
    public function ellipse($count,$fill=false)
    {
        $this->elipse = $count;
        $this->elfill = $fill;
        return $this;
    }

    /**
     * bn_captcha::text()
     *
     * @param string $text
     * @return
     */
    public function text($text='')
    {
        $this->text = $text;
        return $this;
    }

    /**
     * bn_captcha::show()
     *
     * @param integer $text_count
     * @param string $text_type
     * @param string $bgcolor
     * @param string $txtcolor
     * @param bool $echo
     * @return
     */
    public function show($text_count=5,$text_type='all',$bgcolor='#000',$txtcolor='#FFFFFF',$echo=true)
    {
        if(empty($this->text)){
            $this->text    = self::RandomString($text_count,$text_type);
            $_SESSION['__captcha'] = strtolower($this->text);
            $this->text    = wordwrap($this->text,1,' ',true) ;
        }
        $size = self::get_img_size();
        $this->img = imagecreatetruecolor($size['width'],$size['height']);
        self::draw_ellipse($size);
        self::draw_line($size);
        imagefill($this->img,0,0,hexdec($bgcolor));
        if(empty($this->fontfilename))
            imagestring($this->img,5,10,rand(5,15),$this->text,hexdec($txtcolor));
        else
            imagettftext($this->img,$this->font_size,0,10,rand($size['height']-15,$size['height']-5),hexdec($txtcolor),$this->fontfilename,$this->text);
        self::draw_noise($size);
        ob_start();
        ImagePng($this->img);
        $img = base64_encode(ob_get_clean());
        if($echo)
        echo "<img src='data:image/jpeg;base64,$img' />";
        else
        return "<img src='data:image/jpeg;base64,$img' />";
    }

    /**
     * bn_captcha::check_captcha()
     *
     * @param mixed $post
     * @return
     */
    public function check_captcha($post)
    {
        return ($post==$_SESSION['__captcha']?true:false);
    }
//______________________________________________________________________________________________________________________________
    /**
     * bn_captcha::get_img_size()
     *
     * @return
     */
    private function get_img_size()
    {
        $ret = array();
        if(empty($this->fontfilename)){
            $w    = imagefontwidth($this->font_size);
            $h    = imagefontheight($this->font_size);
            $ret['width'] = ($w * strlen($this->text)) + 20;
            $ret['height'] = $h + 20;
        }else{
            $siz = imagettfbbox($this->font_size,0,$this->fontfilename,$this->text);
            $ret['width'] = $siz[4]+20;
            $ret['height'] = abs($siz[7])+20;
        }
        return $ret;
    }

    /**
     * bn_captcha::draw_line()
     *
     * @param mixed $size
     * @return
     */
    private function draw_line($size)
    {
        if($this->line>0){
            $width  = $size['width'];
            $height = $size['height'];
            $minwidth = intval($size['width'] / 4);
            for ($i = 0; $i <= $this->line; $i++){
                imageline($this->img, rand(1, $minwidth), rand(1, $height), rand($width - $minwidth,$width),
                rand(1, $height), rand(1,16000000));
            }
        }
    }

    /**
     * bn_captcha::draw_noise()
     *
     * @param mixed $size
     * @return
     */
    private function draw_noise($size)
    {
        if($this->noise>0){
            $width  = $size['width'];
            $height = $size['height'];
            for ($i = 0; $i <= $this->noise; $i++){
                imagesetpixel($this->img, rand(1, $width), rand(1, $height), rand(1,16000000));
            }
        }
    }

    /**
     * bn_captcha::draw_ellipse()
     *
     * @param mixed $size
     * @return
     */
    private function draw_ellipse($size)
    {
        if($this->elipse>0){
            $width  = $size['width'];
            $height = $size['height'];
            for ($i = 0; $i < $this->elipse; $i++){
                $cx = (int)rand(15, $width - 5);
                $cy = (int)rand(15, $height - 5);
                $h  = rand(1, 30);
                $w  = rand(1, 30);
                if ($this->elfill)
                    imagefilledellipse($this->img, $cx, $cy, $w, $h,rand(1,16000000));
                else
                    imageellipse($this->img, $cx, $cy, $w, $h,rand(1,16000000));
            }
        }
    }
}
?>