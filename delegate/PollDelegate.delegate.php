<?php
//require_once('plugins/textmarkswrapper.class.php');

class PollDelegate
{

	//var $textmarks;

   public function PollDelegate()
	{
		//$this->textmarks = new TextmarksWrapper();

		return "";
	} //End PollDelegate

   public function getPoll($validator)
	{
		//$id = $validator->getVar("idPoll");
		//$id = 206;
		$id = 1471;
		if(empty($id))//Means that the user is creating a new poll
      		return null;

		$record = Doctrine::getTable("Poll")->find($id);
		return $record;

	} //End getPoll

   public function getMyPolls($validator)
   {
	    $user = $_SESSION["user"];
		$page = $validator->getOptionalVar("page");
		$total = $validator->getOptionalVar("total");
		if(!$page) $page = 0;
		if(!$total) $total = 2;
		
	    $q = Doctrine_Query::create()
             ->select('p.id,p.question,p.sesion_id,p.repeatanswer,p.createdate,p.status,p.type,p.id_user')
             ->from('Poll p')
             ->where('p.id_user=?',$user->id)
			 ->orderBy("p.createdate DESC");

		$pager = new Doctrine_Pager(
		      $q,
		      $page, // Current page of request
		      $total // (Optional) Number of results per page. Default is 25
		);
		$records = $pager->execute()->toArray();
		
		if($pager->getPage()>=$page)
	   		echo json_encode($records);
		else
			echo "[]";

   } // END getMyPolls

   public function getPollOptions($validator)
   {
   	  //$id = $validator->getVar("idPoll");
	  //$id = 206;
   		$id = 1471;
      	if(empty($id))//Means that the user is creating a new poll
      		return null;

 	 	$records = Doctrine::getTable("PollOption")->findBy("id_poll",$id);
   	 	return $records;
   }//END getPollOptions

   public function getPublicPolls($validator)
   {
	    $user = $_SESSION["user"];
		$page = $validator->getOptionalVar("page");
		$total = $validator->getOptionalVar("total");
		if(!$page) $page = 0;
		if(!$total) $total = 2;
		
	    $q = Doctrine_Query::create()
             ->select('p.id,p.question,p.sesion_id,p.repeatanswer,p.createdate,p.status,p.type,p.id_user')
             ->from('Poll p')
             ->where('p.type=?',"public")
			 ->orderBy("p.createdate DESC");

		$pager = new Doctrine_Pager(
		      $q,
		      $page, // Current page of request
		      $total // (Optional) Number of results per page. Default is 25
		);
		$records = $pager->execute()->toArray();
		
		if($pager->getPage()>=$page)
	   		echo json_encode($records);
		else
			echo "[]";

   } // END getMyPolls

   public function getTrendingPolls($validator)
   {
	    $q = mysql_query("select question,id_poll,count(id_poll) as cuenta from poll,poll_option WHERE poll.id=poll_option.id_poll AND poll.type='public' group by id_poll order by cuenta desc LIMIT 0,15");
		
		$polls = array();
		while($object = mysql_fetch_object($q))
			array_push($polls,$object);
			
		echo json_encode($polls);

   } // END getMyPolls

   public function vote($validator)
   {
   	  $ans = $validator->getVar("ans");
   	  $id = $validator->getVar("id");
   	  $type = $validator->getVar("type");

	 header("Location: http://ivoted.com:8080/ivote/httprequest.jsp?ans=$ans&id=$id&type=$type");

   }//END getPublicPolls

   public function getTag($validator)
   {
   	 $user = $_SESSION["user"];
   	 if($user->roleName=='invalid')
		return null;

	 $records = Doctrine::getTable("Tag")->findBy("id_user",$user->id);
     return $records;
   }// END getTag

   public function getTagAvailable($validator)
   {
   	  $id = $validator->getVar("idPoll");

     $result2 = Doctrine_Manager::getInstance()
                  ->getConnection($GLOBALS["connectionName"])
                  ->getDbh()
                  ->query("SELECT id AS id_available, name as name_available FROM tag WHERE id NOT IN (SELECT w.id AS id_use  FROM tag_poll p, tag w WHERE p.id_poll ='$id' AND p.id_tag = w.id)")
                 ->fetchAll();
      $records = $result2;
	return $records;

   }// END getTagAvailable

   public function removeTagsAvs($validator)
   {

     $idPoll = $validator->getVar("idPoll");
     $idTag = $validator->getVar("idTag");

	 $q= Doctrine_Query::create()->delete("tagpoll t")->where("t.id_poll=".$idPoll)->andWhere("t.id_tag=".$idTag);
     $q->execute();

     return  $GLOBALS["baseURL"]."view/".$GLOBALS["PRIVATE_VIEW"];
   } // END removeTag

   public function getWays($validator)
   {
   	  $q = Doctrine_Query::create()
   	 ->from("way w");
   	 $records = $q->execute();
     return $records;
   }// END getWays
   

   public  function  getKeyword($validator)
   {
         require_once('phputils/pwgen.class.php');
          $result = array();
          $result["keywords"] = array();
          $result["resultCode"] = 0;
           $num= $validator->getVar("num");
          for ($i=0; $i<$num; $i++)
          {
              $code = 1;
                while($code==1)
                {
		          //-- Reserve the Keyword in TextMarks --
                    $key_word = "P".$i.strtoupper($this->genetateKeyword());
		            $code = $this->textmarks->reserveKeyword($key_word);
                    if($code==0)
                    {
                      $result["resultCode"] = 200;
                      break;
                    }
                }
              array_push($result["keywords"],$key_word);
          }
          //return JSON_ENCODE($result);
         echo json_encode($result);

   }

