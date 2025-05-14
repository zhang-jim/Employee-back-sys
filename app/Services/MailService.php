<?php

namespace App\Services;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class MailService
{
    private $mailer;
    public function __construct()
    {
        $this->mailer = new PHPMailer(true);
    }

    private function init(){
        $this->mailer->isSMTP();
        $this->mailer->CharSet = 'UTF-8';
        $this->mailer->Host = $_ENV['MAIL_HOST'];
        $this->mailer->Port = $_ENV['MAIL_PORT'];
        $this->mailer->Username = $_ENV['MAIL_USERNAME'];
        $this->mailer->Password = $_ENV['MAIL_PASSWORD'];
        $this->mailer->SMTPSecure = $_ENV['MAIL_ENCRYPTION'];
        $this->mailer->SMTPAuth = true;
        $this->mailer->setFrom($_ENV['MAIL_FROM_ADDRESS'], $_ENV['MAIL_FROM_NAME']);
    }
    public function sendVerificationEmail(string $email, string $name, string $link)
    {
        $this->init();
        $body = <<<HTML
            <h2>Hi ，{$name}</h2>
            <p>感謝您註冊使用 XX 公司員工後台系統。</p>
            <p>請點擊以下按鈕完成信箱驗證：</p>
            <p>
              <a href="{$link}" style="padding: 10px 20px; background-color: #007bff; color: white; text-decoration: none; border-radius: 5px;">
                點我完成驗證
              </a>
            </p>
            <p>若您無法點擊按鈕，請將下列連結複製到瀏覽器開啟：</p>
            <p>{$link}</p>
        HTML;

        try {
            $this->mailer->clearAllRecipients();
            $this->mailer->isHTML(true);
            $this->mailer->addAddress($email, $name);
            $this->mailer->Subject = '歡迎使用XX公司員工後台系統，請完成信箱驗證';
            $this->mailer->Body = $body;
            $this->mailer->send();
            return true;
        } catch (\Throwable $th) {
            error_log("Mail Error: " . $this->mailer->ErrorInfo);
            return false;
        }
    }
}
