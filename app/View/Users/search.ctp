<?php
if(!isset($users)){
	echo $this->Form->create('User');
	echo $this->Form->input('name');
	echo $this->Form->end('検索');
}else{
	foreach($users as $user):
		echo $this->Html->link($user['User']['name'],array('action'=>'view',$user['User']['id']));
		echo '<br />';
	endforeach;
} 
 ?>