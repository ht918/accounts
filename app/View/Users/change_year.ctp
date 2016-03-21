<?php
	debug(getenv("ACNT_SYS_YEAR"));
	echo $this->Form->create('User',array('action'=>'changeYear'));
	echo $this->Form->year('change',2015,2016);
	echo $this->Form->end('設定');