<?php
include "libs/db/db.php";
include "libs/db/select.php";

include "core.php";

include 'libs/phpmailer/Exception.php';
include 'libs/phpmailer/PHPMailer.php';
include 'libs/phpmailer/SMTP.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

$mail = new PHPMailer(true);
$mail->CharSet = 'UTF-8';
$mail->Mailer = 'smtp';
$mail->Host = 'ssl://smtp.jino.ru';
$mail->Port = 465;
$mail->SMTPAuth = true;
$mail->Username = 'support@hohlovdiplom.ru';
$mail->Password = 'aN58EwJksb7';
$mail->setFrom('support@hohlovdiplom.ru', 'hohlovdiplom');

/*сообщение на email
$mail->addAddress('glebhoh@yandex.ru', 'user');
$mail->Subject = 'Новое действие!';
$mail->msgHTML("<html><body>
                <h1>111!</h1>
                Новая заявка!<br>
                </html></body>");
$mail->send();
*/

$param = json_decode(file_get_contents("php://input"), true);

if(!isset($param['act'])) exit;
$act = $param['act'];

foreach($param as $key => $val){
    $param[$key] = htmlentities($val, ENT_QUOTES, 'UTF-8');
}

$response = [];

match(true) {
    
    //регистрация+
    $act===0 && isset($param['login'],
                      $param['pass'],
                      $param['email'],
                      $param['name']) => register(...['login'=>strtolower($param['login']),
                                                      'pass'=>md5($param['pass']),
                                                      'email'=>strtolower($param['email']),
                                                      'name'=>$param['name']]),
    //вход с формы+
    $act===1 && isset($param['login'],
                      $param['pass']) => login_form(...['login'=>strtolower($param['login']),
                                                        'pass'=>md5($param['pass'])]),
    //автовход по токену+
    $act===2 && isset($param['token']) => login_token(...['token'=>$param['token']]),
    
    //лента статей+
    $act===3 && isset($param['system'],
                      $param['u_id'],
                      $param['sort'],
                      $param['start_pos']) => art_feed(...['system'=>$param['system'],
                                                           'u_id'=>$param['u_id'],
                                                           'sort'=>$param['sort'],
                                                           'start_pos'=>$param['start_pos']]),
    //подписаться на юзера+
    $act===4 && isset($param['user_sub'],
                      $param['user_pub']) => add_pub(...['user_sub'=>$param['user_sub'],
                                                         'user_pub'=>$param['user_pub']]),
    
    //добавить статью+
    $act===5 && isset($param['title'],
                      $param['text'],
                      $param['name'],
                      $param['u_id'],
                      $param['token'],
                      $param['imgData'],
                      $param['sort']) => art_add(...['title'=>$param['title'], 
                                                     'text'=>$param['text'], 
                                                     'name'=>$param['name'],
                                                     'u_id'=>$param['u_id'],
                                                     'token'=>$param['token'],
                                                     'imgData'=>$param['imgData'],
                                                     'sort'=>$param['sort'], 
                                                     'date'=>date('d.m.Y')]),
    //отписаться от юзера+
    $act===6 && isset($param['user_sub'],
                      $param['user_pub']) => del_pub(...['user_sub'=>$param['user_sub'],
                                                         'user_pub'=>$param['user_pub']]),
    //добавить комментарий+
    $act===7 && isset($param['art_id'],
                      $param['name'], 
                      $param['token'],
                      $param['text']) => comment_add(...['art_id'=>$param['art_id'],
                                                         'name'=>$param['name'], 
                                                         'token'=>$param['token'],
                                                         'text'=>$param['text'], 
                                                         'date'=>date('d.m.Y')]),
    //аккаунт юзера+
    $act===8 && isset($param['user_id'],
                      $param['token']) => account_data(...['user_id'=>$param['user_id'],
                                                           'token'=>$param['token']]),
    
    //автовход по токену+
    $act===9 && isset($param['token']) => pub_feed(...['token'=>$param['token']]),
    
    //поиск+
    $act===10 && isset($param['text']) => search(...['text'=>$param['text']]),
    
    //создать обсуждение+
    $act===11 && isset($param['discuss'],
                       $param['pass'],
                       $param['token']) => discuss(...['discuss'=>$param['discuss'],
                                                       'pass'=>$param['pass'],
                                                       'token'=>$param['token']]),
    //все обсуждения+
    $act===12 => all_discuss(),
    
    //вход в дискуссию+
    $act===13 && isset($param['user_id'],
                       $param['token']) => account_data(...['user_id'=>$param['user_id'],
                                                            'token'=>$param['token']]),
                                                            
    //добавить сообщение в чат+
    $act===14 && isset($param['discuss_id'],
                       $param['user_name'], 
                       $param['text']) => message_add(...['discuss_id'=>$param['discuss_id'],
                                                          'user_name'=>$param['user_name'],
                                                          'text'=>$param['text'], 
                                                          'date'=>date('d.m.Y')]),
    //обновление чата+
    $act===15 && isset($param['chat_id'],
                       $param['last_message_id']) => chat_upload(...['chat_id'=>$param['chat_id'],
                                                                     'last_message_id'=>$param['last_message_id']]),
                                                                     
    //админ получает пароль на вход в закрытое обсуждение+
    $act===16 && isset($param['discuss_id'],
                       $param['token']) => get_pass(...['discuss_id'=>$param['discuss_id'],
                                                        'token'=>$param['token']]),
                                                        
    //удалить статью
    $act===17 && isset($param['discuss_id'],
                       $param['token']) => del_art(...['discuss_id'=>$param['discuss_id'],
                                                       'token'=>$param['token']]),
                                                       
    //удалить коммент
    $act===18 && isset($param['comment_id'],
                       $param['token']) => del_cmn(...['comment_id'=>$param['comment_id'],
                                                       'token'=>$param['token']]),
                                                       
    //удалить дискуссию
    $act===19 && isset($param['discuss_id'],
                       $param['token']) => del_discuss(...['discuss_id'=>$param['discuss_id'],
                                                           'token'=>$param['token']]),
    //удалить коммент
    $act===20 && isset($param['message_id'],
                       $param['token']) => del_msg(...['message_id'=>$param['message_id'],
                                                       'token'=>$param['token']]),
    
    //левый запрос
    default => exit
};

echo json_encode($response);
?>