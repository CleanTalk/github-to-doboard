<?php

namespace CleantalkHubGitKeeper\Response;


use CleantalkHubGitKeeper\Request\IssueRequest;
use CleantalkHubGitKeeper\Utils\Utils;

class DoBoardResponse extends ResponseBase
{
    private $session_id_path;

    private $session_id;

    private $request;

    /**
     * @throws \Exception
     */
    public function __construct()
    {
        global $app_dir;
        $this->session_id_path = $app_dir . '/.session_id';
        $this->session_id = $this->getSessionId();
    }

    public function process(IssueRequest $request)
    {
        $this->request = $request;
    }

    public function send($route = '')
    {
        try {
            $task_id = $this->taskAdd();
        } catch (\Exception $e) {
            // Second try to add task if the first one was failure
            $this->setSessionId();
            $task_id = $this->taskAdd();
        }
        
        $this->commentAdd($task_id);
        return true;
    }

    private function getSessionId()
    {
        $session_id = '';
        if ( file_exists($this->session_id_path) ) {
            $session_id  = @file_get_contents($this->session_id_path);

            if ( $session_id === false ) {
                $error = error_get_last();
                throw new \Exception($error['message']);
            }
        }

        if ( ! $session_id ) {
            $session_id = $this->setSessionId();            
        }

        return $session_id;
    }
    
    private function setSessionId()
    {
        $session_id = $this->authorize();
        $write_res = @file_put_contents($this->session_id_path, $session_id);
        if ( $write_res === false ) {
            $error = error_get_last();
            throw new \Exception($error['message']);
        }
        $this->session_id = $session_id;
        return $session_id;
    }

    private function authorize()
    {
        $data = [
            'email' => trim(Utils::getEnv('DOBOARD_USERNAME')),
            'password' => trim(Utils::getEnv('DOBOARD_PASSWORD')),
        ];
        $this->data = http_build_query($data);
        $this->url = 'https://api-ctask.cleantalk.org/';
        $raw_result = parent::send('user_authorize');
        $result = json_decode($raw_result, true, 512, JSON_THROW_ON_ERROR);
        if ( ! isset($result['data']['session_id']) ) {
            throw new \Exception('Auth failed');
        }
        return $result['data']['session_id'];
    }

    private function taskAdd()
    {
        $company_id = Utils::getEnv('DOBOARD_COMPANY_ID');
        $this->url = 'https://api.doboard.com/' . $company_id . '/task_add';
        $task_title = '[GitHub issue] ' . $this->request->issueTitle . ' (created automatically)';
        $data = array(
            'session_id' => $this->session_id,
            'name' => $task_title,
            'user_id' => Utils::getEnv('DOBOARD_AUTHOR_ID'),
            'project_id' => Utils::getEnv('DOBOARD_PROJECT_ID'),
        );
        $this->data = http_build_query($data);
        $raw_result = parent::send();
        $result = json_decode($raw_result, true, 512, JSON_THROW_ON_ERROR);
        if ( ! isset($result['data']['task_id']) ) {
            throw new \Exception('Task add wrong request: ' . $raw_result);
        }
        return $result['data']['task_id'];
    }

    private function commentAdd($task_id)
    {
        $company_id = Utils::getEnv('DOBOARD_COMPANY_ID');
        $this->url = 'https://api.doboard.com/' . $company_id . '/comment_add';
        $comment = sprintf(
            '<a href="%s">%s</a><br><br>%s',
            $this->request->issueUrl,
            $this->request->issueTitle,
            $this->request->body
        );
        $data = array(
            'session_id' => $this->session_id,
            'task_id' => $task_id,
            'comment' => $comment,
            'project_id' => Utils::getEnv('DOBOARD_PROJECT_ID'),
        );
        $this->data = http_build_query($data);
        $raw_result = parent::send();
        $result = json_decode($raw_result, true, 512, JSON_THROW_ON_ERROR);
        if ( ! isset($result['data']['comment_id']) ) {
            throw new \Exception('Comment add wrong request: ' . $raw_result);
        }
        return $result['data']['comment_id'];
    }
}
