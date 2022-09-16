import { modal } from '../service/shieldui.js';
import { toaster } from '../service/toaster.js';
import { ucFirst } from '../service/service.js';

import '../../styles/table/table.css';

$(document).ready(function(){

	////////////
	// ON EVENTS
	////////////

	// Modal edit 
	$("body").on("click", ".openTable", function(e){
		modalAdd()
	})


	////////////
	// FONCTIONS
	////////////

	function modalAdd(){
		console.log(1)
		modal(null, null, "d'un jeu", false, 1100, 400)
		$('#game_name').focus()
	}

	// function getdatas(entity_id){

	// 	$.ajax({
	// 		type: "POST",
	// 		url: Routing.generate('sondage_result', { id: entity_id }),
	// 		timeout: 15000,
	// 		success: function(response){
	// 			$('.loading_' + entity_id).hide()
	// 			pie($('#pie_' + entity_id)[0].getContext('2d'), response.labels, response.datas);
	// 		},
	// 		error: function(error){
	// 			console.log('Erreur ajax: ' + error)
	// 		}
	// 	})
	// }
})