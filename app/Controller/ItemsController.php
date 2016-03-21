<?php


class ItemsController extends AppController {
	public $helpers = array('Html','Form');

	public $uses = array('User','Dept','Big','Small','Expense','Item');
	
	public function index(){
		$this->redirect(array('controller'=>'users','action'=>'index'));
	}
	

	public function beforeFilter() {
		parent::beforeFilter();
	}

	public function add(){
		$this->set('title_for_layout','新規内訳登録');
		if($this->request->is('post')){
			$conditions = array('id'=>$this->Auth->user('id'));
			$user = $this->User->find('first',array('conditions'=>$conditions));
			if($user['User']['role'] != 'admin'){
				$this->Session->setFlash('登録権限がありません');
				$this->redirect(array('controller'=>'users','action'=>'index'));
			}
			if($this->Item->save($this->request->data)){
				$this->Session->setFlash('小項目登録に成功しました');
				$this->set('depts',$this->Dept->find('list',array('fields'=>array('id','dept'))));
				$this->set('bigs',$this->Big->find('all'));
				$this->set('smalls',$this->Small->find('all'));
			}else{
				$this->Session->setFlash('小項目登録に失敗しました');
			}
		}else{
			$this->set('depts',$this->Dept->find('list',array('fields'=>array('id','dept'))));
			$this->set('bigs',$this->Big->find('all'));
			$this->set('smalls',$this->Small->find('all'));
		}
	}
	
	public function view(){
		$this->Dept->recursive = 3;
		$this->Dept->unbindModel(array('hasMany'=>array('Expense')));
		$this->Big->unbindModel(array('hasMany'=>array('Expense')));
		$this->Small->unbindModel(array('hasMany'=>array('Expense')));
		$this->Item->unbindModel(array('hasMany'=>array('Expense')));
		$this->set('depts',$this->Dept->find('all'));
	}
	
