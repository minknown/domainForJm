
<?php
if($_GET['getip']!=""){
echo $_SERVER["REMOTE_ADDR"]; 
exit();
}
$tn=$_GET['tn'];
$way=$_GET['way'];
$name=$_GET['name'];
$f=$_GET['f'];
$id=$_GET['id'];
$temp=$_FILES["file"]["tmp_name"];
$b="@";
if($tn=="" and $temp=="" and $way!="end"){die("tn为空白");}
//链接数据库
$config = include('../config.php');
$con = mysql_connect($config['host'], $config['user'], $config['pass']);
if (!$con){echo '系统繁忙';exit(2);}
mysql_select_db($config['dbname'], $con);
mysql_query("SET NAMES UTF8");

//接口使用_上传域名
if($temp!=""){
	$txt=file_get_contents($temp);
	$keys=explode(PHP_EOL,$txt);
	
	foreach ($keys as $key)
	{
		$key=trim(str_replace(".",$b,$key));
	    $qq = "INSERT INTO jm (id, name, tn,sta,f) VALUES (NULL, '".$key."', '".$_POST['tn']."',0,'web')";
		mysql_query($qq, $con);
	}

	
	echo "添加完成！<br><a href='index.php?tn=".$_POST['tn']."'>返回上一页</a>";
	exit();
}
//接口使用_APP
if($way!=""){
	if($way=="end"){
		echo "<h1 style='text-align:center;padding-top:20px'>查录已结束</h1>";
	}
	if($way=="del"){
		$qq = "DELETE FROM jm where tn='".$tn."'";
		mysql_query($qq, $con);
		echo "删除完成！<br><a href='index.php?tn=".$tn."'>返回上一页</a>";
	}
	if($way=="get"){
		$qq = "SELECT * FROM jm WHERE tn='".$tn."' and sta=0 order by rand() limit 1";
		$result = mysql_query($qq, $con);
		$row = mysql_fetch_array($result);   
		
		$row['name']=str_replace($b,".",$row['name']);
		echo $row['name'];
	}
	if($way=="num"){
		$qq = "SELECT count(id) as nums FROM jm WHERE tn='".$tn."' and sta=0";
		$result = mysql_query($qq, $con);
		$row = mysql_fetch_array($result);   
		echo $row['nums'];
	}
	if($way=="ishave"){
		$qq = "SELECT * FROM jm WHERE tn='".$tn."' and name='".$name."' limit 1";
	
		$result = mysql_query($qq, $con);
		$row = mysql_fetch_array($result);   
		echo $row['sta'];
	}
	if($way=="writehave"){
		$qq = "UPDATE jm SET sta=1,f='".$f."' where tn='".$tn."' and sta=0 and name='".$name."'";
		mysql_query($qq, $con);
		
		echo "OK";
	}	
	if($way=="cx"){
		$qq = "UPDATE jm SET sta=2 where tn='".$tn."' and sta=4 and id='".$id."'";
		mysql_query($qq, $con);
		echo "撤销完成！<br><a href='index.php?tn=".$tn."'>返回上一页</a>";
	}
	if($way=="cz"){
		$qq = "UPDATE jm SET sta=0 where tn='".$tn."' and sta=4";
		mysql_query($qq, $con);
		echo "重置完成！<br><a href='index.php?tn=".$tn."'>返回上一页</a>";
	}
	if($way=="deal"){
		$qq = "UPDATE jm SET sta=3 where tn='".$tn."' and sta=4 and id='".$id."'";
		mysql_query($qq, $con);
		echo "处理完成！<br><a href='index.php?tn=".$tn."'>返回上一页</a>";
	}	
	if($way=="writeok"){
		$qq = "UPDATE jm SET sta=4,f='".$f."' where sta!=2 and sta!=3 and tn='".$tn."' and name='".$name."'";
	
		mysql_query($qq, $con);
		echo "OK";
	}		
	mysql_close();
	exit();
}
//查询域名列表
$qq = "SELECT * FROM jm WHERE tn='".$tn."' order by sta desc limit 200";
$result = mysql_query($qq, $con);
while($row=mysql_fetch_assoc($result)){
	
$key=str_replace($b,".",$row['name']);
if($row['sta']=='0'){$row['sta']="Waiting...";}
if($row['sta']=='1'){$row['sta']="不符合条件";}
if($row['sta']=='2'){$row['sta']="不符合条件(手查)";}
if($row['sta']=='3'){$row['sta']="已处理";}
if($row['sta']=='4'){
	$key='<a target="_blank" href="https://www.baidu.com/s?wd=site%3A'.$key.'&ie=UTF-8"><b style="color:green">'.$key.'</b></a>';
	$key=$key.'[<a href="index.php?way=deal&id='.$row['id'].'&tn='.$tn.'">执理</a>|<a href="index.php?way=cx&id='.$row['id'].'&tn='.$tn.'">撤销</a>]';
	$row['sta']="符合条件";
}
$list=$list."<tr><td>".$row['id']."</td><td>".$key."</td><td>".$row['sta']."</td><td>".$row['f']."</td></tr>";
}
?>
<!DOCTYPE html>

<head>
	 <title>JM</title>
	<meta name="author" content="jm">
    <meta name="viewport" content="width=device-width,initial-scale=1.0,user-scalable=no,viewport-fit=cover,maximum-scale=1.0, user-scalable=0">
	<meta charset="UTF-8">
	<style type="text/css">
	table
	  {
	  border-collapse:collapse;
	  }
	
	table, td, th
	  {
	text-align:left;
	  border:1px solid black;
	  }
	</style>
</head>
<body>
	<h1>JM后台-<?php echo $tn;?></h1>
	<?PHP
	$qq = "SELECT count(id) as nums FROM jm WHERE tn='".$tn."' and sta!=0";
	$result = mysql_query($qq, $con);
	$al = mysql_fetch_array($result)['nums'];   
	
	$qq = "SELECT count(id) as nums FROM jm WHERE tn='".$tn."'";
	$result = mysql_query($qq, $con);
	$all = mysql_fetch_array($result)['nums'];
	echo "<br>执行进度:".$al."/".$all;
	?>
	<form action="index.php" method="post" enctype="multipart/form-data">
	   <br> 添加域名列表:<br><input type='hidden' value='<?php echo $tn;?>' name='tn'><input type="file" name="file" id="file"><input type="submit" name="submit" value="提交">
		
	</form>
	<button onclick="location.href='index.php?way=cz&tn=<?php echo $tn;?>'">重置已符合的项目</button>
	<button onclick="location.href='index.php?way=del&tn=<?php echo $tn;?>'">从数据库删除所有本任务的域名</button>
	<hr>
	
	<table>
	<tr>
	<th width="50">ID</th><th width="350">域名</th><th width="150">状态</th><th width="80">来源</th>
	
	</tr>
	<?php echo $list;?>
	</table>
	<?php mysql_close();?>
	
	</body>
	</html>