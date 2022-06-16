async function resp(a) {
	let act = a.act;
	
	//регистрация - no
	if(act==='register_no') {
        if(account===0) {
            let all = document.querySelectorAll('.register_group');
            for (let one of all) {
                one.disabled = false;
            }
            info_open('E-mail или логин заняты!');
        }
	}
	
	//регистрация - ok
	else if(act==='register_ok') {
        if(account===0) {
            let all = document.querySelectorAll('.register_group');
            for (let one of all) {
                one.disabled = false;
                if(one.value !== 'СОЗДАТЬ АККАУНТ') one.value='';
            }
            info_open('Письмо с подтверждением регистрации отправлено Вам на почту.');
            register_close();
        }
	}
	
	//вход с формы - no
	else if(act==='login_form_no') {
        if(account===0) {
            login_login.disabled = false;
            login_pass.disabled = false;
            login_go.disabled = false;
            info_open('Неверные данные авторизации!');
        }
	}
	
	//вход с формы - no active
	else if(act==='login_form_no_active') {
        if(account===0) {
            login_login.disabled = false;
            login_pass.disabled = false;
            login_go.disabled = false;
            info_open('Активируйте аккаунт! Письмо со ссылкой для активации отправлено на Ваш e-mail.');
        }
	}
	
	//вход с формы - ok
	else if(act==='login_form_ok') {
        if(account===0) {
            account=1;
            token = a.token;
            u_id = a.u_id;
            u_name = a.name;
            localStorage.setItem('token', token);
            modal_login.remove();
            modal_register.remove();
            modal.style.display='none';
            
            aut.innerHTML = 
            `
            <button class='exit' onclick='account_exit()'>╳</button>
            <button class='personal' onclick="send_open_personal('`+a.u_id+`')">ЛИЧНЫЙ КАБИНЕТ</button>
            `;
            if(u_id=="1") {
                let all = document.querySelectorAll('.delete');
            	for (let one of all) {
            		one.style.display = 'block';
            	}
            }
        }
	}
	
	//вход с токена - no
	else if(act==='login_token_no') {
        if(account===0) {
            aut.innerHTML = 
            `
                <button class='register' onclick='register_open()'>РЕГИСТРАЦИЯ</button>
                <button class='login' onclick='login_open()'>ВХОД</button>
            `;
            localStorage.removeItem('token');
        }
	}
	
	//вход с токена - ok
	else if(act==='login_token_ok') {
        if(account===0) {
            account=1;
            u_id = a.u_id;
            u_name = a.name;
            token = localStorage.getItem('token');
            modal_login.remove();
            modal_register.remove();
            
            aut.innerHTML = 
            `
            <button class='exit' onclick='account_exit()'>╳</button>
            <button class='personal' onclick="send_open_personal('`+a.u_id+`')">ЛИЧНЫЙ КАБИНЕТ</button>
            `;
            if(u_id=="1") {
                let all = document.querySelectorAll('.delete');
            	for (let one of all) {
            		one.style.display = 'block';
            	}
            }
        }
	}
	
	//добавление статьи - ok
	else if(act==='art_add_ok') {
        document.location.href = url+'?add_art=ok';
	}
	
	//аккаунт юзера (info) - ok
	else if(act==='account_data_ok') {
        if(a.who==='my') {
            content_user_personal_info.innerHTML =
            `
            <div class='name'>`+a.name+`</div>
            <div class='stat'>
                НАПИСАНО СТАТЕЙ: `+a.count_art+`<br><br>
                СОЗДАНО ОБСУЖДЕНИЙ: `+a.count_discuss+`<br><br>
                ОСТАВЛЕНО КОММЕНТАРИЕВ: `+a.count_comments+`<br><br>
                ПОДПИСЧИКОВ: `+a.count_sub+`
            </div>
            `;
        }
        else {
            let btn = "<button id='btn_sub_go' onclick=\"send_sub(this.innerHTML, '"+a.pub_id+"')\" style='margin-top:8px; border:0; border-radius:4px; background:#b53f3f; color:#fff; font-size:0.8vw; letter-spacing:1px; padding:8px 12px; font-weight:600'>ПОДПИСАТЬСЯ</button>";
            if(a.sub==='ok') btn = "<button id='btn_sub_go' onclick=\"send_sub(this.innerHTML, '"+a.pub_id+"')\" style='margin-top:8px; border:0; border-radius:4px; background:#dedede; color:#000; font-size:0.8vw; letter-spacing:1px; padding:8px 12px; font-weight:600'>ОТПИСАТЬСЯ</button>";
            content_user_personal_info.innerHTML =
            `
            <div class='name'>`+a.name+`</div>
            <div class='stat'>
                НАПИСАНО СТАТЕЙ: `+a.count_art+`<br><br>
                СОЗДАНО ОБСУЖДЕНИЙ: `+a.count_discuss+`<br><br>
                ОСТАВЛЕНО КОММЕНТАРИЕВ: `+a.count_comments+`<br><br>
                ПОДПИСЧИКОВ: `+a.count_sub+`
            </div>
            `
            +btn
            ;
        }
	}
	
	//статьи юзера (открытие лк юзера) - no
	else if(act==='u_arts_start_no') {
        content_user_personal_arts.innerHTML = "<div class='wite'>Ещё нет опубликованных статей...</div>";
	}
	
	//статьи юзера (открытие лк юзера) - ok
	else if(act==='u_arts_start_ok') {
	    if(document.getElementById('content_user_personal_arts_wite')) document.getElementById('content_user_personal_arts_wite').remove();
        for(let key in a.all) {
            art_generate('u_arts_start', a.all[key].id, a.all[key].title, a.all[key].sort, a.all[key].img, a.all[key].date);
        }
	}
	
	//все статьи - no
	else if(act==='all_arts_no') {
        content_all_feed_arts.innerHTML = "<div class='wite'>Ещё нет опубликованных статей...</div>";
	}
	
	//все статьи - ok
	else if(act==='all_arts_ok') {
	    if(document.getElementById('content_all_feed_arts_wite')) document.getElementById('content_all_feed_arts_wite').remove();
        for(let key in a.all) {
            art_generate('all_arts', a.all[key].id, a.all[key].title, a.all[key].sort, a.all[key].img, a.all[key].date);
        }
        if(a.next_start < 20) {
            let div = document.createElement('div');
            div.id = 'content_all_feed_arts_next';
            div.innerHTML = 'Больше нет статей...';
            document.getElementById('content_all_feed_arts').append(div);
        }
        else {
            content_all_feed_arts.setAttribute('data-pos', a.next_start);
            let div = document.createElement('div');
            div.id = 'content_all_feed_arts_next';
            div.innerHTML = 'Загрузка...';
            document.getElementById('content_all_feed_arts').append(div);
        }
	}
	
	//все статьи сортировка - no
	else if(act==='all_arts_sort_no') {
        content_all_feed_arts.innerHTML = "<div class='wite'>Ещё нет опубликованных статей...</div>";
	}
	
	//все статьи сортировка - ok
	else if(act==='all_arts_sort_ok') {
	    if(document.getElementById('content_all_feed_arts_wite')) document.getElementById('content_all_feed_arts_wite').remove();
        for(let key in a.all) {
            art_generate('all_arts', a.all[key].id, a.all[key].title, a.all[key].sort, a.all[key].img, a.all[key].date);
        }
        if(a.next_start < 20) {
            let div = document.createElement('div');
            div.id = 'content_all_feed_arts_next';
            div.innerHTML = 'Больше нет статей...';
            document.getElementById('content_all_feed_arts').append(div);
        }
        else {
            content_all_feed_arts.setAttribute('data-pos', a.next_start);
            let div = document.createElement('div');
            div.id = 'content_all_feed_arts_next';
            div.innerHTML = 'Загрузка...';
            document.getElementById('content_all_feed_arts').append(div);
        }
	}
	
	//все статьи/сортировка ПОДГРУЗКА - no
	else if(act==='all_arts_next_no') {
	    send_load_next_arts_check=0;
	    if(document.getElementById('content_all_feed_arts_next')) content_all_feed_arts_next.innerHTML = 'Больше нет статей...';
	}
	
	//все статьи/сортировка ПОДГРУЗКА - ok
	else if(act==='all_arts_next_ok') {
	    send_load_next_arts_check=0;
	    if(document.getElementById('content_all_feed_arts_next')) document.getElementById('content_all_feed_arts_next').remove();
        for(let key in a.all) {
            art_generate('all_arts', a.all[key].id, a.all[key].title, a.all[key].sort, a.all[key].img, a.all[key].date);
        }
        if(a.next_start < 20) {
            let pos = Number(content_all_feed_arts.getAttribute('data-pos'));
            pos = pos+a.next_start;
            content_all_feed_arts.setAttribute('data-pos', pos);
            
            let div = document.createElement('div');
            div.id = 'content_all_feed_arts_next';
            div.innerHTML = 'Больше нет статей...';
            document.getElementById('content_all_feed_arts').append(div);
        }
        else {
            let pos = Number(content_all_feed_arts.getAttribute('data-pos'));
            pos = pos+a.next_start;
            content_all_feed_arts.setAttribute('data-pos', pos);
            
            let div = document.createElement('div');
            div.id = 'content_all_feed_arts_next';
            div.innerHTML = 'Загрузка...';
            document.getElementById('content_all_feed_arts').append(div);
        }
	}

	//подписка - no
	else if(act==='add_pub_no') {
	    btn_sub_go.disabled=false;
	    info_open('Вы уже подписаны!');
	}
	
	//подписка - ok
	else if(act==='add_pub_ok') {
	    btn_sub_go.disabled=false;
	    btn_sub_go.style.cssText = 'margin-top:8px; border:0; border-radius:4px; background:#dedede; color:#000; font-size:0.8vw; letter-spacing:1px; padding:8px 12px; font-weight:600';
	    btn_sub_go.innerHTML = 'ОТПИСАТЬСЯ';
	}
	
	//отписка - no
	else if(act==='del_pub_no') {
	    btn_sub_go.disabled=false;
	    info_open('Вы не подписаны!');
	}
	
	//отписка - ok
	else if(act==='del_pub_ok') {
	    btn_sub_go.disabled=false;
	    btn_sub_go.style.cssText = 'margin-top:8px; border:0; border-radius:4px; background:#b53f3f; color:#fff; font-size:0.8vw; letter-spacing:1px; padding:8px 12px; font-weight:600';
	    btn_sub_go.innerHTML = 'ПОДПИСАТЬСЯ';
	}
	
	//статьи подписок - no
	else if(act==='pub_feed_no') {
	    content_pubs_art.innerHTML = "<div class='wite'>У Вас нет подписок...</div>";
	}
	
	//статьи подписок - ok
	else if(act==='pub_feed_ok') {
	    content_pubs_art.innerHTML = a.html;
	}
	
	//добавили коммент - ok
	else if(act==='comment_add_ok') {
	    location.reload();
	}
	
	//поиск - ok
	else if(act==='search_ok') {
	    if(a.text===search_txt.value) {
    	    search_resp.innerHTML = "";
            for(let key in a.all) {
                //art_generate('all_arts', a.all[key].id, a.all[key].title, a.all[key].sort, a.all[key].img, a.all[key].date);
                let div = document.createElement('div');
                div.className = 'one_art';
                div.setAttribute('onclick', "open_one_art('"+a.all[key].id+"')");
                div.innerHTML = a.all[key].title;
                document.getElementById('search_resp').append(div);
            }
	    }
	}
	
	//поиск - ok
	else if(act==='search_no') {
	    if(a.text===search_txt.value) {
	        search_resp.innerHTML = "<div class='wite' id='search_wite'>Нет подходящих статей...</div>";
	    }
	}
	
	//создали обсуждение - ok
	else if(act==='add_new_discuss_ok') {
	    open_discuss_modal();
        
        text_discuss.disabled=false;
        pass_discuss.disabled=false;
        add_discuss_go.disabled=false;
        
        text_discuss.value='';
        pass_discuss.value='';
        add_discuss_go.value='';
	}
	
	//получаем дискусси - no
	else if(act==='all_discuss_no') {
	    all_discuss.innerHTML = "<div id='discuss_wite'>Нет дискуссий...</div>";
	}
	
	//получаем дискусси - ok
	else if(act==='all_discuss_ok') {
	    all_discuss.innerHTML = "";
	    
        for(let key in a.all) {
            //art_generate('all_arts', a.all[key].id, a.all[key].title, a.all[key].sort, a.all[key].img, a.all[key].date);
            let joinGen = '';
    	    if(a.all[key].join==='open') {
    	        joinGen = "<div class='join_open'>открытое обсуждение</div>";
    	    } else {
    	        joinGen = "<div class='join_close'>закрытое обсуждение</div>";
    	    }
    	    
    	    let div = document.createElement('div');
            div.className = 'one_discuss';
            div.setAttribute('onclick', "open_discuss('"+a.all[key].id+"', '"+a.all[key].join+"')");
            div.innerHTML = 
            `
            <div class='txt'>`+a.all[key].name+`</div>
            `
            +joinGen;
            
            document.getElementById('all_discuss').append(div);
        }
	}
	
	//добавили сообщение в чат - ok
	else if(act==='message_add_ok') {
	    message_text.disabled=false;
        message_go.disabled=false;
        message_text.value='';
	}
	
	//реал-тайм - no
	else if(act==='chat_upload_no') {
	    if(discuss_id.value!=='') {
	        chat_upload(discuss_id.value, last_message_id.value);
	    }
	}
	
	//реал-тайм - ok
	else if(act==='chat_upload_ok') {
	    if(discuss_id.value!=='') {
	        if(document.getElementById('all_message_wite')) document.getElementById('all_message_wite').remove();
    	    for(let key in a.all) {
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
            all_message.scrollTop=all_message.scrollHeight;
            last_message_id.value=a.last_id;
            chat_upload(discuss_id.value, last_message_id.value);
            if(u_id=="1") {
                let all = document.querySelectorAll('.delete');
            	for (let one of all) {
            		one.style.display = 'block';
            	}
            }
	    }
	}

	else if(act==='get_pass') {
	    discuss_pass_text.value=a.pass;
	}
	else if(act==='del_art') {
	    history.pushState(null, null, '/');
	    location.reload();
	}
	else if(act==='del_comment') {
	    location.reload();
	}
	else if(act==='del_discuss') {
	    history.pushState(null, null, '/');
	    location.reload();
	}
	else if(act==='del_messages') {
	    location.reload();
	}
	
	
	
	
	
	
	
	
	
}