<?php

namespace CleantalkHubGitKeeper\Response;

use CleantalkHubGitKeeper\Utils\Utils;

class TelegramResponse extends ResponseBase
{
    const CONTENT_TYPE = 'application/json';

    public $actions = [
        'created' => 'comments the issue',
        'closed' => 'closed the issue',
        'reopened' => 'ceopened the issue',
    ];

    public function __construct()
    {
        $method = 'sendMessage';
        $this->url = 'https://api.telegram.org/bot' . Utils::getEnv('TG_BOT_API_KEY') . '/' . $method;
    }

    public function process($request)
    {
        $author = sprintf(
            '<a href="%s" target="_blank">%s</a>',
            $request->authorUrl,
            $request->author
        );
        $action = $this->actions[$request->action];
        $issue = sprintf(
            '<a href="%s" target="_blank">%s</a>',
            $request->issueUrl,
            $request->issueTitle
        );
        $message = "📢ℹ️ Achtung on $issue \n";
        $message .= "$author $action\n";
        $message .= $request->body ? "<code>$request->body</code>" : "";
        $this->data = [
            'chat_id' => Utils::getEnv('TG_CHAT_ID'),
            'text' => $message,
            'parse_mode' => 'HTML',
            'disable_web_page_preview' => true
        ];
        $this->data = json_encode($this->data);
    }
}
