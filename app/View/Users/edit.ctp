<h3>ユーザー情報編集</h3>
<?php
 echo $this->Form->create('User');
 echo $userdata['User']['name'];
 echo $this->Form->input('post',array('label'=>'把握部署(改行区切り)'));
 echo $this->Form->input('role',array('label'=>'編集権限','type'=>'select','options'=>array('author'=>'author','admin'=>'admin','account'=>'account')));
 echo $this->Form->end('変更');
 
 ?>
</div>
