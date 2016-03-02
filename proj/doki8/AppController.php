<?php
namespace aisle\proj\doki8;

use aisle\web\Controller;
use aisle\proj\doki8\grab\Article;

class AppController extends Controller{
		
	public function GrabUser($name,$reset=false){
		
		$grab = new GrabController($this->managers,$this->request);
		
		if($reset){
			
			$this->write(sprintf('data of user "%s" has begun to remove!',$name));
			$this->DeleteUser($name);
		}
		
		$this->write(sprintf('data of user "%s" has begun to grab!',$name));
		$grab->User($name);
		$this->write(sprintf('data of user "%s" has been grabed to complete!',$name));
	}
	
	public function GrabAllUser($users=null,$reset=false){
		
		$grab = new GrabController($this->managers,$this->request);
		
		$users = $users ? $users : array_merge(array_map(function($item){ return $item['name']; },$grab->Leaderboard()),array('alloozhang','cecaalan','davidtah','07zq','dksnear'));
		
		foreach($users as $user)
			$this->GrabUser($user,$reset);
			
	}
	
	public function GrabAllArticles(){
		
		$grab = new GrabController($this->managers,$this->request);
		
		$this->write(sprintf('articles data has begun to grab!'));
		$grab->AllArticles();
		$this->write(sprintf('articles data has been grabed to complete!'));
	}
	
	public function GrabHistory(){	

		$grab = new GrabController($this->managers,$this->request);
		// $grab->PointHistory('alloozhang');
		//$grab->PointHistory('kibduy',1);
	}

	public function GrabUserList(){
		
		$grab = new GrabController($this->managers,$this->request);
		$grab->UserList(1,489);				
	}
	
	public function GrabArticle(){
		
		$grab = new GrabController($this->managers,$this->request);
		$grab->Article(array('19861','19934','20083','20161','20313','20479','20808','20937','21144','21406'));
		
	}
	
	public function GrabProfile(){
		
		$grab = new GrabController($this->managers,$this->request);
		$grab->Profile('dksnear');
	}

	public function ClearCache(){
		
		Article::ClearCache();
	}
	
	public function DeleteUser($name){
		
		$this->view('json',
			$this->dbm
				->DirectTrans("delete from `profile` where `name`='$name'")
				->DirectTrans("delete from `article` where `author`='$name'")
				->DirectTrans("delete from `point_history` where user_name='$name'")
				->Run());	
	}
	
	protected function write($statements){
		
		$this->logm->Client('grab')->Write($statements);
	}
	
}