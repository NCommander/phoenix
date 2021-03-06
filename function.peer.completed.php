<?php

function peer_completed() {

	global $connection, $settings;

	require_once __DIR__.'/once.db.connect.php';

	mysqli_query(
		$connection,
		'INSERT INTO `'.$settings['db_prefix'].'torrents` '.
		'(`info_hash`, `downloads`) '.
		'VALUES ('.
			// 40-byte info_hash in HEX
			'\''.$_GET['info_hash'].'\', '.
			// initial value = 1
			'1'.
		') '.
		'ON DUPLICATE KEY UPDATE '.
			// if exists then increment
			'`downloads`=`downloads`+1;'
	);

	// Silent fail
	//tracker_error('Failed to update downloads count.');
	return true;

}
