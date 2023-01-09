import '../../styles/interact/actu.css';

$(document).ready(function(){

	////////////
	// ON LOAD
	////////////


	////////////
	// ON EVENTS
	////////////

	$("body").on("click", ".thumbUp", function(e){
		ajax(true, 'thumbUp', $(this), $(this).data('id'), 'thumbUpSolid', 'thumbDownSolid')
	})
	$("body").on("click", ".thumbUpSolid", function(e){
		ajax(false, 'thumbUp', $(this), $(this).data('id'), 'thumbUp')
	})

	$("body").on("click", ".thumbDown", function(e){
		ajax(true, 'thumbDown', $(this), $(this).data('id'), 'thumbDownSolid', 'thumbUpSolid')
	})
	$("body").on("click", ".thumbDownSolid", function(e){
		ajax(false, 'thumbDown', $(this), $(this).data('id'), 'thumbDown')
	})

	$("body").on("click", ".heart", function(e){
		ajax(true, 'heart', $(this), $(this).data('id'), 'heartSolid')
	})
	$("body").on("click", ".heartSolid", function(e){
		ajax(false, 'heart', $(this), $(this).data('id'), 'heart')
	})


	////////////
	// FONCTIONS
	////////////

	// Toggle icone - a(sign), b(categorie), c(this), d(actu_id), e(icon), f(duo)
	function ajax(a, b, c, d, e, f = false){
		$.ajax({
			type: "POST",
			url: Routing.generate('interact_actu', { action: b, id: d }),
			timeout: 15000,
			beforeSend: function(){
				c.hide()
				$('#spinner_interact_' + d).show()
			},
			success: function(response){
				$('#spinner_interact_' + d).hide()
				if (response == true || response == 'true'){
					count(a, b, d)
					toggleIcon(c, e, f)
				}
			},
			error: function(error){
				$('#spinner_interact_' + d).hide()
				c.show()
				console.log('Erreur ajax: ' + error)
			}
		})
	}

	// Toggle icone
	function toggleIcon(elem, duo, elem_impact_text = false){

		let actu_id = elem.data('id')

		elem.hide()
		$('#' + duo + '_' + actu_id).show()

		// Désactive autre élement
		if (elem_impact_text !== false){

			let elem_impact = $('#' + elem_impact_text + '_' + actu_id)
			elem_impact_text = elem_impact_text.replace('Solid','')
			if (elem_impact.css('display') !== 'none'){
				toggleIcon(elem_impact, elem_impact_text)
				count(false, elem_impact_text, actu_id)
			}
		}
	}

	// Count & show number
	function count(sign, classCount, actu_id){

		let count = parseInt($('#' + classCount + 'Count_' + actu_id).data('value'))

		count = sign
			? count + 1
			: count - 1

		count = count < 0
			? 0
			: count

		$('#' + classCount + 'Count_' + actu_id).data('value', count)

		count > 0
			? $('#' + classCount + 'Count_' + actu_id).html(count).removeClass('heart-marg')
			: $('#' + classCount + 'Count_' + actu_id).html('').addClass('heart-marg')
	}
})