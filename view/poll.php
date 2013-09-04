<html lang="es">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<link type="text/css" rel="stylesheet" href="../../css/poll.css" />
<link type="text/css" rel="stylesheet" href="../../css/new/web-vote.css"/>
<script type="text/javascript" src="../../js/jquery.js"></script>
<script type="text/javascript" src="../../apetest/APE_JSF/Build/uncompressed/apeClientJS.js"></script>
</head>
<body>
<?php 
$pollId = $_GET["p"];
    
$query = "
	select 
		poll.id,poll.keyword, answers.votos, poll.answer
	from 
		poll_option poll 
			left join 
		(select id_option, count(id_option) as votos from answer where id_poll = '".$pollId."' group by id_option) as answers on answers.id_option = poll.id 
	where 
		poll.id_poll = '".$pollId."
	order by
		votos desc
			';
";

$link =  mysql_connect('localhost', 'root', 'mysqla18064066');
if (!$link) {
    die('No pudo conectarse: ' . mysql_error());
}

$db_selected = mysql_select_db('ivoted', $link);
if (!$db_selected) {
    die ('Can\'t use foo : ' . mysql_error());
}

$result = mysql_query($query);

if (!$result) {
    $message  = 'Invalid query: ' . mysql_error() . "\n";
    $message .= 'Whole query: ' . $query;
    die($message);
}

?>
<p id="submitResult" class="success" style="display: none;margin-top: 0px;font-size: 12px"></p>
 <div class="section"> 
    <ul class="chartlist">    
    <?php 
    while ($row = mysql_fetch_assoc($result)) {
    	$cant = $row['votos'] == "" ? 0 : $row['votos'];
    	echo '<li id="'.$row['keyword'].'" onclick="vote(this)">
        <a href="#live-poll">'.$row['answer'].'</a> 
        <span class="count">'.$cant.'</span>
        <span class="index" style="width: 0%">0</span>
      </li>';
	}
    ?>
    </ul>
  </div>
  
  
  <script type="text/javascript">

function showInstructions(){
	$('#submitResult').hide(300);
}  
  
function vote(objectOption){
	   $(objectOption).css("color", "black");
	   
	   $.post("../../crud.php",	{
			view: "vote",
			action: "votepoll",
			id: "<?php echo urlencode($_SERVER['REMOTE_ADDR']); ?>",
			ans: $(objectOption).attr("id"),
			poll: <?php echo $pollId; ?>,
			type: "web"
		},
	   function(data){
	   	if(data.indexOf("Success") == -1){
				$('#submitResult').html(data).addClass("success").show(300);
				setTimeout(showInstructions,5000);
			}
		});
	return false;

}  

function acomodarBarras(){
	var lis = $("ul.chartlist li");
	  var maxValor = 0;
	  var maxOpt = "";
	  
	  for(i = 0; i < lis.length ; i++){
		  var spanCount = $("span.count",lis[i]);
		  var maxCount = parseInt(spanCount.html());

		 if(maxCount > maxValor){
			  maxValor = maxCount;
			  maxOpt = lis[i].id;
			}
		  
		}

	  if(maxOpt != "")
		  for(i = 0; i < lis.length ; i++){
				var spanBar = $("span.index",lis[i]);
				var id =  lis[i].id;
	
				if(id == maxOpt){
					//spanBar.css("width","90%");
					//spanBar.animate({width:"0%"},1000);
					spanBar.animate({width:"90%"},1000);
				}else{
					var spanCount = $("span.count",lis[i]);
					var count = parseInt(spanCount.html());
					var pct = count * 90 / maxValor;
					//spanBar.css("width",pct + "%");
					//spanBar.animate({width:"0%"},1000);
					spanBar.animate({width: pct+"%"},1000);					
				}
			}
}

$(document).ready(function() {
  acomodarBarras();

  var client;

  try{
  	client = new APE.Client();
  }catch(e){
  	alert(e);
  }

  if(client) {
  	client.load();

  	client.addEvent('load', function() {
      	
  		client.core.start();
  	});

  	client.addEvent('ready', function() {  	
  		client.core.join('<?php echo $pollId; ?>');//POLLID
  	});

  	client.addEvent('onRaw', function(args) {
  		if(args.raw == "VOTO"){
  			var htmlId = args.data.key
  			var liTarget = $("#" + htmlId);
  			var spanCount = $("span.count",liTarget);
  			var oldCount = parseInt(spanCount.html());  			
  			var newCount = oldCount + 1;  			
  			spanCount.html(newCount);
  			acomodarBarras();  			  		
  			
  			/*elementsProgress[0].style.width = newWidth + "%";
  			var htmlId = args.data.key
  			var element = document.getElementById(htmlId);
  			var elementsProgress = element.getElementsByTagName("div");
  			var strWidth = elementsProgress[0].style.width;
  			var i = strWidth.lastIndexOf("%");
  			var strOldWidth = parseInt(strWidth.substring(0,i));
  			var newWidth = strOldWidth + 5 ;
  			elementsProgress[0].style.width = newWidth + "%";*/
  		}
  	});
  }


  
});




  
  </script>
  
  
  
  </body>
  </html>
<?php 
mysql_free_result($result);
mysql_close($link);
?>
