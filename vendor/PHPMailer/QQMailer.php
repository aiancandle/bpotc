<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'src/Exception.php';
require 'src/PHPMailer.php';
require 'src/SMTP.php';

class QQMailer
{
  public $receiver=null;//发件人邮箱
  public $name="你好";//称呼
  public $subject=null;//主题
  public $body=null;//内容
  public $sender=null;//发件人邮箱
  public $password=null;//发件人密码
  public $attach=null;

  public function send()
  {
    $mail = new PHPMailer(true);
    try {
        //Server settings
        $mail->CharSet = "UTF-8";
        $mail->SMTPDebug = 0;
        $mail->isSMTP();                                      // 使用SMTP发送邮件
        $mail->Host = 'smtp.qq.com';              //SMTP邮件服务器地址（腾讯企业邮为例）
        $mail->SMTPAuth = true;
        $mail->Username = $this->sender;//admin@drawshell.cn                 // SMTP 发件人邮箱
        $mail->Password = $this->password;  //grbdktipxsrpbchd                       // SMTP 发件人邮箱密码
        $mail->SMTPSecure = 'tls';                            // Enable TLS encryption, `ssl` also accepted
        $mail->Port = 587;                                    // TCP port to connect to

        //发件人
        $mail->setFrom($this->sender, '订单通知系统');          //发件人邮箱（同 $mail->Username项设置）、发件人名称

      //收件人。多收件人可设置多个addAddress
        $mail->addAddress($this->receiver, $this->name);     //收件人邮箱地址，收件人姓名（选填）
        //$mail->addAddress('ellen@example.com');               // 收件人邮箱地址

        if($this->attach){
          $mail->addAttachment($this->attach);
          //$mail->addAttachment($this->attach, 'index.php');
        }

        // //发送附件
        // $mail->addAttachment('/var/tmp/file.tar.gz');         // 添加附件
        // $mail->addAttachment('/tmp/image.jpg', 'new.jpg');    // 设置附件以及附件名称

        //邮件内容
        $mail->isHTML(true);                                  //   发送html格式邮件
        $mail->Subject = $this->subject;                                //邮件标题
        $mail->Body    = $this->body;
        //$mail->AltBody = '邮件摘要';                    //目测没什么用，可去掉
        $mail->send();
        return 1;
    } catch (Exception $e) {
        return 0;
    }
  }
}
