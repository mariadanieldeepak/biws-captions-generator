<?php

include 'vendor/autoload.php';
include 'include/youtube-captions-uploader-autoload.php';

$client = new Google_Client();
$client->setAuthConfig( 'include/client_secret.json' );
$client->addScope( 'https://www.googleapis.com/auth/youtube' );
$client->setRedirectUri( 'http://' . $_SERVER['HTTP_HOST'] . '/oauth2callback.php' );
$client->setAccessType( 'offline' );      // offline access
$client->setIncludeGrantedScopes( true ); // incremental auth

if ( ! isset( $_GET['code'] ) ) {
	$auth_url = $client->createAuthUrl();
	header( 'Location: ' . filter_var( $auth_url, FILTER_SANITIZE_URL ) );
} else {
	$client->fetchAccessTokenWithAuthCode( $_GET['code'] );
	$_SESSION['access_token'] = $client->getAccessToken();
	$redirect_uri = 'http://' . $_SERVER['HTTP_HOST'] . '/';
	header( 'Location: ' . filter_var( $redirect_uri, FILTER_SANITIZE_URL ) );
}