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
		echo "<table border=1 width=\"100%\" height=\"100%\">";
		$columns=$this->columnList;
		$rowsList=$this->rows;
		echo "<tr>";
		foreach($columns as $column)
		{
			echo "<th>$column</th>";
		}
		echo"</tr>";
		$size=count($rowsList);
		$count=0;
		
		foreach($rowsList as $row)
		{
			
			echo "<tr>";
			foreach($row as $element)
			{
				$count++;
				if(checkisLink($element))
				{
					echo "<td><a href=\"$element\">Link</a></td>";
				}
				elseif(checkisImage($element))
				{
					echo "<td><img src=\"$element \" width=\"100\" height=\"100\"</td>";
				}
				else
				{
					echo "<td>$element</td>";
				}
				
				
				
			}
			echo "</tr>";
		}
		
		
		echo "</table>";
		echo"</div>";
	}
	
}
	function findStr($mystring,$findme)
	{
		//$mystring = 'abc';
		//$findme   = 'a';
		$pos = strpos($mystring, $findme);

		if ($pos === false) 
		{
			
			return false;
		} 
		else 
		{
		
			return true;
		}
	}
	function checkisImage($element)
	{
		if(findStr($element,".png")||findStr($element,".jpeg")||
			findStr($element,".JPG")||findStr($element,".jpg")||findStr($element,".gif"))
			return true;
			else return false;
	}
	function checkisLink($element)
	{
		if(findStr($element,"http:")||findStr($element,"https:"))
		{
			// check if it is image
			if(checkisImage($element))
			{
				return false;
			}
			return true;
			
		}
		else
		{
			return false;
		}
	}


	$db=$GLOBALS['dbc'];
	$que="Select * from course_data,coursedetails where coursedetails.id=course_data.id";
		$result=$db->query($que) or die(mysqli_error($db));
		$table =new TableInfo($result);
		$table->DisplayTable();
?>