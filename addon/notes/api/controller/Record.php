<?php
namespace addon\notes\api\controller;
use app\api\controller\BaseApi;
use addon\notes\model\Record as RecordModel;
/**
 * 文章点赞
 * @author Administrator
 *
 */
class Record extends BaseApi
{
    /**
     * 添加点赞
     */
    public function add()
    {
        $token = $this->checkToken();
        if ($token['code'] < 0) return $this->response($token);
        $note_id = isset($this->params['note_id']) ? $this->params['note_id'] : 0;
        if (empty($note_id)) {
            return $this->response($this->error('', 'REQUEST_NOTE_ID'));
        }
        $record_model = new RecordModel();
        $data         = [
            'member_id' => $token['data']['member_id'],
            'note_id'   => $note_id
        ];
        $res          = $record_model->addRecord($data);
        return $this->response($res);
    }
    /**
     * 删除点赞
     */
    public function delete()
    {
        $token = $this->checkToken();
        if ($token['code'] < 0) return $this->response($token);
        $note_id = isset($this->params['note_id']) ? $this->params['note_id'] : 0;
        if (empty($note_id)) {
            return $this->response($this->error('', 'REQUEST_NOTE_ID'));
        }
        $record_model = new RecordModel();
        $res          = $record_model->deleteRecord($token['data']['member_id'], $note_id);
        return $this->response($res);
    }
    /**
     * 是否点赞
     * @return string
     */
    public function isDianzan()
    {
        $token = $this->checkToken();
        if ($token['code'] < 0) return $this->response($token);
        $note_id = isset($this->params['note_id']) ? $this->params['note_id'] : 0;
        if (empty($note_id)) {
            return $this->response($this->error('', 'REQUEST_NOTE_ID'));
        }
        $record_model = new RecordModel();
        $res          = $record_model->getIsDianzan($note_id, $token['data']['member_id']);
        return $this->response($res);
    }
}