<!DOCTYPE html>
<html lang="ru">
<head>
    <title>Site Name</title>
    <meta charset="UTF-8">
    <link rel="icon" href="img/favicon.ico" type="image/x-icon">
    <link rel="stylesheet" type="text/css" href="css/style.css">
</head>
<body>
	
	<!--шапка-->
	<div class='top'>
	    <div class='logo'>Site Name</div>
	    <div class='menu' align='center'>
	        <button id='my_feed' onclick='menu(1)'>МОЯ ЛЕНТА</button>
	        <button id='all_feed' onclick='menu(2)'>ВСЕ СТАТЬИ</button>
	        <button id='add_art' onclick='menu(3)'>НАПИСАТЬ СТАТЬЮ</button>
	        <button id='discuss' onclick='menu(4)'>ОБСУЖДЕНИЯ</button>
	        <button id='search' onclick='menu(5)'>ПОИСК</button>
	    </div>
	    <div id='aut'></div>
	</div>
	
	<!--контент-->
	<div class='content'>
	    <!--все статьи-->
	    <div id='content_all_feed'>
	        <div id='content_all_feed_arts' align='center' data-sort='all' data-pos='0' onscroll='send_load_next_arts()'></div>
	        <div id='content_all_feed_sort' align='center'>
	            <br>
                <button id='btn_sort_1' class='group_btn_sort' onclick="send_open_sort_feed(this.innerHTML, 'no', 'btn_sort_1')">Режиссура</button>
                <button id='btn_sort_2' class='group_btn_sort' onclick="send_open_sort_feed(this.innerHTML, 'no', 'btn_sort_2')">Сценарий</button>
                <button id='btn_sort_3' class='group_btn_sort' onclick="send_open_sort_feed(this.innerHTML, 'no', 'btn_sort_3')">Продюсирование</button>
                <button id='btn_sort_4' class='group_btn_sort' onclick="send_open_sort_feed(this.innerHTML, 'no', 'btn_sort_4')">Операторская</button>
                <button id='btn_sort_5' class='group_btn_sort' onclick="send_open_sort_feed(this.innerHTML, 'no', 'btn_sort_5')">Звук</button>
                <button id='btn_sort_6' class='group_btn_sort' onclick="send_open_sort_feed(this.innerHTML, 'no', 'btn_sort_6')">Монтаж</button>
                <button id='btn_sort_7' class='group_btn_sort' onclick="send_open_sort_feed(this.innerHTML, 'no', 'btn_sort_7')">Новости кино</button>
                <button id='btn_sort_8' class='group_btn_sort' onclick="send_open_sort_feed(this.innerHTML, 'no', 'btn_sort_8')">Обзорные статьи</button>
	        </div>
	    </div>
	    <!--одна статья-->
	    <div id='content_one_art'>
	        <div id='content_one_art_art' align='center'></div>
	        <div id='content_one_art_autor' align='center'></div>
	    </div>
	    <!--статьи подписок-->
	    <div id='content_pubs' align='center'>
	        <div id='content_pubs_art' align='center'></div>
	    </div>
	    <!--кабинет пользователя-->
	    <div id='content_user_personal'>
	        <div id='content_user_personal_arts' align='center'></div>
	        <div id='content_user_personal_info' align='center'></div>
	    </div>
	</div>
	
	<!--модалки-->
	<div id='modal'>
	    <!--поиск-->
	    <div id='modal_search'>
	        <div class='top_txt'>ПОИСК СТАТЕЙ:</div>
	        <img src='img/close.svg' class='close' onclick='close_search_modal()'>
	        <div class='body'>
	            <input type='text' id='search_txt' placeholder='Начните вводить текст...' oninput='start_search()'>
	            <div id='search_resp'></div>
	        </div>
	    </div>
	    <!--обсуждения-->
	    <div id='modal_discuss'>
	        <div class='top_txt'>ОБСУЖДЕНИЯ:</div>
	        <!--список комнат-->
	        <div class='body' id='discuss_all_list'>
	            <div id='all_discuss'></div>
	            <div id='add_discuss'>
	                <div class='txt'>Создание нового обсуждения:</div>
	                <div class='data'>
	                    <input id='text_discuss' placeholder='Тема для обсуждения...'>
	                    <input id='pass_discuss' placeholder='Пароль (необязательно)'>
	                </div>
	                <button id='add_discuss_go' onclick='add_new_discuss()'>СОЗДАТЬ НОВОЕ ОБСУЖДЕНИЕ</button>
	            </div>
	        </div>
	        <!--пароль - прослойка-->
	        <div class='pass' id='discuss_pass'>
	            <div class='back' onclick='open_discuss_modal()'>НАЗАД К ВЫБОРУ ДИСКУССИИ</div>
	            <div class='body'>
    	            <div>Это закрытое обсуждение!</div>
    	            <input id='discuss_id' hidden>
    	            <input id='discuss_pass_text' placeholder='введите пароль'>
    	            <button id='discuss_pass_go' onclick='open_discuss_pass()'>ВОЙТИ</button>
	            </div>
	        </div>
	        <!--чат-->
	        <div class='chat' id='discuss_chat'>
	            <div class='back' onclick='open_discuss_modal()'>НАЗАД К ВЫБОРУ ДИСКУССИИ</div>
	            <input id='last_message_id' hidden>
	            <button class='delete' onclick='del_discuss()'>УДАЛИТЬ ДИСКУССИЮ</button>
	            <div id='all_message'></div>
	            <div class='bottom'>
	                <input id='message_text' placeholder='Введите своё сообщение...'>
	                <button id='message_go' onclick='message_add()'>OK</button>
	            </div>
	        </div>
	        <img src='img/close.svg' class='close' onclick='close_discuss_modal()'>
	    </div>
	    <!--вход-->
	    <form id='modal_login'>
	        <br>
	        <div>Логин:</div>
	        <input type="text" id='login_login' maxlength="16" placeholder='username' autocomplete='off' pattern="[A-Za-z0-9]{6,16}" title="Логин 6-16 символов. Только латинские буквы и цифры!" required>
	        <div>Пароль:</div>
	        <input type='password' id='login_pass' maxlength="16" placeholder='******' autocomplete='off' pattern="[A-Za-z0-9]{8,16}" title="Пароль 8-16 символов. Только латинские буквы и цифры!" required>
	        <br>
	        <button id='login_go'>ВОЙТИ В АККАУНТ</button>
	        
	        <div class='top_txt'>ВХОД:</div>
	        <img src='img/close.svg' class='close' onclick='login_close()'>
	    </form>
	    <!--регистрация-->
	    <form id='modal_register' autocomplete="off">
	        <br>
	        <div>Имя:</div>
	        <input type="text" id='register_name' class='register_group' maxlength="16" placeholder='Иван' autocomplete='off' pattern="[А-Яа-яЁё]{1,16}" title="Имя не более 16 символов. Только русские буквы!" required>
	        <div>E-mail:</div>
	        <input type="email" id='register_email' class='register_group' maxlength="26" placeholder='example@gmail.com' autocomplete='off' required>
	        <div>Логин:</div>
	        <input type="text" id='register_login' class='register_group' maxlength="16" placeholder='username' autocomplete='off' pattern="[A-Za-z0-9]{6,16}" title="Логин 6-16 символов. Только латинские буквы и цифры!" required>
	        <div>Пароль:</div>
	        <input type='password' id='register_pass1' class='register_group' maxlength="16" placeholder='******' autocomplete='off' pattern="[A-Za-z0-9]{8,16}" title="Пароль 8-16 символов. Только латинские буквы и цифры!" required>
	        <div>Повторите пароль:</div>
	        <input type='password' id='register_pass2' class='register_group' maxlength="16" placeholder='******' autocomplete='off' pattern="[A-Za-z0-9]{8,16}" title="Пароль 8-16 символов. Только латинские буквы и цифры!" required>
	        <br>
	        <button id='register_go' class='register_group'>СОЗДАТЬ АККАУНТ</button>
	        
	        <div class='top_txt'>РЕГИСТРАЦИЯ:</div>
	        <img src='img/close.svg' class='close' onclick='register_close()'>
	    </form>
	    <!--добавить статью-->
	    <form id='modal_add_art' autocomplete="off">
	        <br>
	        <div class='txt'>Заголовок:</div>
	        <input type="text" id='add_art_title' maxlength="300" placeholder='Новый фильм от Marvel' autocomplete='off' required>
	        <div class='txt'>Описание:</div>
	        <textarea id='add_art_text' maxlength="10000" placeholder='Новый фильм про супергероев и о том, как они спасли мир...' autocomplete='off' required></textarea>
	        <div class='bottom'>
	            <label>
	                <input id='add_art_file' type='file' onchange='add_art_file_change()' hidden>
	                <div id='add_art_file_mask'>ВЫБРАТЬ ИЗОБРАЖЕНИЕ</div>
	            </label>
	            <div>
    	            <div>Категория:</div>
    	            <select id='add_art_sort'>
    					<option value='Режиссура' label = "Режиссура">
    					<option value='Сценарий' label = "Сценарий">
    					<option value='Продюсирование' label = "Продюсирование">
    					<option value='Операторская работа' label = "Операторская работа">
    					<option value='Звук' label = "Звук">
    				    <option value='Монтаж' label = "Монтаж">
    			        <option value='Новости кино' label = "Новости кино">
    			        <option value='Обзорные статьи' label = "Обзорные статьи">
    				</select>
				</div>
	        </div>
	        <br>
	        <button id='add_art_go'>ДОБАВИТЬ СТАТЬЮ</button>
	        
	        <div class='top_txt'>НОВАЯ СТАТЬЯ:</div>
	        <img src='img/close.svg' class='close' onclick='add_art_close()'>
	    </form>
	</div>
	
	<!--модал инфо-->
	<div id='modal_info_fon'>
	    <div id='modal_info_div'>
	        <div id='modal_info_text'></div>
	        <img src='img/close.svg' class='close' onclick='info_close()'>
	    </div>
	</div>
	
	<script src="js/resp.js"></script>
	<script src="js/chat.js"></script>
	<script src="js/send.js"></script>
	<script src="js/evt.js"></script>
	<script src="js/ls.js"></script>
	
	<?php
        include "php/libs/db/db.php";
        include "php/libs/db/select.php";
        //подтверждение регистрации
        if(isset($_GET['register'])) {
            $token = htmlentities($_GET['register'], ENT_QUOTES, 'UTF-8');
            $data = array(
                'token' => $token
            );
            foreach(sel("SELECT * FROM users WHERE token = :token AND active = 0 LIMIT 1", $data) as $doc) {
        		if($doc !== 'no') {
        			$sql = "UPDATE users SET active = 1 WHERE token = :token LIMIT 1";
                    $statement = $pdo->prepare($sql);
                    $statement->execute($data);
                    echo 
                    "
                    <script>
                        info_open('Аккаунт активирован!');
                        history.pushState(null, null, '/');
                    </script>
                    ";
        		}
        	}
        }
        //статья добавлена
        else if(isset($_GET['add_art'])) {
            echo 
            "
            <script>
                info_open('Ваша статья опубликована!');
                send_open_all_feed('no', 'no');
                history.pushState(null, null, '/');
            </script>
            ";
        }
        //одна новость
        else if(isset($_GET['one_art'])) {
            
            echo 
            "
            <script>
                send_open_one_art();
            </script>
            ";
            
            $id = htmlentities($_GET['one_art'], ENT_QUOTES, 'UTF-8');
            $data = array(
                'id' => $id
            );
            foreach(sel("SELECT * FROM art WHERE id = :id LIMIT 1", $data) as $doc) {
                if($doc !== 'no') {
                    $all_comments = '';
                    foreach(sel("SELECT * FROM users WHERE id = '".$doc['u_id']."' LIMIT 1") as $doc2) {
                        if($doc2 !== 'no') {
                            echo
                            "
                            <script>
                            content_one_art_autor.innerHTML = 
                            `
                            <div>Автор: <font color='#db0f3f'>".$doc2['name']."</font></div>
                            <button onclick=\"send_open_personal('".$doc2['id']."')\">АККАУНТ АВТОРА</button>
                            `;
                            </script>
                            ";
                            
                            //собираем сразу комменты
                            foreach(sel("SELECT * FROM comments WHERE art_id = :id", $data) as $doc3) {
                                if($doc3 !== 'no') {
                                    
                                    $all_comments = $all_comments.
                                    "
                                    <div class='one_comment'>
                                        <div class='autor'><div onclick=\"send_open_personal('".$doc2['id']."')\">".$doc2['name']."</div></div>
                                        <div class='txt'>
                                            ".$doc3['text']."
                                            <div>".$doc3['date']."</div>
                                        </div>
                                        <button class='delete' onclick=\"del_comment('".$doc3['id']."')\">УДАЛИТЬ</button>
                                    </div>
                                    ";
                                }
                                else {
                                    $all_comments = "<div class='no_comment'>Нет комментариев...</div>";
                                }
                            }
                        }
                    }
                    
                    //само тело статьи и комменты
                    $imgGen = '';
                    if($doc['img'] !== 'no') $imgGen = "<div class='img'><img src='../php/user_img/".$doc['img'].".png'></div>";
                    
                    echo
                    "
                    <script>
                    content_one_art_art.innerHTML = 
                    `
                    <div>
                        <div class='date'>Дата публикации: <div>".$doc['date']."</div></div>
                        <div class='title'>".$doc['title']."</div>
                        ".$imgGen."
                        <div class='sort'>".$doc['sort']."</div>
                        <div class='txt'>".$doc['text']."</div>
                        <button class='delete' onclick=\"del_art('".$doc['id']."')\">УДАЛИТЬ СТАТЬЮ</button>
                        <br>
                        
                        <div class='all_comment' id='all_comment_id'>
                            <div class='all_comment_top'>Комментарии:</div>
                            
                            ".$all_comments."
                            
                        </div>
                        
                        <div class='add_comment'>
                            <div class='add_comment_top'>Добавить комментарий:</div>
                            <div class='add_comment_body'>
                                <input id='add_comment_text' type='text' maxlength='200' placeholder='Напишите свой комментарий...'>
                                <button id='add_comment_go' onclick=\"send_add_comment('".$doc['id']."')\">ОК</button>
                            </div>
                        </div>
                        
                        <br>
                    </div>
                    `;
                    </script>
                    ";
                    
                }
            }
        }
        //чат-обсуждение
        else if(isset($_GET['discuss'])) {
            
            //обсуждение без пароля
            if(!isset($_GET['join'])) {
                
                $id = htmlentities($_GET['discuss'], ENT_QUOTES, 'UTF-8');
                $data = array(
                    'id' => $id
                );
                foreach(sel("SELECT * FROM discuss WHERE id = :id LIMIT 1", $data) as $doc) {
                    if($doc === 'no') {
                        //ошибка входа в дискуссию, нет такого id
                        echo 
                        "
                        <script>
                            err_chat_discuss();
                        </script>
                        ";
                    } else {
                        $html_gen=[];
                        
                        foreach(sel("SELECT * FROM discuss_messages WHERE discuss_id = '".$doc['id']."'") as $doc2) {
                            if($doc2 === 'no') {
                                $html_gen['html']="no";
                            } else {
                                $html_gen['all']['id'.$doc2['id']]['user_name']=$doc2['user_name'];
                                $html_gen['all']['id'.$doc2['id']]['text']=$doc2['text'];
                                $html_gen['all']['id'.$doc2['id']]['date']=$doc2['date'];
                                $html_gen['all']['id'.$doc2['id']]['id']=$doc2['id'];
                                $html_gen['last_id']=$doc2['id'];
                            }
                        }
                        
                        //вбрасываем сообщения
                        echo 
                        "
                        <script>
                            send_open_all_feed('no', 'no');
                            open_chat_discuss('".$id."', '".json_encode($html_gen)."');
                        </script>
                        ";
                    }
                }
            }
            
            //обсуждение с паролем
            if(isset($_GET['join'])) {
                
                $id = htmlentities($_GET['discuss'], ENT_QUOTES, 'UTF-8');
                $pass = htmlentities($_GET['join']);
                
                $data = array(
                    'id' => $id,
                    'pass' => $pass
                );
                
                foreach(sel("SELECT * FROM discuss WHERE id = :id AND pass = :pass LIMIT 1", $data) as $doc) {
                    if($doc === 'no') {
                        //ошибка входа в дискуссию, нет такого id+pass
                        echo 
                        "
                        <script>
                            send_open_all_feed('no', 'no');
                            err_chat_discuss();
                            history.pushState(null, null, '/');
                        </script>
                        ";
                    } else {
                        $html_gen=[];
                        
                        foreach(sel("SELECT * FROM discuss_messages WHERE discuss_id = '".$doc['id']."'") as $doc2) {
                            if($doc2 === 'no') {
                                $html_gen['html']="no";
                            } else {
                                $html_gen['all']['id'.$doc2['id']]['user_name']=$doc2['user_name'];
                                $html_gen['all']['id'.$doc2['id']]['text']=$doc2['text'];
                                $html_gen['all']['id'.$doc2['id']]['date']=$doc2['date'];
                                $html_gen['all']['id'.$doc2['id']]['id']=$doc2['id'];
                                $html_gen['last_id']=$doc2['id'];
                            }
                        }
                        echo 
                        "
                        <script>
                            send_open_all_feed('no', 'no');
                            open_chat_discuss('".$id."', '".json_encode($html_gen)."');
                        </script>
                        ";
                    }
                }
            }
        }
        else {
            echo 
            "
            <script>
                send_open_all_feed('no', 'no');
            </script>
            ";
        }
	?>
	
</body>
</html>