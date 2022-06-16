<?php

//РЕГИСТРАЦИЯ
function register(...$arr) {
    global $pdo;
    global $mail;
    global $response;
    
    $data = array(
        'login' => $arr['login'],
        'email' => $arr['email']
    );
    
    foreach(sel("SELECT * FROM users WHERE email = :email OR login = :login LIMIT 1", $data) as $doc) {
        if($doc === 'no') {
            $data = array(
                'login' => $arr['login'],
                'pass' => $arr['pass'],
                'email' => $arr['email'],
                'name' => $arr['name']
            );
            $token = md5(microtime()).rand(0, 9999);
            $sql = "INSERT INTO users (email, pass, name, login, token) VALUES(:email, :pass, :name, :login, '".$token."')";
            $statement = $pdo->prepare($sql);
            $statement->execute($data);
            
            $mail->addAddress($arr['email'], 'user');
            $mail->Subject = 'Завершение регистрации!';
            $mail->msgHTML("<html>
                                <body>
                                    Чтобы завершить регистрацию, пройдите по этой ссылке:<br>
                                    https://hohlovdiplom?register=".$token."
                                </body>
                            </html>");
            $mail->send();
            
            $response['act'] = 'register_ok';
        } else {
            $response['act'] = 'register_no';
        }
    }
}

//ВХОД С ФОРМЫ
function login_form(...$arr) {
    global $pdo;
    global $response;
    
    $data = array(
        'login' => $arr['login'],
        'pass' => $arr['pass']
    );
    
    foreach(sel("SELECT * FROM users WHERE login = :login AND pass = :pass LIMIT 1", $data) as $doc) {
        if($doc === 'no') {
            $response['act'] = 'login_form_no';
        } else {
            if($doc['active'] === 1 || $doc['active'] === 777) {
                $response['act'] = 'login_form_ok';
                $response['token'] = $doc['token'];
                $response['name'] = $doc['name'];
                $response['u_id'] = $doc['id'];
            }
            else $response['act'] = 'login_form_no_active';
        }
    }
}

//ВХОД С ТОКЕНА
function login_token(...$arr) {
    global $pdo;
    global $response;
    
    $data = array(
        'token' => $arr['token']
    );
    
    foreach(sel("SELECT * FROM users WHERE token = :token LIMIT 1", $data) as $doc) {
        if($doc === 'no') {
            $response['act'] = 'login_token_no';
        } else {
            $response['act'] = 'login_token_ok';
            $response['name'] = $doc['name'];
            $response['u_id'] = $doc['id'];
        }
    }
}

//ДОБАВИТЬ СТАТЬЮ
function art_add(...$arr) {
    global $pdo;
    global $response;
    $new_name_img = 'no';
    if($arr['imgData'] !== 'no') {
        $imgData = str_replace(" ", "+", $arr['imgData']);
        $checkSize = strlen(base64_decode($imgData));
        if($checkSize <= 5242880) {
            $name = md5(microtime().rand(0, 9999)).rand(0, 9999);
            function base64_to_gif($base64_string, $output_file) {
                $ifp = fopen($output_file, "wb");
                $data = explode(',', $base64_string);
                fwrite($ifp, base64_decode($data[1]));
                fclose($ifp);
                return $output_file;
            }
            base64_to_gif($imgData, 'user_img/'.$name.'.png');
            $new_name_img = $name;
        }
    }
    $data = array(
    	'title' => $arr['title'],
    	'text' => $arr['text'],
    	'date' => $arr['date'],
    	'u_id' => $arr['u_id'],
    	'sort' => $arr['sort'],
    	'u_name' => $arr['name'],
    	'u_token' => $arr['token'],
    	'img' => $new_name_img
    );
    $sql = "INSERT INTO art (title, text, date, sort, u_name, u_token, u_id, img) VALUES(:title, :text, :date, :sort, :u_name, :u_token, :u_id, :img)";
    $statement = $pdo->prepare($sql);
    $statement->execute($data);
    
    $upd_count = array(
    	'u_token' => $arr['token']
    );
    $sql = "UPDATE users SET count_art = count_art+1 WHERE token = :u_token LIMIT 1";
    $statement = $pdo->prepare($sql);
    $statement->execute($upd_count);
    
    $response['act'] = 'art_add_ok';
}

//ЛИЧНЫЙ КАБИНЕТ
function account_data(...$arr) {
    global $pdo;
    global $response;
    
    $data = array(
        'id' => $arr['user_id']
    );
    $data1 = array(
        'id' => $arr['user_id'],
        'token' => $arr['token']
    );
    
    foreach(sel("SELECT * FROM users WHERE id = :id LIMIT 1", $data) as $doc) {
        if($doc !== 'no') {
            $response['act'] = 'account_data_ok';
            if($doc['token'] === $arr['token']) $response['who'] = 'my';
            else {
                $response['who'] = 'nmy';
                foreach(sel("SELECT * FROM sub WHERE pub = :id AND sub = :token LIMIT 1", $data1) as $doc2) {
                    if($doc2 === 'no') {
                        $response['sub'] = 'no';
                    } else {
                        $response['sub'] = 'ok';
                    }
                }
            }
            $response['name'] = $doc['name'];
            $response['count_art'] = $doc['count_art'];
            $response['count_discuss'] = $doc['count_discuss'];
            $response['count_comments'] = $doc['count_comments'];
            $response['count_sub'] = $doc['count_sub'];
            $response['pub_id'] = $doc['id'];
        }
    }
}

//ЛЕНТА СТАТЕЙ
function art_feed(...$arr) {
    global $pdo;
    global $response;

    $system = $arr['system'];
    $u_id = $arr['u_id'];
    $sort = $arr['sort'];
    $start_pos = $arr['start_pos'];
    
    $sql = '';
    $data = [];
    $act_gen = '';
    
    //статьи юзера
    if($system === 'u_arts_start') {
        $sql = "SELECT * FROM art WHERE u_id = :u_id ORDER BY id DESC";
        $data = array(
            'u_id' => $arr['u_id']
        );
        $act_gen = 'u_arts_start_';
    }
    
    //все статьи
    else if($system === 'all_arts') {
        if($sort==='no' && $start_pos==='no') {
            $sql = "SELECT * FROM art ORDER BY id DESC LIMIT 20";
            $data = 'no';
            $act_gen = 'all_arts_';
        }
    }
    
    //все статьи сортировка
    else if($system === 'all_arts_sort') {
        if($sort!=='no' && $start_pos==='no') {
            $sql = "SELECT * FROM art WHERE sort = :sort ORDER BY id DESC LIMIT 20";
            $data = array(
                'sort' => $sort
            );
            $act_gen = 'all_arts_sort_';
        }
    }
    
    //подгрузка
    else if($system === 'all_arts_next') {
        if($sort!=='no' && $start_pos!=='no') {
            if($sort!=='all') {
                $sql = "SELECT * FROM art WHERE sort = :sort ORDER BY id DESC LIMIT :start, 20";
                $data = array(
                    'sort' => $sort,
                    'start' => $start_pos
                );
                $act_gen = 'all_arts_next_';
            }
            else {
                $sql = "SELECT * FROM art ORDER BY id DESC LIMIT :start, 20";
                $data = array(
                    'start' => $start_pos
                );
                $act_gen = 'all_arts_next_';
            }
        }
    }
    
    $count=0;
    foreach(sel($sql, $data) as $doc) {
        if($doc !== 'no') {
            if($count===0) $response['act'] = $act_gen.'ok';
            $response['all']['sd'.$doc['id']]['id'] = $doc['id'];
            $response['all']['sd'.$doc['id']]['title'] = $doc['title'];
            $response['all']['sd'.$doc['id']]['date'] = $doc['date'];
            $response['all']['sd'.$doc['id']]['sort'] = $doc['sort'];
            $response['all']['sd'.$doc['id']]['img'] = $doc['img'];
            $count++;
        }
        else $response['act'] = $act_gen.'no';
    }
    if($count!==0) $response['next_start'] = $count;
}

//ПОДПИСКА
function add_pub(...$arr) {
    global $pdo;
    global $response;
    
    $data = array(
        'id' => $arr['user_pub'],
        'token' => $arr['user_sub']
    );
    
    foreach(sel("SELECT * FROM sub WHERE sub = :token AND pub = :id LIMIT 1", $data) as $doc) {
        if($doc === 'no') {
            $response['act'] = 'add_pub_ok';
            $sql = "INSERT INTO sub (sub, pub) VALUES(:token, :id)";
            $statement = $pdo->prepare($sql);
            $statement->execute($data);
        } else {
            $response['act'] = 'add_pub_no';
        }
    }
    
    $upd_count = array(
    	'id' => $arr['user_pub'],
    );
    $sql = "UPDATE users SET count_sub = count_sub+1 WHERE id = :id LIMIT 1";
    $statement = $pdo->prepare($sql);
    $statement->execute($upd_count);
}

//ОТПИСКА
function del_pub(...$arr) {
    global $pdo;
    global $response;
    
    $data = array(
        'id' => $arr['user_pub'],
        'token' => $arr['user_sub']
    );
    
    foreach(sel("SELECT * FROM sub WHERE sub = :token AND pub = :id LIMIT 1", $data) as $doc) {
        if($doc === 'no') {
            $response['act'] = 'del_pub_no';
        } else {
            $response['act'] = 'del_pub_ok';
            $sql = "DELETE FROM sub WHERE sub = :token AND pub = :id";
            $statement = $pdo->prepare($sql);
            $statement->execute($data);
        }
    }
    
    $upd_count = array(
    	'id' => $arr['user_pub'],
    );
    $sql = "UPDATE users SET count_sub = count_sub-1 WHERE id = :id LIMIT 1";
    $statement = $pdo->prepare($sql);
    $statement->execute($upd_count);
}

//ЛЕНТА СТАТЕЙ ПОДПИСОК
function pub_feed(...$arr) {
    global $pdo;
    global $response;

    $data = array(
        'token' => $arr['token']
    );
    $response['act'] = 'pub_feed_ok';
    $ht = '';
    foreach(sel("SELECT * FROM sub WHERE sub = :token ORDER BY id DESC", $data) as $doc) {
        
        if($doc !== 'no') {
            
            //автор статей (публикатор для подписки)
            foreach(sel("SELECT * FROM users WHERE id = '".$doc['pub']."' LIMIT 1") as $doc2) {
                if($doc2 !== 'no') {
                    
                    $ht=$ht.
                    "
                    <div class='pub_feed_autor'>Последние статьи автора: <div onclick=\"send_open_personal('".$doc2['id']."')\"><font color='#db0f3f'>".$doc2['name']."</font></div></div>
                    ";
                }
            }
            
            //статьи этого автора-публикатора подписки
            foreach(sel("SELECT * FROM art WHERE u_id = '".$doc['pub']."' LIMIT 3") as $doc2) {
                if($doc2 !== 'no') {
                    
                    $imgGen = '';
                    if($doc2['img'] !== 'no') $imgGen = "<div class='img'><img src='../php/user_img/".$doc2['img'].".png'></div>";
                    
                    $ht=$ht.
                    "
                    <div class='one_art' onclick=\"open_one_art('".$doc2['id']."')\">
                        ".$imgGen."
                        <div class='sort'>".$doc2['sort']."<div class='date'>".$doc2['date']."</div></div>
                        <div class='title'>".$doc2['title']."</div>
                    </div>
                    ";
                } else {
                    $ht=$ht."<div class='no_art'>Нет опубликованных статей...</div>";
                }
            }
        }
        else {
            $response['act'] = 'pub_feed_no';
        }
    }
    $ht=$ht.'<br><br>';
    $response['html'] = $ht;
}

//ДОБАВИТЬ КОММЕНТ
function comment_add(...$arr) {
    global $pdo;
    global $response;
    
    $data = array(
        'art_id' => $arr['art_id'],
        'name' => $arr['name'],
        'text' => $arr['text'],
        'date' => $arr['date'],
    );
    
    $sql = "INSERT INTO comments (art_id, name, text, date) VALUES(:art_id, :name, :text, :date)";
    $statement = $pdo->prepare($sql);
    $statement->execute($data);
    
    $response['act'] = 'comment_add_ok';
    
    $upd_count = array(
    	'token' => $arr['token'],
    );
    $sql = "UPDATE users SET count_comments = count_comments+1 WHERE token = :token LIMIT 1";
    $statement = $pdo->prepare($sql);
    $statement->execute($upd_count);
}

//ПОИСК
function search(...$arr) {
    global $pdo;
    global $response;
    
    $data = array(
        'title' => $arr['text']
    );
    $response['act'] = 'search_ok';
    $response['text'] = $arr['text'];
    foreach(sel("SELECT * FROM art WHERE title LIKE CONCAT('%', :title, '%') LIMIT 10", $data) as $doc) {
        if($doc !== 'no') {
            $response['all'][$doc['id']]['id'] = $doc['id'];
            $response['all'][$doc['id']]['title'] = $doc['title'];
        } 
        else {
            $response['act'] = 'search_no';
        }
    }
}

//СОЗДАТЬ ОБСУЖДЕНИЕ
function discuss(...$arr) {
    global $pdo;
    global $response;
    
    $pass='';
    
    if($arr['pass']!=='') {
        $pass=htmlentities($arr['pass']);
    }
    
    $data = array(
        'name' => $arr['discuss'],
        'pass' => $pass,
        'token' => $arr['token'],
    );
    
    $sql = "INSERT INTO discuss (name, pass, u_token) VALUES(:name, :pass, :token)";
    $statement = $pdo->prepare($sql);
    $statement->execute($data);
    
    $name_back = mb_strimwidth($arr['discuss'], 0, 200, "");
    
    $response['act'] = 'add_new_discuss_ok';
    
    $upd_count = array(
    	'token' => $arr['token'],
    );
    $sql = "UPDATE users SET count_discuss = count_discuss+1 WHERE token = :token LIMIT 1";
    $statement = $pdo->prepare($sql);
    $statement->execute($upd_count);
}

//ВСЕ ОБСУЖДЕНИЯ
function all_discuss() {
    global $pdo;
    global $response;
    
    $response['act'] = 'all_discuss_ok';

    foreach(sel("SELECT * FROM discuss ORDER BY id DESC") as $doc) {
        if($doc !== 'no') {
            $response['all']['id'.$doc['id']]['id'] = $doc['id'];
            $response['all']['id'.$doc['id']]['name'] = $doc['name'];
            if($doc['pass']==='') {
                $response['all']['id'.$doc['id']]['join'] = 'open';
            } else {
                $response['all']['id'.$doc['id']]['join'] = 'close';
            }
        } 
        else {
            $response['act'] = 'all_discuss_no';
        }
    }
}

//ДОБАВИТЬ СООБЩЕНИЕ В ЧАТ
function message_add(...$arr) {
    global $pdo;
    global $response;
    
    $data = array(
        'discuss_id' => $arr['discuss_id'],
        'user_name' => $arr['user_name'],
        'text' => $arr['text'],
        'date' => $arr['date'],
    );
    
    $sql = "INSERT INTO discuss_messages (discuss_id, user_name, text, date) VALUES(:discuss_id, :user_name, :text, :date)";
    $statement = $pdo->prepare($sql);
    $statement->execute($data);
    
    $response['act'] = 'message_add_ok';
}

//ОБНОВЛЕНИЕ ЧАТА
function chat_upload(...$arr) {
    global $pdo;
    global $response;
    
    $response['act'] = 'chat_upload_ok';
    
    $data = array(
        'chat_id' => $arr['chat_id'],
        'last_message_id' => $arr['last_message_id']
    );
    
    foreach(sel("SELECT * FROM discuss_messages WHERE discuss_id = :chat_id AND id > :last_message_id", $data) as $doc2) {
        if($doc2 === 'no') {
            $response['act'] = 'chat_upload_no';
        } else {
            $response['all']['id'.$doc2['id']]['user_name']=$doc2['user_name'];
            $response['all']['id'.$doc2['id']]['text']=$doc2['text'];
            $response['all']['id'.$doc2['id']]['date']=$doc2['date'];
            $response['all']['id'.$doc2['id']]['id']=$doc2['id'];
            $response['last_id']=$doc2['id'];
        }
    }
}

//админ получает пароль на вход в закрытое обсуждение
function get_pass(...$arr) {
    global $pdo;
    global $response;
    $data = array(
        'token' => $arr['token']
    );
    $data2 = array(
        'id' => $arr['discuss_id']
    );
    foreach(sel("SELECT * FROM users WHERE id = 1 AND token = :token LIMIT 1", $data) as $doc2) {
        if($doc2 !== 'no') {
            $response['act'] = 'get_pass';
            foreach(sel("SELECT * FROM discuss WHERE id = :id LIMIT 1", $data2) as $doc3) {
                if($doc3 !== 'no') {
                    $response['pass'] = $doc3['pass'];
                }
            }
        }
    }
}

//удалить статью
function del_art(...$arr) {
    global $pdo;
    global $response;
    $data = array(
        'token' => $arr['token']
    );
    $data2 = array(
        'id' => $arr['discuss_id']
    );
    foreach(sel("SELECT * FROM users WHERE id = 1 AND token = :token LIMIT 1", $data) as $doc2) {
        if($doc2 !== 'no') {
            $response['act'] = 'del_art';
            
            foreach(sel("SELECT * FROM art WHERE id = :id LIMIT 1", $data2) as $doc2) {
                if($doc2 !== 'no') {
                    
                    $filename = 'user_img/'.$doc2['img'].'.png';
                    
                    if (file_exists($filename)) {
                        unlink($filename);
                    }
                }
            }
            
            $sql = "DELETE FROM art WHERE id = :id";
            $statement = $pdo->prepare($sql);
            $statement->execute($data2);
            
            $sql = "DELETE FROM comments WHERE art_id = :id";
            $statement = $pdo->prepare($sql);
            $statement->execute($data2);
        }
    }
}

//удалить коммент
function del_cmn(...$arr) {
    global $pdo;
    global $response;
    $data = array(
        'token' => $arr['token']
    );
    $data2 = array(
        'id' => $arr['comment_id']
    );
    foreach(sel("SELECT * FROM users WHERE id = 1 AND token = :token LIMIT 1", $data) as $doc2) {
        if($doc2 !== 'no') {
            $response['act'] = 'del_comment';
            
            $sql = "DELETE FROM comments WHERE id = :id";
            $statement = $pdo->prepare($sql);
            $statement->execute($data2);
        }
    }
}

//удалить дискуссию
function del_discuss(...$arr) {
    global $pdo;
    global $response;
    $data = array(
        'token' => $arr['token']
    );
    $data2 = array(
        'id' => $arr['discuss_id']
    );
    foreach(sel("SELECT * FROM users WHERE id = 1 AND token = :token LIMIT 1", $data) as $doc2) {
        if($doc2 !== 'no') {
            $response['act'] = 'del_discuss';
            
            $sql = "DELETE FROM discuss WHERE id = :id";
            $statement = $pdo->prepare($sql);
            $statement->execute($data2);
            
            $sql = "DELETE FROM discuss_messages WHERE discuss_id = :id";
            $statement = $pdo->prepare($sql);
            $statement->execute($data2);
        }
    }
}

//удалить сообщение
function del_msg(...$arr) {
    global $pdo;
    global $response;
    $data = array(
        'token' => $arr['token']
    );
    $data2 = array(
        'id' => $arr['message_id']
    );
    foreach(sel("SELECT * FROM users WHERE id = 1 AND token = :token LIMIT 1", $data) as $doc2) {
        if($doc2 !== 'no') {
            $response['act'] = 'del_messages';
            
            $sql = "DELETE FROM discuss_messages WHERE id = :id";
            $statement = $pdo->prepare($sql);
            $statement->execute($data2);
        }
    }
}











?>