   public function getWayAvailable($validator)
   {
     $id = $validator->getVar("idPoll");

     $result1 = Doctrine_Manager::getInstance()
                  ->getConnection($GLOBALS["connectionName"])
                  ->getDbh()
                  ->query("SELECT w.id AS id_use, w.name as name_use, w.stringid as string_use,TRUE FROM poll_way p, way w WHERE p.id_poll ='$id' AND p.id_way = w.id")
                  ->fetchAll();

     $result2 = Doctrine_Manager::getInstance()
                  ->getConnection($GLOBALS["connectionName"])
                  ->getDbh()
                  ->query("SELECT id AS id_available, name as name_available FROM way WHERE id NOT IN (SELECT w.id AS id_use  FROM poll_way p, way w WHERE p.id_poll ='$id' AND p.id_way = w.id)")
                 ->fetchAll();
      $records =  array_merge($result1,$result2);


    return $records;
   }

   public function searchMyPolls($validator)
	{
	    $user = $_SESSION["user"];
		$search = $validator->getOptionalVar("query");
		$page = $validator->getOptionalVar("page");
		$total = $validator->getOptionalVar("total");
		if(!$search)
		{
			die("[]");
		}
		if(!$page) $page = 0;
		if(!$total) $total = 2;

        $q = Doctrine_Query::create()
   	    ->select("p.*")
   	    ->from("poll p")
   	    ->where("p.question LIKE ?","%".$search."%")
		->andWhere("p.id_user=?",$user->id)
		->orderBy("p.createdate DESC");
		
		$pager = new Doctrine_Pager(
		      $q,
		      $page, // Current page of request
		      $total // (Optional) Number of results per page. Default is 25
		);
		$records = $pager->execute()->toArray();
		
		if($pager->getPage()>=$page)
	   		echo json_encode($records);
		else
			echo "[]";

	}//END

   public function searchPublicPolls($validator)
	{
	    $user = $_SESSION["user"];
		$search = $validator->getOptionalVar("query");
		$page = $validator->getOptionalVar("page");
		$total = $validator->getOptionalVar("total");
		if(!$search)
		{
			die("[]");
		}
		if(!$page) $page = 0;
		if(!$total) $total = 2;

        $q = Doctrine_Query::create()
   	    ->select("p.*")
   	    ->from("poll p")
   	    ->where("p.question LIKE ?","%".$search."%")
		->andWhere("p.type=?","public")
		->orderBy("p.createdate DESC");
		
		$pager = new Doctrine_Pager(
		      $q,
		      $page, // Current page of request
		      $total // (Optional) Number of results per page. Default is 25
		);
		$records = $pager->execute()->toArray();
		
		if($pager->getPage()>=$page)
	   		echo json_encode($records);
		else
			echo "[]";

	}//END

   public function countPublicPolls($validator)
	{
        $q = mysql_query("select count(*) as total from poll where type='public'");
		$object = mysql_fetch_object($q);
		
		return $object->total;
	}//END

   public function getNumAnswer($validator)
   {
   	  $q = Doctrine_Query::create()
           ->select('a.id')
           ->from('Answer a')
           ->where('a.id_poll=?',$validator->getVar("idPoll"));
      $record = $q->execute();
      $num_answer = $record->count();

    return $num_answer;

   }//END getNumAnswer

   public function pausePoll($validator)
	{
      $user = $_SESSION["user"] ;

      $id =  $validator->getVar("idPoll");
      $poll = Doctrine::getTable("Poll")->find($id);
      $poll->status ='pause';
      $poll->save();

      return $GLOBALS["baseURL"]."view/my-polls";
	}// End pausePoll

   public function pausePollInResults($validator)
	{


	  $pollId = $validator->getVar("idPoll");

      $poll = $record = Doctrine::getTable("Poll")->find($pollId);

      if(($_SESSION["user"]->roleName!='invalid')&&($poll->id_user==$_SESSION["user"]->id))
      {

      	  $user = $_SESSION["user"];
          $poll = Doctrine::getTable("Poll")->find($pollId);
          $poll->status ='pause';
          $poll->save();

	      return $GLOBALS["baseURL"]."poll/".$pollId;
      }
      else
          if (($_SESSION["user"]->roleName=='invalid')&&($poll->sesion_id == $_COOKIE['ivotedcookie']))
          {


	      	  $user = $_SESSION["user"];
	          $poll = Doctrine::getTable("Poll")->find($pollId);
	          $poll->status ='pause';
	          $poll->save();
		      $view = $validator->getVar("view");

		      return $GLOBALS["baseURL"]."poll/".$pollId;

          }


	}// End pausePollInResults

   public function playPollinResults($validator)
	{

	  $pollId = $validator->getVar("idPoll");

      $poll = $record = Doctrine::getTable("Poll")->find($pollId);

      if(($_SESSION["user"]->roleName!='invalid')&&($poll->id_user==$_SESSION["user"]->id))
      {

      	  $user = $_SESSION["user"];
          $poll = Doctrine::getTable("Poll")->find($pollId);
          $poll->status ='active';
          $poll->save();

	      return $GLOBALS["baseURL"]."poll/".$pollId;
      }
      else
          if (($_SESSION["user"]->roleName=='invalid')&&($poll->sesion_id == $_COOKIE['ivotedcookie']))
          {


	      	  $user = $_SESSION["user"];
	          $poll = Doctrine::getTable("Poll")->find($pollId);
	          $poll->status ='active';
	          $poll->save();
		      $view = $validator->getVar("view");

		      return $GLOBALS["baseURL"]."poll/".$pollId;

          }
	}// End playPollinResults

