@extends('layouts.main')
@section('content')
		<div id="content" class="content mx-auto col-sm-12 col-md-12 col-lg-12 col-xl-10 row mb-3 border-bottom border-white">
			<h2 class="contentTitle text-center mt-4 mb-4">Entenda os benefícios da Fitoterapia.</h2>
			<div class="personalSite col-sm-12 col-md-6 col-lg-6 col-xl-6">
				<h3 class="personalSitetitle">O que é:</h3>
				<div class="personalSiteText">
					<p>Fitoterapia é uma técnica que estuda as funções terapêuticas das plantas e vegetais para prevenção e tratamento de doenças. Médicos, nutricionistas, farmacêuticos, fisioterapeutas e outros profissionais são capacitados para indicar fitoterápicos aos seus pacientes, com o objetivo de melhorar o organismo, ajudar no combate de doenças e atuar na prevenção de problemas de saúde.</p>
				</div>
			</div>
			<div class="companySite col-sm-12 col-md-6 col-lg-6 col-xl-6">
				<h3 class="companySitetitle">Origem</h3>
				<div class="companySiteText">
                    <p>O termo tem origem grega: “phyton”, que significa “vegetal”, e “therapeia”, que remete a “tratamento”. Desta forma, a técnica tem como base uma cultura milenar de uso das plantas para cuidar da saúde.</p>
					<p>Vale destacar que a fitoterapia é somada a estudos e análises no campo científico continuamente. Neste contexto, as pesquisas avaliam a atuação química, toxicológica e farmacológica das plantas medicinais e dos princípios ativos.</p>
				</div>
			</div>
		</div>
		{{-- TODO: Inserir fotos e links de ervas nessa seção --}}
		<div id="myWorks" class="myWorks justify-content-center mx-auto col-sm-12 col-md-12 col-lg-12 col-xl-10 row mb-3 border-bottom border-white pb-4">
			<h2 class="myWorksTitle">Mais populares</h2>
			<div class="myWorksImages row row-cols-1 row-cols-sm-1 row-cols-md-1 row-cols-lg-2 row-cols-xl-2">
				<div class="col mt-4 work-div">
					<img class="img-fluid my-2 w-100 popular-images" src="images/popular_plants/BOLDO-DO-CHILE.jpg" alt="Boldo do chile">
					<span class="work-title d-block">Boldo do Chile</span>
					<span class="work-description">Digestivo, Calmante...</span>
				</div>
				<div class="col mt-4 work-div">
					<img class="img-fluid my-2 w-100 popular-images" src="images/popular_plants/QUEBRA-PEDRA1.jpg" alt="">
					<span class="work-title d-block">Quebra Pedra</span>
					<span class="work-description">Cólica, Depurativo, Diurético...</span>
				</div>
				<div class="col mt-4 work-div">
					<img class="img-fluid my-2 w-100 popular-images" src="images/popular_plants/ESPINHEIRA-SANTA3.jpg" alt="">
					<span class="work-title d-block">Espinheira Santa</span>
					<span class="work-description">Analgésico, Cicatrizante, Digestivo...</span>
				</div>
				<div class="col mt-4 work-div">
					<img class="img-fluid my-2 w-100 popular-images" src="images/popular_plants/MARCELA.jpg" alt="">
					<span class="work-title d-block">Marcela</span>
					<span class="work-description">Anti-inflamatório, Digestivo, Congestão...</span>
				</div>
			</div>
		</div>
		<div id="aboutMe" class="aboutMe mx-auto col-sm-12 col-md-12 col-lg-12 col-xl-10 row mb-3 py-4 align-items-center border-bottom border-white">
			<div class="col-sm-12 col-md-12 col-lg-6 col-xl-6 text-center">
				<h2 class="aboutMeTitle text-center">Sobre nós</h2>
				<p class="aboutMeText">Nosso objetivo e trazer informações científicas e detalhadas sobre plantas que podem ser usadas para diversos tratamentos.</p>
				<p class="aboutMeText">Organizamos a estrutura do site para facitar as pesquisas.</p>
				<h2 class="aboutMeTitle text-center">Redes sociais</h2>
				<ul class="list-unstyled d-flex justify-content-around social-icons-list col-sm-6 col-md-6 col-lg-8 col-xl-8 mx-auto">
					<li><a href=""><i class="bi bi-whatsapp social-icons-style"></i></a></li>
					<li><a href=""><i class="bi bi-instagram social-icons-style"></i></a></li>
					<li><a href=""><i class="bi bi-github social-icons-style"></i></a></li>
					<li><a href=""><i class="bi bi-linkedin social-icons-style"></i></a></li>
					<li><a href=""><i class="bi bi-facebook social-icons-style"></i></a></li>
				</ul>
				<button type="button" class="btn btn-outline-light mb-4">Pesquisar</button>
			</div>
			<div class="col-sm-12 col-md-12 col-lg-6 col-xl-6 text-center">
				<img src="images/logo3.jpg" alt="" class="img-fluid aboutMeImage">
			</div>
		</div>
		<div id="contactMe" class="contactMe mx-auto col-sm-12 col-md-12 col-lg-12 col-xl-10 row mb-3 justify-content-center">
			<h2 class="contactMeTitle text-center">Contato</h2>
			<h3 class="contactMeSubTitle text-center">Se você deseja enviar sugestões ou trabalhar conosco entre em contato preenchendo o formulário</h3>
			<form class="row g-3">
				<div class="col-sm-12 col-md-6 col-lg-6 col-xl-6">
				  <label for="inputName4" class="form-label">Nome</label>
				  <input type="name" class="form-control" id="inputName4" placeholder="Digite o seu nome">
				</div>
				<div class="col-sm-12 col-md-6 col-lg-6 col-xl-6">
				  <label for="inputPhone4" class="form-label">Telefone</label>
				  <input type="text" class="form-control" id="inputPhone4" placeholder="Digite o seu telefone">
				</div>
				<div class="col-12">
				  <label for="inputEmail" class="form-label">E-mail</label>
				  <input type="email" class="form-control" id="inputEmail" placeholder="Digite o seu email">
				</div>
				<div class="col-12 pb-4">
				  <label for="inputMessage" class="form-label">Mensagem</label>
				  <textarea class="form-control" id="inputMessage" rows="5" placeholder="Digite a sua mensagem"></textarea>
				</div>
				<div class="col-12 text-center">
				  <button type="submit" class="btn btn-primary col-sm-6 col-md-3 col-lg-3 col-xl-3">Enviar</button>
				</div>
			  </form>
		</div>
@endsection