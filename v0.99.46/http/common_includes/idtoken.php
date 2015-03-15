<?php
include_once "../../includes/google-api-php-client/examples/templates/base.php";
require_once "../../includes/google-api-php-client/autoload.php";
 
session_start();
 
$client = new Google_Client();
$client->setAuthConfigFile('client_secrets.json');
$client->setScopes('https://www.googleapis.com/auth/calendar https://www.googleapis.com/auth/userinfo.email');
$client->setApprovalPrompt('force');
?>
<html>
<head>
    <script src="https://apis.google.com/js/client:platform.js" async defer></script>
</head>
<span id="signinButton">
  <span
    class="g-signin"
    data-callback="signInCallback"
    data-clientid="109557080949-bt1k7ve8kgeoe4cpqrton1bfl1ddor2r.apps.googleusercontent.com"
    data-cookiepolicy="single_host_origin"
    data-accesstype="offline"
    data-scope="https://www.googleapis.com/auth/calendar https://www.googleapis.com/auth/userinfo.email">
  </span>
</span>

<script>
function signInCallback(authResult) {
  if (authResult['code']) {
      alert('hi');
      console.log(authResult['code']);
    // Hide the sign-in button now that the user is authorized, for example:
    $('#signinButton').attr('style', 'display: none');
    document.getElementById('signinButton').setAttribute('style', 'display: none');


    // Send the code to the server
    $.ajax({
      type: 'POST',
      url: 'plus.php?storeToken',
      contentType: 'application/octet-stream; charset=utf-8',
      success: function(result) {
        // Handle or verify the server response if necessary.

        // Prints the list of people that the user has allowed the app to know
        // to the console.
        console.log(result);
        if (result['profile'] && result['people']){
          $('#results').html('Hello ' + result['profile']['displayName'] + '. You successfully made a server side call to people.get and people.list');
        } else {
          $('#results').html('Failed to make a server-side call. Check your configuration and console.');
        }
      },
      processData: false,
      data: authResult['code']
    });
  } else if (authResult['error']) {

  }
}
</script>
</html>