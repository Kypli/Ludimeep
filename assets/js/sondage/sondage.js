import { pie } from '../service/chart_pie.js';

$(document).ready(function(){

	////////////
	// ON EVENTS
	////////////

	// Modal edit 
	$("body").on("click", ".test", function(e){
		let id = $(this).data('id')
		$('#tr_' + id).toggle('slow')

		$('#tr_' + id).is(":visible")
			? ($('.loading_' + id).show() && getdatas(id))
			: $('.loading_' + id).hide()
	})


	////////////
	// FONCTIONS
	////////////

	function getdatas(entity_id){

		$.ajax({
			type: "POST",
			url: Routing.generate('sondage_result', { id: entity_id }),
			timeout: 15000,
			success: function(response){
				$('.loading_' + entity_id).hide()
				pie($('#pie_' + entity_id)[0].getContext('2d'), response.labels, response.datas);
			},
			error: function(error){
				console.log('Erreur ajax: ' + error)
			}
		})
	}
})