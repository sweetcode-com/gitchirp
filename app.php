<?php
require_once __DIR__ . '/vendor/autoload.php';

use Carbon\Carbon;

// load .env file which is located in the same directory as this file
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

// check if the required environment variables are set
if (!$_ENV['GITHUB_TOKEN']) {
    echo 'Please set the GITHUB_TOKEN environment variable';
    exit(1);
}

$GITHUB_TOKEN    = $_ENV['GITHUB_TOKEN'];
$SLACK_BOT_TOKEN = $_ENV['SLACK_BOT_TOKEN'];

$icon_emojy = ':robot_face:';
$channel    = $_ENV['SLACK_CHANNEL'];
$repo_url   = $_ENV['GITHUB_REPO_URL'];

// extract the repo name and subfolder from the repo URL
$parts     = explode('/', $repo_url);
$repo      = $parts[3] . '/' . $parts[4];
$subfolder = end($parts);

$guzzleClient = new \GuzzleHttp\Client();

// Calculate the date one week ago
$dateLastWeek = Carbon::now()->subWeek()->toIso8601String();

// echo "Getting commits since $dateLastWeek\n";

try {


    $response = $guzzleClient->get("https://api.github.com/repos/$repo/commits", [
        'headers' => [
            'Authorization' => "token $GITHUB_TOKEN"
        ],
        'query' => [
            'path'  => $subfolder,
            'since' => $dateLastWeek
        ]
    ]);

    $data = json_decode($response->getBody(), true);

    if (!empty($data)) {
        $commitMessages = [];

        foreach ($data as $commit) {

            $message = $commit['commit']['message'];
            // $message .= " - " . $commit['html_url'];
            $message .= " - <{$commit['html_url']}|View commit>";

            $commitMessages[] = $message;
        }

        $commitCount = count($commitMessages);

        $wc_com_edit_url = $_ENV['WC_COM_EDIT_URL'];
        $wc_rules_url = $_ENV['WC_RULES_URL'];

        // Send Slack notification using bot token
        $response = $guzzleClient->post('https://slack.com/api/chat.postMessage', [
            'headers' => [
                'Authorization' => "Bearer $SLACK_BOT_TOKEN"
            ],
            'json' => [
                'channel'         => $channel, // Replace with your Slack channel ID
                'text'            => "(gitchirp app) There are $commitCount commits in the last week for $repo/$subfolder. Please update the <{$wc_com_edit_url}|wc.com> docs and follow these <{$wc_rules_url}|rules>:",
                'reply_broadcast' => true,
                'icon_emoji'      => $icon_emojy,
            ]
        ]);

        // echo "Response: " . $response->getBody() . "\n";

        $thread_ts = json_decode($response->getBody(), true)['ts'];

        // echo "Thread ID: $thread_ts\n";

        foreach ($commitMessages as $commitMessage) {
            $guzzleClient->post('https://slack.com/api/chat.postMessage', [
                'headers' => [
                    'Authorization' => "Bearer $SLACK_BOT_TOKEN"
                ],
                'json' => [
                    'channel' => $channel, // Replace with your Slack channel ID
                    'text' => $commitMessage,
                    'thread_ts' => $thread_ts,
                    'icon_emoji' => $icon_emojy,
                ]
            ]);
        }
    }
} catch (Exception $e) {
    // Handle exceptions
    echo 'Caught exception: ',  $e->getMessage(), "\n";
}
