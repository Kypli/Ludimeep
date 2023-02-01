import { modal } from '../service/shieldui.js';
import { toaster } from '../service/toaster.js';
import { ucFirst } from '../service/service.js';
import { myCustomFilter } from '../service/shieldui.js';

import '../../styles/game/game.css';

$(document).ready(function(){

	////////////
	// ON LOAD
	////////////

	var
		indexSeleted = null,
		action = 'add',
		user_id = $("#grid").data('userid'),
		user = $("#grid").data('user') == 1 ? true : false,
		admin = $("#grid").data('admin') == 1 ? true : false
	;
	buildGrid()

	////////////
	// ON EVENTS
	////////////

	// Modal edit 
	$("body").on("click", "#valider", function(e){
		event.preventDefault()
		$('.loading').show()
		sendForm(getFormDatas())
	})


	////////////
	// FONCTIONS
	////////////

	// Construit le tableau shiel-ui
	function buildGrid(){
		$("#grid").shieldGrid({
			dataSource: {
				remote: {
					read: {
						url: Routing.generate('game_games'),
					},
				},
				schema:
				{
					fields:
					{
						id: {
							path: "id",
							type: Number,
							nullable: false,
						},
						name: {
							path: "name",
							type: String,
							nullable: false,
						},
						user_id: {
							path: "user_id",
							type: Number,
							nullable: false,
						},
						userName: {
							path: "userName",
							type: String,
							nullable: false,
						},
						owner: {
							path: "owner",
							type: String,
							nullable: false,
						},
						nom: {
							path: "nom",
							type: String,
							nullable: false,
						},
						prenom: {
							path: "prenom",
							type: String,
							nullable: false,
						},
						nbPlayers: {
							path: "nbPlayers",
							type: Number,
							nullable: true,
						},
						difficult: {
							path: "difficult",
							type: Number,
							nullable: true,
						},
						version: {
							path: "version",
							type: String,
							nullable: true,
						},
						minAge: {
							path: "minAge",
							type: String,
							nullable: true,
						},
						time_hour: {
							path: "time_hour",
							type: String,
							nullable: true,
						},
						time_minute: {
							path: "time_minute",
							type: String,
							nullable: true,
						},
					}
				},
			},
			selection: {
				type: "row",
				multiple: true,
				toggle: false,
				spreadsheet: false,
			},
			sorting:{
				multiple: false,
				allowUnsort : false
			},
			paging: {
				pageSize: 30,
				pageLinksCount: 10,
				messages: {
					pageLabelTemplate: "{0}",
					infoBarTemplate: "{0} - {1} jeux sur {2}",
					firstTooltip: "Première page",
					previousTooltip: "Page précédante",
					nextTooltip: "Page suivante",
					lastTooltip: "Dernière page"
				},
			},
			filtering: {
				enabled: true
			},
			minWidth: 3000,
			maxHeight: 500,
			editing: user,
			scrolling: true,
			resizing: true,
			columnReorder: true,
			columns: [
				{
					field: "id",
					title: "Id",
					width: 50,
					minWidth: 50,
					editable: false,
					visible: false,
					customFilter: function(cell, item, index){
						myCustomFilter(cell, 'id', 'Id', 'number', 50)
					},
					columnTemplate: function(cell, item, index){
						let text = item.id == undefined ? '' : item.id
						$("<div>" + text + "</div>").appendTo(cell)
					},
				},
				{
					field: "name",
					title: "Nom",
					width: 70,
					minWidth: 70,
					customFilter: function(cell, item, index){
						myCustomFilter(cell, 'name', 'Nom', 'text', 80)
					},
				},
				{
					field: "owner",
					title: "Propriétaire",
					width: 70,
					minWidth: 70,
					customFilter: function(cell, item, index){
						myCustomFilter(cell, 'owner', 'Propriétaire', 'text', 80)
					},
				},
				{
					field: "nbPlayers",
					title: "Nombre de joueurs",
					width: 60,
					minWidth: 60,
					customFilter: function(cell, item, index){
						myCustomFilter(cell, 'nbPlayers', 'Nombre de joueurs', 'text', 70)
					},
					columnTemplate: function(cell, item, index){
						let text = item.nbPlayers == null ? '' : item.nbPlayers
						$("<div>" + text + "</div>").appendTo(cell)
					},
				},
				{
					field: "difficult",
					title: "Difficulté",
					width: 50,
					minWidth: 50,
					customFilter: function(cell, item, index){
						myCustomFilter(cell, 'difficult', 'Difficulté', 'text', 60)
					},
					columnTemplate: function(cell, item, index){
						let text = item.difficult == null ? '' : item.difficult
						$("<div>" + text + "</div>").appendTo(cell)
					},
				},
				{
					field: "version",
					title: "Version ",
					width: 60,
					minWidth: 60,
					customFilter: function(cell, item, index){
						myCustomFilter(cell, 'version', 'Version', 'text', 70)
					},
					columnTemplate: function(cell, item, index){
						let text = item.version == null ? '' : item.version
						$("<div>" + text + "</div>").appendTo(cell)
					},
				},
				{
					field: "minAge",
					title: "Age minimum",
					width: 50,
					minWidth: 50,
					customFilter: function(cell, item, index){
						myCustomFilter(cell, 'minAge', 'Age minimum', 'text', 50)
					},
					columnTemplate: function(cell, item, index){
						let text = item.minAge == null ? '' : item.minAge
						$("<div>" + text + "</div>").appendTo(cell)
					},
				},
				{
					field: "time",
					title: "Durée",
					width: 80,
					minWidth: 80,
					customFilter: function(cell, item, index){
						myCustomFilter(cell, 'time', 'Durée', 'text', 80)
					},
					columnTemplate: function(cell, item, index){

						let hour = item.time_hour
						let minute = item.time_minute

						hour = 
							hour == null ||
							hour == '0' ||
							hour == '00'
								? 0
								: hour

						minute = 
							minute == null ||
							minute == '0' ||
							minute == '00'
								? 0
								: minute

						let pluriel_hour = hour > 1 ? 's' : ''
						let pluriel_minute = minute > 1 ? 's' : ''
						let jonction = hour > 0 && minute > 0 ? ' et ' : ''

						let text_hour = hour == 0
							? ''
							: parseInt(hour, 10) + ' heure' + pluriel_hour

						let text_minute = minute == 0
							? ''
							: parseInt(minute, 10) + ' minute' + pluriel_minute

						$("<div>" + text_hour + jonction + text_minute + "</div>").appendTo(cell)
					},
				},
				{
					field: "action",
					title: "Actions",
					filterable: false,
					width: 60,
					minWidth: 60,
					visible: user,
					columnTemplate: function(cell, item, index){

						let id = item.id
						let user_id_bis = item.user_id

						// return "Supprimer ce jeu ? Attention cette action est définitive !";

						if (admin || user_id_bis == user_id){

							$(
								"<div class='icon'>" +
									"<button id='but_edit_"+item.id+"'><i class='far fa-edit' title='Modifier'></i></button>" +
									"<button id='but_delete_"+item.id+"'><i class='far fa-trash-alt' title='Supprimer'></i>" +
								"</div>"
							).appendTo(cell)

							$("#but_edit_"+item.id).shieldButton({
								events: {
									click: function (e){
										setAction('edit')
										setIndexSelected(index)

										if (admin || user_id_bis == user_id){
											$('#valider').data('id', id)
											modalAdd()
											getEntity(id)
										} else {
											toaster("Vous n'êtes pas propriétaire de ce jeu.")
										}
									}
								}
							});

							$("#but_delete_"+item.id).shieldButton({
								events: {
									click: function (e){
										if (confirm("Supprimer ce jeu ? Attention cette action est définitive !")){
											setAction('delete')
											setIndexSelected(index)
											let datas = {id: item.id}
											sendForm(datas)
										}
									}
								}
							});

						} else {
							$("<div></div>").appendTo(cell)
						}
					},
				},
			],
			toolbar: !user ? false : [
				{
					buttons: [
						{
							// commandName: "insert",
							caption: "<i class='fas fa-plus-circle'></i> Ajouter un jeu",
							click: function (e){
								setAction('add')
								modalAdd()
							},
						},
					],
					position: "top"
				}
			],
			noRecordsTemplate: $("#noRecordsTemplate").html(),
		})
	}

	// Get index selected row
	function getIndexSelected(){
		return indexSeleted
	}

	// Set index selected row
	function setIndexSelected(newIndexSelected){
		indexSeleted = newIndexSelected
	}

	// Get action
	function getAction(){
		return action
	}

	// Set action
	function setAction(new_action){
		action = new_action
	}

	// Set index selected row
	function getEntity(entity_id){
		$('.loading').show()
		$.ajax({
			type: "POST",
			url: Routing.generate('game_game', { id: entity_id }),
			timeout: 15000,
			success: function(response){
				setFormDatas(response)
			},
			error: function(error){
				console.log('Erreur ajax: ' + error)
			}
		})
		$('.loading').hide()
	}

	// Get form Add datas
	function getFormDatas(){

		return {
			id: $("#valider").data('id'),
			name: $("#game_name").val(),
			nbPlayers: $("#game_nbPlayers").val(),
			difficult: $("#game_difficult").val(),
			version: $("#game_version").val(),
			minAge: $("#game_minAge").val(),
			time_hour: $("#game_time_hour").val(),
			time_minute: $("#game_time_minute").val(),
			user_id: $("#game_owner").val(),
		}
	}

	// Set form datas
	function setFormDatas(datas){
		$("#valider").data('id', datas.id)
		$("#game_name").val(datas.name)
		$("#game_nbPlayers").val(datas.nbPlayers)
		$("#game_difficult").val(datas.difficult)
		$("#game_version").val(datas.version)
		$("#game_minAge").val(datas.minAge)
		$("#game_time_hour").val(parseInt(datas.time_hour, 10))
		$("#game_time_minute").val(parseInt(datas.time_minute, 10))
		$("#game_owner").val(datas.user_id)
	}

	// Modal Add
	function modalAdd(){
		modal(null, null, "d'un jeu", false, 1100, 400)
		$('#game_name').focus()
	}

	// AJAX - Send form
	function sendForm(datas){

		let action = getAction()

		$.ajax({
			type: "POST",
			url: Routing.generate('game_' + action, { id: datas.id, datas: datas }),
			timeout: 15000,
			success: function(response){

				if (response.save == 'true' || response.save == true){

					// Get owner
					datas.userName = response.userName
					datas.nom = response.nom
					datas.prenom = response.prenom
					datas.user_id = response.user_id
					datas.owner = datas.nom != '' && datas.prenom != '' && datas.nom != null && datas.prenom != null
						? datas.nom + ' ' + datas.prenom
						: datas.userName

					action == 'edit'
						? toaster("Le jeu à bien été modifié") + editItem(datas, getIndexSelected())
						: null

					action == 'add'
						? toaster("Le jeu à bien été enregistré") + addItem(datas, response.id)
						: null

					action == 'delete'
						? toaster("Le jeu à bien été supprimé") + deleteItem(getIndexSelected())
						: $("#modal").swidget().close()

				} else {
					toaster(response.raison)
				}
			},
			error: function(error){
				console.log('Erreur ajax: ' + error)
			}
		})
		$('.loading').hide()
	}

	// Add Grid item
	function addItem(datas, id){
		datas.id = id
		let grid = $("#grid").swidget();
		grid.insertRow(0, datas)
		grid.saveChanges()
	}

	// Edit Grid item
	function editItem(datas, index){

		let
			grid = $("#grid").swidget(),
			item = $("#grid").swidget().dataItem(index)
		;

		item.name = datas.name
		item.nbPlayers = datas.nbPlayers
		item.difficult = datas.difficult
		item.version = datas.version
		item.minAge = datas.minAge
		item.time_hour = datas.time_hour
		item.time_minute = datas.time_minute
		item.user_id = datas.user_id
		item.userName = datas.userName
		item.nom = datas.nom
		item.prenom = datas.prenom

		grid.dataSource.edit(index).item
		grid.saveChanges()
	}

	// Edit Grid item
	function deleteItem(index){
		$("#grid").swidget().deleteRow(index)
	}
})