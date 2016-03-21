<h2>予算額登録</h2>
<ul>
<?php
foreach($depts as $dept):
	echo '<a href = "#'.$dept['Dept']['id'].'">'.$dept['Dept']['dept'].'</a>';
endforeach;
echo '<br />';
foreach($depts as $dept):
	echo $this->Form->create('Item');
	echo '<a name = "'.$dept['Dept']['id'].'"></a>';
	echo '<h3>'.$dept['Dept']['dept'].'</h3>';
	echo '<ul>';
	foreach($dept['Big'] as $big):
		echo '<li>'.$big['big'].'</li>';
		echo '<ul>';
		foreach($big['Small'] as $small):
			echo '<li>'.$small['small'].'</li>';
			echo '<ul>';
			foreach($small['Item'] as $item):
				echo '<li>'.$item['item'];
				echo $this->Form->hidden('Item.'.$item['id'].'.id',array('value' => $item['id']));
				echo $this->Form->hidden('Item.'.$item['id'].'.small_id',array('value' => $item['small_id']));
				echo $this->Form->input('Item.'.$item['id'].'.budget',array('label'=>'予算額'));
				echo '</li>';
			endforeach;
			echo '</ul>';
		endforeach;
		echo '</ul>';
	endforeach;
	echo '</ul>';
	echo $this->Form->end('保存');
endforeach;
