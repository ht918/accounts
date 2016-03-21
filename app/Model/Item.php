<?php

class Item extends AppModel{
	var $belongsTo = array('Small');
	var $hasMany = array('Expense');
}