   public function playPoll($validator)
	{

	   $user = $_SESSION["user"] ;

      $id =  $validator->getVar("idPoll");

      $poll = Doctrine::getTable("Poll")->find($id);
      $poll->status ='active';
      $poll->save();

	   return $GLOBALS["baseURL"]."view/my-polls";
	}// End activePoll
	
	public function getHomePoll($validator){
		
		$query = "SELECT id,question FROM poll WHERE id_user = ".$GLOBALS["homeUserId"]." ORDER BY RAND() LIMIT 1";
		//exit($query);
		$result = mysql_query($query);
		$object = mysql_fetch_object($result);
		
		$poll["id"] = $object->id;
		$poll["question"] = $object->question;
		
		$query = "SELECT id,answer,keyword FROM poll_option WHERE id_poll=".$poll["id"];
		//echo $query;
		$result = mysql_query($query);

		$options = array();
		while($object = mysql_fetch_array($result))
		{
			array_push($options, $object);
		}

		$poll["options"] = $options;
		echo json_encode($poll);
		return 'void';
	}
	
	public function getErrorPoll($validator)
	{
		
		$query = "SELECT id,question FROM poll WHERE id_user=6 ORDER BY RAND() LIMIT 1";
		$result = mysql_query($query);
		$object = mysql_fetch_object($result);
		
		$poll["id"] = $object->id;
		$poll["question"] = $object->question;
		
		$query = "SELECT id,answer,keyword FROM poll_option WHERE id_poll=".$poll["id"];
		//echo $query;
		$result = mysql_query($query);

		$options = array();
		while($object = mysql_fetch_array($result))
		{
			array_push($options, $object);
		}

		$poll["options"] = $options;
		echo json_encode($poll);
		return 'void';
	}
	
   public function insertPoll($validator)
   {
   			//exit("this weay");
           $question =$validator->getVar("poll-question","Error writing the question");
           $repeat = false;

           //---- Save info from the new poll
           $poll = $this->insertPollData($validator,$question,$repeat);
           //$poll = the poll id
           if(!empty($poll)){
               //-- Save options info
               if(isset($_POST["options"]["new"])) 
               		$options = $_POST["options"]["new"];
			   else 
			   		$options = array();

               if(isset($_POST["key"]["new"])) 
               		$key = $_POST["key"]["new"];
			   else 
			   		$key = array();
			   
               $id_poll = $poll;
               
               $option = $this->insertPollOptionData($validator,$options,$key,$id_poll);
               
               if($option){//-- Save the way selected to the Poll
                         $ways = Doctrine_Query::create()
	                          ->select("w.id")
	                          ->from("Way w");
                         $records = $ways->execute();
			           foreach($records as $way)    {
			             $pollway = new PollWay();
			             $pollway->id_poll=$poll;
			             $pollway->id_way =$way->id;
			             $pollway->save();
				        }
               }else{
	        	   $validator->addError("There was an error creating the poll.");
	            }
           }else
				$validator->addError('Can�t save the question info');

    if ($validator->getTotalErrors()==0)
    {
	    return $GLOBALS["baseURL"]."poll/".$poll."/".$this->post_slug($question);
    }
    else
   {
      return "Error in the process of create a poll";
   }

   } //-- END INSERTPOLL
   
   public function changeWay($validator)
   {
   		$way = $validator->getVar('way');
   		$pollid = $validator->getVar('pollid');
   		$remove = $validator->getVar('remove');
   			
   		if($way == "5"){
   			$strRepeat = $remove == "true" ? "1" : "0";
   			$this->setRepeatAnswerState($pollid, $strRepeat);   
   			//TODO			
   			/*This doesn't work
   			$poll = Doctrine::getTable("poll")->find($pollid);  	
   			echo $poll->question ."  -  " . $poll->repeatanswer;   		
   			$strRemove = $remove == true ? "1" : "0";
   			$poll->repeatanswer = $strRemove;
   			echo $poll->question ."  -  " . $poll->repeatanswer;
   			var_dump($poll->save());
   			echo $remove;*/
   			echo $remove;
   			return 'void';
   		}   		
				
		$q = Doctrine_Query::create()->from("Poll p")->where("p.id=?",$pollid)->andWhere("p.id_user=?",$_SESSION["user"]->id);
    	$polls = $q->execute()->toArray();

        if(count($polls)==1){
			$q = Doctrine_Query::create()->delete("PollWay p")->where("p.id_way=?",$way)->andWhere("p.id_poll=?",$pollid);
	    	$q->execute();	
	
			if($remove=='true')	{
		         $pollway = new PollWay();
		         $pollway->id_poll=$pollid;
		         $pollway->id_way =$way;
		         $pollway->save();
			}
			
			echo $remove;
		}
		
		return 'void';
   }
   
