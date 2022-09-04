<?php
/* ====================
[BEGIN_COT_EXT]
Hooks=rc
[END_COT_EXT]
==================== */

/**
 * Header file for Autocomplete plugin
 *
 * @package Autocomplete
 * @copyright (c) Cotonti Team
 * @license https://github.com/Cotonti/Cotonti/blob/master/License.txt
 */

defined('COT_CODE') or die('Wrong URL');


if (cot::$cfg['jquery'] && cot::$cfg['turnajax'] && cot::$cfg['plugin']['autocomplete']['autocomplete'] > 0) {
    Resources::addFile(cot::$cfg['plugins_dir'] . '/autocomplete/lib/jquery.autocomplete.min.js');

	if(cot::$cfg['plugin']['autocomplete']['css']) {
        Resources::addFile(cot::$cfg['plugins_dir'] . '/autocomplete/lib/jquery.autocomplete.css');
	}

    Resources::addEmbed(
        '$(document).ready(function(){
		    $( document ).on( "focus", ".userinput", function() {
		        $(".userinput").autocomplete("index.php?r=autocomplete", {multiple: true, minChars: ' .
                    cot::$cfg['plugin']['autocomplete']['autocomplete'] . '});
		    });
		});',
        'js',
        50,
        'global',
        'autocomplete'
    );
}