	public function itemlist($id = null){
		if($user['User']['role'] == 'author' && empty($user['User']['post'])){
			$this->Session->setFlash('閲覧権限がありません');
			$this->redirect(array('controller'=>'user','action'=>'index'));
		}
		if(empty($id)){
			$this->Session->setFlash('idを選択してください');
			$this->redirect(array('controller'=>'users','action'=>'index'));
		}
		$conditions = array('`Item`.`id`' => $id);
		$this->set('item',$this->Item->find('first',array('conditions'=>$conditions)));
		
		$conditions = array('item_id' => $id);
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

	public function budget(){
		if($this->Auth->user('role') != 'admin'){
			$this->Session->setFlash('編集権限がありません');
			$this->redirect(array('controller'=>'user','action'=>'index'));
		}
		$this->Dept->recursive = 3;
		$this->Dept->unbindModel(array('hasMany'=>array('Expense')));
		$this->Big->unbindModel(array('hasMany'=>array('Expense')));
		$this->set('depts',$this->Dept->find('all'));
		if($this->request->is('post')){
			$smalls = array();
			$smallids = array();
			foreach($this->request->data['Item'] as $data):
				$smalldata = array('id' => $data['small_id'],'budget' => (int)$data['budget']);
				if(in_array($data['small_id'],$smallids)){
					foreach($smalls as $key => $small):
						if($small['id'] === $smalldata['id']){
							$smalls[$key]['budget'] += $smalldata['budget'];
							break;
						}
					endforeach;
				}else{
					array_push($smallids,$smalldata['id']);
					array_push($smalls,$smalldata);
				}
			endforeach;
			if($this->Item->saveAll($this->request->data['Item']) && $this->Small->saveAll($smalls)){
				$this->Session->setFlash('内訳額更新成功');
				$this->redirect(array('action'=>'budget'));
			}else{
				$this->Session->setFlash('内訳額更新失敗');
				$this->redirect(array('action'=>'budget'));
			}
		}else{
			$datas = $this->Item->find('all');
			foreach($datas as $data):
				$this->request->data['Item'][$data['Item']['id']] = $data['Item'];
			endforeach;
		}
	}
	
	public function check(){
		$this->set('title_for_layout','支出額確認');
		$this->Dept->recursive = 3;
		$this->Dept->unbindModel(array('hasMany'=>array('Expense')));
		$this->Big->unbindModel(array('hasMany'=>array('Expense')));
		$this->set('depts',$this->Dept->find('all'));
		$this->set('expenses',$this->Expense->find('all',array('fields'=>array('small_id','item_id','subtotal','approval'))));
		$conditions = array('id'=>$this->Auth->user('id'));
		$user = $this->User->find('first',array('conditions'=>$conditions));
		if($user['User']['role'] == 'author' && empty($user['User']['post'])){
			$this->Session->setFlash('閲覧権限がありません');
			$this->redirect(array('controller'=>'user','action'=>'index'));
		}
	}

	public function closing(){
		$this->Dept->recursive = 3;
		$this->Dept->unbindModel(array('hasMany'=>array('Expense')));
		$this->Big->unbindModel(array('hasMany'=>array('Expense')));
		$depts = $this->Dept->find('all');
		$expenses = $this->Expense->find('all',array('fields'=>array('small_id','item_id','price','number','subtotal','approval','admission','receipt')));
		$conditions = array('id'=>$this->Auth->user('id'));
		$user = $this->User->find('first',array('conditions'=>$conditions));
		if($user['User']['role'] == 'author' && empty($user['User']['post'])){
			$this->Session->setFlash('閲覧権限がありません');
			$this->redirect(array('controller'=>'user','action'=>'index'));
		}
		$smallsum = array();
		$smallprice = array();
		$smallcount = array();
		$smallflag = array();
		$itemsum = array();
		$itemprice = array();
		$itemcount = array();
		$itemflag = array();
		foreach($expenses as $expense):
			if(!$expense['Expense']['approval'] && $expense['Expense']['receipt'] && $expense['Expense']['admission']){
				if(!array_key_exists($expense['Expense']['small_id'],$smallsum)){
					$smallsum[$expense['Expense']['small_id']] = 0;
					$smallprice[$expense['Expense']['small_id']] = $expense['Expense']['price'];
					$smallcount[$expense['Expense']['small_id']] = $expense['Expense']['number'];
					$smallflag[$expense['Expense']['small_id']] = true;
				}else{
					if($smallflag[$expense['Expense']['small_id']]){
						if($smallprice[$expense['Expense']['small_id']] !== $expense['Expense']['price']){
							$smallflag[$expense['Expense']['small_id']] = false;
						}else{
							$smallcount[$expense['Expense']['small_id']] += $expense['Expense']['number'];
						}
					}
				}
				$smallsum[$expense['Expense']['small_id']] += $expense['Expense']['subtotal'];

				if(!empty($expense['Expense']['item_id'])){
					if(!array_key_exists($expense['Expense']['item_id'],$itemsum)){
						$itemsum[$expense['Expense']['item_id']] = 0;
						$itemprice[$expense['Expense']['item_id']] = $expense['Expense']['price'];
						$itemcount[$expense['Expense']['item_id']] = $expense['Expense']['number'];
						$itemflag[$expense['Expense']['item_id']] = true;
					}else{
						if($itemflag[$expense['Expense']['item_id']]){
							if($itemprice[$expense['Expense']['item_id']] !== $expense['Expense']['price']){
								$itemflag[$expense['Expense']['item_id']] = false;
							}else{
								$itemcount[$expense['Expense']['item_id']] += $expense['Expense']['number'];
							}
						}
					}
					$itemsum[$expense['Expense']['item_id']] += $expense['Expense']['subtotal'];
				}
			}
		endforeach;
		$data = array();
		foreach($depts as $dept):
			array_push($data,array($dept['Dept']['dept']));
			foreach($dept['Big'] as $big):
				array_push($data,array($big['big']));
				foreach($big['Small'] as $small):
					$tmpdata = array();
					if(!empty($small['Item'])){
						array_push($tmpdata,$small['small'].'(以下内訳)');
					}else{
						array_push($tmpdata,$small['small']);
					}
					$smallnum = 0;
					$smallpri = 0;
					$tmpsmallsum = 0;
					if(array_key_exists($small['id'],$smallflag) && $smallflag[$small['id']]){
						$smallpri = $smallprice[$small['id']];
						$smallnum  =$smallcount[$small['id']];
					}
					$tmpsmallsum = $smallsum[$small['id']];
					array_push($tmpdata,'\\'.number_format($smallpri));
					array_push($tmpdata,number_format($smallnum));
					array_push($tmpdata,'\\'.number_format($tmpsmallsum));
					array_push($data,$tmpdata);
					foreach($small['Item'] as $item):
						$tmpdata = array();
						array_push($tmpdata,' '.$item['item']);
						$itemnum = 0;
						$itempri = 0;
						$tmpitemsum = 0;
						if(array_key_exists($item['id'],$itemflag) && $itemflag[$item['id']]){
							$itempri = $itemprice[$item['id']];
							$itemnum = $itemcount[$item['id']];
						}
						$tmpitemsum = $itemsum[$item['id']];
						array_push($tmpdata,'\\'.number_format($itempri));
						array_push($tmpdata,number_format($itemnum));
						array_push($tmpdata,'\\'.number_format($tmpitemsum));
						array_push($data,$tmpdata);
					endforeach;
				endforeach;
			endforeach;
		endforeach;
		$this->layout = false;
		$this->set('data',$data);
	}
	
	public function edit($id = null){
		$this->set('title_for_layout','内訳編集');
		if($this->request->is('post')){
			$conditions = array('id'=>$this->Auth->user('id'));
			$user = $this->User->find('first',array('conditions'=>$conditions));
			if($user['User']['role'] != 'admin'){
				$this->Session->setFlash('編集権限がありません');
				$this->redirect(array('controller'=>'users','action'=>'index'));
			}
			if($this->Item->save($this->request->data)){
				$this->Session->setFlash('内訳変更に成功しました');
				$this->redirect(array('controller'=>'items','action'=>'view'));
			}else{
				$this->Session->setFlash('内訳変更に失敗しました');
			}
		}else{
			$this->set('depts',$this->Dept->find('list',array('fields'=>array('id','dept'))));
			$this->set('bigs',$this->Big->find('all'));
			$this->set('smalls',$this->Small->find('all'));
			if(empty($id)){
				$this->redirect(array('action'=>'view'));
			}
			$this->Item->id = $id;
			$this->request->data = $this->Item->read();
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
		$this->Item->id = $id;
		if($this->Item->delete($id)){
			$this->Session->setFlash('削除に成功しました');
		}else{
			$this->Session->setFlash('削除に失敗しました');
		}
		$this->redirect(array('action'=>'view'));
	}
}
