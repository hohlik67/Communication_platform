let url = 'https://hohlovdiplom';
let account = 0;
let token = 0;
let u_id = '';
let u_name = '';

//чистка меню c очисткой url
function menu_clear() {
    my_feed.style.color='#000';
    all_feed.style.color='#000';
    add_art.style.color='#000';
    discuss.style.color='#000';
    search.style.color='#000';
    history.pushState(null, null, '/');
}

//чистка меню без очистки url
function menu_clear2() {
    my_feed.style.color='#000';
    all_feed.style.color='#000';
    add_art.style.color='#000';
    discuss.style.color='#000';
    search.style.color='#000';
}

//чистка сортировки
function btn_sort_clear() {
    let all = document.querySelectorAll('.group_btn_sort');
	for (let one of all) {
		one.style.cssText='background:#dedede; color:#000';
	}   
}

//чистка разделов
function content_clear() {
    content_all_feed.style.display='none';
    content_one_art.style.display='none';
    content_user_personal.style.display='none';
    content_pubs.style.display='none';
}

//меню
function menu(a) {
    
    if(a===1) {
        if(account===1) {
            menu_clear();
            my_feed.style.color='#fff';
            send_open_pub_feed();
        }
        else info_open('Необходима авторизация!');
    }
    else if(a===2) {
        menu_clear();
        all_feed.style.color='#fff';
        send_open_all_feed('no', 'no');
    }
    else if(a===3) {
        
        if(account===1) {
            add_art_open();
        }
        else info_open('Необходима авторизация!');
    }
    else if(a===4) {
        open_discuss_modal();
    }
    else {
        open_search_modal();
    }
    btn_sort_clear();
}
//вход - окно
function login_open() {
    modal.style.display='flex';
    modal_login.style.display='flex';
}
function login_close() {
    modal_login.style.display='none';
    modal.style.display='none';
}
//регистрация - окно
function register_open() {
    modal.style.display='flex';
    modal_register.style.display='flex';
}
function register_close() {
    modal_register.style.display='none';
    modal.style.display='none';
}
//выход из аккаунта
function account_exit() {
    localStorage.removeItem('token');
    document.location.href = url;
}
//добавить статью окно
function add_art_open() {
    modal.style.display='flex';
    modal_add_art.style.display='flex';
}
function add_art_close() {
    modal_add_art.style.display='none';
    modal.style.display='none';
}
//файл выбран (при добавлении статьи)
function add_art_file_change() {
    if(add_art_file_mask.innerHTML==='Изображение добавлено! Выбрать другое?') {
        info_open('Выбрано новое изображение!');
    }
    else {
        add_art_file_mask.innerHTML = 'Изображение добавлено! Выбрать другое?';
    }
}
//генерируем статьи
function art_generate(system, id, title, sort, img, date) {
    let imgGen = '';
    if(img !== 'no') imgGen = "<div class='img'><img src='../php/user_img/"+img+".png'></div>";
    
    let div = document.createElement('div');
	div.className = 'one_art';
	div.setAttribute('onclick', "open_one_art('"+id+"')");
	div.innerHTML = 
	imgGen+
	`
	<div class='sort'>`+sort+`<div class='date'>`+date+`</div></div>
	<div class='title'>`+title+`</div>
	`;
	
	if(system==='u_arts_start') {
    	document.getElementById('content_user_personal_arts').append(div);
	}
	else if (system==='all_arts') {
    	document.getElementById('content_all_feed_arts').append(div);
	}
}
//открыть одну статью
function open_one_art(id) {
    document.location.href = url+'?one_art='+id;
}
//открыть поиск
function open_search_modal() {
    search_resp.innerHTML = "";
    search_txt.value='';
    modal.style.display='flex';
    modal_search.style.display='flex';
}
function close_search_modal() {
    modal_search.style.display='none';
    modal.style.display='none';
}


//инфо - окно
function info_open(a) {
    modal_info_text.innerHTML = a;
    modal_info_fon.style.display='flex';
}
function info_close() {
    modal_info_fon.style.display='none';
}