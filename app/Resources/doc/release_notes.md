# Github Release Notes

## Setup Github API Token

The release notes controller connects to the Github API using an access token, since the cdpaccess repository is Private.

The access token is set to the `github_api_token` parameter in the `app/config/parameters.yml` file.

To generate an access token, follow the directions on Github: https://help.github.com/articles/creating-an-access-token-for-command-line-use/

Once generated, copy the token and paste it in your parameters.yml file.

## View Release Notes

Note: To view the Release Notes page, you must be logged in as an Admin user.

View the release notes at `/release-notes` (e.g. https://cdpaccess.com/release-notes).
This defaults to view the release notes for the production environment.
To view the release notes for Test, click the link on the page or go to `/release-notes/test`.
