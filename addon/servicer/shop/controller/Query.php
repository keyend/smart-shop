<?php
declare(strict_types=1);
namespace addon\servicer\shop\controller;
use addon\servicer\model\Dialogue;
use app\shop\controller\BaseShop;
/**
 * 聊天记录查询
 */
class Query extends BaseShop
{
    /**
     * 聊天记录查询页面
     *
     * @return mixed
     */
    public function index()
    {
        return $this->fetch('query/index');
    }
    /**
     * 获取聊天记录
     *
     * @return mixed
     */
    public function getChatlogs()
    {
        // 当前请求为Ajax获取数据请求，执行获取数据逻辑
        if (request()->isAjax()) {
            $memberId = input('member_id', 0);
            $page = input('member_id', 0);
            $limit = input('member_id', 0);
            $dialogueList = (new Dialogue())->getDialogueList($memberId, $page, $limit);
            return success(0, '请求成功', $dialogueList);
        }
        return error();
    }
}