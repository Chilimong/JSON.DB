<?php
	/*
		-- JSON.DB --
		A PHP class created in order to use JSON files in a database-like way
		
		Author: Vladi Pryadko
		Version: 1.01
	*/
	class JSONDB
	{
		protected $file;
		
		public function __construct($file)
		{
			$this->file = $file;
			$this->data = (array)json_decode(file_get_contents($file), true);
			if($this->data == null) $this->data = array();
		}
		
		protected function Save()
		{
			file_put_contents($this->file, json_encode($this->data));
		}
		
		protected function Error($error)
		{
			trigger_error($error, E_USER_ERROR);
		}
		
		public function getTables() // Returns: Array
		{
			return array_keys($this->data);	
		}
		
		public function newTable($name, $structure)
		{
			if(!is_array($structure)) $this->Error('You must pass an array to the "newTable" function as the 2nd parameter.');
			$this->data[$name] = array($structure); 
			$this->Save();
			return $this;
		}
		
		public function deleteTable($name)
		{
			unset($this->data[$name]);
			$this->Save();
		}
		
		public function Insert($table, $data)
		{
			if(!is_array($data)) $this->Error('You must pass an array to the "Insert" function as the 2nd parameter.');
			$data = array_values($data);
			array_push($this->data[$table], $data);
			$this->Save();
			return $this;
		}
		
		public function Select($table, $where_key, $equals_value, $result_type = MYSQLI_NUM)
		{
			$rowKey = array_search($where_key, $this->data[$table][0]);
			$keys = array();
			for($i = 1; $i < count($this->data[$table]); $i++) if($this->data[$table][$i][$rowKey] == $equals_value) array_push($keys, $i);
			switch($result_type)
			{
				case 1: // MYSQLI_ASSOC 
				{
					$return = [];
					$structure = $this->data[$table][0];
					foreach($keys as $result_key => $result_value)
					{
						foreach($structure as $structure_key => $structure_value)
						{
							$return[$result_key][$structure_value] = $this->data[$table][$result_value][$structure_key];
						}
					}
					return $return;
				}
				break;
				
				case 2: // MYSQLI_NUM
					{
						$return = [];
						foreach($keys as $result_key)
						{
							array_push($return, $this->data[$table][$result_key]);
						}
						return $return;
					}
				break;
			}
		}
		
		public function getTable($table, $result_type = MYSQLI_NUM) // Better use MYSQLI_NUM for performance :) ( MYSQLI_NUM / MYSQLI_ASSOC - DO NOT USE MYSQLI_BOTH )
		{
			switch($result_type)
			{
				case 1: // MYSQLI_ASSOC 
				{
					$return = array();
					$structure = $this->data[$table][0];
					$row_length = count($structure);
					for($i = 1; $i < count($this->data[$table]); $i++)
					{
						for($j = 0; $j < count($structure); $j++)
						{
							$return[$i][$structure[$j]] = $this->data[$table][$i][$j];
						}
					}
					return $return;
				}
				break;
				
				case 2: // MYSQLI_NUM
					return array_slice($this->data[$table], 1);
				break;
			}
		}
		
		public function Delete($table, $where_key, $equals_value)
		{
			$rowKey = array_search($where_key, $this->data[$table][0]);
			for($i = 1; $i < count($this->data[$table])-1; $i++) if($this->data[$table][$i][$rowKey] == $equals_value) unset($this->data[$table][$i]);
			$this->Save();
			return $this;
		}
		
		public function Update($table, $where_key, $equals_value, $newData)
		{
			if(!is_array($newData)) $this->Error('You must pass an array to the "Update" function as the 4th parameter.');
			$rowKey = array_search($where_key, $this->data[$table][0]);
			$keys = [];
			for($i = 1; $i < count($this->data[$table]); $i++) if($this->data[$table][$i][$rowKey] == $equals_value) array_push($keys, $i);
			foreach($newData as $key => $value)
			{
				$row_key = array_search($key, $this->data[$table][0]);
				foreach($keys as $result_key)
				{
					$this->data[$table][$result_key][$row_key] = $value;
				}
			}
			$this->Save();
			return $this;
		}
		
		public function getNextID($table)
		{
			return $this->data[$table][count($this->data[$table])-1][0]+1;
		}
	}	