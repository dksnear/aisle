<?php
namespace aisle\db\driver;

use aisle\core\Trace;
use aisle\db\Statement;
use aisle\db\Table;
use aisle\db\Field;
use aisle\ex\DbException;

class MysqlClient extends \aisle\db\PdoClient{
			
	protected $queryKeyWordsMap = Array(

		'table' => Array(
		
			'1' => 'select',
			'2' => 'insert',
			'4' => 'update',
			'8' => 'delete',
			'16' => 'where',
			'32' => 'limit',
			'64' => 'desc',
			'128' => 'asc',
			'256' => 'group by',
			'512' => 'having',
			'1024' => 'inner join',
			'2048' => 'left join',
			'4096' => 'right join',
			'8192' => 'on',
			'16384' => 'union',
			'32768' => 'union all'
		),

		'cond' => Array(
		
			'1' => 'and',
			'2' => 'or',
			'4' => 'exists',
			'8' => 'not exists',
			'16' => 'in',
			'32' => 'in',
			'64' => 'not in',
			'128' => 'not in',
			'256' => '>',
			'512' => '>=',
			'1024' => '<',
			'2048' => '<=',
			'4096' => '=',
			'8192' => '<>',
			'16384' => 'like',
			'32768' => 'is null',
			'65536' => 'is not null',
			'131072' => 'between',
			'262144' => 'not between'
		),

		'field' => Array(
		
			'1' => 'avg',
			'2' => 'count',
			'4' => 'min',
			'8' => 'max',
			'16' => 'sum'
		)
	);
			
