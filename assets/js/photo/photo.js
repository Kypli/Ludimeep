import '../../styles/photo/style.css';

$(document).ready(function(){

	////////////
	// ON LOAD
	////////////

	var 
		idLastImage = setIdLastImage(),
		getNextPhotos = true
	;


	////////////
	// ON EVENTS
	////////////

	// Scroll
	$(window).scroll(function(){
		let scrollH = $(window).scrollTop()+$(window).height();
		let documentH = $(document).height();
		let getNextPhotos = getGetNewPhotos();

		if(scrollH == documentH && getNextPhotos){
			setGetNewPhotos(false)
			getNextPhotos = false
			getNewImages()
		}
	})


	////////////
	// FONCTIONS
	////////////

	// Charge les nouvelles images
	function getNewImages(){
		$.ajax({
			type: "POST",
			url: Routing.generate('photo_next_images', { idLastImage: getIdLastImage() }),
			timeout: 15000,
			beforeSend: function(){
				$('.spinner-border').show()
			},
			success: function(response){
				if (response.presencePhotos){
					loadImages(response.render)
				}
				setGetNewPhotos(response.reste)
				$('.spinner-border').hide()
			},
			error: function(error){
				console.log('Erreur ajax: ' + error)
				$('.spinner-border').hide()
			}
		})
	}

	// Charge les nouvelles images
	function loadImages(images){
		$(images).appendTo('#show_photos')
		setIdLastImage()
	}

	// Récupère l'id de la dernière photo
	function getIdLastImage(){
		return idLastImage
	}

	// Update l'id de la dernière photo
	function setIdLastImage(){
		return idLastImage = $('.photo_id').last().data('id')
	}

	// Récupère l'id de la dernière photo
	function getGetNewPhotos(){
		return getNextPhotos
	}

	// Update l'id de la dernière photo
	function setGetNewPhotos(autorise){
		getNextPhotos = autorise
	}
})