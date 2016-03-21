
<ul>
<?php
echo "<h2>".$item['Small']['small']."</h2>";
echo "<h3>".$item['Item']['item']."</h3>";
foreach($serials as $data):
	echo "<li>";
	echo $this->Html->link($data['number'],array('controller'=>'expenses','action'=>'view',$data['number']));
	echo '('.$data['date'].')';
	echo "</li>";
endforeach;
?>
</ul>