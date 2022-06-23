import '../../styles/service/toaster.css';


// Efface le toaster on click
$('#toaster').on("click", function (){
	$(this)
		.fadeOut('slow')
		.removeClass("coucou")
})

// Lance le toaster
export function toaster(text = null){

	var toaster = $('#toaster')

	// Texte par défaut
	if (text == null && toaster.data('toaster') != null){
		text = toaster.data('toaster')
	}

	// Présence active du toaster
	var active = toaster.hasClass("coucou")

	// Texte
	!active

		// Nouveau toaster
		? toaster.html(text)

		// Fusion des textes
		: toaster.append('<br />' + text)

	// Afficher
	text != "" && text != null && text != 'undefined'
		? toaster.addClass("coucou")
		: toaster.removeClass("coucou")

	// Cacher
	setTimeout(
		function(){ 
			toaster.removeClass("coucou")
		},
		35000 // 35s
	)
}
