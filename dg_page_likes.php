<?php
/**
 * @package DG Page likes
 * @version 1.0
 */
/*
Plugin Name:	Page like function
Description: 	Adding like function to posts
Author: 		Dragonet
Version: 		1.0
Author URI: 	http://www.dragonet.nl/
*/

require_once( __DIR__ . '/dg_page_likes-front-functions.php');

$likes = new DgPageLikes();
$likes->setActions();