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
		var datas = getFormDatas()
		sendForm(datas)
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
					// modify: {
					// 	remove: {
					// 		url: Routing.generate('game_delete'),
					// 		type: "POST",
					// 		data: function(item) {

					// 			return {
					// 				datas: item[0].data,
					// 			}
					// 		},
					// 		success: function(response){

					// 			if (response.save === true){
					// 				toaster("Le jeu a été supprimé.")

					// 			} else {
					// 				toaster(response.raison)
					// 			}
					// 		},
					// 		error: function(error){
					// 			toaster("Formulaire incorrect.")
					// 			console.log('Erreur ajax: ' + error)
					// 		},
					// 	}
					// },
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
						userName: {
							path: "userName",
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
						time: {
							path: "time",
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
				pageSize: 100,
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
			maxHeight: 600,
			editing: {
				enabled: true,
				type: "row",
				confirmation: {
					"delete": {
						enabled: true,
						template: function (item) {
							return "Supprimer ce jeu ? Attention cette action est définitive !";
						}
					}
				},
			},
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
						var text = item.id == undefined ? '' : item.id
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
					columnTemplate: function(cell, item, index){
						var text = item.nom == null || item.prenom == null ? item.userName : ucFirst(item.nom) + ' ' + ucFirst(item.prenom)
						$("<div>" + text + "</div>").appendTo(cell)
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
						var text = item.nbPlayers == null ? '' : item.nbPlayers
						$("<div>" + text + "</div>").appendTo(cell)
					},
				},
				{
					field: "difficult",
					title: "Difficulté",
					width: 60,
					minWidth: 60,
					customFilter: function(cell, item, index){
						myCustomFilter(cell, 'difficult', 'Difficulté', 'text', 70)
					},
					columnTemplate: function(cell, item, index){
						var text = item.difficult == null ? '' : item.difficult
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
						var text = item.version == null ? '' : item.version
						$("<div>" + text + "</div>").appendTo(cell)
					},
				},
				{
					field: "minAge",
					title: "Age minimum",
					width: 60,
					minWidth: 60,
					customFilter: function(cell, item, index){
						myCustomFilter(cell, 'minAge', 'Age minimum', 'text', 70)
					},
					columnTemplate: function(cell, item, index){
						var text = item.minAge == null ? '' : item.minAge
						$("<div>" + text + "</div>").appendTo(cell)
					},
				},
				{
					field: "time",
					title: "Durée",
					width: 60,
					minWidth: 60,
					customFilter: function(cell, item, index){
						myCustomFilter(cell, 'time', 'Durée', 'text', 70)
					},
					columnTemplate: function(cell, item, index){
						var text = item.nbPlayers == null ? '' : item.nbPlayers
						$("<div>" + text + "</div>").appendTo(cell)
					},
				},
				{
					field: "actions",
					title: "Actions",
					width: 60,
					minWidth: 60,
					visible: admin,
					buttons: [
						{
							caption: "<i class='far fa-edit' title='Modifier'></i>",
							click: function (e){
								setAction('edit')
								setIndexSelected(e)
								var id = $("#grid").swidget().dataItem(e).id
								$('#valider').data('id', id)
								modalAdd()
								getEntity(id)
							},
						},
						{ 
							commandName: "delete",
							caption: "<i class='far fa-trash-alt' title='Supprimer'></i>"
						}
					]
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
			time: $("#game_time").val(),
		}
	}

	// Set form datas
	function setFormDatas(datas){
		$("#valider").data('id', datas.id)
		$("#game_name").val(datas.libelle)
		$("#game_nbPlayers").val(datas.fabriquant)
		$("#game_difficult").val(datas.type)
		$("#game_version").val(datas.immobilisation)
		$("#game_minAge").val(datas.type_supervision)
		$("#game_time").val(datas.prog)
	}

	// Modal Add
	function modalAdd(){
		modal(null, null, "d'un jeu", false, 1100, 400)
	}

	// AJAX - Send form
	function sendForm(datas){

		var action = getAction()

		$.ajax({
			type: "POST",
			url: Routing.generate('game_' + action, { id: datas.id, datas: datas }),
			timeout: 15000,
			success: function(response){

				if (response.save == 'true' || response.save == true){
					$("#modal").swidget().close()
					action == 'edit'
						? toaster("Le jeu à bien été modifié")
						: toaster("Le jeu à bien été enregistré")
					action == 'edit'
						? editItem(datas, getIndexSelected())
						: addItem(datas, response.id)
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
		var grid = $("#grid").swidget();
		grid.insertRow(0, datas)
		grid.saveChanges()
	}

	// Edit Grid item
	function editItem(datas, index){

		var
			grid = $("#grid").swidget(),
			item = $("#grid").swidget().dataItem(index)
		;

		item.libelle = datas.libelle
		item.fabriquant = datas.fabriquant
		item.type = datas.type
		item.immobilisation = datas.immobilisation
		item.type_supervision = datas.type_supervision
		item.prog = datas.prog
		item.zone_usine = datas.zone_usine

		grid.dataSource.edit(index).item
		grid.saveChanges()
	}
})
