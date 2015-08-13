<?php
namespace aisle\attr;

use aisle\core\Reflection;
use aisle\ex\ClassVerifyException;
use aisle\ex\RoutingFailedException;

class AttributeResolver {
	
	protected $confm;
	
	public function __construct($confm){
		
		$this->confm = $confm;
		
	}

	public function Invoke($className,$methodName,$constructParams=null,$methodParams=null){
		
		$reflector = new Reflection($className);
		
		// # 实例处理
							
		$classAttributes = $reflector->getDefaultProperties();
		$classAttributes = isset($classAttributes['___attributes']) ? $classAttributes['___attributes'] : null;
		
		$classAttributeInstances = $this->init($classAttributes,array(
			
			'className'=>$className,
			'methodName'=>$methodName,
			'attributes'=>$classAttributes
		
		));	
		
		// 实例创建前调用特性类拦截方法		
		if(!$this->exec($classAttributeInstances,'BeforeConstruct'))
			return false;
		
		// 创建实例
		$instance = $reflector->NewInstanceWithAssocArgs($constructParams);

		if(!is_callable(array($instance,$methodName)))
			throw new RoutingFailedException();
		
		// 实例创建后调用特性类拦截方法	
		$this->exec($classAttributeInstances,'AfterConstruct',array(
		
			'instance' => $instance
		
		));
		
		// # 方法处理
		
		// 魔术方法__call不做method attribute处理 
		if(!$reflector->hasMethod($methodName))
			return $instance->$methodName();
		
		$method = $reflector->getMethod($methodName);
		$invokeArgs = array();
		$methodAttributes = $classAttributeInstances ? $classAttributeInstances : null;
		$methodAttributeInstances = null;
							
		// # 方法参数处理
		
		$params = $method->getParameters();
		
		// 绑定备选参数列表中的参数值 
		foreach($params as $param){
			
			$invokeArgs[$param->name] = isset($methodParams[$param->name]) ? 
				$methodParams[$param->name] :
				($param->isDefaultValueAvailable() ? $param->getDefaultValue() : null);		
					
		}

		if(array_key_exists('___attributes',$invokeArgs) && !empty($invokeArgs['___attributes'])){
		
			$methodAttributes = is_array($methodAttributes) ?  array_merge($methodAttributes, $invokeArgs['___attributes']) : $invokeArgs['___attributes'];	
			unset($invokeArgs['___attributes']);
						
		}
		
		$methodAttributeInstances = $this->init($methodAttributes,array(
		
			'className' => $className,
			'methodName' => $methodName,
			'instance' => $instance,
			'attributes' => $methodAttributes,
			'params' => $invokeArgs
		
		));	
		
		// 方法调用前执行特性类拦截方法		
		if(!$this->exec($methodAttributeInstances,'BeforeInvoke'))
			return false;
		
		// 执行方法
		$result = $method->invokeArgs($instance,$invokeArgs);
		
		// 方法调用后执行特性类拦截方法		
		$this->exec($methodAttributeInstances,'AfterInvoke',array(
		
			'methodInvokedResult' => $result
		));
				
		return $result;
	}

	protected function init($attributes,$properties=null){
		
		if(empty($attributes))
			return null;
	
		$attrInstances = array();
		$class = null;
		$instance = null;
		
		foreach($attributes as $attr){
			
			if($attr instanceof Attribute){
				
				$attr -> SetProperties($properties);
				$attrInstances [] = $attr;
				continue;
			}
			
			$name = $attr[0];
			$args = isset($attr[1]) ? $attr[1] : null;
		
			$class = $this->getAttrClass($name);
			$Attribute = 'aisle\\attr\\Attribute';
			
			if(!is_subclass_of($class,$Attribute))
				throw new ClassVerifyException($class,$Attribute);
			
			$instance = new $class($properties);
			$instance->SetProperties($args);
			
			$attrInstances [] = $instance;
		
		}
		
		// 处理队列排序
		usort($attrInstances,function($left,$right){
			
			if ($left->order == $right->order) return 0;
			
			return ($left->order < $right->order) ? -1 : 1;
			
		});
		
		return $attrInstances;
		
	}
	
	protected function exec($attributes,$callee,$properties=null){
		
		if(empty($attributes))
			return true;
	
		$result = true;
		$affectMethods = array('BeforeConstruct','AfterConstruct','BeforeInvoke','AfterInvoke');
		
		if(!in_array($callee,$affectMethods))
			return $result;
	
		foreach($attributes as $instance){
							
			$instance->SetProperties($properties);
			
			if($callee == $affectMethods[0] && $instance->classAffect)
				$result = $instance->$callee();
			
			if($callee == $affectMethods[1] && $instance->classAffect)
				$result = $instance->$callee();
			
			if($callee == $affectMethods[2] && $instance->methodAffect)
				$result = $instance->$callee();
				
			if($callee == $affectMethods[3] && $instance->methodAffect)
				$result = $instance->$callee();
			
			if(is_bool($result) && !$result)
				return false;
		
		}
		
		return true;
	}
	
	protected function getAttrClass($alias){
		
		return $this->confm->Config()->GetClassMap('attribute',$alias);
		
	}
}
