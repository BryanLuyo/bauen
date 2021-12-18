@extends('../master_layout/web')

@section('metadata')

<meta name="image" property="og:image" content="https://www.bauenfreight.com/transporter/public/assets/bauenfreight/images/Que es y como Funciona Thumbnail.png">

<meta property="og:title" content="Bauen | 5 minute reads">

<meta property="og:description" content="Conectando cargas con transportistas homologados">

<meta property="description" content="Conectando cargas con transportistas homologados">


@endsection

@section('custom_js')
<script>
    $( document ).ready(function() {
      
    if( navigator.userAgent.match(/Android/i)){
      
        $('.download-link').attr("href", "https://play.google.com/store/apps/developer?id=Bauen");
      }
    else if(navigator.userAgent.match(/iPhone/i) || navigator.userAgent.match(/iPad/i)|| navigator.userAgent.match(/iPod/i)){
  
      $('.download-link').attr("href", "https://itunes.apple.com/us/developer/sergio-olcese/id1288677361?mt=8");
    }
   else {
     
      $('.download-link').attr("href", "http://www.bauenfreight.com");
    }
  
  });
  </script>
@endsection


@section('custom_css')
<style type="text/css">
  .download-btn {
    display: inline-block;
    width: 200px;
    height: 69px;
    margin: 0.5rem 2rem;
    background-position: top left;
    background-size: 100% auto;
    border-radius: 0px;
    transition: transform 0.3s, box-shadow 0.3s;
  }

  .download-btn:hover {
    background-position: bottom left;
    box-shadow: 0 5px 10px rgba(0, 0,0,0.3);
    transform: translateY(-0.5rem);
  }

  .google-play-btn {
    background-image: url({{asset('public/assets/bauenfreight/images/sprite-playstore-01-01.png')}});
  }

  .ios-play-btn {
    background-image: url({{asset('public/assets/bauenfreight/images/sprite-ios-1-01.png')}});
  }

  .download-btns {
    display: flex;
    align-items:center;
    justify-content:center;
    flex-direction: row;
    margin-top:2rem;
  }

  @media screen and (max-width: 690px) {
      .download-btns {
        flex-direction: column;
      }
	  
  }

  .hero {
    display: center;
    width: 60%;
    display: block;
    margin-left: auto;
    margin-right: auto;
    background:black;
  }

  @media screen and (max-width: 1300px) {
    .hero {
      width: 100%;
    }
  }

  .hero-pattern {
    background-size: contain;
    width: 100%;
  }

  .hero-pattern--left {
    background-image:url({{asset('public/assets/bauenfreight/images/bauenjorney--left.png')}});
  }

  .hero-pattern--right {
    background-image:url({{asset('public/assets/bauenfreight/images/bauenjorney--right.png')}});
  }

  .hero-img {
    width:100%;
    height:auto;
    max-width:1300px;
  }

  @media screen and (max-width: 1300px) {
    .hero {
      display: block;
    }

    .hero-pattern {
      display: none;
    }
  }

  .lead {
    font-weight: 120 !important;
    /* font-size: 30px !important; */
	font-size: 3.15em !important;
    max-width: 60rem;
    line-height: 1.3em;
    margin: auto;
  }

  .text-center {
    text-align: center !important;
  }

  .text-right {
    text-align: right !important;
  }

  .section-title {
    font-weight: 1000 !important;
    font-size: 2px !important;
    margin-top: 1rem !important;
    margin-bottom:1rem !important;
    color: #3b3b3b;
    padding-top: 2rem;
  }
  
  .section-title1 {
    font-weight: 1000 !important;
    font-size: 22px !important;
    margin-top: 2rem !important;
    margin-bottom:1rem !important;
    color: #3b3b3b;
    padding-top: 5 rem;
	text-align: left;
	display:block;
    padding-left: 4%;
    padding-right:12%;
    line-height: 30px;
  }

  .text-md {
    margin-top:2px;
    font-size: 19px !important;
    font-weight: 400 !important;
    color: #3b3b3b;
    display:block;
    padding-left:4%;
    padding-right:12%;
    line-height: 30px;
  }

  .bg-light {
    background-color: #f2f1ee;
  }

  .bg-dark {
    background-color: #3b3b3b;
    color: #fff;
    padding-top: 0rem;
    padding-bottom: 1.5rem;
  }

  .white-title {
    display: block;
    font-size: 24px;
    text-align: center;
    padding-top:0rem;
    color:white;
  }
  
  .white-bold-title {
    display: block;
    font-size: 26px;
    font-weight: bold;
    text-align: center;
    padding-top:3rem;
    color: #dcb62d;
  }

  @media screen and (min-width: 470px) {
    .white-bold-title {
      font-size: 34px;
    }
  }
  
  @media only screen and (max-width: 470px) {
	  .lead {
			font-weight: 120 !important;
			font-size: 2.15em !important;
			max-width: 60rem;
			line-height: 1.3em;
			margin: auto;
	   }
	   
	   .text-md {
			margin-top: 2px;
			font-size: 22px !important;
			font-weight: 400 !important;
			color: #3b3b3b;
			display: block;
			padding-left: 4%;
			padding-right: 7%;
			line-height: 30px;
		}
		
		.section-title1 {
			font-weight: 1000 !important;
			font-size: 22px !important;
			margin-top: 1rem !important;
			margin-bottom: 1rem !important;
			color: #3b3b3b;
			padding-top: 5 rem;
			text-align: left;
			display: block;
			padding-left: 4%;
			padding-right: 7%;
			line-height: 30px;
		}
	   
  }
  
  .left{
    float: left; 
}
  .right{
    float: right; 
  }

  .page-header {
    position: relative;
    padding-top: 10px;
    padding-bottom: 10px;
    margin: 0 !important;
    border-bottom: 0;
  }

  @media screen and (min-width: 768px) {
    .page-header {
      padding-top: 20px;
      padding-bottom: 20px;
    }
  }

  .page-header__container {
    position: relative;
    z-index: 2;
  }

  .page-header__copy {
    font-weight: 600 !important;
    margin-bottom: 0rem;
    color: #fff;
    text-shadow: 1px 1px 5px rgba(0, 0, 0, 0.5);
  }

  .page-header__img {
    position: absolute;
    top: 0;
    right: 0;
    bottom: 0;
    left: 0;
    width: 100%;
    height: 100%;
    object-fit: cover;
    opacity: 0.9;
  }
  
  html, body {
    font-family: 'Montserrat', sans-serif!important;
    font-size: 16px;
    /* height: 100%; */
    position: relative;
    color: #3b3b3b;
    font-weight: 500;
  }
