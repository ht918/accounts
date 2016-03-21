<?php

class SmallsController extends AppController {
	public $helpers = array('Html','Form');

	public $uses = array('User','Dept','Big','Small','Expense');
	
	public function index(){
		$this->redirect(array('controller'=>'users','action'=>'index'));
	}

	public function beforeFilter() {
		parent::beforeFilter();
	}

	public function add(){
		$this->set('title_for_layout','新規小項目登録');
		if($this->request->is('post')){
			$conditions = array('id'=>$this->Auth->user('id'));
			$user = $this->User->find('first',array('conditions'=>$conditions));
			if($user['User']['role'] != 'admin'){
				$this->Session->setFlash('登録権限がありません');
				$this->redirect(array('controller'=>'users','action'=>'index'));
			}
			if($this->Small->save($this->request->data)){
				$this->Session->setFlash('小項目登録に成功しました');
				$this->set('depts',$this->Dept->find('list',array('fields'=>array('id','dept'))));
				$this->set('bigs',$this->Big->find('all'));
//				$this->redirect(array('controller'=>'smalls','action'=>'add'));
			}else{
				$this->Session->setFlash('小項目登録に失敗しました');
			}
		}else{
			$this->set('depts',$this->Dept->find('list',array('fields'=>array('id','dept'))));
			$this->set('bigs',$this->Big->find('all'));
		}
	}
	
	public function view(){
		$this->Dept->recursive = 2;
		$this->set('depts',$this->Dept->find('all'));
	}

	public function budget(){
		if($this->Auth->user('role') != 'admin'){
			$this->Session->setFlash('編集権限がありません');
			$this->redirect(array('controller'=>'user','action'=>'index'));
		}
		$this->Dept->recursive = 2;
		$this->set('depts',$this->Dept->find('all'));
		if($this->request->is('post')){
			$this->Small->saveAll($this->request->data['Small']);
		}else{
			$datas = $this->Small->find('all');
			foreach($datas as $data):
				$this->request->data['Small'][$data['Small']['id']] = $data['Small'];
			endforeach;
		}
	}
	public function smalllist($id = null){
		if($user['User']['role'] == 'author' && empty($user['User']['post'])){
			$this->Session->setFlash('閲覧権限がありません');
			$this->redirect(array('controller'=>'user','action'=>'index'));
		}
		if(empty($id)){
			$this->Session->setFlash('idを選択してください');
			$this->redirect(array('controller'=>'users','action'=>'index'));
		}
		$conditions = array('`Small`.`id`' => $id);
		$this->set('small',$this->Small->find('first',array('conditions'=>$conditions)));
		
		$conditions = array('`Expense`.`small_id`' => $id);
		$datas = $this->Expense->find('all',array('conditions'=>$conditions));
		if(empty($datas)){
			$this->Session->setFlash('該当内訳項目の支出は存在しません');
			$this->redirect(array('action'=>'check'));
		}
		$serials = array();
		foreach($datas as $key => $data):
			$serNum = date('m',strtotime($data['Expense']['date'])).str_pad($data['Expense']['no'],3,'0',STR_PAD_LEFT);
			$serial = array('number' => $serNum,'date' => $data['Expense']['date']);
			if(!in_array($serial,$serials)) array_push($serials,$serial);
		endforeach;
		foreach($serials as $key => $value):
			$key_id[$key] = $value['number'];
		endforeach;
		array_multisort($key_id,SORT_ASC,$serials);
		$this->set('serials',$serials);
	}
	
	public function check(){
		$this->set('title_for_layout','支出額確認');
		$this->Dept->recursive = 2;
		$this->set('depts',$this->Dept->find('all'));
		$this->set('expenses',$this->Expense->find('all',array('fields'=>array('small_id','subtotal','approval'),'order'=>array('small_id'=>'asc'))));
		$conditions = array('id'=>$this->Auth->user('id'));
		$user = $this->User->find('first',array('conditions'=>$conditions));
		if($user['User']['role'] == 'author' && empty($user['User']['post'])){
			$this->Session->setFlash('閲覧権限がありません');
			$this->redirect(array('controller'=>'user','action'=>'index'));
		}
	}
	
	public function edit($id = null){
		$this->set('title_for_layout','小項目編集');
		if($this->request->is('post')){
			$conditions = array('id'=>$this->Auth->user('id'));
			$user = $this->User->find('first',array('conditions'=>$conditions));
			if($user['User']['role'] != 'admin'){
				$this->Session->setFlash('編集権限がありません');
				$this->redirect(array('controller'=>'users','action'=>'index'));
			}
			if($this->Small->save($this->request->data)){
				$this->Session->setFlash('小項目変更に成功しました');
				$this->redirect(array('controller'=>'smalls','action'=>'view'));
			}else{
				$this->Session->setFlash('小項目変更に失敗しました');
			}
		}else{
			$this->set('depts',$this->Dept->find('list',array('fields'=>array('id','dept'))));
			$this->set('bigs',$this->Big->find('all'));
			if(empty($id)){
				$this->redirect(array('action'=>'view'));
			}
			$this->Small->id = $id;
			$this->request->data = $this->Small->read();
		}
	}
	
	public function delete($id = null){
		$this->request->onlyAllow('post');
		if(empty($id)){
			$this->Session->setFlash('該当する項目はありません');
			$this->redirect(array('action'=>'view'));
		}
		$conditions = array('id'=>$this->Auth->user('id'));
		$this->User->id = $this->Auth->user('id');
		$user = $this->User->read();
		if($user['User']['role'] != 'admin'){
			$this->Session->setFlash('削除権限がありません');
			$this->redirect(array('action'=>'view'));
		}
		$this->Small->id = $id;
		if($this->Small->delete($id)){
			$this->Session->setFlash('削除に成功しました');
		}else{
			$this->Session->setFlash('削除に失敗しました');
		}
		$this->redirect(array('action'=>'view'));
	}
}
