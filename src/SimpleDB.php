<?php 
namespace DB\SimpleDB;
use DB\SimpleDB\simpleDBManagerException;

class SimpleDB
{
	protected $table;
	protected $fields=[];
	protected $selectTable;
	protected $limit;
	protected $where=array();
	protected $fromTable;
	protected $whereVal;
	protected $createTable;
	protected $vals=[];
	protected $createFields;

	public function __construct()
	{
		
	}

	public function select()
	{
		$this->selectTable=func_get_args();
		return $this;
	}

	public function from($Fromtable)
	{
		$this->Fromtable=$Fromtable;
		return $this;
	}

	public function limit($limit)
	{
		$this->limit=$limit;
		return $this;
	}

	public function where($where,$whereVal=null)
	{
		$this->where=$where;
		$this->whereVal=$whereVal;
		return $this;
	}

	public static function addQuotes($string)
	{
		//break into an array
		$stringArray=explode(' ', $string);
		$newArray=[];
		array_push($newArray, "'");
		foreach($stringArray as $string)
		{
			
			array_push($newArray, $string);
		}
		array_push($newArray, "'");

		return join('',$newArray);
	}


	public function result()
	{
		$query[]="SELECT ";
		if(empty($this->selectTable))
		{
			$query[]="*";
		}
		else
		{
			$allTables=[];
			
			foreach($this->selectTable as $table)
			{	
				$newTable=self::addQuotes($table);
				array_push($allTables, $newTable);
			}
			
			$query[]=implode(',',$allTables);
		}

		//other conditions
		if(!empty($this->Fromtable))
		{
			$query[]="FROM $this->Fromtable";
		}
		if(!empty($this->where))
		{
			$query[]="WHERE";
			//check if it is an array
			if(is_array($this->where))
			{
				$castWhere=[];
				$castValues=[];
				foreach($this->where as $key=>$value)
				{
					array_push($castWhere, array($key=>$value));
									
				}
				
				if(count($castWhere)>0)
				{
					$selectValues=[];
					for($count=0; $count<count($castWhere); $count++)
					{
						foreach ($castWhere[$count] as $key => $value) 
						{
							array_push($selectValues,$key);
							array_push($selectValues,'=');
							array_push($selectValues, "'$value'");	
							array_push($selectValues,',');
						}						
					}
					$newString=implode(' ',$selectValues);
					if(substr($newString,-1)==',')
					{
						$newString=substr_replace($newString, ' ', -1);
						if(isset($newString))
						{
							if (preg_match('/[^,$]/', $newString)) 
							{
								$newString=str_replace(',', 'AND', $newString);
							}
							else
							{
								die('..did not');
							}
						}
					}
					else
					{
						return true;
					}
					$query[]=$newString;

				}
			
			}
			else
			{
				$where=$this->where;
				$whereVal=$this->whereVal;
				$query[]="$where='$whereVal'";

			}
		}
		if(!empty($this->limit))
		{
			$query[]="LIMIT $this->limit";
		}
		$join=join(' ',$query);
		return $join;
	}


	/**
	 * create function
	 * @param 
	 * @return   [<A create instance using PDO>]
	 */
	public function table($createTable,$fields=null)
	{
		$this->createTable=$createTable;
		$this->createFields=$fields;
		return $this;
	}
	public function create($vals)
	{
		$this->vals=$vals;
		return $this;
		
	}	

	public function save()
	{
		$createQuery[]="INSERT INTO";

		if(!empty($this->createTable))
		{
			$createQuery[]=$this->createTable;
			if(!empty($this->createFields))
			{
				$newFields=[];
				array_push($newFields,'(');
				foreach($this->createFields as $fields)
				{
					array_push($newFields,"'");
					array_push($newFields, $fields);
					array_push($newFields,"',");
				}
				array_push($newFields, ')');
				$newFields=implode('', $newFields);
				if(substr($newFields, -1)==')')
				{
					//$selectedString=substr($newFields,-2,-1);
					$newFields=substr_replace($newFields, '', -2,-1);
					//$newFields=substr_replace($newFields,' ', $beforeLast);
				
				}
				$createQuery[]=$newFields;
			}
		}
		if(!empty($this->vals))
		{
			$createQuery[]="VALUES ";

			$valuesArray=[];
			array_push($valuesArray,'(');
			foreach($this->vals as $values)
			{
				array_push($valuesArray,"'$values'");
			}
			array_push($valuesArray, ')');
			$valuesArray=implode(',',$valuesArray);
			$valuesArray=substr_replace($valuesArray, ' ',1,1);
			$valuesArray=substr_replace($valuesArray, ' ', -2,-1);
			$createQuery[]=$valuesArray;
		}
		

		$createQuery=join(' ',$createQuery);
		return $createQuery;

	}
}