   private function setRepeatAnswerState($pollid, $strRepeat){
		$query = "
			UPDATE
				poll
			SET
				repeatanswer = '".$strRepeat."' 
			where 
				id = '".$pollid."';
		";
		//echo $query;
		//TODO - temp solution
		$link =  mysql_connect('localhost', 'root', 'mysqla18064066');
		if (!$link) {
		    die("Can't connect: " . mysql_error());
		}
		
		$db_selected = mysql_select_db('ivoted', $link);
		if (!$db_selected) {
		    die ('Can\'t use DB : ' . mysql_error());
		}
		
		$result = mysql_query($query);
		
		if (!$result) {
		    $message  = 'Invalid query: ' . mysql_error() . "\n";
		    $message .= 'Whole query: ' . $query;
		    die($message);
		}else{
			return;
		}   	
   }
   
   private function insertPollData($validator,$question,$repeat)
   {
          $entity = new poll();
          $entity->question=$question;
          $entity->createdate = date("Y-m-d H:i:s");

          if($_SESSION['user']->roleName!='invalid')
          		$entity->id_user = $_SESSION['user']->id;
	      else{
	          if(empty($_COOKIE["ivotedcookie"])){
	              $code = $_SERVER["REMOTE_ADDR"].session_id();
	              setcookie("ivotedcookie", $code, time() + (60 * 60 * 24 * 365));
	              $entity->sesion_id = $code;
	            }else
	                  $entity->sesion_id = $_COOKIE["ivotedcookie"];
	        }
	      if ($repeat)
	         $entity->repeatanswer = $repeat;

       	$entity->save();

       return $entity->id;
   } //END insertPollData

   private function insertPollOptionData($validator,$options,$key, $id_poll)
   {
      require_once('phputils/pwgen.class.php');

     $i=0;
     foreach($options as $option) {
           if(!empty($option)){
	       	   //-object for the answers from a poll
	       	   $answer = $option;
	       	   $entity2 = new PollOption();
	           $entity2->answer = $option;
	           $entity2->createdate = date("Y-m-d H:i:s");
	           $entity2->id_poll =$id_poll;

             //--Save  the keyword--//
	         //Check that the keyword is not empty
               if(!empty($key[$i])){
                    //.-- Check is the user is a FREE --//
                    if($_SESSION["user"]->accountPlanId()==0){
                          $entity2->keyword = $key[$i];
                          $entity2->save();
                          $i++;
                    }
                    else{
		                   //-- Reserve the Keyword in TextMarks --
                           $key_word = strtoupper($key[$i]);
		                   $code = 0;
			                   $code = $this->textmarks->reserveKeyword($key_word);

		                   if($code!=0)
		                   	 $validator->addError('Error creating keyword.');
		                    else{
	                          $i++;
	                          $entity2->keyword = $key_word;
	                          $entity2->save();
	                        }
                    }
               }else{
                  //Creating a keyword
                    if(empty($key[$i])){
	                     $key_word = $this->genetateKeyword($i);
		                 $code = 0;

                         //-- Reserve the Keyword in TextMarks --
		                 $code = $this->textmarks->reserveKeyword($key_word);
//echo "2". $code;exit();
		                 if($code!=0)
		                 {
		                   $validator->addError('Error creating keyword.');
		                 }
		                 else
	                     {
	                     	$i++;
	                     	$entity2->keyword =  $key_word;
	                     	$entity2->save();
	                     } // END ELSE
                    } // END IF
                }// END else
           } // END IF $OPTION
            else
           {
           	 $validator->addError("You can't leave the option field empty, please write an option or delete last option");
             break;
           }// END if
     }//END FOREACH

	   if($validator->getTotalErrors()==0)
	     	return TRUE;
	   else
	   	 return FALSE;

   }

   public function addTags ($validator)
   {
      $tagsString = $validator->getVar("tagsString");
      $poll = $validator->getVar("pollid");

	  $tags = explode(",",$tagsString);
	  $cont = 0;
	  if(count($tags)>0)
	  foreach($tags as $tagName)
	  {
	  	$tagName = trim($tagName);
	  	$records = Doctrine_Query::create()->from("Tag t")->where("t.name=?",$tagName)
	  	->andWhere("t.id_user=?",$_SESSION["user"]->id)->execute();
	  	$total = $records->count();
	  	if($total==0)
	  	{
		  	$entity = new Tag();
		  	$entity->name = $tagName;
		  	$entity->createdate = date("Y-m-d H:i:s");
		  	$entity->id_user = $_SESSION["user"]->id;
	  		$entity->save();

		  	$tagPoll = new TagPoll();
		  	$tagPoll->id_tag = $entity->id;
		  	$tagPoll->id_poll = $poll;
		  	$tagPoll->createdate = date("Y-m-d H:i:s");
	  		$tagPoll->save();
	  	}
		else if($total==1)
		{
			$id = $records[0]->id;
		  	$records = Doctrine_Query::create()->from("TagPoll t")->where("t.id_poll=?",$poll)
		  	->andWhere("t.id_tag=?",$id)->execute();
		  	if($records->count()==0)
		  	{
			  	$tagPoll = new TagPoll();
			  	$tagPoll->id_tag = $id;
			  	$tagPoll->id_poll = $poll;
			  	$tagPoll->createdate = date("Y-m-d H:i:s");
		  		$tagPoll->save();
		  	}
		  	else $validator->addError("Tag already used in this poll.");

		}

		$cont++;
	  }

	  return $GLOBALS["baseURL"]."view/".$GLOBALS["PRIVATE_VIEW"];

   }

