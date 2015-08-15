<?php
namespace aisle\view;

interface IView{
	
	// 设置视图参数
	// @direct(bool) 直接给视图对象相的statements属性初始化(true) 给视图对象的所有属性初始化(false)
	// @return IView
	public function Set($options,$direct=false);
	
	// 向前端输出视图
	public function Render($statements=null,$ret=false);
	
	// 通知视图管理器输出情况
	public function Notify($viewm);

}