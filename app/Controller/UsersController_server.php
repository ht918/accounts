<?php

define('client_id' , "6452672906-pq86vfa9389nfp6q74q82pctjeir5781.apps.googleusercontent.com");
define('redirect_uri' , "http://www.wasedasai.net/websystem/users/oauth_callback");
define('auth_url' , 'https://accounts.google.com/o/oauth2/auth');
define('client_secret' , "QOMhgOwDITvWcm0ZT6KD-rvj");
define('TOKEN_URL', 'https://accounts.google.com/o/oauth2/token');
define('INFO_URL', 'https://www.googleapis.com/oauth2/v1/userinfo');

class UsersController extends AppController {
	public $helpers = array('Html','Form');

	public $uses = array('User','Expense');
	
	public $components = array(
		'Auth'=> array(
			'authenticate'=>array(
				'Form'=>array(
					'userModel'=>'User',
				'fields'=>array('username'=>'email','password'=>'password')
				)
			)
		)
	);
	public function index(){
		$this->set('title_for_layout','ホーム');
		$this->User->id = $this->Auth->user('id');
		$conditions = array('user_id' => $this->Auth->user('id'));
		$this->set('expenses',$this->Expense->find('all',array('conditions'=>$conditions)));
		if(!$this->User->exists()){
			throw new NotFoundException(__('Invalid user'));
		}
		$this->set('user',$this->User->read());
		$user = $this->User->read();
		$admissions = array();
		$admitted = array();
		if(!empty($user['User']['post'])){
			$posts = explode(PHP_EOL,$user['User']['post']);
			foreach($posts as $post):
				$conditions = array('team' => $post,'admission'=>false);
				$data = $this->Expense->find('all',array('conditions'=>$conditions));
				if(!empty($data)){
					array_push($admissions,$data);
				}
				$conditions = array('team' => $post,'admission'=>true);
				$data = $this->Expense->find('all',array('conditions'=>$conditions));
				if(!empty($data)){
					array_push($admitted,$data);
				}
			endforeach;
			if(!empty($admissions)){
				$this->set('admissions',$admissions);
			}
			if(!empty($admitted)){
				$this->set('admitted',$admitted);
			}
		}
	}
	
	public function search(){
		$this->set('title_for_layout','ユーザー検索');

		$this->User->id = $this->Auth->user('id');
		$authority = $this->User->read();

		if($authority['User']['role'] == 'admin'){
			if(!$this->request->is('get')){
				$conditions = array('name LIKE'=> '%' . $this->request->data['User']['name'] . '%');
				$this->set('users',$this->User->find('all',array('conditions'=>$conditions)));
			}
		}else{
			$this->Session->setFlash('権限がありません');
			$this->redirect(array('action'=>'index'));
		}
	}
	
	public function refund($id = null){
		$this->set('title_for_layout','返金情報');
		if(empty($id)){
			if(!empty($this->request->data['User']['searchname'])){
				$conditions = array('name LIKE'=> '%' . $this->request->data['User']['searchname'] . '%');
				$this->set('userlist',$this->User->find('list',array('conditions'=>$conditions,'fields'=>array('id','name'))));
				unset($this->request->data['User']['searchname']);
			}else if(!empty($this->request->data['User']['newuser'])){
				$this->redirect(array('action'=>'refund',$this->request->data['User']['newuser']));
				unset($this->request->data['User']['newuser']);
			}else{
				$this->redirect(array('action'=>'refund',$this->Auth->user('id')));
			}
		}
		$this->User->id = $id;
		$this->set('user',$this->User->read());
		$conditions = array('user_id' => $id,'method' => '立替','reported' => true,'approval' => false,'admission' => true,'receipt' => true,'refunded' => false);
		$this->set('expenses',$this->Expense->find('all',array('conditions'=>$conditions)));
		$conditions = array('user_id' => $id);
		$this->set('authuser',$this->Auth->user('role'));
	}
	
	public function set_refunded($id = null){
		if(empty($id) || !$this->request->is('post') || $this->Auth->user('role') == 'author'){
			$this->Session->setFlash('この操作は無効です');
			$this->redirect(array('controller'=>'users','action'=>'index'));
		}else{
			$conditions = array('user_id' => $id,'method'=>'立替','reported' => true,'approval' => false,'admission' => true,'receipt' => true);
			$expenses = $this->Expense->find('all',array('conditions'=>$conditions));
			foreach($expenses as $expense):
				$expense['Expense']['refunded'] = true;
				$this->Expense->id = $expense['Expense']['id'];
				$this->Expense->save($expense);
			endforeach;
			$this->Session->setFlash('返金済み処理が完了しました');
			$this->redirect(array('controller'=>'users','action'=>'index'));
		}
	}
	
	public function refundlist($flag = true){
		$this->User->id = $this->Auth->user('id');
		$user = $this->User->read();
		if($user['User']['role'] == 'author' && empty($user['User']['post'])){
			$this->Session->setFlash('閲覧権限がありません');
			$this->redirect(array('action'=>'index'));
		}
		$userdata = $this->User->find('all');
		$datas = array();
		$flag = (bool)$flag;
		$i = 0;
		foreach($userdata as $user):
			$expense = $user['Expense'];
			$datas[$i] = array($user['User']['name'],0,);
			$count = 0;
			$s = $i;
			foreach($expense as $exp):
				if(!$exp['admission'] || !$exp['receipt'] || $exp['approval'] || $exp['refunded'] == $flag || $exp['method'] !== '立替') continue;
				$datas[$s][1] += (int)$exp['subtotal'];
				array_push($datas[$i],$exp['product']);
				array_push($datas[$i],$exp['subtotal']);
				array_push($datas[$i],$exp['date']);
				$month = date('m',strtotime($exp['date']));
				array_push($datas[$i],str_pad($month,2,'0',STR_PAD_LEFT).str_pad($exp['no'],3,'0',STR_PAD_LEFT));
				$count++;
				if($count === 8){
					$count = 0;
					$i++;
					$datas[$i] = array($user['User']['name'],null,);
				}
			endforeach;
			if($datas[$i] ===  array($user['User']['name'],null,)){
				unset($datas[$i]);
			}else if($datas[$s][1] === 0){
				unset($datas[$s]);
			}else{
				$i++;
			}
		endforeach;
		$this->set('data',$datas);
		$this->layout = false;
	}
	
