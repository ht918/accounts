<?php


class DeptsController extends AppController {
	public $helpers = array('Html','Form');

	public $uses = array('User','Dept');
	
	public function index(){
		$this->redirect(array('controller'=>'users','action'=>'index'));
	}

	public function beforeFilter() {
		parent::beforeFilter();
	}

	public function add(){
		$this->set('title_for_layout','新規部門登録');
		if($this->request->is('post')){
			$conditions = array('id'=>$this->Auth->user('id'));
			$user = $this->User->find('first',array('conditions'=>$conditions));
			if($user['User']['role'] != 'admin'){
				$this->Session->setFlash('登録権限がありません');
				$this->redirect(array('controller'=>'users','action'=>'index'));
			}
			if($this->Dept->save($this->request->data)){
				$this->Session->setFlash('部門登録に成功しました');
				$this->redirect(array('controller'=>'users','action'=>'index'));
			}else{
				$this->Session->setFlash('部門登録に失敗しました');
			}
		}
	}
	
}
