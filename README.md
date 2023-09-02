# gitchirp


## Description

Check if a GitHub repo subfolder was updated recently and post all new commits to Slack.

Our use case for which this script was created:

We maintain our main documentation for our plugins in a GitHub repo. But, some of our plugins are published on woocommerce.com as well and need to be updated there as well. Currently, there is no way to automatically sync the documentation between GitHub and woocommerce.com. We do this manually. But due to the high frequency of updates, very often just small changes, we tend to forget or ignore the documentation update on woocommerce.com. Plus, our support team is responsible for updating the documentation on woocommerce.com and they are not always aware of the changes in the GitHub repo.

That's why we created this script.

This script will check if the documentation folder was updated recently and post all new commits to Slack. This way the support team is aware of the changes and can update the documentation on woocommerce.com. They also can acknowledge the update in Slack, and they can add a checkmark emoji to each commit message to indicate that the update was done.

![Demo](/assets/img/demo-1.png)

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