	public function Connect($dsn,$reset=false){
		
		if($this->connected && !$reset)
			return $this;
		
		if($reset) $this->Close();
		
		$dsn = is_array($dsn) ? $dsn : array();
	
		$this->dsn = array_merge(array(
		
			'charset'=> 'UTF8',

			// 主机名或 IP 地址
			'host'=> '127.0.0.1',

			// 端口号
			'port'=> 3306,

			// 用户名
			'username'=> 'root',

			// 密码
			'password'=> '',

			// 规定默认使用的数据库
			'dbname'=> '',

			// 时区
			'timezone'=> '+8:00'
			
		),$this->dsn,$dsn);
		
		try{
			
			$this->pdo = new \PDO(sprintf('mysql:dbname=%s;host=%s;port=%s',$this->dsn['dbname'],$this->dsn['host'],$this->dsn['port']),
				$this->dsn['username'],
				$this->dsn['password'],
				array(\PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES '.$this->dsn['charset']));
		
			
			// 设置时区	
			$this->pdo->exec('SET time_zone = \''.$this->dsn['timezone'].'\';');
		
			// 允许捕获在发生致命错误时的异常
			$this->pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
			
			// 关闭模拟预处理语句
			$this->pdo->setAttribute(\PDO::ATTR_EMULATE_PREPARES, false);
			
			$this->connected = true;
			
		
		} catch(\PDOException $ex){
		
			throw new DbException($ex);
		
		}
		
		return $this;
		
		
	}
		
	public function Compile($trace=false){
		
		$trace = $trace ? new Trace('mysql client compile trace') : false;
		
		$this->queryStatements = array();
				
		foreach($this->queryTables as $table)
			$this->queryStatements []= $this->compileTable($table);
		
		if($trace){
			
			Trace::WriteLine($this->queryStatements);
			Trace::WriteLine($this->queryParams);
			$trace->End();
		}
		
		return $this;
	}
		
	protected function compileTable($table){
		
		$export = $table->Export();
		$tableName = $export['conflict'] ? ($export['name'] ? sprintf('`%s`',$export['name']) : '') : $export['name'];
		$tableNameWithAlias = $export['alias'] ? sprintf('%s as `%s`',$tableName,$export['alias']) : $tableName;
		$joinIdx = 0;
		
		$segments = array(
		
			'insert'=>'',
			'update'=>'',
			'delete'=>'',
			'select'=>'',
			'table'=>$tableName ? 'from '.$tableNameWithAlias : '',
			'union'=>'',
			'join'=>array(),
			'where'=>'',
			'groupby'=>'',
			'having'=>'',
			'orderby'=>array(),
			'limit'=>''
			
		);
				
		foreach($export['statements'] as $statement){
			
			$op = $this->queryKeyWordsMap['table'][$statement->operator].' ';
						
			//'select'
			if($statement->OpIn(1)){
														
				$segments['select'] = 'select '.implode(', ',$this->compileFields($statement->operands[0],'select'));
				
				continue;
			}
			
			//'insert'
			if($statement->OpIn(2)){
				
				$fields = array();
				$values = array();
	
				$this->compileFields($statement->operands[0],'insert',function($compiled) use(&$fields,&$values){
					
					$fields []= $compiled[0];
					$values []= $compiled[1];
					
				});
				
				$segments['insert'] = !$statement->operands[1] ?
					sprintf('insert into %s (%s) values (%s)',$tableName,implode(', ',$fields),implode(', ',$values)) :
					sprintf('insert into %s (%s) %s',$tableName,implode(', ',$fields),$this->compileTable($statement->operands[1]));
								
				$segments['table'] = '';
				
				continue;
			}
			
			//'update'
			if($statement->OpIn(4)){
				
				$segments['update'] = sprintf('update %s set %s',$tableName,implode(', ',$this->compileFields($statement->operands[0],'update')));
				$segments['table'] = '';
				
				continue;			
			}
			
			//'delete'
			if($statement->OpIn(8)){
				
				$segments['delete'] = 'delete';
				continue;
				
			}
						
			//'where','having'
			if($statement->OpIn(528)){
				
				$segments[trim($op)] = $op.$this->compileCond($statement->operands[0]);
				
				continue;
			}
			
			//'groupby'
			if($statement->OpIn(256)){
				
				$segments['groupby'] = $op.implode(',',$this->compileFields($statement->operands[0],'select'));
				
				continue;
			}
			
			//'asc','desc'
			if($statement->OpIn(192)){
				
				$segments['orderby'] []= implode(',',$this->compileFields($statement->operands[0],'select')).' '.$op;
				
				continue;
			}
			
			//'limit'
			if($statement->OpIn(32)){
				
				$segments['limit'] = $op.$statement->operands[0].','.$statement->operands[1];
				
				continue;
			}
			
			//'join','leftjoin','rightjoin'
			if($statement->OpIn(7168)){
				
				$segments['join'] []= ($statement->operands[2] ? '' : $op).'( '.$this->compileTable($statement->operands[0]).' )';
				
				$statement->operands[1] && $segments['join'][$joinIdx] .= ' as `'.$statement->operands[1].'`';
				
				$joinIdx++;
				
				continue;
			}
			
			//'on'
			if($statement->OpIn(8192)){
				
				$segments['join'][$joinIdx-1] .= ' '.$op.$this->compileCond($statement->operands[0]);
				
				continue;
			}
			
			// 'union','unionall'
			if($statement->OpIn(49152)){
				
				$segments['union'] = ($statement->operands[2] ? '' : $op).'( '.$this->compileTable($statement->operands[0]).' )';
				
				continue;
			}
			
		}
		
		if($segments['union']){
			
			$segments['table'] = 'from '.$segments['union'].($tableName ? ' as '.$tableName : '');
			$segments['union'] = '';
		}
		
		if(!empty($segments['join'])){
			
			$segments['table'] = 'from '.implode(' ',$segments['join']).($tableName ? ' as '.$tableName : '');
			$segments['join'] = '';

		}
					
		$segments['orderby'] = (!empty($segments['orderby']) ? 'order by ' : '').implode(',',$segments['orderby']);
		
		return implode(' ',array_filter($segments,function($item){ return !empty($item); }));
			
	}
		
	protected function compileCond($cond){
		
		$segments = array();
		$export=$cond->Export();
		
		foreach($export['statements'] as $statement){
				
			// logical operator
			$lo = strtolower($statement->operands[0]);
			$lo = in_array($lo,array('and','or')) ? $lo.' ' : '';
			$op = $this->queryKeyWordsMap['cond'][$statement->operator];
			
			//130816
			//'gt','gte','lt','lte','eq','ne','like','null','notnull'
			if($statement->OpIn(130816)){
				
				$compiled = $this->compileField($statement->operands[1],'cond');			
				$segments []= $lo.$compiled[0].' '.$op.' '.$compiled[1]; 			
				continue;
			}
			
			//160
			//'ins','notins'
			if($statement->OpIn(160)){
				
				$segments []= $lo.$this->compileField($statement->operands[1],'select').' '.$op.' ('.implode(',',array_map(function($item){ return ' \''.$item.'\' '; },$statement->operands[2])).') ';				
				continue;
			}
			
			//393216
			//'between','notbetween'
			if($statement->OpIn(393216)){
				
				$segments []= $lo.$this->compileField($statement->operands[1],'select').' '.$op.' '.$statement->operands[2].' and '.$statement->operands[3];		
				continue;
			}
			
			//92
			//'exists','notexists','in','notin'
			if($statement->OpIn(92)){
				
				$segments []= $lo.$op.' ( '.$this->compileTable($statement->operands[1]).' ) ';
				continue;
			}
			
			//3
			//'and','or'
			if($statement->OpIn(3)){
				
				$segments []= ($statement->operands[2] ? '' : $op).' ( '.$this->compileCond($statement->operands[1]).' )';			
				continue;
			}
			
		}
			
		return implode(' ',$segments);
	}
	
	protected function compileFields($fields,$type,$fn=null){
		
		if(!is_array($fields))
			return array($fields);
		
		$compiledFields = array();
		
		foreach($fields as $field){
			
			$compiledFields []= is_callable($fn) ? $fn($this->compileField($field,$type)) : $this->compileField($field,$type);
		}
				
		return $compiledFields;
		
	}
	
	//@type insert|update|select|cond
	protected function compileField($field,$type){
		
		if(!($field instanceof Field))
			return $field;
		
		$export = $field->Export();
		
		$segment = '';
		$fieldName = $export['tableName'] ? sprintf('`%s`.`%s`',$export['tableName'],$export['name'])
				: sprintf('`%s`',$export['name']);
		
		$isVField = $export['value'] instanceof Field;
		$export['value'] = $isVField ? $this->compileField($export['value'],'select') : $export['value'];
		$fieldValue = $export['bind'] ? ':'.$export['bindName']: ($isVField ? $export['value'] : '\''.$export['value'].'\'');
		
		if($type == 'select'){
			
			if(!is_null($export['value']) || $export['bind'])
				return $fieldValue;
		
			$segment = $fieldName;
			
			if($export['statement']->operator)
				$segment = sprintf('%s(%s)',$this->queryKeyWordsMap['field'][$export['statement']->operator],$segment);
			
			if($export['alias'])
				$segment = sprintf('%s as `%s`',$segment,$export['alias']);
			
			return $segment;
			
		}
		
		if($type == 'insert'){
			
			return array(sprintf('`%s`',$export['name']),$fieldValue);
		}
		
		if($type == 'update'){
			
			return sprintf('`%s` = %s',$export['name'],$fieldValue);
		}
		
		if($type == 'cond'){
					
			$segment = array($fieldName,$fieldValue ? $fieldValue : '');
			
			if($export['statement']->operator)
				$segment[0] = sprintf('%s(%s)',$this->queryKeyWordsMap['field'][$export['statement']->operator],$segment[0]);
		}
			
		return $segment;
		
	}

}