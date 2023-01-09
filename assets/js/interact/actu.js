import '../../styles/interact/actu.css';

$(document).ready(function(){

	////////////
	// ON LOAD
	////////////


	////////////
	// ON EVENTS
	////////////

	$("body").on("click", ".thumbUp", function(e){
		ajax(true, 'thumbUp', $(this), 'thumbUpSolid', 'thumbDownSolid')
	})
	$("body").on("click", ".thumbUpSolid", function(e){
		ajax(false, 'thumbUp', $(this), 'thumbUp')
	})

	$("body").on("click", ".thumbDown", function(e){
		ajax(true, 'thumbDown', $(this), 'thumbDownSolid', 'thumbUpSolid')
	})
	$("body").on("click", ".thumbDownSolid", function(e){
		ajax(false, 'thumbDown', $(this), 'thumbDown')
	})

	$("body").on("click", ".heart", function(e){
		ajax(true, 'heart', $(this), 'heartSolid')
	})
	$("body").on("click", ".heartSolid", function(e){
		ajax(false, 'heart', $(this), 'heart')
	})


	////////////
	// FONCTIONS
	////////////

	// Toggle icone - a(sign), b(what), c(this), d(witch), e(duo)
	function ajax(a, b, c, d, e = false){
		$.ajax({
			type: "POST",
			url: Routing.generate('interact_actu', { action: b, id: c.data('id') }),
			timeout: 15000,
			beforeSend: function(){
				c.hide()
				$('#spinner_interact_' + c.data('id')).show()
			},
			success: function(response){
				$('#spinner_interact_' + c.data('id')).hide()
				if (response == true || response == 'true'){
					count(a, b)
					toggleIcon(c, d, e)
				}
			},
			error: function(error){
				$('#spinner_interact_' + c.data('id')).hide()
				c.show()
				console.log('Erreur ajax: ' + error)
			}
		})
	}

	// Toggle icone
	function toggleIcon(elem, duo, elem_impact_text = false){

		let id = elem.data('id')

		elem.hide()
		$('#' + duo + '_' + id).show()

		// Désactive autre élement
		if (elem_impact_text !== false){

			let elem_impact = $('#' + elem_impact_text + '_' + id)
			elem_impact_text = elem_impact_text.replace('Solid','')
			if (elem_impact.css('display') !== 'none'){
				toggleIcon(elem_impact, elem_impact_text)
				count(false, elem_impact_text)
			}
		}
	}

	// Count & show number
	function count(sign, classCount){

		let count = parseInt($('#' + classCount + 'Count').data('value'))

		count = sign
			? count + 1
			: count - 1

		count = count < 0
			? 0
			: count

		$('#' + classCount + 'Count').data('value', count)

		count > 0
			? $('#' + classCount + 'Count').text(count).removeClass('heart-marg')
			: $('#' + classCount + 'Count').text('').addClass('heart-marg')
	}
})