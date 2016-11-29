<?php




// This code will execute if the user entered a search query in the form
// and submitted the form. Otherwise, the page displays the form above.
if (isset($_GET['q']) && isset($_GET['maxResults'])) {
  /*
   * Set $DEVELOPER_KEY to the "API key" value from the "Access" tab of the
   * Google API Console <https://console.developers.google.com/>
   * Please ensure that you have enabled the YouTube Data API for your project.
   */
  $DEVELOPER_KEY = 'AIzaSyBJTmvpPfPXEkwlKrLcYGXHWpstfC8-7JU';

  $client = new Google_Client();
  $client->setDeveloperKey($DEVELOPER_KEY);

  // Define an object that will be used to make all API requests.
  $youtube = new Google_Service_YouTube($client);

  $htmlBody = '';
  try {

    // Call the search.list method to retrieve results matching the specified
    // query term.
    $searchResponse = $youtube->search->listSearch('id,snippet', array(
      'q' => $_GET['q'],
      'maxResults' => $_GET['maxResults'],
    ));

    $videos = '';
    $channels = '';
    $playlists = '';

    // Add each result to the appropriate list, and then display the lists of
    // matching videos, channels, and playlists.
    foreach ($searchResponse['items'] as $searchResult) {
      switch ($searchResult['id']['kind']) {
        case 'youtube#video':
          $videos .= sprintf('<li>%s (%s)</li>',
              $searchResult['snippet']['title'], $searchResult['id']['videoId']."<a href=http://www.youtube.com/watch?v=".$searchResult['id']['videoId']." target=_blank>   Watch This Video</a>");
          break;
        case 'youtube#channel':
          $channels .= sprintf('<li>%s (%s)</li>',
              $searchResult['snippet']['title'], $searchResult['id']['channelId']."<a href=http://www.youtube.com/channel/".$searchResult['id']['channelId']." target=_blank>   Direct Channel</a>");
          break;
        case 'youtube#playlist':
          $playlists .= sprintf('<li>%s (%s)</li>',
              $searchResult['snippet']['title'], $searchResult['id']['playlistId']);
          break;
      }
    }

    $htmlBody .= <<<END
    <h3>Videos</h3>
    <ul>$videos</ul>
    <h3>Channels</h3>
    <ul>$channels</ul>
    <h3>Playlists</h3>
    <ul>$playlists</ul>
END;
  } catch (Google_Service_Exception $e) {
    $htmlBody .= sprintf('<p>A service error occurred: <code>%s</code></p>',
      htmlspecialchars($e->getMessage()));
  } catch (Google_Exception $e) {
    $htmlBody .= sprintf('<p>An client error occurred: <code>%s</code></p>',
      htmlspecialchars($e->getMessage()));
  }
}
?>


<!doctype html>
<html>
  <head>
    <title>YouTube Search</title>
    <link href="{{url('css/bootstrap.min.css')}}" rel="stylesheet">


  </head>
  <body>

  <form method="GET" class="form-horizontal">
  <div class="form-group " style="width: 80%; margin-left: 200px; ">
  <h1 style="color: #b71c1c;">You <small>Tube</small></h1>
  <div class="row">
    <div class="col-xs-8">
      <input type="search" id="q" name="q" class="form-control" placeholder="Enter Search Term">
    </div>
    <div class="col-xs-8">
      Max Results: <input type="number" id="maxResults" class="form-control" name="maxResults" min="1" max="50" step="1" value="25">
    </div>

    <div class="col-xs-8" style="margin-top: 10px;">
      <button type="submit" class="btn btn-danger">Search</button>
    </div>
  </div> 
  <small> 
      @if(Request::input('q') == "")
        
      @else
        <?= $htmlBody ?>
      @endif
  </small>

  </div>    
  </form>





    
</body>
</html>

