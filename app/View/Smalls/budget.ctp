<h2>予算額登録</h2>
<ul>
<?php
echo $this->Form->create('Small');
foreach($depts as $dept):
	echo '<li>'.$dept['Dept']['dept'].'</li>';
	echo '<ul>';
	foreach($dept['Big'] as $big):
		echo '<li>'.$big['big'].'</li>';
		echo '<ul>';
		foreach($big['Small'] as $small):
			echo '<li>'.$small['small'];
			echo $this->Form->hidden('Small.'.$small['id'].'.id',array('value' => $small['id']));
			echo $this->Form->input('Small.'.$small['id'].'.budget',array('label'=>'予算額'));
			echo '</li>';
		endforeach;
		echo '</ul>';
	endforeach;
	echo '</ul>';
endforeach;
echo $this->Form->end('保存');
