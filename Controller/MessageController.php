<?php
require __DIR__ . '/../Model/message.php';
class MessageController
{
    private $messageModel;
    public function __construct($pdo){
        session_start();
        $this->messageModel = new Message($pdo);
    }
    private function response($success,$message){
        echo json_encode(['success' => $success, 'message' => $message]);
        exit();
    }
    // 留言板頁
    public function index()
    {
        require __DIR__ . '/../View/message/index.php';
    }
    // 新增留言頁
    public function create()
    {
        echo "這是GET請求，新增留言頁";
    }
    // 新增留言功能
    public function store()
    {
        header('Content-Type:application/json');
        // 取得前端JSON
        $input = json_decode(file_get_contents('php://input'), true);
        $message = trim($input['message'] ?? '');
        // 判斷留言是否為空
        if (empty($message)) {
            $this->response(false,'留言文字為空，無法新增');
        }
        // 新增留言
        $this->messageModel->create($_SESSION['user_id'], $message);
        $this->response(true,'留言新增成功');
    }
    // 顯示所有留言
    public function show(){
        header('Content-Type:application/json');
        $messages = $this->messageModel->getAll();
        $this->response(true,$messages);
    }
    // 更新留言功能
    public function update($id)
    {
        header('Content-Type:application/json');
        $input = json_decode(file_get_contents('php://input'), true);
        $newMessage = $input['new_message'] ?? null;
        // 訊息、ID是否為空
        if (empty($id) || empty($newMessage)) {
            $this->response(false,'資料不完整，編輯失敗');
        }
        // 查詢該筆資料
        $message = $this->messageModel->getMessage($id);
        // 資料是否存在
        if (!$message) {
            $this->response(false,'資料不存在，編輯失敗');
        }
        // 判斷是否有權限
        if ($_SESSION['user_id'] !== $message['user_id']) {
            $this->response(false,'權限不足，無法編輯');
        }
        // 判斷資料是否有更動
        if ($newMessage === $message['content']) {
            $this->response(false,'資料無異動');
        }
        // 執行編輯
        $this->messageModel->update($id, $newMessage);
        $this->response(true,'編輯成功！');
    }
    // 刪除留言功能
    public function delete($id)
    {
        header('Content-Type:application/json');
        // 判斷ID是否存在
        if (!isset($id)) {
            $this->response(false,'資料不完整，刪除失敗');
        }
        $message = $this->messageModel->getMessage($id);
        // 判斷是否有資料
        if (!$message) {
            $this->response(false,'資料不存在，刪除失敗');
        }
        // 判斷使用者是否相符
        if ($_SESSION['user_id'] !== $message['user_id']) {
            $this->response(false,'權限不足，無法刪除');
        }
        // 執行刪除
        $this->messageModel->delete($id);
        $this->response(true,'刪除成功！');
    }
}