   public function updatePoll($validator)
   {

	    require_once('phputils/pwgen.class.php');

		$user = $_SESSION["user"];
		$newQuestion = $validator->getVar("poll-question","Question.");

		//Fin the Question instance
		$id_poll = $validator->getVar("id-poll");
		$poll = Doctrine::getTable("poll")->find($id_poll);

        if((($_SESSION["user"]->roleName!='invalid')&&($poll->id_user==$_SESSION["user"]->id)) || (($_SESSION["user"]->roleName=='invalid')&&($poll->sesion_id == $_COOKIE['ivotedcookie'])))
        {
	             		//Update the name of the question
	        if($poll->question!=$newQuestion){
		        $poll->question=$newQuestion;
	        	$poll->save();
			}
			
			//Elimino las opciones que le hicieron click en "eliminar"
			if(isset($_REQUEST["options"]["deleted"]))
			foreach($_REQUEST["options"]["deleted"] as $option)
			{
	             $record = Doctrine::getTable("PollOption")->find($option);
				 if($record) $record->delete();
			}
			$totalOptions = 0;
			//Modifico las old options que han sido actualizadas
	        $i = 0;
	        if(isset($_REQUEST["options"]["old"]))
			foreach($_REQUEST["options"]["old"] as $option)
			{
				if($option!='')
				{
					 $id = $_REQUEST["idoptions"][$i];
					 
					 if(isset($_REQUEST["key"]["old"])) $key = $_REQUEST["key"]["old"][$i];
					 else $key = "";
					 
		             $record = Doctrine::getTable("PollOption")->find($id);
		             if($record->answer!=$option)
		             {
		                $record->answer=$option;
		             	$record->save();
		             }
		
		             if($key!='' && $key!=$record->keyword)
		             {
		    			if($this->textmarks->checkKeyword($keyword)!='available')
		    			{
		    				$validator->addError('Keyword "'.$keyword.'" not avaibable.');
		    				break;
		    			}
						else
						{
							$this->textmarks->deleteKeyword($record->keyword);
							$this->textmarks->reserveKeyword($key);
						}
		
						$record->keyword = $key;
			            $record->save();
		             }
				}
				 
				 $i++;
			}
			
			$totalOptions += $i;
			//ahora agrego las new options que han sido agregadas
			$i = 0;
			if(isset($_REQUEST["options"]["new"]))
			foreach($_REQUEST["options"]["new"] as $option)
			{
				if($option!='')
				{
					if(isset($_REQUEST["key"]))
						$key = $_REQUEST["key"]["new"][$i];
					else
						$key = "";
					
		         	$record = new PollOption();
		            $record->answer = $option;
		            $record->createdate = date("Y-m-d H:i:s");
		            $record->id_poll =$id_poll;
		
		             if(!empty($key))//Si el usuario elegio su propio keyword
		                  $record->keyword = strtoupper($key);
			 	     else if(empty($key))//Debo auto generar el keyword
		                $record->keyword="P".($totalOptions+$i).strtoupper($this->genetateKeyword());
		
					 $code = 0;
		             $code = $this->textmarks->reserveKeyword($record->keyword);
		             if($code!=0)
		             {
		             	$validator->addError('Error creating keyword.');
		             }
		
					//Si hey errores interrumpo el procesamiento
					if($validator->getTotalErrors()>0)
		         		break;
		
		         	$record->save();
		            $i++;
				}
	       }
	
			$totalOptions += $i;
			
	     	if($totalOptions<2) $validator->addError('You have to set at least 2 options.');
			
			if(($_SESSION["user"]->roleName!='invalid')&&($poll->id_user==$_SESSION["user"]->id))
	        	return $GLOBALS["baseURL"]."view/".$GLOBALS["PRIVATE_VIEW"];
			else 
		        return $GLOBALS["baseURL"]."poll/$id_poll";
			
        }
		else
		{
			$validator->addError("You cannot update this poll.");
		}

   } //END updatePoll
   
   public function checkKeyword($validator)
   {
   		$keyword = $validator->getVar('keyword');
		require_once("plugins/textmarkswrapper.class.php");
		
		$textmark = new TextmarksWrapper();
		$answer = $textmark->checkKeyword($_REQUEST["keyword"]);
		if($answer=='available')
			echo $answer;
		else
			echo $answer;

   }

   public function makePublic($validator)
   {
   	  $user = $_SESSION["user"] ;

	  $q = Doctrine_Query::create()
	  ->update("Poll")
	  ->set('type', '?','public')
	  ->where("id = ".$validator->getVar("idPoll"))
	  ->andWhere("id_user = ".$user->id);
      $q->execute();

	   return $GLOBALS["baseURL"]."view/".$GLOBALS["PRIVATE_VIEW"];

   }//END updateType

   public function makePrivate($validator)
   {
   	  $user = $_SESSION["user"] ;

	  $q = Doctrine_Query::create()
	  ->update("Poll")
	  ->set('type', '?','private')
	  ->where("id = ".$validator->getVar("idPoll"))
	  ->andWhere("id_user = ".$user->id);
      $q->execute();

	   return $GLOBALS["baseURL"]."view/".$GLOBALS["PRIVATE_VIEW"];

   }//END updateType

