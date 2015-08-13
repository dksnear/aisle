<?php
namespace aisle\attr;

/************************


	#函数特性类
	
	#usecase
	
		class T{
		
			// 特性标记属性
			// @argsArray 特性类构造函数实参
			// 如不需要该特性在子类中生效 可将访问属性设为private
			protected $___attributes=array(
			
				'attribute_name1'=> $argsArray1,
				'attribute_name2'=> $argsArray2
			
			);
		
			// @p 正常参数
			// @___attributes 特性标记参数
			// @argsArray 特性类构造函数实参
			// 如不需要该特性在子类方法中生效 可重写子类方法
			public function m($p,$___attributes=array(
			
				'attribute_name1'=> $argsArray1,
				'attribute_name2'=> $argsArray2		
			)){
			
				// do
			}
		
		}
	
****************************/

abstract class Attribute{

	// 该特性只影响类的创建
	protected $classAffect = false;
		
	// 该特性只影响方法调用
	protected $methodAffect = false;
	
	// 触发顺序
	protected $order = 1;

	// 特性指向的目标函数所属类名
	protected $className;
	
	// 特性指向的目标函数名
	protected $methodName;
	
	// 特性指向的目标函数的返回值 仅在afterInvoke中生效
	protected $methodInvokedResult = null;
	
	// 特性指向的目标函数参数集合
	// array( 'name'=>'','value'=>'' )
	protected $params;
	
	// 特性指向的目标函数的特性参数集合
	protected $attributes;
	
	// 特性指向的目标函数的调用实例
	protected $instance;
			
	// @properties array();
	public function __construct($properties=null){
	
		$this->SetProperties($properties);
	
	}
	
	// 属性设置器
	public function SetProperties($properties=null){
	
		if(!$properties) return $this;
	
		foreach($properties as $key=>$value){
			
			if(!property_exists($this,$key))
				continue;
			$this->$key = $value;
			
		}	
	}
	
	// 属性访问器
	public function __get($name){
	
		return property_exists($this,$name) ? $this->$name : null;
	
	}
	
	// 当$class_affect=true时 在目标类实例创建前调用
	// 如果该方法返回false(bool) 那么将放弃配置该特性的类后续的attribute处理并阻拦该配置该特性的类生成实例
	public function BeforeConstruct(){}
	
	// 当$class_affect=true时 目标类实例创建后调用
	public function AfterConstruct(){}

	// 当$method_affect=true时 该方法在目标函数调用前调用 
	// 如果该方法返回false(bool) 那么将放弃配置该特性的方法后续的attribute处理并阻拦配置该特性的方法的执行
	public function BeforeInvoke(){}
	
	// 当$method_affect=true时 该方法在目标函数调用后调用
	public function AfterInvoke(){}

}

