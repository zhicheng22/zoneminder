<?php
	if ( !canView( 'Monitors' ) )
	{
		$view = "error";
		return;
	}
	if ( $mid > 0 )
	{
		$result = mysql_query( "select * from Monitors where Id = '$mid'" );
		if ( !$result )
			die( mysql_error() );
		$monitor = mysql_fetch_assoc( $result );
	}
	else
	{
		$monitor = array();
		$monitor[Name] = "New";
		$monitor['Function'] = "None";
		$monitor[Type] = "Local";
		$monitor[Port] = "80";
		$monitor[Orientation] = "0";
		$monitor[LabelFormat] = '%%s - %y/%m/%d %H:%M:%S';
		$monitor[LabelX] = 0;
		$monitor[LabelY] = 0;
		$monitor[ImageBufferCount] = 100;
		$monitor[WarmupCount] = 25;
		$monitor[PreEventCount] = 10;
		$monitor[PostEventCount] = 10;
		$monitor[MaxFPS] = 0;
		$monitor[FPSReportInterval] = 1000;
		$monitor[RefBlendPerc] = 10;
	}
	$local_palettes = array( "Grey"=>1, "RGB24"=>4, "RGB565"=>3, "YUV420P"=>15 );
	$remote_palettes = array( "8 bit greyscale"=>1, "24 bit colour"=>4 );
	$orientations = array( "Normal"=>0, "Rotate Right"=>90, "Inverted"=>180, "Rotate Left"=>270 );
?>
<html>
<head>
<title>ZM - Monitor <?= $monitor[Name] ?></title>
<link rel="stylesheet" href="zm_styles.css" type="text/css">
<script language="JavaScript">
<?php
	if ( $refresh_parent )
	{
?>
opener.location.reload(true);
<?php
	}
?>
window.focus();
function validateForm(Form)
{
	return( true );
}

function closeWindow()
{
	window.close();
}
</script>
</head>
<body>
<table border="0" cellspacing="0" cellpadding="0" width="100%">
<tr>
<td colspan="2" align="left" class="head">Monitor <?= $monitor[Name] ?></td>
</tr>
<form name="monitor_form" method="get" action="<?= $PHP_SELF ?>" onsubmit="return validateForm( document.monitor_form )">
<input type="hidden" name="view" value="<?= $view ?>">
<input type="hidden" name="action" value="">
<input type="hidden" name="mid" value="<?= $mid ?>">
<tr>
<td align="left" class="smallhead">Parameter</td><td align="left" class="smallhead">Value</td>
</tr>
<tr><td align="left" class="text">Name</td><td align="left" class="text"><input type="text" name="new_name" value="<?= $monitor[Name] ?>" size="12" class="form"></td></tr>
<tr><td align="left" class="text">Function</td><td align="left" class="text"><select name="new_function" class="form">
<?php
	foreach ( getEnumValues( 'Monitors', 'Function' ) as $opt_function )
	{
		if ( !ZM_OPT_X10 && $opt_function == 'X10' )
			continue;
?>
<option value="<?= $opt_function ?>"<?php if ( $opt_function == $monitor['Function'] ) { ?> selected<?php } ?>><?= $opt_function ?></option>
<?php
	}