   public function deletePoll($validator)
   {
		$id = $validator->getVar("idPoll");

        $poll = $record = Doctrine::getTable("Poll")->find($id);

      if(($_SESSION["user"]->roleName!='invalid')&&($poll->id_user==$_SESSION["user"]->id))
       {

	   	 $q = Doctrine_Query::create()
	   	  ->select("p.*")
	   	  ->from("PollOption p ")
	   	  ->where("p.id_poll =".$id);
	   	 $records = $q->execute();
	   	 foreach($records as $option)
	   	 {
	   	 	$this->textmarks->deleteKeyword($option->keyword);

	   	 }

        $q= Doctrine_Query::create()->delete("PollOption p")->where("p.id_poll = ".$id);
        $q->execute();

        $q= Doctrine_Query::create()->delete("TagPoll t")->where("t.id_poll = ".$id);
        $q->execute();

        $q= Doctrine_Query::create()->delete("Answer a")->where("a.id_poll = ".$id);
        $q->execute();

        $q= Doctrine_Query::create()->delete("PollWay p")->where("p.id_poll = ".$id);
        $q->execute();

		$q = Doctrine_Query::create()->delete("Poll p")->where("p.id = ".$id);
		$q->execute();

       	return $GLOBALS["baseURL"]."view/".$GLOBALS["PRIVATE_VIEW"];
       }
      else
           if (($_SESSION["user"]->roleName=='invalid')&&($poll->sesion_id == $_COOKIE['ivotedcookie']))
           {


			   	 $q = Doctrine_Query::create()
			   	  ->select("p.*")
			   	  ->from("PollOption p ")
			   	  ->where("p.id_poll =".$id);
			   	 $records = $q->execute();
			   	 foreach($records as $option)
			   	 {
			   	 	$this->textmarks->deleteKeyword($option->keyword);

			   	 }

		        $q= Doctrine_Query::create()->delete("PollOption p")->where("p.id_poll = ".$id);
		        $q->execute();

		        $q= Doctrine_Query::create()->delete("TagPoll t")->where("t.id_poll = ".$id);
		        $q->execute();

		        $q= Doctrine_Query::create()->delete("Answer a")->where("a.id_poll = ".$id);
		        $q->execute();

		        $q= Doctrine_Query::create()->delete("PollWay p")->where("p.id_poll = ".$id);
		        $q->execute();

				$q = Doctrine_Query::create()->delete("Poll p")->where("p.id = ".$id);
				$q->execute();

                return $GLOBALS["baseURL"]."view/".$GLOBALS["DEFAULT_VIEW"];
           }


   } // END deletePoll

   public function removeTag($validator)
   {

     $idPoll = $validator->getVar("idPoll");
     $idTag = $validator->getVar("idTag");

	 $q= Doctrine_Query::create()->delete("tagpoll t")->where("t.id_poll=".$idPoll)->andWhere("t.id_tag=".$idTag);
     $q->execute();

     return  $GLOBALS["baseURL"]."view/".$GLOBALS["PRIVATE_VIEW"];
   } // END removeTag

   public function deleteOption($validator)
   {
     	$id = $validator->getVar("idOption");
     	$idPoll = $validator->getVar("idPoll");

     	$records = Doctrine::getTable("PollOption")->findBy("id_poll",$idPoll);
     	if(count($records)>2)
		{
		 	$poll = Doctrine::getTable("PollOption")->find($id);
		 	if($poll)
		 		$this->textmarks->deleteKeyword($poll->keyword);

			$q = Doctrine_Query::create()->delete("PollOption p")->where("p.id = ".$id);
			$q->execute();

	   	}
		else
		{
			$validator->addError("You must have a minimum of 2 options per question.");
		}


		return $GLOBALS["baseURL"].'view/edit-poll/'.$idPoll;

   }//END deleteOption

   public function deleteAnswers($validator)
   {

   	    $id = $validator->getVar("idPoll");

        $poll = $record = Doctrine::getTable("Poll")->find($id);

        if(($_SESSION["user"]->roleName!='invalid')&&($poll->id_user==$_SESSION["user"]->id))
        {
         	$q = Doctrine_Query::create()->delete("answer a")->where("a.id_poll = ".$id);
		    $q->execute();
            return $GLOBALS["baseURL"]."view/".$GLOBALS["PRIVATE_VIEW"];
        }
        else
        if (($_SESSION["user"]->roleName=='invalid')&&($poll->sesion_id == $_COOKIE['ivotedcookie']))
        {
        	$q = Doctrine_Query::create()->delete("answer a")->where("a.id_poll = ".$id);
		    $q->execute();
			return $GLOBALS["baseURL"]."poll/".$id;
        }


   } //END deleteAnswers

   public function deleteTag($validator)
   {
     $id = $validator->getVar("idTag");

		$q = Doctrine_Query::create()->delete("tag t")->where("t.id = ".$id);
		$q->execute();

		return $GLOBALS["baseURL"]."view/admin-tag";

   } //END deleteTag

   public function copyPublicPoll($validator)
   {
        $id = $validator->getVar("idPoll");

       /*object that have the data of the public pol*/
        $q = Doctrine_Query::create()
        ->select('p.*')
        ->from('poll p')
        ->where('p.id='.$id);
        $polls = $q->execute();

        foreach ($polls as $poll)
        {

		  /*object that will save the date of the new private poll */
		    $entity = new poll();
		    $entity->question= $poll->question;
		    $entity->createdate = date("Y-m-d H:i:s");
		    $entity->id_user = 1;
			$entity->save();
        }
     /*object that have the answers of the public poll */
          $q = Doctrine_Query::create()
	        ->select('p.*')
	        ->from('pollOption p')
	        ->where('p.id_poll='.$id);
	      $answers = $q->execute();

      /*object that have the answers of the public poll that we select */

      foreach ($answers as $answer)
      {
      	$entity2 = new PollOption();
	    $entity2->answer = $answer->answer;
	    $entity2->createdate = date("Y-m-d H:i:s");
	    $entity2->id_poll = $entity->id;
		$entity2->keyword = $answer->keyword;
		$entity2->save();
      }

     return $GLOBALS["baseURL"]."view/".$GLOBALS["PRIVATE_VIEW"] ;

   }// END copyPublicPoll

