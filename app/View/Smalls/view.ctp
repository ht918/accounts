<h2>小項目一覧</h2>
<ul>
<?php
foreach($depts as $dept):
	echo '<li>'.$dept['Dept']['dept'].'</li>';
	echo '<ul>';
	foreach($dept['Big'] as $big):
		echo '<li>'.$big['big'].' '.$this->Html->link('編集',array('controller'=>'bigs','action'=>'edit',$big['id'])).' '.$this->Form->postLink('削除',array('controller'=>'bigs','action'=>'delete',$big['id']),array('confirm'=>'削除してもよろしいですか？')).'</li>';
		echo '<ul>';
		foreach($big['Small'] as $small):
			echo '<li>'.$small['small'].' '.$this->Html->link('編集',array('action'=>'edit',$small['id'])).' '.$this->Form->postLink('削除',array('action'=>'delete',$small['id']),array('confirm'=>'削除してもよろしいですか？')).'</li>';
		endforeach;
		echo '</ul>';
	endforeach;
	echo '</ul>';

endforeach;
