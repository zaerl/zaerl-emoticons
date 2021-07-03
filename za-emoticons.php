<?php
/*
Plugin Name: zaerl Emoticons
Plugin URI: http://zaerl.com
Description: Simple and plain smiley function for bbPress
Author: Francesco Bigiarini
Version: 1.0
Author URI: http://zaerl.com
*/

function za_em_init()
{
	global $za_em_trans, $za_em_search;

	if ( !isset( $za_em_trans ) ) {
		$za_em_trans = array(
		  ':arrow:' => 'stock_right.png',
		   ':evil:' => 'face-devilish.png',
		   ':idea:' => 'idea.png',
		    ':cry:' => 'face-crying.png',
		    ':lol:' => 'face-laugh.png',
		    ':mad:' => 'face-angry.png',
		    ':sad:' => 'face-sad.png',
			'O:-)' => 'face-angel.png',
		      '8-O' => 'face-surprise.png',
			  'O:)' => 'face-angel.png',
		      ':-(' => 'face-sad.png',
		      ':-)' => 'face-smile.png',
		      ':-?' => 'face-uncertain.png',
		      ':-D' => 'face-smile-big.png',
		      ':-P' => 'face-raspberry.png',
			  ':-S' => 'face-worried.png',
		      ':-o' => 'face-surprise.png',
		      ':-x' => 'face-embarrassed.png',
		      ':-|' => 'face-plain.png',
		      ';-)' => 'face-wink.png',
			  ':-*' => 'face-kiss.png',
		      '8-)' => 'face-cool.png',
		       ':(' => 'face-sad.png',
		       ':)' => 'face-smile.png',
		       ':?' => 'face-uncertain.png',
		       ':D' => 'face-smile-big.png',
		       ':P' => 'face-raspberry.png',
		       ':o' => 'face-surprise.png',
		       ':x' => 'face-embarrassed.png',
		       ':|' => 'face-plain.png',
		       ';)' => 'face-wink.png',
			   ':*' => 'face-kiss.png',
		      ':!:' => 'exclaim.png',
		      ':?:' => 'question.png'
		);
	}

	if (count($za_em_trans) == 0) {
		return;
	}

	/*
	 * NOTE: we sort the smilies in reverse key order. This is to make sure
	 * we match the longest possible smilie (:???: vs :?) as the regular
	 * expression used below is first-match
	 */
	krsort($za_em_trans);

	$za_em_search = '/(?:\s|^)';

	$subchar = '';
	foreach ( (array) $za_em_trans as $smiley => $img ) {
		$firstchar = substr($smiley, 0, 1);
		$rest = substr($smiley, 1);

		// new subpattern?
		if ($firstchar != $subchar) {
			if ($subchar != '') {
				$za_em_search .= ')|(?:\s|^)';
			}
			$subchar = $firstchar;
			$za_em_search .= preg_quote($firstchar, '/') . '(?:';
		} else {
			$za_em_search .= '|';
		}
		$za_em_search .= preg_quote($rest, '/');
	}

	$za_em_search .= ')(?:\s|$)/m';
	//echo implode(array_keys($za_em_trans), ' ');

	add_filter('post_text', 'za_em_convert_smilies');
}

add_action('bb_init', 'za_em_init');

function za_em_convert_smilies($text)
{
	global $za_em_search;
	$output = '';

	if (!empty($za_em_search) ) {
		// HTML loop taken from texturize function, could possible be consolidated
		$textarr = preg_split("/(<.*>)/U", $text, -1, PREG_SPLIT_DELIM_CAPTURE); // capture the tags as well as in between
		$stop = count($textarr);// loop stuff
		for ($i = 0; $i < $stop; $i++) {
			$content = $textarr[$i];
			if ((strlen($content) > 0) && ('<' != $content{0})) { // If it's not a tag
				$content = preg_replace_callback($za_em_search, 'za_em_translate_smiley', $content);
			}
			$output .= $content;
		}
	} else {
		// return default text.
		$output = $text;
	}
	return $output;
}

function za_em_translate_smiley($smiley)
{
	global $za_em_trans;

	if(count($smiley) == 0) return '';

	$url = BB_PLUGIN_URL . 'za-emoticons/smilies/';

	$smiley = trim(reset($smiley));
	$img = $za_em_trans[$smiley];
	$smiley_masked = esc_attr($smiley);

	//$srcurl = apply_filters('smilies_src', "$siteurl/wp-includes/images/smilies/$img", $img, $siteurl);

	return " <img src='$url$img' alt='$smiley_masked' class='wp-smiley' /> ";
}

?>
