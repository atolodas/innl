<html>
<head>
<style>
table tbody tr td {
padding: 5px;
border: 1px solid; 
 } 
</style>
</head>
<body>
<?php function recursedir($BASEDIR) 
{
	$ext = array("php", "phtml", "html", "htm");
	$files = array();
	$hndl=opendir($BASEDIR);
		while($file=readdir($hndl)) 
		{
			if ($file=='.' || $file=='..') 
			continue;
			$completepath="$BASEDIR/$file";
			if (is_dir($completepath))
			{
# its a dir, recurse.
			recursedir($BASEDIR.'/'.$file);
			
			} 
			else 
			{
# its a file.
			for($i=0;$i<sizeof($ext);$i++)
				if(strstr($file, ".".$ext[$i]))
				{
				$content = file($BASEDIR."/".$file);
				$str="TODO";
                                foreach($content as $key=>$value)
                                        {
                                                if(substr_count($value,$str))
                                                {
                                                        echo "<tr><td>{$BASEDIR}/{$file}</td>";
                                                        echo "<td>".$key . "</td>";
                                                        echo "<td>{$value} </td></tr>";
       
                                                }
                                        }

				}		
			}
		}
	}

                                 echo "<table><tr class='head'>
                                <td>File</td>
                                <td>Line</td>
                                <td>TODO</td>
                                </tr>";




$BASEDIR="../app/code/local";
recursedir($BASEDIR);
$BASEDIR="../app/code/community";
recursedir($BASEDIR);
$BASEDIR="../app/design";
recursedir($BASEDIR);

echo "</table>";
?> 
</body>
</html>
