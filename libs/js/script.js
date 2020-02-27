$(document).ready(function() {
	var tweetsCount = 0;
	$(document ).on('change', '.user-picture-file' , function(){ 
		var $this = $(this),
			$form = $this.parents('form');
			
		$form.submit();
	});
	
});

function test(){
	tweetsCount += tweetsCount;
	var text = document.getElementById("textArea").value;
	document.getElementById('tweets').insertAdjacentHTML('afterbegin', '<div class="tweets"><span class="item-tweets">'+text+'</span></div>');
	document.getElementById('profile-card-tweets').value = tweetsCount;
}

