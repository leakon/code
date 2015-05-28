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

	'eval_php'		=> array('HandleEval',		TYPE_TEXTAREA,		"echo md5('123456'); => e10adc3949ba59abbe56e057f20f883e"),

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
	<th width="140">
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

	if (isset($arrForm[$key]) && strlen($strOfForm = trim($arrForm[$key]))) {
		if (IS_MAGIC_QUOTE) {
			$strOfForm	= stripslashes($strOfForm);
		}

		if (0) {
			// 不适用于 eval 函数，eval 直接输出数据，不在 pre 标签内
			#	$ret		= call_user_func($mapData[MAPPING_FUNC_NAME], $strOfForm);
			#	Output($ret);
		} else {
			// 手动执行 Output 函数

			echo	'<pre class="output">';

			$var	= call_user_func($mapData[MAPPING_FUNC_NAME], $strOfForm);
			if ($GLOBALS['useHtmlSpecialChars']) {
				$var	= htmlspecialchars($var, ENT_COMPAT, 'UTF-8');
			}

			print_r($var);

			echo	'</pre>';

		}


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


################################################################################
## Handler
function HandleEval($strInput) {
	return		eval($strInput . ';');
}

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
pre		{background:#ecf2fa; line-height:20px;}

span.demo	{padding:4px; line-height:28px; font-weight:bold;}

.paragraph,
.inputText,
.inputTextarea	{width:900px; height:16px; padding:4px;}
.inputTextarea	{height:200px;}

.output		{padding:8px; border:1px dotted gray;}
.button		{padding:8px;}

table.myTable	{background:black; width:100%; margin:8px 0;}
table.myTable th,
table.myTable td	{background:white;}

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

<head>

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

<form name="myForm" id="myForm" method="post" action="eval.php">
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