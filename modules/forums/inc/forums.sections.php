<?php

/**
 * @package forums
 * @version 0.7.1
 * @copyright Copyright (c) 2008-2011 Cotonti Team
 * @license BSD
 */

defined('COT_CODE') or die('Wrong URL');

list($usr['auth_read'], $usr['auth_write'], $usr['isadmin']) = cot_auth('forums', 'any');
/* === Hook === */
foreach (cot_getextplugins('forums.sections.rights') as $pl)
{
	include $pl;
}
/* ===== */
cot_block($usr['auth_read']);

$s = cot_import('s','G','TXT');
$c = cot_import('c','G','TXT');

$sys['sublocation'] = $L['Home'];

/* === Hook === */
foreach (cot_getextplugins('forums.sections.first') as $pl)
{
	include $pl;
}
/* ===== */

if ($n == 'markall' && $usr['id'] > 0)
{
	$db->update($db_users, array('user_lastvisit' => $sys['now_offset']), "user_id=".$usr['id']);
	$usr['lastvisit'] = $sys['now_offset'];
}

if (!$cot_sections_act)
{
	$timeback = $sys['now'] - 604800; // 7 days
	$sqltmp = $db->query("SELECT fp_cat, COUNT(*) FROM $db_forum_posts WHERE fp_creation > $timeback GROUP BY fp_cat");
	while ($tmprow = $sqltmp->fetch())
	{
		$cot_sections_act[$tmprow['fp_cat']] = $tmprow['COUNT(*)'];
	}
	$sqltmp->closeCursor();
	$cache && $cache->db->store('cot_sections_act', $cot_sections_act, 'system', 7200);
}

$cache && $cache->mem && $cot_sections_vw = $cache->mem->get('sections_wv', 'forums');
if (!$cot_sections_vw)
{
	$sqltmp = $db->query("SELECT online_subloc, COUNT(*) FROM $db_online WHERE online_location='Forums' GROUP BY online_subloc");
	while ($tmprow = $sqltmp->fetch())
	{
		$cot_sections_vw[$tmprow['online_subloc']] = $tmprow['COUNT(*)'];
	}
	$sqltmp->closeCursor();
	$cache && $cache->mem && $cache->mem->store('sections_vw', $cot_sections_vw, 'forums', 120);
}

$sql_forums = $db->query("SELECT * FROM $db_forum_stats WHERE 1 ORDER by fs_cat DESC");
foreach ($sql_forums->fetchAll() as $row)
{
	if (!$cat_top[$row['fs_cat']]['fs_lt_id'])
	{
		cot_forums_sectionsetlast($row['fs_cat']);
	}
	$cat_top[$row['fs_cat']] = $row;
	$cat_top[$row['fs_cat']]['topiccount'] = $cat_top[$row['fs_cat']]['fs_topiccount'];
	$cat_top[$row['fs_cat']]['postcount'] = $cat_top[$row['fs_cat']]['fs_postcount'];
	$cat_top[$row['fs_cat']]['viewcount'] = $cat_top[$row['fs_cat']]['fs_topiccount'];
}

$fstlvl = array();
$nxtlvl = array();
foreach ($structure['forums'] as $i => $x)
{
	$parents = explode('.', $x['path']);
	$depth = count($parents);
	if (cot_auth('forums', $i, 'R'))
	{
		if ($depth < 2)
		{
			$fstlvl[$i] = $i;
		}
		elseif($depth < 4)
		{
			$nxtlvl[$parents[$depth-2]][$i] = $i;
		}
		$depmax = ($depth < 4) ? ($depth - 1) : 3;
		for ($ii = 0; $ii < $depmax; $ii++)
		{
			if($cat_top[$i]['fs_lt_date'] > $cat_top[$parents[$ii]]['fs_lt_date'] || !isset($cat_top[$parents[$ii]]['fs_lt_date']))
			{
				$cat_top[$parents[$ii]]['fs_lt_id'] = $cat_top[$i]['fs_lt_id'];
				$cat_top[$parents[$ii]]['fs_lt_title'] = $cat_top[$i]['fs_lt_title'];
				$cat_top[$parents[$ii]]['fs_lt_date'] = $cat_top[$i]['fs_lt_date'];
				$cat_top[$parents[$ii]]['fs_lt_posterid'] = $cat_top[$i]['fs_lt_posterid'];
				$cat_top[$parents[$ii]]['fs_lt_postername'] = $cat_top[$i]['fs_lt_postername'];
			}
			$cat_top[$parents[$ii]]['topiccount'] += $cat_top[$i]['fs_topiccount'];
			$cat_top[$parents[$ii]]['postcount'] += $cat_top[$i]['fs_postcount'];
			$cat_top[$parents[$ii]]['viewcount'] += $cat_top[$i]['fs_viewcount'];
		}
		if ($depth > 1)
		{
			$cot_act[$parents[1]] += $cot_sections_act[$section];
		}
	}
}
$secact_max = is_array($cot_act) ? (max($cot_act)) : 0;

$out['subtitle'] = $L['Forums'];

/* === Hook === */
foreach (cot_getextplugins('forums.sections.main') as $pl)
{
	include $pl;
}
/* ===== */

