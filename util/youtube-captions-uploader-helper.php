<?php
/**
 * Uploads a caption track in draft status that matches the API request parameters.
 * (captions.insert)
 *
 * @param Google_Service_YouTube $youtube YouTube service object.
 * @param Google_Client $client Google client.
 * @param $videoId the YouTube video ID of the video for which the API should
 *  return caption tracks.
 * @param $captionLanguage language of the caption track.
 * @param $captionName name of the caption track.
 * @param $captionFile caption track binary file.
 * @param $htmlBody html body.
 */
function uploadCaption( Google_Service_YouTube $youtube, Google_Client $client, $videoId,
	$captionFile, $captionName, $captionLanguage, &$htmlBody ) {
	# Insert a video caption.
	# Create a caption snippet with video id, language, name and draft status.
	$captionSnippet = new Google_Service_YouTube_CaptionSnippet();
	$captionSnippet->setVideoId($videoId);
	$captionSnippet->setLanguage($captionLanguage);
	$captionSnippet->setName($captionName);

	# Create a caption with snippet.
	$caption = new Google_Service_YouTube_Caption();
	$caption->setSnippet($captionSnippet);

	// Specify the size of each chunk of data, in bytes. Set a higher value for
	// reliable connection as fewer chunks lead to faster uploads. Set a lower
	// value for better recovery on less reliable connections.
	$chunkSizeBytes = 1 * 1024 * 1024;

	// Setting the defer flag to true tells the client to return a request which can be called
	// with ->execute(); instead of making the API call immediately.
	$client->setDefer( true );

	// Create a request for the API's captions.insert method to create and upload a caption.
	// Always allow YouTube to Sync the captions.
	$insertRequest = $youtube->captions->insert("snippet", $caption, array( 'sync' => true ) );

	// Create a MediaFileUpload object for resumable uploads.
	$media = new Google_Http_MediaFileUpload(
		$client,
		$insertRequest,
		'*/*',
		null,
		true,
		$chunkSizeBytes
	);
	$media->setFileSize(filesize($captionFile));


	// Read the caption file and upload it chunk by chunk.
	$status = false;
	$handle = fopen($captionFile, "rb");
	while (!$status && !feof($handle)) {
		$chunk = fread($handle, $chunkSizeBytes);
		$status = $media->nextChunk($chunk);
	}

	fclose($handle);

	// If you want to make other calls after the file upload, set setDefer back to false
	$client->setDefer(false);

	$htmlBody .= "<h2>Inserted video caption track for</h2><ul>";
	$captionSnippet = $status['snippet'];
	$htmlBody .= sprintf('<li>%s(%s) in %s language, %s status.</li>',
		$captionSnippet['name'], $status['id'], $captionSnippet['language'],
		$captionSnippet['status']);
	$htmlBody .= '</ul>';
}

/**
 * Returns a list of caption tracks. (captions.listCaptions)
 *
 * @param Google_Service_YouTube $youtube YouTube service object.
 * @param string $videoId The videoId parameter instructs the API to return the
 * caption tracks for the video specified by the video id.
 * @param $htmlBody - html body.
 */
function listCaptions( Google_Service_YouTube $youtube, $videoId, &$htmlBody ) {
	// Call the YouTube Data API's captions.list method to retrieve video caption tracks.
	$captions = $youtube->captions->listCaptions("snippet", $videoId);

	$htmlBody .= "<h3>Video Caption Tracks</h3><ul>";
	foreach ($captions as $caption) {
		$htmlBody .= sprintf('<li>%s(%s) in %s language</li>', $caption['snippet']['name'],
			$caption['id'],  $caption['snippet']['language']);
	}
	$htmlBody .= '</ul>';

	return $captions;
}