?>
</select></td></tr>
<?php
$select_name = "new_type";
$$select_name = $$select_name?$$select_name:$monitor[Type];
$source_types = array( "Local"=>"Local", "Remote"=>"Remote" );
?>
<tr><td align="left" class="text">Source Type</td><td><?php buildSelect( $select_name, $source_types, "document.monitor_form.submit();" ); ?></td></tr>
<?php
	if ( $$select_name == "Local" )
	{
?>
<tr><td align="left" class="text">Device Number (/dev/video?)</td><td align="left" class="text"><input type="text" name="new_device" value="<?= $monitor[Device] ?>" size="4" class="form"></td></tr>
<tr><td align="left" class="text">Device Channel</td><td align="left" class="text"><input type="text" name="new_channel" value="<?= $monitor[Channel] ?>" size="4" class="form"></td></tr>
<tr><td align="left" class="text">Device Format (0=PAL,1=NTSC etc)</td><td align="left" class="text"><input type="text" name="new_format" value="<?= $monitor[Format] ?>" size="4" class="form"></td></tr>
<tr><td align="left" class="text">Capture Palette</td><td align="left" class="text"><select name="new_palette" class="form"><?php foreach ( $local_palettes as $name => $value ) { ?><option value="<?= $value ?>"<?php if ( $value == $monitor[Palette] ) { ?> selected<?php } ?>><?= $name ?></option><?php } ?></select></td></tr>
<?php
	}
	else
	{
?>
<tr><td align="left" class="text">Remote Host Name</td><td align="left" class="text"><input type="text" name="new_host" value="<?= $monitor[Host] ?>" size="16" class="form"></td></tr>
<tr><td align="left" class="text">Remote Host Port</td><td align="left" class="text"><input type="text" name="new_port" value="<?= $monitor[Port] ?>" size="6" class="form"></td></tr>
<tr><td align="left" class="text">Remote Host Path</td><td align="left" class="text"><input type="text" name="new_path" value="<?= $monitor[Path] ?>" size="36" class="form"></td></tr>
<tr><td align="left" class="text">Remote Image Colours</td><td align="left" class="text"><select name="new_palette" class="form"><?php foreach ( $remote_palettes as $name => $value ) { ?><option value= <?= $value ?>"<?php if ( $value == $monitor[Palette] ) { ?> selected<?php } ?>><?= $name ?></option><?php } ?></select></td></tr>
<?php
	}
?>
<tr><td align="left" class="text">Capture Width (pixels)</td><td align="left" class="text"><input type="text" name="new_width" value="<?= $monitor[Width] ?>" size="4" class="form"></td></tr>
<tr><td align="left" class="text">Capture Height (pixels)</td><td align="left" class="text"><input type="text" name="new_height" value="<?= $monitor[Height] ?>" size="4" class="form"></td></tr>
<tr><td align="left" class="text">Orientation</td><td align="left" class="text"><select name="new_orientation" class="form"><?php foreach ( $orientations as $name => $value ) { ?><option value="<?= $value ?>"<?php if ( $value == $monitor[Orientation] ) { ?> selected<?php } ?>><?= $name ?></option><?php } ?></select></td></tr>
<tr><td align="left" class="text">Timestamp Label Format</td><td align="left" class="text"><input type="text" name="new_label_format" value="<?= $monitor[LabelFormat] ?>" size="20" class="form"></td></tr>
<tr><td align="left" class="text">Timestamp Label X</td><td align="left" class="text"><input type="text" name="new_label_x" value="<?= $monitor[LabelX] ?>" size="4" class="form"></td></tr>
<tr><td align="left" class="text">Timestamp Label Y</td><td align="left" class="text"><input type="text" name="new_label_y" value="<?= $monitor[LabelY] ?>" size="4" class="form"></td></tr>
<tr><td align="left" class="text">Image Buffer Size (frames)</td><td align="left" class="text"><input type="text" name="new_image_buffer_count" value="<?= $monitor[ImageBufferCount] ?>" size="4" class="form"></td></tr>
<tr><td align="left" class="text">Warmup Frames</td><td align="left" class="text"><input type="text" name="new_warmup_count" value="<?= $monitor[WarmupCount] ?>" size="4" class="form"></td></tr>
<tr><td align="left" class="text">Pre Event Image Buffer</td><td align="left" class="text"><input type="text" name="new_pre_event_count" value="<?= $monitor[PreEventCount] ?>" size="4" class="form"></td></tr>
<tr><td align="left" class="text">Post Event Image Buffer</td><td align="left" class="text"><input type="text" name="new_post_event_count" value="<?= $monitor[PostEventCount] ?>" size="4" class="form"></td></tr>
<tr><td align="left" class="text">Maximum FPS</td><td align="left" class="text"><input type="text" name="new_max_fps" value="<?= $monitor[MaxFPS] ?>" size="4" class="form"></td></tr>
<tr><td align="left" class="text">FPS Report Interval</td><td align="left" class="text"><input type="text" name="new_fps_report_interval" value="<?= $monitor[FPSReportInterval] ?>" size="4" class="form"></td></tr>
<tr><td align="left" class="text">Reference Image Blend %ge</td><td align="left" class="text"><input type="text" name="new_ref_blend_perc" value="<?= $monitor[RefBlendPerc] ?>" size="4" class="form"></td></tr>
<?php if ( ZM_OPT_X10 ) { ?>
<tr><td align="left" class="text">X10 Activation String</td><td align="left" class="text"><input type="text" name="new_x10_activation" value="<?= $monitor[X10Activation] ?>" size="20" class="form"></td></tr>
<tr><td align="left" class="text">X10 Input Alarm String</td><td align="left" class="text"><input type="text" name="new_x10_alarm_input" value="<?= $monitor[X10AlarmInput] ?>" size="20" class="form"></td></tr>
<tr><td align="left" class="text">X10 Output Alarm String</td><td align="left" class="text"><input type="text" name="new_x10_alarm_output" value="<?= $monitor[X10AlarmOutput] ?>" size="20" class="form"></td></tr>
<?php } ?>
<tr><td colspan="2" align="left" class="text">&nbsp;</td></tr>
<tr>
<td align="left">&nbsp;</td>
<td align="left"><input type="submit" value="Save" class="form" onClick="document.monitor_form.view.value='none'; document.monitor_form.action.value='monitor';"<?php if ( !canEdit( 'Monitors' ) ) { ?> disabled<?php } ?>>&nbsp;&nbsp;<input type="button" value="Cancel" class="form" onClick="closeWindow()"></td>
</tr>
</table>
</body>
</html>
