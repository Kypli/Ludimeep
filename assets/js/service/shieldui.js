import { ucFirst } from './service.js';
import { lcFirst } from './service.js';

import '../../styles/service/shieldui.css';

////////////
// FONCTION EXPORT
////////////

// Affiche Modal Fiche
export function modal(id, index, value, duplicate = false, width = 1150, height = 760){

	var title = id != null && !duplicate
		? "Édition"
		: "Création"

	$("#modal").shieldWindow({
		title: title + " " + value,
		width: width,
		height: height,
		modal: true,
		visible: true,
		center: true,
		titleBarButtons: [ 'maximize', 'close' ]
	}).swidget()

	if (!duplicate){
		$("#valider").data("id", id)
		$("#valider").data("index", index)
	}
}

// Filtres actif
export function useFilter(libelle, path, filter, value, fiche = false, remplace = null){

	// Fiche ?
	fiche = fiche == false
		? ''
		: 'Fiche'

	// Lance une recherche
	if (value != ''){
		$("#grid"+fiche).swidget().filter({ path: path, filter: filter, value: value });
		$("#filtre"+fiche+"Actif, #close"+fiche+"Filtres, #filtre"+fiche+"ActifType").show()

		// Remplace la valeur si demandé
		value = remplace !== null
			? remplace
			: value

		// Convertit les Boolean
		var val = (value === true && filter != 'neq') || value == 'true'
				? 'Oui'
				: value === false || value == 'false' || value === true
					? 'Non'
					: value

		// Affiche le détail de la recherche
		$("#filtre"+fiche+"ActifType").html('(' + libelle +  ' -> "' + val + '")')

	// Reset la recherche
	} else {
		$("#grid"+fiche).swidget().filter({ path: '', filter: '', value: '' });
		$("#filtre"+fiche+"Actif, #close"+fiche+"Filtres, #filtre"+fiche+"ActifType").hide()
		$("#filtre"+fiche+"ActifType").html('')
	}
}

// Filtres personnalisé
export function myCustomFilter(cell, path, libelle, type, width = '', fiche = ''){

	width = width != ''
		? "style='max-width: " + width + "px!important;'"
		: ''

	$('<input id="filter' + ucFirst(path) + '" class="search' + fiche + '" type="' + type + '" ' + width + ' data-libelle="' + libelle + '" />')
		.shieldTextBox({})
		.swidget()
		.value('')
		.appendTo(cell)
}

// Barre de progression
export function barProgress(titre_id){

	$("#" + titre_id).shieldProgressBar({
		value: 0,
		text: {
			enabled: true,
		template: "{0} %"
		}
	})

	var shouldIncrease = true
	var progress = $("#" + titre_id).swidget()

	setInterval(function (){

		if (progress.value() < 100 && shouldIncrease){
			progress.value(progress.value() + 1);

		} else {
			shouldIncrease = false;
			progress.value(progress.value() - 1);
			if (progress.value() == 0) {
				shouldIncrease = true;
			}
		}
	}, 30)
}

////////////
// ON EVENTS
////////////

// Modal fermeture filtre
$("body").on("click","#closeFiltres", function(e){
	useFilter('', '', '', '')
})

// Modal fermeture filtre fiche
$("body").on("click","#closeFicheFiltres", function(e){
	useFilter('', '', '', '', true)
})

// Modal filtre
$("body").on("change",".search", function(e){

	var value = this.id
	var libelle = $(this).data('libelle')
	value = lcFirst(value.substring(6))

	useFilter(libelle, value, 'con', $("#filter" + ucFirst(value)).val())
})

// Modal filtre fiche
$("body").on("click",".searchFiche", function(e){

	var value = this.id
	value = lcFirst(value.substring(6))

	useFilter(ucFirst(value), value, 'con', $("#filter" + ucFirst(value)).val(), true)
})

// Modal close 
$("body").on("click", "#closeModal", function(e){
	$("#modal").swidget().close()
})

// Alerte couleur input pour sauvegarde
$("body").on("click", "tbody .sui-input", function(e){
	if ($(this).val() == ''){
		$(this)
			.removeClass('inputSuiNonVide')
			.addClass('inputSuiVide')
		}
})

$("body").on("focusout", "tbody .sui-input", function(e){

	// var drop = $(this).prev().prev()['prevObject'][0]

	if ($(this).val() != ''){
		$(this)
			.removeClass('inputSuiVide')
			.addClass('inputSuiNonVide')
	} else {
		$(this)
			.removeClass('inputSuiVide')
			.removeClass('inputSuiNonVide')
	}
})