<?php

/*
 * Phoenix - A modern fork of PeerTracker, a lightweight PHP/SQL BitTorrent Tracker.
 * Copyright 2015 Phoenix Team
 *
 * Phoenix is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * Phoenix is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Phoenix.  If not, see <http://www.gnu.org/licenses/>.
*/

// error level
error_reporting(E_ALL);
// error_reporting(E_ALL & ~E_WARNING);
// error_reporting(E_ALL | E_STRICT | E_DEPRECATED);

// Ignore Disconnects
ignore_user_abort(true);

$settings = array(

	// General Tracker Options
	'open_tracker'      => true,          /* track anything announced to it */
	'announce_interval' => 1800,          /* how often client will send requests */
	'min_interval'      => 600,           /* how often client can force requests */
	'default_peers'     => 50,            /* default # of peers to announce */
	'max_peers'         => 100,           /* max # of peers to announce */

	// Advanced Tracker Options
	'external_ip'       => true,          /* allow client to specify ip address */
	'force_compact'     => false,         /* force compact announces only */
	'full_scrape'       => true,          /* allow scrapes without info_hash */
	'random_limit'      => 500,           /* if peers > #, use alternate SQL RAND() */
	'clean_idle_peers'  => 10,            /* tweaks % of time tracker attempts idle peer removal */
	                                      /* if you have a busy tracker, you may adjust this */
	                                      /* example: 1 = 1%, 10 = 10%, 50 = 50%, 100 = every time */

	// General Database Options
	// Can be better overridden with a config.php file.
	'db_host'           => 'localhost',   /* ip or hostname to mysql server */
	'db_user'           => 'root',        /* username used to connect to mysql */
	'db_pass'           => '',            /* password used to connect to mysql */
	'db_name'           => 'phoenix',     /* name of the Phoenix database */

	// Advanced Database Options
	'db_prefix'         => '',            /* name prefixes for the Phoenix tables */
	'db_persist'        => false,         /* use persistent connections if available. */
	'db_reset'          => true,          /* allow database to be reset in admin */

);

////	DO NOT MODIFY BELOW THIS POINT

// Override the default database variables with this.
if ( is_readable(__DIR__.'/config.php') ) {
	include __DIR__.'/config.php';
}

// require_once __DIR__.'/once.db.connect.php';
require_once __DIR__.'/function.tracker.error.php';
// require_once __DIR__.'/function.tracker.stats.php';

header('Access-Control-Allow-Origin: *');

if ( !$settings['open_tracker'] ) {
	require_once __DIR__.'/function.tracker.allowed.php';
	$torrents = tracker_allowed();
}