<?php
function getTextHex(string $color): string
{
	$hexBackground = $color;
	if(strlen($hexBackground) == 4)
		$hexBackground = '#' . implode('',
				array_map('str_repeat', str_split(str_replace('#', '', $hexBackground)), [2, 2, 2]));
	list($r, $g, $b) = sscanf($hexBackground, "#%02x%02x%02x");
	//thanks to https://stackoverflow.com/questions/1855884/determine-font-color-based-on-background-color
	$luminance = (0.299 * $r + 0.587 * $g + 0.114 * $b) / 255;
	if($luminance > 0.5)
		return '#000';
	return '#fff';
}
