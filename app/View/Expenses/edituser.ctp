<h2>変更後支出者選択</h2>
<?php
echo $this->Form->create('Expense');
echo $this->Form->input('newuser',array('label'=>'支出者氏名','type'=>'select','options'=>$userlist));
echo $this->Form->hidden('no',array('value'=>$no));
echo $this->Form->end('変更');
?>