   private function genetateKeyword($num)
   {
   		while(true)
   		{
   			$pwgen = new PWGen(4,false,false,false,false);
		    $keyword = $pwgen->generate();
		    $keyword = "P".$num.strtoupper($keyword);
 
 /*for($i = 0; $i < 30 ; $i++){
	
echo $keyword. ":";
echo $this->textmarks->checkKeyword($keyword);
echo "</br>";
var_dump($this->textmarks->checkKeyword($keyword) == "available");
$keyword = $pwgen->generate();
$keyword = "P".$num.strtoupper($keyword);
}

	exit();*/

		    $q = Doctrine_Query::create()->delete("PollOption t")->where("t.keyword = '".$keyword."'");
			$c = $q->count();
		
        	if($c ==0 && $this->textmarks->checkKeyword($keyword) == "available")
				return $keyword;
   		}

   		return "";
   }
   
   private function checkKeywordsAvaliability($validator)
   {
	    $i = 1;
	    $continuar = true;
	    //Verify if all the keywords are availabel
	    while($continuar)
	    {
	    	if(empty($_POST['option'.$i]))
	    	{
	    		$continuar = false;
	    		break;
	    	}
			else
			{
		    	if(!empty($_POST['key'.$i]) && strlen($_POST['key'.$i])>3)
		    	{
		    		if($this->textmarks->checkKeyword($_POST['key'.$i])!='available')
			    		$validator->addError('Keyword "'.$_POST['key'.$i].'" not avaibable.');
		    	}
		  		else if(strlen($_POST['key'.$i])!=0)
		  		{
		    		$validator->addError('Keyword "'.$_POST['key'.$i].'" must have at least 4 characters.');
		  		}
				$i++;
			}

	    }

	    return $validator;
   }

   private function removeKeywords($validator)
   {
	    $i = 1;
	    $continuar = true;
	    //Verify if all the keywords are availabel
	    while($continuar)
	    {
	    	if(empty($_POST['option'.$i]))
	    	{
	    		$continuar = false;
	    		break;
	    	}
			else
			{
		    	if(!empty($_POST['key'.$i]))
		    		$this->textmarks->deleteKeyword($_POST['key'.$i]);

				$i++;
			}

	    }

	    return $validator;
   }

   public function getAccount($validator)
	{

		$idUsuario = $_SESSION["user"]->id;
		if($_SESSION["cheddar-getter-user"]!=null) $plan = $_SESSION["cheddar-getter-user"]->getCustomerPlan($idUsuario);

	    if (isset($plan) && $plan != null)
	    {
	    	$planCode = $plan["code"];
	    	$accountPlan = Doctrine::getTable("Account")->find($planCode);
			if($accountPlan==null) $validator->errors->addError(ErrorManager::CANIS_FATAL,"The free account $planCode is created in recurly but no in ivoted mysql.");

			return $accountPlan;
	    }
	    else
	    {
	    	if ($_SESSION["user"]->roleName == 'subadmin')
	    	{
	    	   	$admin = Doctrine::getTable("UserInvitation")->findBy('subadmin_id',$_SESSION["user"]->id);
	            $useraccount = Doctrine::getTable("UsersAccount")->findBy('id_user',$admin[0]->admin_id);
	    	   	$accountPlan = Doctrine::getTable("Account")->findBy('id',$useraccount[0]->id_account);

	    	   	return 	$accountPlan[0] ;

	    	}
	    	else
	    	{
		    	$accountPlan = Doctrine::getTable("Account")->find("FREE");
				if($accountPlan==null) $validator->errors->addError(ErrorManager::CANIS_FATAL,"The free account needs to be created in mysql.");
		        return $accountPlan;
	    	}
	    }

       /*
			// Make request (synchronous):
			$sResponse = curl_exec($ch);

			// Check for transport-level errors:
			$iCurlErrNo     = curl_errno($ch);
			$iHttpRespCode  = curl_getinfo($ch, CURLINFO_HTTP_CODE);
			if (($iCurlErrNo != 0) && ($iHttpRespCode != 200)) {
				curl_close($ch);
			}
			$validator->addError('Your vote status'.$sResponse.'see this poll results click <a href="">here</a>');
			//return $sResponse;
       */
	}

   public function votePoll($validator)
    {
        $id = $validator->getVar("id");
        $option = $validator->getVar("ans");
        $poll = $validator->getVar("poll");
		
		$entity2 = new Answer();
		$entity2->type = "web";
		$entity2->id_poll = $poll;
		$entity2->id_option = $option;
		$entity2->identifier = $id;
		$entity2->datetime  = $today = date("Y-m-d H:m:s");  
		
		$entity2->save();
		setcookie("iVotedPoll".$poll,1,time()+86400);
		
        header("Location: ".$GLOBALS["baseURL"]."mobile/poll/".$poll);

         return "void";

    }
	
