<?php

class Expense extends AppModel{
	var $belongsTo = array('User','Dept','Big','Small','Item');
	
	public $validate = array(
		'method' => array(
			'valid'=>array(
				'rule' => array('inList', array('立替','前払','振込'))
			)
		)
	);
}
