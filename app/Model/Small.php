<?php

class Small extends AppModel{
	var $belongsTo = array('Big');
	var $hasMany = array('Item'=>array('dependent'=>true),'Expense');
}
