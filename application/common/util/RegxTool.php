<?php
namespace app\common\util;
/**
 * @Title: file_name
 * @Package package_name
 * @Description: todo(用一句话描述该文件做什么)
 * @
 * @company
 * @copyright 本文件归属于
 * @date 2015-11-26 下午4:09:38
 * @version V1.0
 */
class RegxTool{ 
	//电话号码
	public static function is_mobile( $mobile )
	{ 
		return preg_match("/^(?:13\d|14\d|15\d|16\d|17\d|19\d|18[0123456789])-?\d{5}(\d{3}|\*{3})$/", $mobile);
	}
	//邮箱
	public static function is_email( $email )
	{
		return strlen($email) > 6 && preg_match("/^[\w\-\.]+@[\w\-\.]+(\.\w+)+$/", $email);
	}
	/**身份证验证**/
	public static function is_id_number($id_Number)
	{
		//return preg_match("/^(\d{6})(\d{4})(\d{2})(\d{2})(\d{3})([0-9]|X)$/",$id_Number);//不严谨 先注释掉 dirct by yuansl 2015 08 24
		return preg_match('/^[1-9]\d{5}[1-9]\d{3}((0\d)|(1[0-2]))(([0|1|2]\d)|3[0-1])\d{3}([0-9]|X)$/', $id_Number);
	}
	/* 密码
	 * 1 可以全数字 2 可以全字母 3 可以全特殊字符(~!@#$%^&*.) 4 三种的组合 5 可以是任意两种的组合 6 长度6-22
	*/
	public static function is_password($pws="")
	{
		return  preg_match('/^[\@A-Za-z0-9\!\#\$\%\^\&\*\.\~]{6,22}$/', $pws);
	}
	//邮编
	public static function is_postcode( $code='' )
	{
		return preg_match('/^[1-9][0-9]{5}$/',$code );
	}
	//电话和手机 其中一个就可以
	public static function is_tell($tell)
	{
		return preg_match('/^(((13[0-9]{1})|(15[0-9]{1})|(17[0-9]{1})|(18[0-9]{1}))+\d{8})$/',$tell ) || preg_match('/^(0[0-9]{2,3}\-)?([2-9][0-9]{6,7})+(\-[0-9]{1,4})?$/',$tell );
	}
	//除开特殊字符的字符:"数字~字母
	public static function is_ennum( $ennum )
	{
		return preg_match('/^[A-Za-z0-9_-]{3,16}$/',$ennum );
	}
	//是否全为中文字符串
	public static function is_zhstr($zhstr)
	{
		return preg_match("/^[\x7f-\xff]+$/", $zhstr);
	}
	//根绝luhn算法判断银行卡号码真伪
	public static function is_luhn($s) 
	{
		$s = intval($s);
		if( !$s ){return false; }
		$n = 0;
		for($i=strlen($s)-1; $i>=0; $i--) {
			if($i % 2){
				$n += $s{$i};
			}else {
				$t = $s{$i} * 2;
				if($t > 9) $t = $t{0} + $t{1};
				$n += $t;
			}
		}
		return ($n % 10) == 0;
	}
	//url链接(跟问号带参数的)
	public static function is_urlparm( $url ) 
	{
		return preg_match('/^(\w+:\/\/)?\w+(\.\w+)+.*$/', $url);
	}
}