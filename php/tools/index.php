<?php

date_default_timezone_set('PRC');
header("Content-Type: text/html; charset=UTF-8");
define("IS_MAGIC_QUOTE",	get_magic_quotes_gpc());

define("MAPPING_FUNC_NAME",	0);
define("MAPPING_FORM_TYPE",	1);
define("MAPPING_DEMO",		2);

define("TYPE_TEXT",		1);
define("TYPE_TEXTAREA",		2);

define("NAV_COLUMN",		5); // Navigation columns
define("HIGHLIGHT_ID_PREFIX",	"row_id_");

$arrMapping	= array(
//					 func_name:0		form_type:1		demo:2
	'md5'			=> array('HandleMd5',		TYPE_TEXT,		'leakon => 374705bc8057061ff384d6e9b0f7d3e3'),
	'gbk_encode'		=> array('HandleGBKEncode',	TYPE_TEXT,		'百度 => %B0%D9%B6%C8'),
	'gbk_decode'		=> array('HandleGBKDecode',	TYPE_TEXT,		'%B0%D9%B6%C8 => 百度'),
	'utf8_encode'		=> array('urlencode',		TYPE_TEXT,		'百度 => %E7%99%BE%E5%BA%A6'),
	'utf8_decode'		=> array('urldecode',		TYPE_TEXT,		'%E7%99%BE%E5%BA%A6 => 百度'),

	'ncr_encode'		=> array('ncr_encode',		TYPE_TEXT,		'谷歌 => ' . htmlspecialchars('&#35895;&#27468;')),
	'ncr_decode'		=> array('ncr_decode',		TYPE_TEXT,		htmlspecialchars('&#35895;&#27468;') . ' => 谷歌'),
	'str_to_per'		=> array('str_to_percent',	TYPE_TEXT,		'谷歌 => \u8c37\u6b4c'),
	'per_to_str'		=> array('percent_to_str',	TYPE_TEXT,		'\u8c37\u6b4c => 谷歌'),

	'str_time'		=> array('my_strtotime',	TYPE_TEXT,		'1983-08-03 10:10 => 428724600 (Timezone:PRC)'),
	'time_str'		=> array('timetostr',		TYPE_TEXT,		'1173522000 => 2007-03-10 18:20 (Timezone:PRC)'),
	'1970_days'		=> array('Days_after_1970',	TYPE_TEXT,		'13794 => 1790-01-01 + (13794 * 86400) => 2007-10-08 (Timezone:PRC)'),
	'log_decode'		=> array('HandleLogDecode',	TYPE_TEXT,		'\xb0\xd9\xb6\xc8 => 百度'),
	'htmlentities'		=> array('my_htmlentities',	TYPE_TEXT,		'&lt;a href=&quot;http://www.leakon.com/&quot;&gt;Leakon&#039;s Blog&lt;/a&gt; => &amp;lt;a href=&amp;quot;http://www.leakon.com/&amp;quot;&amp;gt;Leakon&amp;#039;s Blog&amp;lt;/a&amp;gt;'),
	'html_entity_decode'	=> array('my_html_entity_decode',	TYPE_TEXT,		'&amp;lt;a href=&amp;quot;http://www.leakon.com/&amp;quot;&amp;gt;Leakon&amp;#039;s Blog&amp;lt;/a&amp;gt; => &lt;a href=&quot;http://www.leakon.com/&quot;&gt;Leakon&#039;s Blog&lt;/a&gt;'),

	'unserialize'		=> array('unserialize',		TYPE_TEXTAREA,		'a:2:{s:4:"name";s:6:"Leakon";s:3:"age";i:25;} => Array([name] => Leakon, [age] => 25)'),
	'gbk_unserialize'	=> array('gbk_unserialize',	TYPE_TEXTAREA,		'a:2:{s:4:"name";s:6:"Leakon";s:3:"age";i:25;} => Array([name] => Leakon, [age] => 25)'),

	'base64_encode'		=> array('base64_encode',	TYPE_TEXTAREA,		'leakon.com => bGVha29uLmNvbQ=='),
	'base64_decode'		=> array('base64_decode',	TYPE_TEXTAREA,		'bGVha29uLmNvbQ== => leakon.com'),

#	'json_encode'		=> array('json_encode',		TYPE_TEXTAREA,		'leakon.com => bGVha29uLmNvbQ=='),
	'json_decode'		=> array('json_decode_array',	TYPE_TEXTAREA,		'{"user":"leakon","homepage":"leakon.com"} => Array

(
    [user] => leakon
    [homepage] => leakon.com
)
'),

	'js_template'		=> array('js_template',	TYPE_TEXTAREA,		''),

);


$useHtmlSpecialChars	= false;
if (isset($_REQUEST['use_special_chars']) && '1' == $_REQUEST['use_special_chars']) {
	$useHtmlSpecialChars	= true;
}



################################################################################
## Function Begin

function DrawInput($key, $mapData) {

	static $isOddRow	= 0;

	$arrForm	=& $_POST['form'];

	$classOfTr	= $isOddRow ? 'oddRow' : '';
	$isOddRow	= abs($isOddRow - 1);

	$strLastObj	= '';
	if (isset($_POST['func']) && $key == $_POST['func']) {
		$strLastObj	= '<script type="text/javascript">'
				. "LastObj = {'_id':'" . HIGHLIGHT_ID_PREFIX . $key . "', '_class':'$classOfTr'};"
				. '</script>';
		$classOfTr	= 'rowFocus';
	}

	echo	'
<tr class="' . $classOfTr . '" id="' . HIGHLIGHT_ID_PREFIX .  $key . '">
	<th width="140" class="item_title">
		' . $key . ' <br />
		<a href="#Top">Top</a>
		<a href="javascript:SubmitForm(\'' . $key . '\');">Submit</a>
	</th>
	<td>
		<a  name="' . $key . '"></a>
		<p>';

	if (isset($mapData[MAPPING_DEMO]) && strlen($mapData[MAPPING_DEMO])) {
		echo	'<span class="demo">' . $mapData[MAPPING_DEMO] . '</span><br />';
	}

	if (TYPE_TEXT == $mapData[MAPPING_FORM_TYPE]) {

		echo	'
		<input type="text" name="form[' . $key . ']" value="' . SetInput($arrForm[$key]) . '" class="inputText" onfocus="SetFocus(this, \'' . $key . '\')" />';

	} else if (TYPE_TEXTAREA == $mapData[MAPPING_FORM_TYPE]) {

		echo	'
		<textarea name="form[' . $key . ']" class="inputTextarea" onfocus="SetFocus(this, \'' . $key . '\')">' . SetInput($arrForm[$key]) . '</textarea>';

	}

	echo	'</p>';

	//if (isset($arrForm[$key]) && strlen($strOfForm = trim($arrForm[$key]))) {
	if (isset($arrForm[$key]) && strlen($strOfForm = ($arrForm[$key]))) {
		if (IS_MAGIC_QUOTE) {
			$strOfForm	= stripslashes($strOfForm);
		}

		$ret		= call_user_func($mapData[MAPPING_FUNC_NAME], $strOfForm);
		Output($ret);
	}

	echo	$strLastObj;

	echo	'
	</td>
</tr>';

}

function Output($var) {

	if ($GLOBALS['useHtmlSpecialChars']) {
		$var	= htmlspecialchars($var, ENT_COMPAT, 'UTF-8');
	}

	echo	'<pre class="output">';
	print_r($var);
	echo	'</pre>';
}

function SetInput($strInput) {
	if (IS_MAGIC_QUOTE) {
		$strInput = stripslashes($strInput);
	}
	return	htmlspecialchars($strInput, ENT_QUOTES, 'UTF-8');
}

// From http://w3.org/International/questions/qa-forms-utf-8.html
function isUTF8($string) {

	//	IF mb_string is compiled, use mb_detect_order & mb_detect_encoding is prefered.
	//	mb_detect_order("UTF-8,GBK,SJIS,EUC-JP");
	//	$encoding	= mb_detect_encoding($string);

	$regex	= '/^('
		. '[\x09\x0A\x0D\x20-\x7E]|'		# ASCII
		. '[\xC2-\xDF][\x80-\xBF]|'		# non-overlong 2-byte
		. '\xE0[\xA0-\xBF][\x80-\xBF]|'		# excluding overlongs
		. '[\xE1-\xEC\xEE\xEF][\x80-\xBF]{2}|'	# straight 3-byte
		. '\xED[\x80-\x9F][\x80-\xBF]|'		# excluding surrogates
		. '\xF0[\x90-\xBF][\x80-\xBF]{2}|'	# planes 1-3
		. '[\xF1-\xF3][\x80-\xBF]{3}|'		# planes 4-15
		. '\xF4[\x80-\x8F][\x80-\xBF]{2}'	# plane 16
		. ')*\z/x';

	return	1 == preg_match($regex, $string);
}

################################################################################
## Handler
function HandleGBKEncode($strInput) {
	$strInput	= mb_convert_encoding($strInput, 'GBK', 'UTF-8');
	return		urlencode($strInput);
}

function HandleGBKDecode($strInput) {
	$strInput	= urldecode($strInput);
	return		mb_convert_encoding($strInput, 'UTF-8', 'GBK');
}

function HandleMd5($strInput) {
	return	md5($strInput);
}

function HandleLogDecode($strInput) {
	$strInput	= str_ireplace("\\x", "%", $strInput);
	$strInput	= urldecode($strInput);
	return		HandleGBKDecode($strInput);
}

function timetostr($strInput) {
	$time		= substr($strInput, 0, 10);
	return	date('Y-m-d H:i:s', $strInput) . Append1970Days($strInput);
}

function my_strtotime($strInput) {
	$time	= strtotime($strInput);
	return	$time . Append1970Days($time);
}

function Append1970Days($strInput) {
	$daysAfter1970	= floor($strInput / 86400);
	return	"<br />$daysAfter1970 days after 1970-01-01";
}

function Days_after_1970($strInput) {
	return	date('Y-m-d H:i:s', $strInput * 86400);
}

function my_htmlentities($strInput) {
	return	htmlentities(htmlentities($strInput, ENT_QUOTES), ENT_QUOTES);
}

function my_html_entity_decode($strInput) {
	return	htmlentities(html_entity_decode($strInput, ENT_QUOTES), ENT_QUOTES);
}

function gbk_unserialize($strInput) {
	$gbkString	= mb_convert_encoding($strInput, 'GBK', 'UTF-8');
	$arr		= unserialize($gbkString);
	return		ConvertR($arr, 'UTF-8', 'GBK');
}

/**
 * 默认把 json 转换为数组
 */
function json_decode_array($strInput) {
	$arr		= json_decode($strInput, true);
	return		$arr;
}

function ConvertR($mixedInput, $to, $from) {

	if (is_array($mixedInput)) {
		$arrNew	= array();
		foreach ($mixedInput as $key => $val) {
			$newKey			= mb_convert_encoding($key, $to, $from);
			$arrNew[$newKey]	= ConvertR($val, $to, $from);
		}
		return	$arrNew;
	} else {
		return	mb_convert_encoding($mixedInput, $to, $from);
	}
}

function ncr_encode($str) {
	$str		= mb_encode_numericentity($str, array(0x7F, 0xFFFFFF, 0x0, 0xFFFFFF), 'UTF-8');
	return		htmlspecialchars($str);
}

function ncr_decode($str) {
	$str		= mb_decode_numericentity($str, array(0x7F, 0xFFFFFF, 0x0, 0xFFFFFF), 'UTF-8');
	return		$str;
}

function str_to_percent($str) {
	$ncr		= mb_encode_numericentity($str, array(0x7F, 0xFFFFFF, 0x0, 0xFFFFFF), 'UTF-8');
	$arrPercent	= array();
	$arr		= explode(';', $ncr);
	foreach ($arr as $one) {
		$one	= trim($one);
		if (0 == strlen($one)) {
			continue;
		};
		$dec	= str_replace('&#', '', $one);
		$hex	= dechex($dec);
		$arrPercent[]	= "\\u" . $hex;
	}
	return		implode('', $arrPercent);
}

function percent_to_str($str) {
	$arr		= explode("\u", $str);
	$arrPercent	= array();
	foreach ($arr as $one) {
		$one	= trim($one);
		if (0 == strlen($one)) {
			continue;
		};
		$hex	= $one;
		$dec	= hexdec($hex);
		$arrPercent[]	= "&#" . $dec . ';';
	}
	return		implode('', $arrPercent);
}

function js_template($str) {

    $cont       = str_replace("\r\n", "\n", $str);
    $cont       = str_replace("\r", "", $cont);

    $lines      = explode("\n", $cont);

    if (count($lines) > 1) {
    } else {
        return  $str;
    }

    $head       = sprintf("#^([%s%s]*)([^%s%s])#i", "\s", "\t", "\s", "\t");
    $tail       = sprintf("#([^%s%s])([%s%s]*)$#i", "\s", "\t", "\s", "\t");

    $s_head     = '\1' . "'" . '\2';
    $s_tail     = '\1' . "'," . '\2';

    foreach ($lines as $key => $val) {
        //$val            = htmlspecialchars($val);
        //$val            = "'" . $val . "',";
        $val            = preg_replace($head, $s_head, $val);
        $val            = preg_replace($tail, $s_tail, $val);
        $val            = htmlspecialchars($val);
        $lines[$key]    = $val;
    }

    $return     = implode("\n", $lines);

    return  $return;

}

## Function End
################################################################################

?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="zh" xml:lang="zh">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title>DevelopTools - Leakon</title>

<style type="text/css">
body, th, td, textarea,
input, pre	{font-family:Tahoma; font-size:12px;}
td		{padding:8px;}
pre		{background:#ecf2fa;}

p		{margin:0; padding:3px;}

span.demo	{padding:4px; line-height:28px; font-weight:bold;}

.paragraph,
.inputText,
.inputTextarea	{width:900px; height:16px; padding:4px;}
.inputTextarea	{height:100px;}

.output		{padding:8px; border:1px dotted gray;}
.button		{padding:8px;}

table.myTable	{background:black; width:100%; margin:8px 0;}
table.myTable th,
table.myTable td	{background:white;}

table.myTable th.item_title	{vertical-align:top; padding-top:18px;}

table.myTable tr.rowFocus td,
table.myTable tr.rowFocus th	{background:#ffffcc;}

table.myTable tr.oddRow td,
table.myTable tr.oddRow th	{background:#eeeeee;}

form input			{vertical-align:middle;}

</style>

<script type="text/javascript">

function SubmitForm(m) {
	return	SetForm(m).submit();
}

function SetForm(m) {
	var f = document.getElementById('myForm');
	f.action = f.action.replace(/\#.*/g, '') + '#' + m;
	f.func.value = m;
	return f;
}

function SetFocus(o, m) {
	SetForm(m);
	o.select();
}

function Refresh() {
	setTimeout(
		function() {
			window.location = '';
		}, 10
	);
}

var LastObj = {'_id':'', '_class':''};
function HighLight(targetId) {
	var obj = document.getElementById(targetId);
	if (obj) {
		if (LastObj._id) {
			document.getElementById(LastObj._id).className = LastObj._class;
		}
		LastObj = {'_id':targetId, '_class':obj.className};
		obj.className = 'rowFocus';
	}
}

</script>

</head>

<body>
<a name="Top"></a>
<table class="myTable" cellpadding="0" cellspacing="1">
<?php
$arrRows	= array();
$arrCols	= array();

$arrIndex	= array();
foreach ($arrMapping as $key => $val) {
	$arrIndex[]	= '<a href="#' . $key . '" onclick="HighLight(\'' . HIGHLIGHT_ID_PREFIX . $key . '\')">' . $key . '</a>';
}
$arrIndex[]	= '<a href="http://www.leakon.com/" target="_blank">Leakon</a>';

$mod		= count($arrIndex) % NAV_COLUMN;
$patchNum	= $mod == 0 ? 0 : NAV_COLUMN - $mod;

$rowCount	= 0;
foreach ($arrIndex as $val) {
	$arrRows[$rowCount][]	= $val;
	if (NAV_COLUMN == count($arrRows[$rowCount])) {
		$rowCount++;
	}
}
for ($i = 0; $i < $patchNum; $i++) {
	$arrRows[$rowCount][]	= '&nbsp;';
}

$strTable	= '';
foreach ($arrRows as $oneRow) {
	$strTable	.= '<tr><td>' . implode('</td><td>', $oneRow) . '</td></tr>';
}

echo	$strTable;

?>

</table>

<form name="myForm" id="myForm" method="post" action="<?php echo $_SERVER['PHP_SELF'] ?>">
<input type="hidden" name="func" id="func" value="" />

<input type="submit" value="Submit" class="button" />
<input type="button" value="Clear" class="button" onclick="Refresh()" />

<?php

$checkBox	= $useHtmlSpecialChars ? 'checked="checked"' : '';

?>
<input type="checkbox" name="use_special_chars" value="1" id="id_special_chars" <?php echo $checkBox ?> />
<label for="id_special_chars">SpecialChars</label>

<table class="myTable" cellpadding="0" cellspacing="1">

<?php

foreach ($arrMapping as $key => $val) {
	DrawInput($key, $val);
}

?>

</table>

<input type="submit" value="Submit" class="button" />
<input type="button" value="Clear" class="button" onclick="Refresh()" />

</form>

</body>
</html>
