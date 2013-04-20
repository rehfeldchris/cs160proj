<?php
require_once "connection.php";
class TableInfo
{
	private $columnList;
	private $rows;
	
	function __construct($queryResult) 
	{
		$result=$queryResult;
		$finfo = $queryResult->fetch_fields();
		$this->columnList=array();
		$this->rows=array();
		$columns=$this->columnList;
		
		 foreach ($finfo as $val) 
		 {
			array_push($this->columnList,$val->name);
			
		 }
		
		while ($row = $result->fetch_row()) 
		{
			array_push($this->rows,$row);
		
		}
		//print_r($this->columnList);
	}
	function DisplayTable()
	{
		echo "<div>";
		echo "<table border=1>";
		$columns=$this->columnList;
		$rowsList=$this->rows;
		echo "<tr>";
		foreach($columns as $column)
		{
			echo "<th>$column</th>";
		}
		echo"</tr>";
		$size=count($rowsList);
		
		
		foreach($rowsList as $row)
		{
			
			echo "<tr>";
			foreach($row as $element)
			{
				echo "<td>$element</td>";
				
				
			}
			echo "</tr>";
		}
		
		
		echo "</table>";
		echo"</div>";
	}
	
}
	$db=$GLOBALS['dbc'];
	$que="Select * from course_data,coursedetails where coursedetails.id=course_data.id";
		$result=$db->query($que) or die(mysqli_error($db));
		$table =new TableInfo($result);
		$table->DisplayTable();
?>