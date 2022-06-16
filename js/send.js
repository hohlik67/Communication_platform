function send(param) {
	return fetch('../php/architecture.php', {
		method: 'POST',
		body: JSON.stringify(param),
		headers: {'Content-Type': 'application/json;charset=utf-8'}
	})
	.then(  
		function(response) {  
			if (response.status !== 200) {  
				send(param);
			} else {
				response.json().then(function (data) {
					resp(data);
					console.log(data);
				});
			}
		}  
	)  
	.catch(function(err) {  
		send(param);
	});
}

/*
//отправка оффера проверка мыла
function check_email() {
	let param = {
		act: 1,
		email: a
	};
	send(param);
}
*/
//регистрация
let register = document.getElementById('modal_register');
register.addEventListener('submit', send_register);
function send_register(one) {
	one.preventDefault();
	
	let pass1 = register_pass1.value;
	let pass2 = register_pass2.value;
	
	if(pass1 !== pass2) {
	    info_open('Пароли не совпадают!');
	}
	else {
        let all = document.querySelectorAll('.register_group');
        for (let one of all) {
            one.disabled = true;
        }
        let param = {
        act: 0,
            name: register_name.value,
            email: register_email.value,
            login: register_login.value,
            pass: register_pass1.value
        };
        send(param);
	}
}

//вход
let login = document.getElementById('modal_login');
login.addEventListener('submit', send_login_form);
function send_login_form(one) {
	one.preventDefault();
	
	login_login.disabled = true;
    login_pass.disabled = true;
    login_go.disabled = true;
	
    let param = {
        act: 1,
        login: login_login.value,
        pass: login_pass.value
    };
    send(param);
}
	
//вход по токену
function send_login_token(a) {
	let param = {
		act: 2,
		token: a
	};
	send(param);
}

//добавить статью
let art = document.getElementById('modal_add_art');
art.addEventListener('submit', send_add_art);
function send_add_art(one) {
	one.preventDefault();
	
	add_art_title.disabled=true;
    add_art_text.disabled=true;
    add_art_file.disabled=true;
    add_art_sort.disabled=true;
    add_art_go.disabled=true;
    
    let filesSelected = document.getElementById('add_art_file').files;
	if (filesSelected.length > 0) {
		let fileToLoad = filesSelected[0];
		let fileReader = new FileReader();
		fileReader.onload = function(fileLoadedEvent) {
			let srcData = fileLoadedEvent.target.result; // <--- data:base64
			let check = srcData.split(',');
			if(check[0] !== 'data:image/png;base64' & check[0] !== 'data:image/jpeg;base64' & check[0] !== 'data:image/jpg;base64') {
				info_open('Формат картинки только: png, jpeg или jpg');
				add_art_title.disabled=false;
                add_art_text.disabled=false;
                add_art_file.disabled=false;
                add_art_sort.disabled=false;
                add_art_go.disabled=false;
                document.getElementById('add_art_file').value = '';
			} else {
				if(document.getElementById('add_art_file').files[0]['size'] <= 52428800) {
				    if(account===1) {
    					let param = {
                            act: 5,
                            title: add_art_title.value,
                            text: add_art_text.value,
                            u_id: u_id,
                            name: u_name,
                            token: token,
                            imgData: srcData,
                            sort: add_art_sort.value
                        };
                        send(param);
                        info_open('Статья добавляется...');
				    } else {
				        info_open('Необходима авторизация!');
				    }
				} else {
					info_open('Картинка не более 5 мб!');
					add_art_title.disabled=false;
                    add_art_text.disabled=false;
                    add_art_file.disabled=false;
                    add_art_sort.disabled=false;
                    add_art_go.disabled=false;
					document.getElementById('add_art_file').value = '';
				}
			}
			
		};
		fileReader.readAsDataURL(fileToLoad);
	}
	else {
	    let param = {
            act: 5,
            title: add_art_title.value,
            text: add_art_text.value,
            u_id: u_id,
            name: u_name,
            token: token,
            imgData: 'no',
            sort: add_art_sort.value
        };
        send(param);
        info_open('Статья добавляется...');
	}
}
	