	public function voteMobilePoll($validator)
    {
        $id = $validator->getVar("id");
         $option = $validator->getVar("ans");
         $poll = $validator->getVar("poll");

         $url = "http://www.ivoted.com:8080/ivote/httprequest.jsp";

        $postData = array("id"=> $id, "ans" => $option,"type" => "web");
        $sPostData = "";

        foreach ($postData as $sK => $sV) {
            $sPostData .= "&" . urlencode($sK) . "=" . urlencode($sV);
        }

        // Prep curl:
        $ch = curl_init();
        $arsHeaders = array();

        curl_setopt($ch, CURLOPT_HTTPGET, 1);
        curl_setopt($ch, CURLOPT_URL, $url . "?" . $sPostData);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $arsHeaders);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); // (response as string, not output)

        // Make request (synchronous):
        $sResponse = curl_exec($ch);

        // Check for transport-level errors:
        $iCurlErrNo     = curl_errno($ch);
        $iHttpRespCode  = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        if (($iCurlErrNo != 0) && ($iHttpRespCode != 200)) {
            curl_close($ch);
        }
        //$validator->addError($sResponse.'see this poll results click <a target="_blank" href='.$GLOBALS["baseURL"].'poll/'.$poll.'>here</a>');
        //return $sResponse;
        setcookie("iVotedPoll".$poll,1,time()+86400);
        //$validator->addError($sResponse.' Se ha ejecutado la selecci&oacute;n. <a target="_blank" href='.$GLOBALS["baseURL"].'mobile/>Volver a la lista de encuestas</a>.');
        return $GLOBALS["baseURL"]."mobile/poll/".$poll;

    }

   public function getAccountPollsInvitation($validator)
   {


      	if($_SESSION["user"]->roleName=='subadmin')
      	{
      		$admin = Doctrine::getTable("UserInvitation")->findBy("subadmin_id",$_SESSION["user"]->id);

           //  echo "SELECT b.poll_id, b.question, b.user_id, email, COUNT(a.id_poll) AS answers FROM (SELECT p.id AS poll_id, p.question, u.id AS user_id, u.email FROM poll p, user u, user_invitation i WHERE (i.admin_id =".$admin[0]->admin_id.") AND (i.subadmin_id = u.id) AND (p.id_user = u.id)) b LEFT OUTER JOIN answer a ON b.poll_id = a.id_poll GROUP BY b.poll_id ";

                  $records = Doctrine_Manager::getInstance()
	                  ->getConnection($GLOBALS["dbName"])
	                  ->getDbh()
	                  ->query("SELECT b.poll_id, b.question, b.user_id, email, COUNT(a.id_poll) AS answers FROM (SELECT p.id AS poll_id, p.question, u.id AS user_id, u.email FROM poll p, user u, user_invitation i WHERE (i.admin_id =".$admin[0]->admin_id.") AND (i.subadmin_id = u.id) AND (p.id_user = u.id)) b LEFT OUTER JOIN answer a ON b.poll_id = a.id_poll GROUP BY b.poll_id ")

                  ->fetchAll();



			return $records;
      	}
      	else
      	   if($_SESSION["user"]->roleName=='admin')
      	   {

                  $records = Doctrine_Manager::getInstance()
	                  ->getConnection($GLOBALS["connectionName"])
	                  ->getDbh()
	                  ->query("SELECT b.poll_id, b.question, b.user_id, email, COUNT(a.id_poll) AS answers FROM (SELECT p.id AS poll_id, p.question, u.id AS user_id, u.email FROM poll p, user u, user_invitation i WHERE i.admin_id =".$_SESSION["user"]->id." AND i.subadmin_id = u.id AND p.id_user = u.id) b LEFT OUTER JOIN answer a ON b.poll_id = a.id_poll GROUP BY b.poll_id ")
                  ->fetchAll();
				return $records;

      	        	   }


   }

   public function getAccountPolls($validator)
    {
    	if ($_SESSION["user"]->roleName=='admin')
    	{
           $polls = Doctrine::getTable("Poll")->findBy('id_user',$_SESSION["user"]->id);
           return $polls;
    	}
    	else
    	if ($_SESSION["user"]->roleName=='subadmin')
    	{
           $admin = Doctrine::getTable("UserInvitation")->findBy('subadmin_id',$_SESSION["user"]->id);
           $polls = Doctrine::getTable("Poll")->findBy('id_user',$admin[0]->admin_id);
           return $polls;
    	}

    }

   public function getIsOwner($validator)
    {

       if (!empty($_COOKIE["ivotedcookie"]))
       {
          return $_COOKIE["ivotedcookie"];
       }
       else
          return "";
    }
	
	
	private function post_slug($str)
	{
		return preg_replace( '/[<>"!?,.]+/', '', strtolower(preg_replace(array('/[^a-zA-Z0-9 -]/', '/[ -]+/', '/^-|-$/'), array('', '-', ''), $this->remove_accent($str))));
	}
	
	private function remove_accent($str)
	{
		$a = array('Á','É','Í','Ó','Í','á','é','í','ó','ú','ñ','Ñ');
		$b = array('A','E','I','O','U','a','e','i','o','u','n','N');
		return str_replace($a, $b, $str);
	}

   public function isRepeatAnswer($validator)  {
      $id = $validator->getVar("idPoll");
      $record = Doctrine::getTable("Poll")->find($id);      
	  return $record->repeatanswer;
   }// END getWays	
}
?>
