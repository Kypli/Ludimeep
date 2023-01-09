import '../../styles/tchat/tchat.css';

$(document).ready(function(){

	////////////
	// ON EVENTS
	////////////

	// Enregistre message
	$("body").on("click", "#tchat_valid", function(e){
		e.preventDefault();

		if ($('#tchat_content').val() != ''){
			newTchat($('#tchat_content').val())
		}
	})

	// Active button
	$("body").on("keyup", "#tchat_content", function(e){

		$(this).val() != ''
			? button_valid(true)
			: button_valid(false)
	})

	////////////
	// FONCTIONS
	////////////

	function newTchat(content){
			
		$.ajax({
			type: "POST",
			url: Routing.generate('tchat_new', { content: content }),
			timeout: 15000,
			beforeSend: function(){
				blockValid(true)
			},
			success: function(response){
				response.save == true
					? addTchat(response.id)
					: blockValid(false)
			},
			error: function(error){
				console.log('Erreur ajax: ' + error)
			}
		})
	}

	function addTchat(id){

		$.ajax({
			type: "POST",
			url: Routing.generate('tchat_add', { id: id }),
			timeout: 15000,
			success: function(response){
				if (response.save == true){
					$('#tchat_ul').prepend(response.render)
				}
				blockValid(false)
			},
			error: function(error){
				console.log('Erreur ajax: ' + error)
			}
		})
	}

	function blockValid(etat){

		if (etat){
			$('#tchat_content').prop('disabled', true)
			$('#spinner_tchat_valid').show()
			$('#tchat_valid').hide()

		} else {
			$('#spinner_tchat_valid').hide()
			$('#tchat_valid').show()
			$('#tchat_content').prop('disabled', false).val('')
			button_valid(false)
		}
	}

	function button_valid(etat){

		etat
			? $('#button_valid_icon').removeClass('grey').addClass('white')
			: $('#button_valid_icon').removeClass('white').addClass('grey')
	}
})