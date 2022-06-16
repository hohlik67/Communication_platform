function chat_upload(chat_id, last_message_id) {
    
    let param = {
		act:15,
		chat_id:chat_id,
		last_message_id:last_message_id
	};
    
	return fetch('../php/architecture.php', {
		method: 'POST',
		body: JSON.stringify(param),
		headers: {'Content-Type': 'application/json;charset=utf-8'}
	})
	.then(  
		function(response) {  
			if (response.status !== 200) {  
				chat_upload(chat_id, last_message_id);
			} else {
				response.json().then(function (data) {
					resp(data);
				});
			}
		}  
	)  
	.catch(function(err) {  
		chat_upload(chat_id, last_message_id);
	});
}