	public function userlist(){
		$this->set('title_for_layout','ユーザーリスト');

		$this->User->id = $this->Auth->user('id');
		$authority = $this->User->read();

		if($authority['User']['role'] == 'admin'){
			$this->set('users',$this->User->find('all'));
		}else{
			$this->Session->setFlash('権限がありません');
			$this->redirect(array('action'=>'index'));
		}
	}
	
	public function edit($id = null){
		$this->set('title_for_layout','ユーザー情報編集');

		$this->User->id = $this->Auth->user('id');
		$authority = $this->User->read();

		if($authority['User']['role'] == 'admin'){
			if($this->request->is('get')){
				$conditions = array('id'=>$id);
				$userdata = $this->User->find('first',array('conditions'=>$conditions));
				$this->set('userdata',$this->User->find('first',array('conditions'=>$conditions)));
				if(empty($userdata)){
					$this->Session->setFlash('該当ユーザーは存在しません');
					$this->redirect(array('action'=>'index'));
				}else{
					$this->request->data = $userdata;
				}
			}else{
				$this->User->id = $id;
				$this->User->read();
				$post = $this->request->data['User']['post'];
				$post = explode(PHP_EOL,$post);
				$count = count($post);
				for($i = 0;$i<$count;$i++){
					if(empty($post[$i])){
						unset($post[$i]);
					}
				}
				$post = implode(PHP_EOL,$post);
				$this->request->data['User']['post'] = $post;
				if($this->User->save($this->request->data)){
					$this->Session->setFlash('ユーザー情報を変更しました');
					$this->redirect(array('action'=>'view',$id));
				}else{
					$this->Session->setFlash('変更内容の保存に失敗しました');
					$this->redirect(array('action'=>'view',$id));
				}
			}
		}else{
			$this->Session->setFlash('権限がありません');
			$this->redirect(array('action'=>'index'));
		}
	}
	
	public function view($id = null){
		$this->set('title_for_layout','ユーザー詳細');

		$this->User->id = $this->Auth->user('id');
		$authority = $this->User->read();

		if($authority['User']['role'] == 'admin' || $id == $this->Auth->user('id')){
			$conditions = array('id'=>$id);
			$this->set('userdata',$this->User->find('first',array('conditions'=>$conditions)));
		}else{
			$this->Session->setFlash('権限がありません');
			$this->redirect(array('action'=>'index'));
		}
		
	}


	public function beforeFilter() {
		parent::beforeFilter();
		$this->Auth->allow('add','login','logout','oauth_callback');
		$this->Auth->autoRedirect = false;
	}


	public function logout() {
		$this->Session->destroy(); //セッションを完全削除
		$this->Session->setFlash(__('ログアウトしました'));
		$this->redirect($this->Auth->logout());
	}

	public function login(){

		$this->set('title_for_layout','ログイン');
		if($this->request->is('post')){
			$params = array(
				'client_id' => client_id,
				'redirect_uri' => redirect_uri,
				'scope' => 'openid profile email',
				'response_type' => 'code',
			);
			$this->redirect(auth_url . '?' . http_build_query($params));
		}
	}

	function oauth_callback() {
	
		if(!isset($_GET['code'])){
			$this->Session->setFlash('error!');
			$this->redirect(array('action'=>'login'));
		}

		$params = array(
			'code' => $_GET['code'],
			'grant_type' => 'authorization_code',
			'redirect_uri' => redirect_uri,
			'client_id' => client_id,
			'client_secret' => client_secret,
		);
		
		$code = $params['code'];
		
		$params = http_build_query($params);
		
		$header = array(
			"Content-Type: application/x-www-form-urlencoded",
			"Content-Length: ".strlen($params)
		);

		$options = array('http' => array(
			'method' => 'POST',
			'header' => implode("\r\n",$header),
			'content' => $params
		));

		if($res = @file_get_contents(TOKEN_URL, false, stream_context_create($options))){
		}else{
			$this->Session->setFlash('get contents error!');
			$this->redirect(array('action'=>'login'));
		}

		$token = json_decode($res, true);
		
		if(isset($token['error'])){
			echo 'エラー発生';
			$this->Session->setFlash('error');
			$this->set('accesstoken','error!');
			exit;
		}

		$access_token = $token['access_token'];

		$params = array('access_token' => $access_token);

		if($res = @file_get_contents(INFO_URL . '?' . http_build_query($params))){
		}else{
			$this->Session->setFlash('get contents error!');
			$this->redirect(array('action'=>'login'));
		}
		
		$this->set('accesstoken',$code);
		$this->set('data',json_decode($res,true));
		if(isset($token['error'])){
			$this->Session->setFlash('get contents error!');
			$this->redirect(array('action'=>'login'));
		}
		
		$data = $this->User->signin(json_decode($res,true));
		
		$login_user = $this->User->findByGoogleId($data['google_id']);

		if(!empty($login_user) && $this->Auth->login($login_user['User'])){
			$this->redirect($this->Auth->redirect());
		}else{
			$this->Session->setFlash($data['error']);
		}
	
	}

}