<?php
namespace aisle\proj\doki8;

use aisle\web\Controller;
use aisle\proj\doki8\grab\PointHistory;
use aisle\proj\doki8\grab\UserList;
use aisle\proj\doki8\grab\AuthorList;
use aisle\proj\doki8\grab\ArticleList;
use aisle\proj\doki8\grab\Article;
use aisle\proj\doki8\grab\Profile;
use aisle\proj\doki8\grab\Home;

class GrabController extends Controller{
	
	public function PointHistory($userName,$pages = -1,$igoreVerify=false){
		
		$args = PointHistory::GetGrabArgs($userName);
		$pageCount = $args[0];
		$lastPageRecordCount = $args[1];

		($pages!=0) && $this->parallelGrab(array_map(function($idx) use($userName,$pageCount,$lastPageRecordCount){
			
			return new PointHistory($userName,$idx,$pageCount,$lastPageRecordCount);
			
		},range(1,$pages===-1 ? $pageCount : $pages)));
		
		return PointHistory::Write($this->dbm,$userName,$pageCount,$lastPageRecordCount,$igoreVerify);
	}
	
	public function UserList($start,$end){
		
		$this->parallelGrab(array_map(function($idx){
			
			return new UserList($idx);
			
		},range($start,$end)));
		
		return UserList::Write($this->dbm);
	}
	
	public function Profile($userNames){
		
		$userNames = is_array($userNames) ? $userNames : array($userNames);
		
		if(empty($userNames)) return false;
		
		$this->parallelGrab(array_map(function($userName){
			
			return new Profile($userName);
			
		},$userNames));
		
		return Profile::Write($this->dbm);
	}
	
	public function AuthorList($userName){
		
		$pageCount = (new AuthorList($userName))->GetPageCount();
		
		$this->parallelGrab(array_map(function($idx) use($userName){
			
			return new AuthorList($userName,$idx);
			
		},range(1,$pageCount)));
		
		return AuthorList::Write($this->dbm,$userName);
		
	}
	
	public function ArticleList($category,$pages=-1){
		
		$pageCount = (new ArticleList($category))->GetPageCount();
		
		($pages!=0) && $this->parallelGrab(array_map(function($idx) use($category){
			
			return new ArticleList($category,$idx);
			
		},range(1,$pages===-1 ? $pageCount : $pages)));
		
		return ArticleList::Write($this->dbm,$category);
		
	}
	
	public function Article($articleIds){
		
		$articleIds = is_array($articleIds) ? $articleIds : array($articleIds);
		
		if(empty($articleIds)) return false;
		
		$this->parallelGrab(array_map(function($id){
			
			return new Article($id);
			
		},$articleIds));
		
		return Article::Write($this->dbm);
	}
	
	public function AllArticles(){
		
		return $this->ArticleList('master') && $this->ArticleList('contributor');	
	}
	
	public function User($userName){
		
		$this->Profile($userName);
		$this->AuthorList($userName);
		$this->Article(array_map(function($item){ return $item['id']; },AuthorList::Get($this->dbm,$userName)));
		
		$user = Profile::Get($this->dbm,$userName);
		$user = $user[0];
				
		if(!$user['status'])
			$this->PointHistory($userName,-1,true);
		
		if($user['status'] == 1){
			
			$args = PointHistory::GetGrabArgs($userName);
			$pageCount = $args[0];
			$this->PointHistory($userName,$user['last_grab_page_count']-$pageCount+1);
		}
		
		return true;
		
	}
	
	public function Leaderboard($clear=true){
		
		$home = new Home();
		return $home->FetchLeaderboard($clear);
	}
	
	protected function parallelGrab($threads,$volumes=5){
		
		$p = new \Pool($volumes);
		
		foreach($threads as $thread){
			
			$p->submit($thread);
		}
		
		$p->shutdown();		
		
	}
	
}