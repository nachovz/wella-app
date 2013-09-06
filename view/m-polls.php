<?php
/**
* @Archivo que contiene los parametros de configuración "facebook_config.php"
* @versión: 1.0
* @autor: Psycho
*/

/*
require_once 'phputils/facebook/src/facebook.php';

$facebook = new Facebook(array(
  'appId'  => '344617158898614',
  'secret' => '6dc8ac871858b34798bc2488200e503d',
));

// Get User ID
$user = $facebook->getUser();

// We may or may not have this data based on whether the user is logged in.
//
// If we have a $user id here, it means we know the user is logged into
// Facebook, but we don't know if the access token is valid. An access
// token is invalid if the user logged out of Facebook.

if ($user) {
  try {
    // Proceed knowing you have a logged in user who's authenticated.
    $user_profile = $facebook->api('/me');
  } catch (FacebookApiException $e) {
    error_log($e);
    $user = null;
  }
}

// Login or logout url will be needed depending on current user state.
if ($user) {
  $logoutUrl = $facebook->getLogoutUrl();
} else {
  $loginUrl = $facebook->getLoginUrl();
}

// This call will always work since we are fetching public data.
$naitik = $facebook->api('/naitik');
*/
?>
<section>
	<div class="container">

<?php if(isset($_COOKIE['iVotedPoll'.$vars['poll']->id])) { ?>
		
		<div class="row">
			<div class="span12" id="caja_info">
				<h1>GRACIAS POR PARTICIPAR</h1>
				<a href="#">CERRAR (X)</a>
			</div>
		</div>

<?php } else { ?>

	<?php foreach($vars['options'] as $index=>$option) { ?>

		<div class="row">
	        <div class="span12">
	            <h1><?php echo utf8_encode( $option->answer ); ?><span><?php echo utf8_encode( $option->answer ); ?></span></h1>
	            <ul id="est_<?php echo $index + 1; ?>" class="pics_estilista">	            	
	            	<?php $pic_name  = strtolower( str_replace( ' ', '-', $vars['poll']->question ) ) . '/' . utf8_encode( strtolower( str_replace( ' ', '-', $option->answer ) ) );?>
	                
	                <?php for( $i = 1; $i <= $option->picture_count; $i++ ): $j = ( $i > 0 && $i < 10 ) ? '0' . $i : $i; ?>
					<li><img alt="Imagen del estilista" src="<?php echo $GLOBALS["baseURL"] . 'images/' . $pic_name . '-thumb-' . $j . '.png'; ?>" width="74" height="74" /></li>
	            	<?php endfor;?>
	            </ul>
	            <a href="<?php echo $GLOBALS["baseURL"]; ?>crud.php?view=mobile&action=votePoll&id=<?php echo urlencode($_SERVER['REMOTE_ADDR']); ?>&ans=<?php echo $option->id; ?>&poll=<?php echo $vars['poll']->id; ?>&type=web" optionKeyword="<?php echo $option->keyword; ?>">VOTAR</a> 
	        </div>
	    </div>

	<?php } ?>

<?php } ?>
	</div>
</section>

<div class="carousel" id="popupZero">
	<?php foreach( $vars['options'] as $index => $option ): $i = $index + 1;?>
	<div id="<?php echo "caja_{$i}";?>" class="caja-pics">
		<?php $pic_name  = strtolower( str_replace( ' ', '-', $vars['poll']->question ) ) . '/' . utf8_encode( strtolower( str_replace( ' ', '-', $option->answer ) ) );?>   
   		<?php for( $i = 1; $i <= $option->picture_count; $i++ ): $j = ( $i > 0 && $i < 10 ) ? '0' . $i : $i; ?>
        <div class="item">
        	<img alt="Imagen del estilista" src="<?php echo $GLOBALS["baseURL"] . 'images/' . $pic_name . '-' . $j . '.png'; ?>" width="278" height="295" />
        </div>
        <?php endfor;?>
    </div>
	<?php endforeach;?>
    <a class="carousel-control left" data-slide="prev" id="btn_prevpic_popup" href="#popupZero" title="Mostrar imagen anterior"></a>
    <a class="carousel-control right" data-slide="next" id="btn_proxpic_popup" href="#popupZero" title="Mostrar imagen siguiente"></a>
    <a id="btn_cerrar_popup" href="#" title="Cerrar galer&iacute;a"></a>
</div>

<script>
    jQuery(document).ready(function($) {

        $('#popupZero').carousel('pause');
        
        $('li','ul.pics_estilista').each(function(i,e){
            $(e).click(function(){
                var nro = $(this).parent('ul').attr('id').split('_')[1],
                    index = $(this).index();
                $('div.caja-pics','#popupZero').removeClass('carousel-inner').filter('#caja_'+nro+'').addClass('carousel-inner').find('div.item').removeClass('active').filter(':eq('+index+')').addClass('active');
                $('#popupZero').WellaPopups();
            });
        });

    });
</script>


	 <!-- <h3 class="subtitle">Voting<br />
		<span>Vote clicking over your preferred option!</span></h3>
	<div id="main-section" class="boxShadow">
	  <h4 id="poll-title"><?php echo $vars['poll']->question; ?></h4>
		<div id="poll-keywords">
			<div id="submitResult"></div>
		<?php if(isset($_COOKIE['iVotedPoll'.$vars['poll']->id])) { 
			$next_poll = 0;
			if ($vars['poll']->id == 2949){
				$next_poll = 2951;
				
			}
			else{
				$next_poll = $vars['poll']->id+1;
			}?>
		
		  <p>Su voto en esta encuesta ya ha sido registrado.</p><p>
		  <?php if ($vars['poll']->id != 2963){?><a href ="<?php echo $GLOBALS['baseURL']."mobile/poll/". $next_poll; ?>" >Ir a la siguiente pregunta</a> &oacute; <?php }?><a href="<?php echo $GLOBALS['baseURL']; ?>mobile">Volver a la lista de encuestas</a>.</p>
		<?php } else { ?>
			<ul class="keywords-list">
  		<?php foreach($vars['options'] as $option) { ?>
				
				<li><a href="<?php echo $GLOBALS["baseURL"]; ?>crud.php?view=mobile&action=votePoll&id=<?php echo urlencode($_SERVER['REMOTE_ADDR']); ?>&ans=<?php echo $option->id; ?>&poll=<?php echo $vars['poll']->id; ?>&type=web" class="option" optionKeyword="<?php echo $option->keyword; ?>"><?php echo $option->answer; ?></a></li>
			<?php } ?>
			</ul>
		<?php } ?>
			<span class="clear">&nbsp;</span>
		</div>
	</div>
	
	<span class="clear">&nbsp;</span>
-->