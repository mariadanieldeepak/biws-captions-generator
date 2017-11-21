<?php
ini_set( 'display_errors', '1' );

include 'vendor/autoload.php';
include 'include/youtube-captions-uploader-autoload.php';

$client = new Google_Client();
$client->setAuthConfig( 'include/client_secret.json' );
$client->addScope( array( 'https://www.googleapis.com/auth/youtube.force-ssl', 'https://www.googleapis.com/auth/youtubepartner' ) );
$client->setRedirectUri( 'http://' . $_SERVER['HTTP_HOST'] . '/oauth2callback.php' );
$client->setAccessType( 'offline' );      // offline access
$client->setIncludeGrantedScopes( true ); // incremental auth

if ( isset( $_SESSION['access_token'] ) && $_SESSION['access_token'] ) {
	$client->setAccessToken( $_SESSION['access_token'] );
	// TODO: Upload files here.
	echo "Connected to YouTube API v3.\n";

	$youtube = new Google_Service_YouTube( $client );

	$videoId         = 'sESj9NriKVs';
	$captionFile     = 'include/resource/upload/sbv/07-01-Valuation-Big-Idea.sbv';
	$captionName     = '07 01 Valuation Big Idea';
	$captionLanguage = 'en';

	//uploadCaption( $youtube, $client, $videoId, $captionFile, $captionName, $captionLanguage, $htmlBody );
	$htmlBody = '';
	listCaptions( $youtube, $videoId, $htmlBody );
	printf( $htmlBody );
	echo "Captions uploaded\n";

} else {
	$redirect_uri = 'http://' . $_SERVER['HTTP_HOST'] . '/oauth2callback.php';
	header( 'Location: ' . filter_var( $redirect_uri, FILTER_SANITIZE_URL ) );
}