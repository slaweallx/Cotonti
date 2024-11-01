<?php

/**
 * Administration panel - Configuration
 *
 * @package Cotonti
 * @license https://github.com/Cotonti/Cotonti/blob/master/License.txt
 */
(defined('COT_CODE') && defined('COT_ADMIN')) or die('Wrong URL.');

list($usr['auth_read'], $usr['auth_write'], $usr['isadmin']) = cot_auth('admin', 'a');
cot_block($usr['isadmin']);

require_once cot_incfile('configuration');

$adminTitle = $L['Configuration'];

$t = new XTemplate(cot_tplfile('admin.config', 'core'));

/* === Hook === */
foreach (cot_getextplugins('admin.config.first') as $pl) {
	include $pl;
}
/* ===== */

switch ($n) {
	case 'edit':
		$o = cot_import('o', 'G', 'ALP');
		$p = cot_import('p', 'G', 'ALP');
		$v = cot_import('v', 'G', 'ALP');
		$o = empty($o) ? 'core' : $o;
		$p = empty($p) ? 'global' : $p;

		$optionslist = cot_config_list($o, $p, '');
		cot_die(!sizeof($optionslist), true);

		// Config language file
        $configExtensionLangFile = cot_langfile($p, $o);
		if ($o !== 'core' && file_exists($configExtensionLangFile)) {
			require $configExtensionLangFile;
		}

		// Config extension file
		if ($o != 'core' && file_exists(cot_incfile($p, $o))) {
			require_once cot_incfile($p, $o);
		}

		/* === Hook  === */
		foreach (cot_getextplugins('admin.config.edit.first') as $pl) {
			include $pl;
		}
		/* ===== */

		if ($a == 'update' && cot_check_xg() && !empty($_POST)) {
			$updated = cot_config_update_options($p, $optionslist, $o);
			$errors = cot_get_messages('', 'error');

			if ($o == 'module' || $o == 'plug') {
				$dir = $o == 'module' ? $cfg['modules_dir'] : $cfg['plugins_dir'];
				if (file_exists($dir . "/" . $p . "/setup/" . $p . ".configure.php")) {
					include $dir . "/" . $p . "/setup/" . $p . ".configure.php";
				}
			}
			/* === Hook  === */
			foreach (cot_getextplugins('admin.config.edit.update.done') as $pl) {
				include $pl;
			}
			/* ===== */
			$cache && $cache->clear();

			if ($updated) {
				$errors ? cot_message('adm_partially_updated', 'warning') : cot_message('Updated');
			} else {
				if (!$errors) cot_message('adm_already_updated');
			}
		} elseif ($a == 'reset' && cot_check_xg() && !empty($v)) {
			cot_config_reset($p, $v, $o, '');
			$optionslist = cot_config_list($o, $p, '');

			/* === Hook  === */
			foreach (cot_getextplugins('admin.config.edit.reset.done') as $pl) {
				include $pl;
			}
			/* ===== */
			$cache && $cache->clear();
			cot_redirect(cot_url('admin', array('m'=>'config', 'n'=>'edit', 'o'=>$o, 'p'=>$p), '', true));
		}

		// Admin Path Configuration
		if ($o == 'core') {
			$adminPath[] = array(cot_url('admin', 'm=config'), $L['Configuration']);
			$adminPath[] = [
                cot_url('admin', 'm=config&n=edit&o=' . $o . '&p=' . $p),
                isset(Cot::$L['core_' . $p]) ? Cot::$L['core_' . $p] : $p,
            ];
		} else {
			$adminPath[] = array(cot_url('admin', 'm=extensions'), $L['Extensions']);
			$plmod = $o == 'module' ? 'mod' : 'pl';
			$ext_info = cot_get_extensionparams($p, $o == 'module');
			$adminPath[] = array(cot_url('admin', "m=extensions&a=details&$plmod=$p"), $ext_info['name']);
			$adminPath[] = array(cot_url('admin', 'm=config&n=edit&o=' . $o . '&p=' . $p), $L['Configuration']);
		}

		// Parse Options List
		foreach ($optionslist as $key => $row) {
			list($title, $hint) = cot_config_titles($row['config_name'], $row['config_text']);
			if ($row['config_type'] == COT_CONFIG_TYPE_SEPARATOR) {
				$t->assign('ADMIN_CONFIG_FIELDSET_TITLE', $title);
				$t->parse('MAIN.EDIT.ADMIN_CONFIG_ROW.ADMIN_CONFIG_FIELDSET_BEGIN');
			} else {
				$t->assign([
					'ADMIN_CONFIG_ROW_CONFIG' => cot_config_input($row),
					'ADMIN_CONFIG_ROW_CONFIG_TITLE' => $title,
					'ADMIN_CONFIG_ROW_CONFIG_MORE_URL' => cot_url(
                        'admin',
                        [
                            'm' => 'config',
                            'n' => 'edit',
                            'o' => $o,
                            'p' => $p,
                            'a' => 'reset',
                            'v' => $row['config_name'],
                        ]
                    ),
					'ADMIN_CONFIG_ROW_CONFIG_MORE' => $hint,
				]);
				$t->parse('MAIN.EDIT.ADMIN_CONFIG_ROW.ADMIN_CONFIG_ROW_OPTION');
			}
			$t->parse('MAIN.EDIT.ADMIN_CONFIG_ROW');
		}

		// Assign Form URL
		$t->assign([
			'ADMIN_CONFIG_FORM_URL' => cot_url(
                'admin',
                ['m' => 'config', 'n' => 'edit', 'o' => $o, 'p' => $p, 'a' => 'update']
            ),
		]);

		$t->parse('MAIN.EDIT');
		break;

	default:
		// Configurations for core, modules, and plugins (details omitted here for brevity)
		break;
}

cot_display_messages($t);

$t->parse('MAIN');
$adminMain = $t->text('MAIN');