//личный кабинет (открытие)
function send_open_personal(a) {
    
	let param1 = {
		act: 8,
		user_id: a,
		token: token
	};
	send(param1);
	let param2 = {
		act: 3,
		system: 'u_arts_start',
		u_id: a,
		sort: 'no',
		start_pos : 'no'
	};
	send(param2);
	content_user_personal_arts.innerHTML = "<div id='content_user_personal_arts_wite' class='wite'>Загрузка...</div>";
    content_user_personal_info.innerHTML = "<div class='wite'>Загрузка...</div>";
    menu_clear();
    content_clear();
	content_user_personal.style.display='flex';
}

//все статьи (открытие старт)
function send_open_all_feed(sort, pos) {
    
    content_all_feed_arts.setAttribute('data-sort', 'all');
    content_all_feed_arts.setAttribute('data-pos', '0');
    
	let param = {
		act: 3,
		system: 'all_arts',
		u_id: 'no',
		sort: sort,
		start_pos : pos
	};
	send(param);
	
	content_all_feed_arts.innerHTML = "<div id='content_all_feed_arts_wite' class='wite'>Загрузка...</div>";
	
    content_clear();
	content_all_feed.style.display='flex';
}

//все статьи (сортировка старт)
function send_open_sort_feed(sort, pos, btn) {
    
    content_all_feed_arts.setAttribute('data-sort', sort);
    content_all_feed_arts.setAttribute('data-pos', '0');
    
	let param = {
		act: 3,
		system: 'all_arts_sort',
		u_id: 'no',
		sort: sort,
		start_pos : pos
	};
	send(param);
	
	content_all_feed_arts.innerHTML = "<div id='content_all_feed_arts_wite' class='wite'>Загрузка...</div>";
    btn_sort_clear();
    document.getElementById(btn).style.cssText='background:#fff; color:#db0f3f';
}

//подгрузка всех статей и сортированных
let send_load_next_arts_check=0;
function send_load_next_arts() {
    if(send_load_next_arts_check===0) {
        if(document.getElementById('content_all_feed_arts_next')) {
            if(content_all_feed_arts_next.innerHTML==='Загрузка...') {
                
                let bb = Number(document.getElementById('content_all_feed_arts').scrollTop + document.getElementById('content_all_feed_arts').clientHeight);
                let cc = Number(document.getElementById('content_all_feed_arts').scrollHeight - 400);
                
                if(bb >= cc) {
                    send_load_next_arts_check=1;
                    let sort = content_all_feed_arts.getAttribute('data-sort');
                    let pos = content_all_feed_arts.getAttribute('data-pos');
                    
                    console.log(sort);
                    console.log(pos);
                    
                    let param = {
                        act: 3,
                        system: 'all_arts_next',
                        u_id: 'no',
                        sort: sort,
                        start_pos: pos
                    };
                    send(param);
                }
            }
        }
    }
}
	
//одна статья (загрузка из index.php)
function send_open_one_art() {
	content_all_feed_arts.innerHTML = "<div id='content_all_feed_arts_wite' class='wite'>Загрузка...</div>";
    content_clear();
    menu_clear2();
    content_one_art.style.display='flex';
}
	
//подписаться\отписаться
function send_sub(txt, pub_id) {
    if(account===1) {
        
        btn_sub_go.disabled=true;
        
        //подписка
        if(txt === 'ПОДПИСАТЬСЯ') {
            let param = {
                act: 4,
                user_sub: token,
                user_pub: pub_id
            };
            send(param);
        }
        
        //отписка
        else if(txt === 'ОТПИСАТЬСЯ') {
            let param = {
                act: 6,
                user_sub: token,
                user_pub: pub_id
            };
            send(param);
        }
    } else {
        info_open('Необходима авторизация!');
    }
}
	
//статьи подписки
function send_open_pub_feed() {
    
    let param = {
		act: 9,
		token: token
	};
	send(param);
	
	content_pubs_art.innerHTML = "<div class='wite'>Загрузка...</div>";
	
    content_clear();
	content_pubs.style.display='block';
}

//добавить комментарий
function send_add_comment(art_id) {
    if(account===1) {
        if(add_comment_text.value!=='') {
            
            add_comment_text.disabled=true;
            add_comment_go.disabled=true;
            
            let param = {
        		act:7,
        		art_id:art_id,
        		name:u_name,
        		text:add_comment_text.value,
        		token: token
        	};
        	send(param);
        }
    } else {
        info_open('Необходима авторизация!');
    }
}

