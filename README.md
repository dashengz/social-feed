# Social Feed
Social media content curator.

Supports Twitter timeline and Instagram recent media.

## Note

Due to **[Instagram's recent API change](https://www.instagram.com/developer/changelog/)**, only the token owner's recent media can be curated.

*Support for other sandbox users coming soon.*

## How to Use
Example of `secret.php`:

```
<?php
// Twitter Consumer Key (API Key)
$KEY_T = '...';
// Instagram Client ID
$KEY_I = '...';

// Twitter Consumer Secret (API Secret)
$SECRET_T = '...';
// Instagram Client Secret
$SECRET_I = '...';

// Instagram CODE needed for acquiring the access_token
// Visit the link below to get the access_token:
// https://api.instagram.com/oauth/authorize/?client_id={CLIENT_ID}&redirect_uri={REDIRECT_URI}&response_type=code&scope=public_content
$CODE_I = '...';

// Refer to social-feed.php for instructions on how to acquire these
// Twitter access_token
$TOKEN_T = '...';
// Instagram access_token
$TOKEN_I = '...';
```