require_once $cfg['system_dir'] . '/header.php';

$t = new XTemplate(cot_tplfile('forums.sections'));

$bhome = ($cfg['homebreadcrumb']) ? cot_rc_link($cfg['mainurl'], htmlspecialchars($cfg['maintitle'])).$cfg['separator'].' ' : '';

$url_markall = cot_url('forums', "n=markall");
$t->assign(array(
	'FORUMS_RSS' => cot_url('rss', 'c=forums'),
	'FORUMS_SECTIONS_PAGETITLE' => $bhome.cot_rc_link(cot_url('forums'), $L['Forums']),
	'FORUMS_SECTIONS_MARKALL' =>  ($usr['id'] > 0) ? cot_rc_link($url_markall, $L['forums_markallasread']) : '',
	'FORUMS_SECTIONS_MARKALL_URL' => ($usr['id'] > 0) ? $url_markall : '',
	'FORUMS_SECTIONS_WHOSONLINE' => $out['whosonline']." : ".$out['whosonline_reg_list']
));


$xx = 0;
/* === Hook - Part1 : Set === */
$extp = cot_getextplugins('forums.sections.loop');
/* ===== */
/* === Hook - Part1 : Set === */
$extps = cot_getextplugins('forums.sections.loop.sections');
/* ===== */
/* === Hook - Part1 : Set === */
$extpss = cot_getextplugins('forums.sections.loop.subsections');
/* ===== */
foreach ($fstlvl as $x)
{
	if (is_array($nxtlvl[$x]))
	{
		$yy = 0;
		foreach ($nxtlvl[$x] as $y)
		{
			if (is_array($nxtlvl[$y]) && $cfg['forums'][$y]['defstate'])
			{
				$zz = 0;
				foreach ($nxtlvl[$y] as $z)
				{
					$zz++;
					$t->assign(cot_generate_sectiontags($z, 'FORUMS_SECTIONS_ROW_', $cat_top[$z]));
					$t->assign(array(
						'FORUMS_SECTIONS_ROW_ODDEVEN' => cot_build_oddeven($zz),
						'FORUMS_SECTIONS_ROW_NUM' => $zz,
					));
					/* === Hook - Part2 : Include === */
					foreach ($extpss as $pl)
					{
						include $pl;
					}
					/* ===== */
					$t->parse('MAIN.FORUMS_SECTIONS.CAT.SECTION.SUBSECTION');
				}
			}
			$yy++;
			$t->assign(cot_generate_sectiontags($y, 'FORUMS_SECTIONS_ROW_', $cat_top[$y]));
			$cot_sections_vw_cur = (!$cot_sections_vw[$fsn['fs_title']]) ? "0" : $cot_sections_vw[$fsn['fs_title']];

			$secact_num = 0;
			if ($secact_max)
			{
				$secact_num = round(6.25 * $cot_sections_act[$fsn['fs_cat']] / $secact_max);
				$secact_num = ($secact_num>5) ? 5 : $secact_num;
				$secact_num =  (!$secact_num && $cot_sections_act[$fsn['fs_cat']]>1) ? 1 : $secact_num;

			}
			$t->assign(array(
				'FORUMS_SECTIONS_ROW_ACTIVITY' => cot_rc('forums_icon_section_activity', array('secact_num'=>$secact_num)),
				'FORUMS_SECTIONS_ROW_ACTIVITYVALUE' => $secact_num,
				'FORUMS_SECTIONS_ROW_VIEWERS' => $cot_sections_vw_cur,
				'FORUMS_SECTIONS_ROW_ODDEVEN' => cot_build_oddeven($yy),
				'FORUMS_SECTIONS_ROW_NUM' => $yy,
			));
			/* === Hook - Part2 : Include === */
			foreach ($extps as $pl)
			{
				include $pl;
			}
			/* ===== */
			$t->parse('MAIN.FORUMS_SECTIONS.CAT.SECTION');
		}
	}
	$xx++;

	$fold = !$cfg['forums'][$x]['defstate'];
	if($c)
	{
		$fold = (int)($c=='fold' ? true : ($c=='unfold' ? false : ($c==$x ? false : true)));
	}

	$t->assign(cot_generate_sectiontags($x, 'FORUMS_SECTIONS_ROW_', $cat_top[$x]));
	$t->assign(array(
		'FORUMS_SECTIONS_ROW_FOLD' => $fold,
		'FORUMS_SECTIONS_ROW_ODDEVEN' => cot_build_oddeven($xx),
		'FORUMS_SECTIONS_ROW_NUM' => $xx,
	));
	/* === Hook - Part2 : Include === */
	foreach ($extp as $pl)
	{
		include $pl;
	}
	/* ===== */
	$t->parse('MAIN.FORUMS_SECTIONS.CAT');
}
$t->parse('MAIN.FORUMS_SECTIONS');

/* === Hook === */
foreach (cot_getextplugins('forums.sections.tags') as $pl)
{
	include $pl;
}
/* ===== */

$t->parse('MAIN');
$t->out('MAIN');

require_once $cfg['system_dir'] . '/footer.php';

if ($cache && $usr['id'] === 0 && $cfg['cache_forums'])
{
	$cache->page->write();
}

?>