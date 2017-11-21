<?php
ini_set( 'display_errors', '1' );

include 'vendor/autoload.php';
include 'include/youtube-captions-uploader-autoload.php';

$client = new Google_Client();
$client->setAuthConfig( 'include/client_secret.json' );
$client->addScope( 'https://www.googleapis.com/auth/youtube' );
$client->setRedirectUri( 'http://' . $_SERVER['HTTP_HOST'] . '/oauth2callback.php' );
$client->setAccessType( 'offline' );      // offline access
$client->setIncludeGrantedScopes( true ); // incremental auth

if ( isset( $_SESSION['access_token'] ) && $_SESSION['access_token'] ) {
	$client->setAccessToken( $_SESSION['access_token'] );
	// TODO: Upload files here.
	echo 'Connected to YouTube API v3.';
} else {
	$redirect_uri = 'http://' . $_SERVER['HTTP_HOST'] . '/oauth2callback.php';
	header( 'Location: ' . filter_var( $redirect_uri, FILTER_SANITIZE_URL ) );
}