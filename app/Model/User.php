<?php
App::uses('SimplePasswordHasher', 'Controller/Component/Auth');

class User extends AppModel{

	public $hasMany = "Expense";
	
	public $validate = array(
		'google_id' => array(
			'isunique' => array(
				'rule' => 'isUnique',
			)
		),
		'email' => array(
			'rule' => array('email',true,"/(\W|^)[\w.+\-]{0,25}@(wasedasai)\.net(\W|$)/"),
			'message' => 'wasedasai.netのアドレスを使用してください'
		),
		'role' => array(
			'valid' => array(
				'rule' => array('inList', array('admin', 'author','account')),
				'message' => 'Please enter a valid role',
				'allowEmpty' => false
			)
		)
	);


	public function beforeSave($options = array()) {
		if (isset($this->data[$this->alias]['password'])) {
			$this->data[$this->alias]['password'] = AuthComponent::password($this->data[$this->alias]['password']);
		}
		return true;
	}
	
	public function signin($token){
 
		$data['google_id'] = $token['id'];
		$data['email'] = $token['email'];
		$data['name'] = $token['name'];
		$data['family_name'] = $token['family_name'];
		$data['given_name'] = $token['given_name'];
		$data['role'] = 'author';
		if($data['email'] == 'kaikeiweb@wasedasai.net'){
			$data['role'] = 'admin';
		}
		
		$this->set($data);

		//バリデーションチェックでエラーがなければ、新規登録
		if($this->validates()){
			$this->save($data);
			$data['error'] = 'saved';
		}else{
			$str = null;
			foreach($this->validationErrors as $error):
				if(empty($str)){
					$str = $error[0];
				}else{
					$str = $str . ' / ' . $error[0];
				}
			endforeach;
			$data['error'] = $str;
		}
		return $data; //ログイン情報
	}

}