</style>
@endsection






@section('main_container')
  <div class="page-header">
    <div class="container page-header__container how-it-works">
      <p class="lead text-center page-header__copy" ><!-- Encuentra al transportista ideal de manera confiable y segura, eligiendo entre una serie de propuestas de los mejores transportistas con precios por adelantado --> Digital Freight Matching: <br> ¿Qué es y cómo funciona?</p>
    </div>
    <img class="page-header__img" src="{{asset('public/assets/bauenfreight/images/img-howitworks-truck.jpg')}}">
  </div>
  <div class="bg-light">
    <div class="container" style="padding-top: 1rem">
      <div class="row wow fadeInUp">
        <div class="col-md-12" style="text-align:left !important; margin-top:20px">
		  
		   <div class="social-links" style="margin-left: 4%;
    margin-right: 12%;
    font-size: 22px !important;
    font-weight: 400 !important;
    color: #3b3b3b;
	border-top: 1px solid #dee2e6!important;
	border-bottom: 1px solid #dee2e6!important;
    padding-top: 1%;
    padding-bottom: 1%;
	">
		                  <span> Compartir en:</span>
						  <a href="mailto:?subject=Digital Freight Matching:¿Qué es y cómo funciona?&amp;body=Digital Freight Matching:¿Qué es y cómo funciona?, https://www.bauenfreight.com/transporter/how-it-works" class="fa fa-send-o"  target="_blank" title="Send email" style="color:#606060; margin-left:1%"></a>
						  <a href="https://www.linkedin.com/shareArticle?mini=true&amp;url=https://www.bauenfreight.com/transporter/how-it-works&amp;title=Digital Freight Matching:¿Qué es y cómo funciona?&amp;source=bauenfreight" class="fa fa-linkedin" style="color:#606060; margin-left:1%"></a>
						  <!-- <a href="https://plus.google.com/share?url=https://www.bauenfreight.com/transporter/how-it-works" class="fa fa-google-plus"  target="_blank" title="Share on Google+" style="color:#606060; margin-left:1%"></a> -->
						  <a href="https://twitter.com/intent/tweet?source=https://www.bauenfreight.com/transporter/how-it-works&amp;text=Digital Freight Matching:¿Qué es y cómo funciona?" class="fa fa-twitter" style="color:#606060; margin-left:1%"></a>
						  <a href="https://www.facebook.com/sharer/sharer.php?u=https://www.bauenfreight.com/transporter/how-it-works&amp;t=Digital Freight Matching:¿Qué es y cómo funciona?" class="fa fa-facebook" style="color:#606060; margin-left:1%"></a>
		   </div>
		</div>
      </div>
    </div>
  </div>
  </div>
  <div class="bg-light">
    <div class="container" style="padding-top: 1rem">
      <div class="row wow fadeInUp">
        <div class="col-md-12" style="text-align:left !important; margin-top:7px">
		  </span><span class="text-md" >El Digital Freight Matching brinda soporte a la logística de transporte, automatizando procesos y haciendolos más eficientes. Se reemplazan los tarifarios y correos por precios dinámicos y trazabilidad 100% de la carga, por medio de un proceso ágil, confiable, ordenado y transparente. </span><br>
		  <span class="text-md" > El DFM brinda visibilidad completa del proceso de transporte por medio del IoT (Internet of Things), facilitando que las empresas tengan visibilidad de más y mejores precios por adelantado. Se obtiene data real de las disponibilidades, mientras que se hace un seguimiento completo de todo el proceso de carga, desde que se negocia hasta completado el servicio. Asegurando eficiencia, transparencia y ahorro de tiempo y dinero a lo largo de todo el proceso.</span><br>
		  <span class="text-md">Programa las cargas diaria, semanal y mensualmente. Con el proceso actual, se está perdiendo toda oportunidad de tener la información ordenada y no se tiene visibilidad de precios ni de la carga. Mientras tanto, los transportistas pierden oportunidades para mostrar su capacidad, impactando el costo de transporte. El <b>DFM</b> ataca estos problemas encontrando al transportista ideal de manera rápida y confiable. Con el uso de Big Data y Tecnologías de la Información, las empresas pueden mejorar sus tiempos, minimizar errores y mejorar la toma de decisiones.</span>
		  <span class="text-md" ></span>
		  </span><br><span class="text-md" >Vayamos a darle una mirada a cómo funciona esta nueva tecnología, ahora disponible en el Perú.</span>
		  <h1 class="section-title1">Ahorro y eficiencia</h1>
          <span class="text-md">Uno de los beneficios principales es que nos provee la habilidad de obtener más y mejores opciones de transporte. Esta visibilidad e inteligencia de mercado es ahorro directo. </span><br>
		  <span class="text-md"><b>En el Perú, hemos obtenido un ahorro promedio del 8 al 10% del flete con el uso de Bauen [2019]</b></span><br>
		  <span class="text-md">En un escenario sin DFM, el generador de carga tiene que "pasarsela" averiguando las disponibilidades de sus transportistas o trabajando con un operador logístico que les asegure mayor capacidad, lo cual cuesta mucho más dinero en el tiempo.</span>
		  <h1 class="section-title1">Trazabilidad y transparencia</h1>
          <span class="text-md">Con Bauen tendrás toda la información a la mano. Sabrás los precios ofertados y aceptados, lead times, <b>ubicaciones de la carga</b>, ETAs, también tendrás un registro de horas en cada parte del proceso de transporte. Registramos a qué hora se carga, se inicia el viaje, cuando llega a destino y completado el servicio. Tambien medimos tiempos de carga y descarga e implementamos notificaciones para que estén al tanto de todo ello. Todo en un sólo sitio. </span>
		  <h1 class="section-title1">Flexibilidad</h1>
          <span class="text-md">Uno de los retos más importantes en la actualidad es la volatilidad de la demanda. Bauen permite escalar la capacidad de carga dinámicamente y facilita ser más flexibles en momentos donde ésta varía. <br><br> En un escenario alternativo, los generadores de carga se ven en la obligación de trabajar con grandes operadores logísticos de manera contractual para asegurar la capacidad que necesitan. Esto puede ser muy costoso o sencillamente puede no funcionar si no sabemos exactamente cuánto es el volumen de carga que vamos a trasladar en el tiempo. Bauen te la precios dinámicos y acceso a una red de 50 empresas de transporte.
          </span><br><span class="text-md" >El DFM ayuda a evitar esta situación. Nos permite identificar transportistas rápidamente a través de una plataforma colaborativa. El resultado final es una plataforma que te permite tener mayor capacidad y flexibilidad sin tener que entrar a tener contratos a largo plazo. Además, realizamos pronósticos de la demanda y de las disponibilidades de los transportistas en el tiempo.</span>
		  <h1 class="section-title1">Reduce los costos laborales</h1>
          <span class="text-md">El DFM introduce, de manera inmediata y visible, eficiencias a los sistemas de planificación de transporte. Reemplazando los procesos manuales con un solo “click” mediante un proceso basado en internet. La plataforma digital ahorra mucho esfuerzo humano por la automatización. Por ejemplo, elimina los Excel, llamadas telefónicas, papeleos, correos electrónicos y la distribución de estos.</span>
		  <h1 class="section-title1">Simplifica la administración del transporte</h1>
          <span class="text-md">La automatización, el acceso a data histórica y el tener información de todas las cargas de manera centralizada permite que se esté al tanto de todo lo que viene sucediendo en la operación en tiempo real. Podemos identificar cuanto se ha ido pagando, porque y mostrar de manera transparente como se fueron tomando las decisiones.</span><br>
          <span class="text-md" >Los procesos manuales nos llevan a cálculos inexactos que hacen del proceso más improductivo y la mala transmisión de información induce a errores operacionales que llevan a sobrecostos. </span>
		  <h1 class="section-title1">Extiende tu red de transportes</h1>
          <span class="text-md">Bauen permite la colaboración con varios transportistas a la vez, asegurando conseguir <b>las mejores condiciones de transporte.</b> Adicionalmente, se puede filtrar por zonas, requisitos, seguros, nivel de servicio, y más.</span>
		  <h1 class="section-title1">Nuestro impacto en el medio ambiente y el tránsito</h1>
          <span class="text-md">Eliminar los viajes innecesarios reduce la congestión y las emisiones. Cualquier tecnología que ayude a reducir la cantidad de vehículos pesados ​​tendrá un impacto ambiental instantáneo. Se estima que el transporte de carga representa alrededor del <b>17% de las emisiones de gases de efecto invernadero del transporte por carretera</b> y más del 20% de las emisiones de óxido de nitrógeno del transporte por carretera.
          </span><br><span class="text-md" >Además, la documentación asociada con los envíos se acumula. El DFM permite que esto se migre a la nube, lo que ahorra tiempo, dinero y energía, reduciendo instantáneamente los desperdicios.</span>
          <h1 class="section-title1">Subastas públicas y privadas</h1>
          <span class="text-md">Es muy importante este punto. En las subastas públicas se permite que el universo de transportistas cotize, es decir, cualquier empresa de transportes tiene acceso a ver y cotizar tu carga. Aquí se reciben muchas propuestas de transporte y cualquiera puede acceder. La segunda opción es para empresas que no buscan aumentar su número de proveedores ya que cuentan con más requisitos u homologaciones en su operación. En este caso, se hace la asignación de la carga por medio de la subastas privada. En este caso, sólo tus transportistas verán la solicitud y sólo se les notificará a ellos que hay una carga disponible. En ambos casos se sigue el mismo proceso digital de transporte que recolecta la información necesaria y con los beneficios ya mencionados.</span>
		  <h1 class="section-title1">Transportando el futuro con Bauen</h1>
          <span class="text-md">El transporte es un esfuerzo colaborativo. En un entorno tan dinámico, los métodos antiguos ya no son suficientemente efectivos para las nuevas exigencias. Con un enfoque comprensible (a diferencia de otras iniciativas digitales), podemos implementar Bauen sin demandar de mucho tiempo, dinero o esfuerzo. Puede ser muy fácil migrar todas las operaciones y sentir los beneficios inmediatamente. Así es cómo el DFM se ha vuelto un “must-have” para las cadenas logísticas en el Perú y todo el mundo. 
          </span><br><span class="text-md" >Generadores de carga y transportistas están volteando la mirada hacia el DFM para encontrar el transporte que necesitan en esta nueva era. Y tú, ¿Qué esperas?</span><br><br>
          <p class="text-center"><a href="{{url('signup')}}" style="margin-top: 0rem;padding-left:3rem;padding-right:3rem;" class="btn btn-lg btn-custom mb-3 mb-md-0"><b>Scan logístico gratuito</b></a></p><br><br>
        </div>
	  </div>
	</div>
  </div>
  <div class="bg-dark wow fadeInUp">
      <div class="container">
        <div class="row">
          <div class="col-12">
            <span class="white-bold-title">Descarga el app movil gratis</span>  
            <span class="white-title">Disponible en iOS y Android</span>
            <div class="download-btns">
              <a href="https://play.google.com/store/apps/developer?id=Bauen" class="download-btn google-play-btn"></a>
              <a href="https://itunes.apple.com/us/developer/sergio-olcese/id1288677361?mt=8" class="download-btn ios-play-btn"></a>
            </div>
          </div>
        </div>
      </div>
  <div class="bg-dark">
    <div class="hero wow fadeInUp">
     <div class="hero-pattern hero-pattern--left"></div>
       <img src="{{asset('public/assets/bauenfreight/images/bauenjorney.png')}}" class="hero-img" />
    <div class="hero-pattern hero-pattern--right"></div>
  </div>

@endsection