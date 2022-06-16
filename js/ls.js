if(localStorage.getItem('token') !== null) {
    
    send_login_token(localStorage.getItem('token'));
}
else {
    
    aut.innerHTML = 
    `
        <button class='register' onclick='register_open()'>РЕГИСТРАЦИЯ</button>
        <button class='login' onclick='login_open()'>ВХОД</button>
    `;
}