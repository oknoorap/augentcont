<?php

class DB_Driver
{
	private static $instance;
	private static $connection;
	/* Public database */
	public $host;
	public $database;
	public $username;
	public $password;

	public $insert_id;
	/* Query */
	private $where = '';
	private $offset;
	private $fields;
	private $limit;
	private $from;
	private $order_by;
	private $group_by;
	private $distinct;
	private $order_by_count = 0;
	private $where_count = 0;

	/* Constructor functions */
	public function __construct($host, $database, $username, $password)
	{
		global $db;
		self::$instance = & $this;

		$this->host = $host;
		$this->database = $database;
		$this->username = $username;
		$this->password = $password;
		$this->connect();
	}

	/* Get reference */
	public static function &get_instance()
	{
		return self::$instance;
	}

	private function connect()
	{
		$mysql_connect = mysql_connect($this->host, $this->username, $this->password);
		if (!$mysql_connect)
		{
			die('Error: Could not connect: ' . mysql_error());
		}
		else
		{
			mysql_select_db($this->database);
		}

		$this->connection = $mysql_connect;
	}

	private function close()
	{
		mysql_close($this->connection);
	}

	public function escape_str($string)
	{
		$search=array("\\","\0","\n","\r","\x1a","'",'"');
		$replace=array("\\\\","\\0","\\n","\\r","\Z","\'",'\"');
		return str_replace($search, $replace, $string);
	}

	private function explode_comma($string, $pre_suffix = '`')
	{
		$string = str_replace(' ', '', $string);
		$string = explode(',', $string);
		$new_string = '';
		foreach($string as $str)
		{
			$new_string.= "{$pre_suffix}{$str}{$pre_suffix}, ";
		}

		$new_string = rtrim($new_string, ', ');
		return $new_string;
	}

	public function query($statement = '')
	{
		$this->query = $statement;
		$this->connect();
		$this->result = mysql_query($this->query);
		$this->affected_rows = mysql_affected_rows();
		$this->insert_id = mysql_insert_id();
		$this->close();
		return $this;
	}

	public function last_query()
	{
		return $this->query;
	}

	public function select($fields = '')
	{
		$this->fields = $this->explode_comma($fields);
		return $this;
	}

	public function distinct()
	{
		$this->distinct = 'DISTINCT ';
	}

	public function from($table_name)
	{
		$this->from = $table_name;
		return $this;
	}

	public function limit($limit = 30, $offset = 0)
	{
		$this->offset = $offset;
		$this->limit = $limit;
		return $this;
	}

	public function where($key, $equal_to = '', $operator = '=')
	{
		$where = array();
		$this->where_count+= 1;
		if (isset($equal_to) && $equal_to != '')
		{
			if ($this->where_count > 1)
			{
				array_push($where, " AND `{$key}` {$operator} '{$equal_to}'");
			}
			else
			{
				array_push($where, "WHERE `{$key}` {$operator} '{$equal_to}'");
			}

			foreach($where as $_where)
			{
				$this->where.= $_where;
			}
		}
		else
		{
			$this->where = "WHERE `{$key}`";
		}

		return $this;
	}

	public function where_in($key, $items, $in = 'IN')
	{
		if (is_array($items))
		{
			$where_in = '';
			foreach($items as $item)
			{
				$where_in.= "'{$item}',";
			}

			$where_in = rtrim($where_in, ',');
			$this->where("`{$key}` {$in} ({$where_in})");
		}
		else
		{
			$this->where("`{$key}` {$in} ('$items')");
		}

		return $this;
	}

	public function or_where_in($key, $items, $in = 'IN')
	{
		if (is_array($items))
		{
			$where_in = '';
			foreach($items as $item)
			{
				$where_in.= "'{$item}',";
			}

			$where_in = rtrim($where_in, ',');
			$this->where = $this->where . " OR `{$key}` {$in} ({$where_in})";
		}
		else
		{
			$this->where = $this->where . " OR `{$key}` {$in} ('$items')";
		}

		return $this;
	}

	public function where_not_in($key, $items)
	{
		$this->where_in($key, $items, 'NOT IN');
		return $this;
	}

	public function or_where_not_in($key, $items)
	{
		$this->or_where_in($key, $items, 'NOT IN');
		return $this;
	}

	public function like($key, $match, $sign = '', $operator = 'AND', $like = 'LIKE')
	{
		$this->where_count+= 1;
		switch ($sign)
		{
		case 'before':
			$sign = "%{$match}";
			break;

		case 'after':
			$sign = "{$match}%";
			break;

		default:
			$sign = "%{$match}%";
			break;
		}

		if ($this->where_count > 1)
		{
			$this->where.= " {$operator} `{$key}` {$like} '{$sign}'";
		}
		else
		{
			$this->where($key);
			$this->where.= " {$like} '{$sign}'";
		}

		return $this;
	}

	public function or_like($key, $match, $sign = '')
	{
		$this->like($key, $match, $sign, 'OR');
		return $this;
	}

	public function and_like($key, $match, $sign = '')
	{
		$this->like($key, $match, $sign, 'AND');
		return $this;
	}

	public function not_like($key, $match, $sign = '')
	{
		$this->like($key, $match, $sign, 'NOT');
		return $this;
	}

	public function or_not_like($key, $match, $sign = '')
	{
		$this->like($key, $match, $sign, 'OR', 'NOT LIKE');
		return $this;
	}

