<?php

namespace CleantalkHubGitKeeper;

use CleantalkHubGitKeeper\Request\IssueRequest;
use CleantalkHubGitKeeper\Response\DoBoardResponse;
use CleantalkHubGitKeeper\Response\TelegramResponse;

class HubGitKeeper
{
    private $allowed_actions = [
        'issues' => [
            'opened',
            'closed',
            'reopened',
        ],
        'issue_comment' => [
            'created',
        ]
    ];

    private $event;

    private $request;

    private $response;

    /**
     * @param string $event
     * @param array $content
     * @throws \Exception
     */
    public function __construct($event, $content)
    {
        if ( ! array_key_exists($event, $this->allowed_actions) ) {
            throw new \Exception('Provided event is not allowed.', 403);
        }
        if ( ! isset($content['action'], $content['issue']) ) {
            throw new \Exception('No action or issue details was provided.', 400);
        }
        $this->event = $event;
        $this->request = $this->getRequest($content);
    }

    public function run()
    {
        $this->response = $this->getResponse();
        $this->response->process($this->request);
        return $this->response;
    }

    private function getRequest($content)
    {
        return new IssueRequest($content, $this->event);
    }

    private function getResponse()
    {
        if ( ! in_array($this->request->action, $this->allowed_actions[$this->event], true) ) {
            throw new \Exception('Provided action is not allowed.', 403);
        }

        if ( $this->event === 'issues' && $this->request->action === 'opened' ) {
            return new DoBoardResponse();
        }

        return new TelegramResponse();
    }
}
