<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js" integrity="sha384-I7E8VVD/ismYTF4hNIPjVp/Zjvgyol6VFvRkX/vR+Vc4jQkC+hVqc2pM8ODewa9r" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.min.js" integrity="sha384-BBtl+eGJRgqQAUMxJ7pMwbEyER4l1g+O15P+16Ep7Q9Q+zqX6gSbd85u4mG4QzX+" crossorigin="anonymous"></script>
<script>
	document.querySelector('.navbar-toggler').addEventListener('click', function(){ 
	if(this.getAttribute('aria-expanded')) {
		// document.querySelector('.navbar-nav').classList.toggle('bg-primary'); //troca cor quando menu aberto
		document.querySelector('.navbar-toggler .menu-button').classList.toggle('bi-list');
		document.querySelector('.navbar-toggler .menu-button').classList.toggle('bi-x-circle');
	};
	});
</script>