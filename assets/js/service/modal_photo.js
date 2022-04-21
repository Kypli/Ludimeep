import '../../styles/service/modal_photo.css';

$(document).ready(function(){

	// Get the modal
	var modal = document.getElementById("myModal");

	// Get the datas
	var imgs = $('.img_grow img')
	var modalImg = document.getElementById("img01");
	var captionText = document.getElementById("caption");

	// Insert img inside the modal - use its "alt" text as a caption
	$.each(imgs, function(index, value){
		$('#' + value.id).on('click', function(){
			modal.style.display = "block";
			modalImg.src = this.src;
			captionText.innerHTML = this.alt;
		})
	})

	// Get the <span> element that closes the modal
	var span = document.getElementsByClassName("close")[0];

	// When the user clicks on <span> (x), close the modal
	span.onclick = function(){
		modal.style.display = "none";
	}
})