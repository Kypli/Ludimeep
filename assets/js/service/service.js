
////////////
// FONCTION EXPORT
////////////

// Convertit ou crée Date en string
export function dateToString(date){

	var 
		newDate = new Date(),
		dateString = typeof date == Date && date != "" && date != null
			? date
			: newDate,
		jour_0 = dateString.getDate() < 10
			? '0'
			: '',
		mois_0 = dateString.getMonth() < 10
			? '0'
			: ''
	;

	return jour_0 + dateString.getDate() + "/" + mois_0 + (dateString.getMonth() + 1) + "/" + dateString.getFullYear()
}


// 1ere lettre Majuscule
export function ucFirst(str){

	return (str + '').charAt(0).toUpperCase() + str.substr(1)
}

// 1ere lettre Minuscule
export function lcFirst(str){

	return (str + '').charAt(0).toLowerCase() + str.substr(1)
}