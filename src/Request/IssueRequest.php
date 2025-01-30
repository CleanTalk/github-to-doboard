<?php

namespace CleantalkHubGitKeeper\Request;

class IssueRequest
{
    public $action;
    public $author;
    public $authorUrl;
    public $body;
    public $issueUrl;
    public $issueTitle;

    public function __construct($content, $event = '')
    {
        if ( $event === 'issue_comment' && isset($content['issue']['pull_request']) ) {
            // No need to do anything on PR comment
            throw new \Exception('IssueRequest build filed: No need to do anything on PR comment.', 200);
        }

        if ( $event === 'issue_comment' && isset($content['comment']['author_association']) && $content['comment']['author_association'] === 'MEMBER' ) {
            // No need to notify about members comments
            throw new \Exception('IssueRequest build filed: No need to notify about members comments.', 200);
        }

        if (
            ! isset($content['action']) ||
            ! isset($content['issue']['html_url']) ||
            ! isset($content['issue']['title'])
        ) {
            throw new \Exception('IssueRequest build filed #1: wrong data provided.', 400);
        }

        $this->action = $content['action'];
        $this->issueUrl = $content['issue']['html_url'];
        $this->issueTitle = $content['issue']['title'];

        if ( $event === 'issues' ) {
            if (
                ! isset($content['sender']['login']) ||
                ! isset($content['sender']['html_url']) ||
                ! isset($content['issue']['body'])
            ) {
                throw new \Exception('IssueRequest build filed #2: wrong data provided.', 400);
            }

            $this->author = $content['sender']['login'];
            $this->authorUrl = $content['sender']['html_url'];
            $this->body = $content['issue']['body'];
        }

        if ( $event === 'issue_comment' ) {
            if (
                ! isset($content['comment']['user']['login']) ||
                ! isset($content['comment']['user']['html_url']) ||
                ! isset($content['comment']['body'])
            ) {
                throw new \Exception('IssueRequest build filed #3: wrong data provided.', 400);
            }

            $this->author = $content['comment']['user']['login'];
            $this->authorUrl = $content['comment']['user']['html_url'];
            $this->body = $content['comment']['body'];
        }
    }
}
