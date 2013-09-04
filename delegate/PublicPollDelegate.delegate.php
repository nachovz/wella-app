<?php

//-------------------------------------
/**
 * @author Ricardo
 * @name   AccountDelegate
 */
//-------------------------------------

class PublicPollDelegate
{
	//--------------------------------------------------
	// @name AccountDelegate
	//--------------------------------------------------

	public function PublicPollDelegate()
	{

		return null;
	}

   public function getPublicPolls($validator)
   {
     $page = $validator->getOptionalVar("page");
     $limit = $validator->getOptionalVar("count");
	 if(empty($limit)) $limit = 20;
	 try
	 {
	 	if(!empty($page))
		{
		     $result = Doctrine_Manager::getInstance()
		                  ->getConnection($GLOBALS["connectionName"])
		                  ->getDbh()
		                  ->query("SELECT w.id AS id, w.question as question FROM poll w WHERE w.id>$id AND type='public' ORDER BY id LIMIT ".($page*$limit).",$limit")
		                  ->fetchAll();
		}
		else
		{
		     $result = Doctrine_Manager::getInstance()
		                  ->getConnection($GLOBALS["connectionName"])
		                  ->getDbh()
		                  ->query("SELECT w.id AS id, w.question as question FROM poll w WHERE type='public' ORDER BY id LIMIT 0,$limit")
		                  ->fetchAll();
			//print_r($result);
		}
	
	      echo json_encode($result);
	 }
	 catch(Exception $e)
	 {
	 	$validator->addError($e->getMessage());
	 }
   }

   public function searchPublicPolls($validator)
   {
     $page = $validator->getOptionalVar("page");
     $limit = $validator->getOptionalVar("count");
     $term = $validator->getVar("term");
	 
	 if(empty($limit)) $limit = 20;
	 try
	 {
	 	if(!empty($page))
		{
		     $result = Doctrine_Manager::getInstance()
		                  ->getConnection($GLOBALS["connectionName"])
		                  ->getDbh()
		                  ->query("SELECT w.id AS id, w.question as question FROM poll w WHERE type='public' AND w.question LIKE '%$term%' ORDER BY id LIMIT ".($page*$limit).",$limit")
		                  ->fetchAll();
		}
		else
		{
		     $result = Doctrine_Manager::getInstance()
		                  ->getConnection($GLOBALS["connectionName"])
		                  ->getDbh()
		                  ->query("SELECT w.id AS id, w.question as question FROM poll w WHERE type='public' AND w.question LIKE '%$term%' ORDER BY id LIMIT 0,$limit")
		                  ->fetchAll();
			//print_r($result);
		}
	
	      echo json_encode($result);
	 }
	 catch(Exception $e)
	 {
	 	$validator->addError($e->getMessage());
	 }
   }
   
   public function getPollDetails($validator)
   {
       $id = $validator->getVar("idpoll");
      if(empty($id))//Means that the user is creating a new poll
      {
        echo "{}";
      }
      else
      {
	       $q = Doctrine_Query::create()
	                  ->select('p.id,p.answer,p.createdate,p.keyword,p.id_poll')
	                  ->from('PollOption p')
	                  ->where('p.id_poll=?',$id);
	        $records = $q->execute()->toArray();

	       echo json_encode($records);
      }

   }//END getPollOptions
}

?>