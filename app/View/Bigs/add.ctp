<h2>新規大項目登録</h2>
<?php
echo $this->Form->create('Big');
echo $this->Form->input('dept_id',array('type'=>'select','options'=>$depts,'label'=>'部門'));
echo $this->Form->input('big',array('label'=>'大項目'));
echo $this->Form->end('登録');