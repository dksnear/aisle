<?php
namespace aisle\view;

interface IView{
	
	// @return IView
	public function Set($options,$direct=false);
	
	public function Render($statements=null,$ret=false);

}