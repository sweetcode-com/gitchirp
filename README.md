# gitchirp


## Description

Check if a GitHub repo subfolder was updated recently and post all new commits to Slack.

## Usage

1. Fork this repo

    `git clone https://github.com/alewolf/gitchirp.git`

2. Install dependencies

    `composer install`

3. Copy the .env.example file to .env

    `cp .env.example .env`

4. Create a Slack app

    https://api.slack.com/

5. set the .env values


6. Create a cron job to run the script

    The following cron job will run the script every week on Monday at 3 am.

    `0 3 * * 1 /usr/bin/php /path/to/gitchirp/app.php`