	public function group_by($fields = '')
	{
		$this->group_by = 'GROUP BY ' . $this->explode_comma($fields, '') . ' ';
	}

	public function order_by($field, $option = '')
	{
		if ($option != '' || $option == 'asc' || $option == 'desc' || $option == 'random')
		{
			$order_by = array();
			$this->order_by_count+= 1;
			$option = str_replace(array(
				'asc',
				'desc',
				'random'
			) , array(
				'ASC',
				'DESC',
				'RANDOM'
			) , $option);
			if ($this->order_by_count > 1)
			{
				array_push($order_by, " {$field} {$option},");
			}
			else
			{
				array_push($order_by, "ORDER BY {$field} {$option},");
			}

			foreach($order_by as $_order_by)
			{
				$this->order_by .= "{$_order_by}";
			}
		}
		else
		{
			$field = str_replace(array(
				'asc',
				'desc',
				'random'
			) , array(
				'ASC',
				'DESC',
				'RANDOM'
			) , $field);
			$this->order_by = "ORDER BY {$field} ";
		}

		return $this;
	}

	public function get($table_name = '', $limit = 30, $offset = 0)
	{
		$_fields = ($this->fields) ? $this->fields : '*';
		$_limit = ($this->limit) ? 'LIMIT ' . $this->offset . ', ' . $this->limit : ($limit) ? 'LIMIT ' . $offset . ', ' . $limit : '';
		$_from = ($this->from) ? $this->from : $table_name;
		$_order_by = rtrim($this->order_by, ',') . ' ';
		$this->query('SELECT ' . $this->distinct . $_fields . ' FROM `' . $_from . '` ' . $this->where . ' ' . $this->group_by . $_order_by . $_limit);
		return $this;
	}

	public function insert($table, $data)
	{
		if (is_array($data))
		{
			$fields = '';
			$values = '';

			foreach($data as $field => $value)
			{
				$fields .= "`{$field}`, ";
				$values .= "'".$this->escape_str($value)."',";
			}

			$fields = rtrim($fields, ', ');
			$values = rtrim($values, ', ');
			$query = "INSERT INTO `{$table}` ({$fields}) VALUES ({$values})";

			$this->query($query);
		}

		return $this;
	}

	public function update($table, $data)
	{
		if (is_array($data))
		{
			$values = '';
			foreach($data as $field => $value)
			{
				$values.= "`{$field}` = '" . $this->escape_str($value) . "', ";
			}

			$values = rtrim($values, ', ');
			$query = "UPDATE `{$table}` SET {$values} " . $this->where;
			$this->query($query);
		}

		return $this;
	}

	public function upsert($table, $keys, $data)
	{
		if ((isset($keys) && $keys != '') && is_array($data))
		{
			$fields = '';
			$values = '';
			foreach($data as $field => $value)
			{
				$fields.= "`{$field}`, ";
				$values.= "'" . $this->escape_str($value) . "', ";
			}

			$fields = rtrim($fields, ', ');
			$values = rtrim($values, ', ');
			$duplicate_keys = str_replace(' ', '', $keys);
			$duplicate_keys = explode(',', $duplicate_keys);
			$query = "INSERT INTO `{$table}` ({$fields}) VALUES ($values) ON DUPLICATE KEY UPDATE ";
			foreach($duplicate_keys as $key)
			{
				if (preg_match('/^\+[\w]+/', $key))
				{
					$key = str_replace('+', '', $key);
					$query.= "{$key} = $key} + 1, ";
				}
				else
				{
					$query.= "`{$key}` = VALUES(`{$key}`), ";
				}
			}

			$query = rtrim($query, ', ');
			$this->query($query);
		}

		return $this;
	}

	public function delete($table)
	{
		$query = "DELETE FROM {$table} " . $this->where;
		$this->query($query);
		return $this;
	}

	public function empty_table($table)
	{
		$query = "TRUNCATE TABLE `{$table}`";
		$this->query($query);
		return $this;
	}

	public function result()
	{
		if(! empty($this->result))
		{
			$output = array();

			while ($row = mysql_fetch_assoc($this->result))
			{
				$output[] = $this->stripslashes_deep($row);
			}

			return array_map(array($this, 'clean_words'), $output);
		}

		return array();
	}

	function clean_words($arr)
	{
		if (isset($arr['keyword'])) {
			$arr['keyword'] = normalize(clean_words(strtolower($arr['keyword'])), true);
		}

		if (isset($arr['title'])) {
			$arr['title'] = normalize(clean_words(strtolower($arr['title'])), true);
		}

		return $arr;
	}

	public function num_rows()
	{
		return mysql_num_rows($this->result);
	}

	public function num_fields()
	{
		return mysql_num_fields($this->result);
	}

	public function affected()
	{
		return $this->affected_rows;
	}

	public function reset_result()
	{
		$this->result = '';
		return $this;
	}

	public function stripslashes_deep($value)
	{
		if ( is_array($value) ) {
			$value = array_map(array($this, 'stripslashes_deep'), $value);
		} elseif ( is_object($value) ) {
			$vars = get_object_vars( $value );
			foreach ($vars as $key=>$data) {
				$value->{$key} = $this->stripslashes_deep( $data );
			}
		} elseif ( is_string( $value ) ) {
			$value = stripslashes($value);
		}

		return $value;
	}
}

/* EOF */