//вводим текст в поиске
function start_search() {
    if(search_txt.value!=='') {
        search_resp.innerHTML = "<div class='wite' id='search_wite'>Загрузка...</div>";
        let param = {
    		act:10,
    		text:search_txt.value
    	};
    	send(param);
    }
    else {
        search_resp.innerHTML = "";
    }
}
	
//открыть обсуждения
function open_discuss_modal() {
    let param = {
		act:12
	};
	send(param);
	discuss_id.value='';
	last_message_id.value='';
    all_discuss.innerHTML = "<div id='discuss_wite'>Загрузка...</div>";
    discuss_pass.style.display='none';
    discuss_chat.style.display='none';
    discuss_all_list.style.display='flex';
    modal.style.display='flex';
    modal_discuss.style.display='flex';
}
function close_discuss_modal() {
    modal_discuss.style.display='none';
    modal.style.display='none';
    discuss_id.value='';
	last_message_id.value='';
}
	
//новое обсуждение
function add_new_discuss() {
    if(account===1) {
        if(text_discuss.value !== '') {
            
            text_discuss.disabled=true;
            pass_discuss.disabled=true;
            add_discuss_go.disabled=true;
            
            let param = {
        		act:11,
        		discuss:text_discuss.value,
        		pass:pass_discuss.value,
        		token: token
        	};
        	send(param);
        }
    } else {
        info_open('Необходима авторизация!');
    }
}
	
//запрос на открытие дискуссии
function open_discuss(id, access) {
    if(access !== 'open') {
        if(u_id=="1") {
            let param = {
        		act:16,
        		token: token,
        		discuss_id: id
        	};
        	send(param);
        }
        discuss_all_list.style.display='none';
        discuss_id.value=id;
        discuss_pass.style.display='flex';
    } else {
        document.location.href = url+'?discuss='+id;
        history.pushState(null, null, '/');
    }
}
//проброс с паролем
function open_discuss_pass() {
    document.location.href = url+'?discuss='+discuss_id.value+'&join='+discuss_pass_text.value;
}

//открыть чат дискуссии
function open_chat_discuss(id, as) {
    
    let a = JSON.parse(as);
    
    discuss_id.value=id;
    
    discuss_pass.style.display='none';
    discuss_chat.style.display='flex';
    discuss_all_list.style.display='none';
    if(!a.html) {
        for(let key in a.all) {
            //art_generate('all_arts', a.all[key].id, a.all[key].title, a.all[key].sort, a.all[key].img, a.all[key].date);
            let div = document.createElement('div');
            div.className = 'oness';
            div.innerHTML = 
            `
            <div class='name'>`+a.all[key].user_name+`</div>
            <div class='text'>`+a.all[key].text+`</div>
            <div class='date'>`+a.all[key].date+`</div>
            <button class='delete' onclick="del_mess('`+a.all[key].id+`')">УДАЛИТЬ</button>
            `;
            document.getElementById('all_message').append(div);
        }
        last_message_id.value=a.last_id;
        chat_upload(id, a.last_id);
    } else {
        all_message.innerHTML = "<div id='all_message_wite'>Нет сообщений...</div>";
        last_message_id.value=1;
        discuss_id.value=id;
        chat_upload(id, 1);
    }
    
    modal.style.display='flex';
    modal_discuss.style.display='flex';
    all_message.scrollTop=all_message.scrollHeight;
}

//ошибка открытия чата дискуссии
function err_chat_discuss() {
    discuss_id.value='';
    open_discuss_modal();
    info_open('Неверные данные дискуссии!');
}
	
//добавить сообщение в чат
function message_add() {
    if(account===1) {
        if(message_text.value !== '') {
            message_text.disabled=true;
            message_go.disabled=true;
            
            let param = {
        		act:14,
        		discuss_id:discuss_id.value,
        		user_name:u_name,
        		text: message_text.value
        	};
        	send(param);
        }
    } else {
        info_open('Необходима авторизация!');
    }
}
	
//удалить статью
function del_art(a) {
    let param = {
		act:17,
		token: token,
		discuss_id: a
	};
	send(param);
}

//удалить коммент
function del_comment(a) {
    let param = {
		act:18,
		token: token,
		comment_id: a
	};
	send(param);
}

//удалить дискуссию
function del_discuss() {
    let param = {
		act:19,
		token: token,
		discuss_id: discuss_id.value
	};
	send(param);
}

//удалить сообщение
function del_mess(a) {
    let param = {
		act:20,
		token: token,
		message_id: a
	};
	send(param);
}
	
	