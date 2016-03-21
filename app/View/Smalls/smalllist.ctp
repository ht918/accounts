
<ul>
<?php
echo "<h2>".$small['Small']['small']."</h2>";
foreach($serials as $data):
	echo "<li>";
	echo $this->Html->link($data['number'],array('controller'=>'expenses','action'=>'view',$data['number']));
	echo '('.$data['date'].')';
	echo "</li>";
endforeach;
?>
</ul>