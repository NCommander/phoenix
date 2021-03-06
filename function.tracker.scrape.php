<?php

function tracker_scrape() {

	global $connection, $settings;

	require_once __DIR__.'/once.db.connect.php';

	$tracker = mysqli_query(
		$connection,
		// select info_hash, total seeders and leechers
		'SELECT '.
		'`p`.`info_hash` AS `info_hash`, '.
		'SUM(`p`.`state`=\'1\') AS `seeders`, '.
		'SUM(`p`.`state`=\'0\') AS `leechers`, '.
		'`t`.`downloads` AS `downloads` '.
		// from peers
		'FROM `'.$settings['db_prefix'].'peers` AS `p` '.
		'LEFT JOIN `'.$settings['db_prefix'].'torrents` AS `t` '.
		'ON `p`.`info_hash`=`t`.`info_hash` '.
		// grouped by info_hash
		'GROUP BY `info_hash`'
	);

	if ( !$tracker ) {
		tracker_error('Unable to scrape the tracker.');
	} else {

		// XML
		if ( isset($_GET['xml']) ) {
			header('Content-Type: text/xml');
			echo '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'.
					'<tracker>';
			while ( $scrape = mysqli_fetch_assoc($tracker) ) {
				$scrape['peers'] = $scrape['seeders'] + $scrape['leechers'];
				echo '<torrent>'.
							'<info_hash>'.$scrape['info_hash']          .'</info_hash>'.
							'<seeders>'  .intval($scrape['seeders'])  .'</seeders>'.
							'<leechers>' .intval($scrape['leechers']) .'</leechers>'.
							'<peers>'    .intval($scrape['peers'])    .'</peers>'.
							'<downloads>'.intval($scrape['downloads']).'</downloads>'.
						'</torrent>';
			}
			echo '</tracker>';

		// JSON
		} else if ( isset($_GET['json']) ) {
			header('Content-Type: application/json');
			$json = array();
			while ( $scrape = mysqli_fetch_assoc($tracker) ) {
				$scrape['peers'] = $scrape['seeders'] + $scrape['leechers'];
				$json[$scrape['info_hash']] = array(
					'seeders'   => intval( $scrape['seeders']),
					'leechers'  => intval( $scrape['leechers']),
					'peers'     => intval( $scrape['peers']),
					'downloads' => intval( $scrape['downloads']),
				);
			}
			echo json_encode($json);

		} else {
			$response = 'd5:filesd';
			while ( $scrape = mysqli_fetch_assoc($tracker) ) {
				$response .= '20:'.hex2bin($scrape['info_hash']).'d8:completei'.intval($scrape['seeders']).'e10:downloadedi'.intval($scrape['downloads']).'e10:incompletei'.intval($scrape['leechers']).'ee';
			}
			echo $response.'ee';
		}

	}

}
