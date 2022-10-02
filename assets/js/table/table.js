import { modal } from '../service/shieldui.js';
import { toaster } from '../service/toaster.js';
import { ucFirst } from '../service/service.js';

import '../../styles/table/table.css';

$(document).ready(function(){

	////////////
	// ON EVENTS
	////////////

	// Modal Table Form 
	$("body").on("click", ".openTable", function(e){
		modalAdd()
		setDate($(this).data('seance'))
		getGameAdherantInSeance($(this).data('seancebis'))
	})

	// Sélection d'un jeu
	$("body").on("change, input", "#table_gameOwner, #table_gamePresent, #table_gameAdherant, #table_gameFree", function(e){

		oneGame($(this))

		$(this)[0].id == 'table_gameFree'
			? $('#suggestMaxPlayer').hide()
			: suggestMaxPlayer($(this).val())
	})

	// Control du formulaire
	$("body").on("change, input", "#table_gameOwner, #table_gamePresent, #table_gameAdherant, #table_gameFree, #table_maxPlayer", function(e){
		controlForm()
	})


	////////////
	// FONCTIONS
	////////////

	// Ouverture de la modal
	function modalAdd(){
		modal(null, null, "d'une table", false, 625, 750)
		$('#table_gameOwner').focus()
	}

	// Sélection d'un seul jeu à la fois
	function oneGame(current){

		let input = $('.row-games').find('input, select').not(current)

		input.each(function(index, item){
			$(item).val('')
		})
	}

	// Récupère la date de la séance
	function setDate(date){
		$('#table_date').val(date)
	}

	// Suggestion du nombre de joueurs maximum
	function suggestMaxPlayer(game_id){

		if ('' != game_id){

			$.ajax({
				type: "POST",
				url: Routing.generate('game_game', { id: game_id }),
				timeout: 15000,
				success: function(response){
					if (null != response && null != response['nbPlayers']){
						
						$('#suggestMaxPlayerNumber').text(response['nbPlayers'])
						$('#suggestMaxPlayer').show()
					}
				},
				error: function(error){
					console.log('Erreur ajax: ' + error)
				}
			})

		} else {
			$('#suggestMaxPlayer').hide()
		}
	}

	// Contrôl du formulaire
	function controlForm(){

		// Game
		let game_valid = 0
		let input_games = $('.row-games').find('input, select')
		input_games.each(function(index, item){
			game_valid = $(item).val() != ''
				? game_valid + 1
				: game_valid
		})

		// Players
		let valid = true
		if ($('#table_maxPlayer').val() == '' || $('#table_maxPlayer').val() < 2){
			valid = false
		}

		valid && game_valid == 1
			? $('#btn_submit').prop('disabled', false)
			: $('#btn_submit').prop('disabled', true)
	}

	// Récupère la liste de jeux des adhérant de la séance
	function getGameAdherantInSeance(date){

			$.ajax({
				type: "POST",
				url: Routing.generate('game_liste_adherant', { date: date }),
				timeout: 15000,
				success: function(response){
					setListAdherant(response)
				},
				error: function(error){
					console.log('Erreur ajax: ' + error)
				}
			})
	}

	// Change le select game adherant
	function setListAdherant(liste){

		// Retire les jeux
		$('#table_gamePresent option').not('#table_gamePresent option:first').remove()

		// Rajoute les jeux de la liste
		$.each(liste, function(index, item){
			$('#table_gamePresent').append($('<option>', { value: item.id, text: item.name }));
		})
	}
})