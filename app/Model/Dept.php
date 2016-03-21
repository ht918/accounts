<?php

class Dept extends AppModel{
	var $hasMany = array('Big'=>array('dependent'=>true),'Expense');
}
