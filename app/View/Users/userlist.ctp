<?php
foreach($users as $user):
	echo $this->Html->link($user['User']['name'],array('action'=>'view',$user['User']['id']));
	echo '<br />';
endforeach;