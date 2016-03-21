<h2>新規部門登録</h2>
<?php
echo $this->Form->create('Dept');
echo $this->Form->input('dept',array('label'=>'部門'));
echo $this->Form->end('登録');