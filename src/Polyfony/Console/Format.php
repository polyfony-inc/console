<?php

namespace Polyfony\Console;

// the class itself
class Format {

	const FOREGROUND_COLORS = [
		'green'		=>'0;32',
		'red'		=>'0;31',
		'yellow'	=>'1;33',
		'blue'		=>'0;34',
		'cyan'		=>'0;36',
		'purple'	=>'0;35',
		'black'		=>'0;30',
		'white'		=>'1;37',
		'gray'		=>'1;30'
	];

	const BACKGROUND_COLORS = [
		'green'		=>'42',
		'red'		=>'41',
		'yellow'	=>'43',
		'blue'		=>'44',
		'cyan'		=>'46',
		'magenta'	=>'45',
		'black'		=>'40'
	];

	public static function colorize(
		string $string, 
		$foreground_color = 'white', 
		$background_color = 'black',
		$styles = []
	) {

		// declare the colored string
		$colored_string = '';

		// check if the given foreground color exists
		if(array_key_exists($foreground_color, self::FOREGROUND_COLORS)) {
			// apply the color
			$colored_string .= "\033[" . self::FOREGROUND_COLORS[$foreground_color] . "m";
		}
		// check if the given background color exists
		if(array_key_exists($background_color, self::BACKGROUND_COLORS)) {
			// apply the color
			$colored_string .= "\033[" . self::BACKGROUND_COLORS[$background_color] . "m";
		}
		// check if is supposed to be bold
		if(in_array('bold', $styles)) {
			// apply the color
			$colored_string .= "\033[1m";
		}
		// check if is supposed to be bold
		if(in_array('italic', $styles)) {
			// apply the color
			$colored_string .= "\033[3m";
		}
		// check if is supposed to be bold
		if(in_array('blink', $styles)) {
			// apply the color
			$colored_string .= "\033[5m";
		}
		// check if is supposed to be bold
		if(in_array('underline', $styles)) {
			// apply the color
			$colored_string .= "\033[4m";
		}

		// Add string and end coloring
		$colored_string .=  $string . "\033[0m";

		// return the colorized string
		return $colored_string;

	}

	public static function raw(
		$string, 
		string $foreground_color=null, 
		string $background_color=null, 
		$styles = []
	) {

		echo self::colorize($string, $foreground_color, $background_color, $styles);

	}

	public static function block(
		$string, 
		string $foreground_color=null, 
		string $background_color=null, 
		$styles = []
	) {

		// get the length of the message to show
		$empty_line =  str_repeat(' ', strlen($string) + 4) . "\n";
		// content line
		$content_line = "  {$string}  \n";
		// the whole thing
		echo self::colorize(
			$empty_line . $content_line . $empty_line,
			$foreground_color , $background_color, $styles
		);


	}

	public static function line(string $string, string $foreground_color=null, string $background_color=null, $styles = []) {

		echo self::colorize($string, $foreground_color, $background_color, $styles) . "\n";

	}

}

?>
