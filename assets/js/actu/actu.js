$(document).ready(function(){

	////////////
	// ON EVENTS
	////////////

	// Xxx
	$("body").on("keyup, change", "#ordre_t1, #ordre_t2, #ordre_t3, #ordre_t4, #ordre_t5, #ordre_t6, #ordre_p1, #ordre_p2, #ordre_p3", function(e){
		adaptOrder($(this))
	})

	////////////
	// FONCTIONS
	////////////

	function adaptOrder(elem){

		let
			chiffres = {
				1: true,
				2: true,
				3: true,
				4: true,
				5: true,
				6: true,
				7: true,
				8: true,
				9: true,
			},
			more = false,
			o = elem.val()
		;

		// Récupère le chiffre manquant
		$('.actu_ordre').each(function(index, item){
			delete chiffres[$('#' + item.id).val()]
		})

		let key = Object.keys(chiffres)[0]

		// Remplace
		$('.actu_ordre').not(elem).each(function(index, item){

			if ($('#' + item.id).val() == o){
				$('#' + item.id).val(key)
			}
		})
	}
})