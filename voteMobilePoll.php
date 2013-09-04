<?php 
        $id = $_GET["id"];
        $option = $_GET["ans"];
        $poll = $_GET["poll"];

        /*$url = "http://www.ivoted.com:8080/ivote/httprequest.jsp";

        $postData = array("id"=> $id, "ans" => $option,"type" => "web");
        $sPostData = "";*/

        $entity2 = new Answer();
		$entity2->type = "web";
		$entity2->id_poll = $poll;
		$entity2->id_option = $option;
		$entity2->identifier = $id;
		
		$entity2->save();
        header("Location: ".$GLOBALS["baseURL"]."mobile/poll/".$poll);
        
?>
