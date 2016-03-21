<?php

class Big extends AppModel{
	var $belongsTo = array('Dept');
	var $hasMany = array('Small'=>array('dependent'=>true),'Expense');
}
