<?php


class BigsController extends AppController {
	public $helpers = array('Html','Form');

	public $uses = array('User','Dept','Big','Small','Item','Expense');
	
	public function index(){
		$this->redirect(array('controller'=>'users','action'=>'index'));
	}

	public function beforeFilter() {
		parent::beforeFilter();
	}

	public function add(){
		$this->set('title_for_layout','新規大項目登録');
		if($this->request->is('post')){
			$conditions = array('id'=>$this->Auth->user('id'));
			$user = $this->User->find('first',array('conditions'=>$conditions));
			if($user['User']['role'] != 'admin'){
				$this->Session->setFlash('登録権限がありません');
				$this->redirect(array('controller'=>'users','action'=>'index'));
			}
			if($this->Big->save($this->request->data)){
				$this->Session->setFlash('大項目登録に成功しました');
				$this->redirect(array('controller'=>'bigs','action'=>'add'));
			}else{
				$this->Session->setFlash('大項目門登録に失敗しました');
			}
		}else{
			$this->set('depts',$this->Dept->find('list',array('fields'=>array('id','dept'))));
		}
	}
	
	public function edit($id = null){
		$this->set('title_for_layout','大項目編集');
		if($this->request->is('post')){
			$conditions = array('id'=>$this->Auth->user('id'));
			$user = $this->User->find('first',array('conditions'=>$conditions));
			if($user['User']['role'] != 'admin'){
				$this->Session->setFlash('編集権限がありません');
				$this->redirect(array('controller'=>'users','action'=>'index'));
			}
			if($this->Big->save($this->request->data)){
				$this->Session->setFlash('大項目変更に成功しました');
				$this->redirect(array('controller'=>'smalls','action'=>'view'));
			}else{
				$this->Session->setFlash('大項目変更に失敗しました');
			}
		}else{
			$this->set('depts',$this->Dept->find('list',array('fields'=>array('id','dept'))));
			if(empty($id)){
				$this->redirect(array('action'=>'view'));
			}
			$this->Big->id = $id;
			$this->request->data = $this->Big->read();
		}
	}
	public function delete($id = null){
		$this->request->onlyAllow('post');
		if(empty($id)){
			$this->Session->setFlash('該当する項目はありません');
			$this->redirect(array('controller'=>'smalls','action'=>'view'));
		}
		$conditions = array('id'=>$this->Auth->user('id'));
		$this->User->id = $this->Auth->user('id');
		$user = $this->User->read();
		if($user['User']['role'] != 'admin'){
			$this->Session->setFlash('削除権限がありません');
			$this->redirect(array('controller'=>'smalls','action'=>'view'));
		}
		$this->Big->id = $id;
		if($this->Big->delete($id)){
			$this->Session->setFlash('削除に成功しました');
		}else{
			$this->Session->setFlash('削除に失敗しました');
		}
		$this->redirect(array('controller'=>'smalls','action'=>'view'));
	}
	
	public function closing($id = null){
		if(empty($id)){
			$this->Session->setFlash('idを指定してください');
			$this->redirect(array('controller'=>'users','action'=>'index'));
		}
		$conditions = array('id'=>$this->Auth->user('id'));
		$this->User->id = $this->Auth->user('id');
		$user = $this->User->read();
		if($user['User']['role'] == 'author'){
			$this->Session->setFlash('閲覧権限がありません');
			$this->redirect(array('controller'=>'users','action'=>'index'));
		}
		$conditions = array('`Expense`.`big_id`'=>$id);
		$expenses = $this->Expense->find('all',array('conditions'=>$conditions));
		$conditions = array('`Big`.`id`'=>$id);
		$this->Big->recursive = 3;
		$this->Big->unbindModel(array('hasMany'=>array('Expense')));
		$this->Dept->unbindModel(array('hasMany'=>array('Expense')));
		$this->Small->unbindModel(array('hasMany'=>array('Expense')));
		$this->Item->unbindModel(array('hasMany'=>array('Expense')));
		$bigs = $this->Big->find('first',array('conditions'=>$conditions));
		$smallsum = array();
		$smallprice = array();
		$smallcount = array();
		$smallflag = array();
		$itemsum = array();
		$itemprice = array();
		$itemcount = array();
		$itemflag = array();
		$products = array();
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
					if(!array_key_exists($expense['Expense']['item_id'],$products)){
						$products[$expense['Expense']['item_id']] = array();
					}
					if(!array_key_exists($expense['Expense']['product'],$products[$expense['Expense']['item_id']])){
						$products[$expense['Expense']['item_id']][$expense['Expense']['product']] = array('price' => $expense['Expense']['price'],'count' => $expense['Expense']['number'],'sum' => $expense['Expense']['subtotal'],'flag'=>true,'purpose' => $expense['Expense']['purpose']);
					}else{
						$products[$expense['Expense']['item_id']][$expense['Expense']['product']]['purpose'] .= ' / ' .$expense['Expense']['purpose'];
						$products[$expense['Expense']['item_id']][$expense['Expense']['product']]['sum'] += $expense['Expense']['subtotal'];
						if($products[$expense['Expense']['item_id']][$expense['Expense']['product']]['flag']){
							if($products[$expense['Expense']['item_id']][$expense['Expense']['product']]['price'] !== $expense['Expense']['price']){
								$products[$expense['Expense']['item_id']][$expense['Expense']['product']]['flag'] = false;
							}else{
								$products[$expense['Expense']['item_id']][$expense['Expense']['product']]['count'] += $expense['Expense']['number'];
							}
						}
					}
				}
			}
		endforeach;

		$datas = array();
		array_push($datas,array(trim($bigs['Big']['big'])));
		array_push($datas,array());
		foreach($bigs['Small'] as $small):
			array_push($datas,array($small['small']));
			array_push($datas,array('項目','単価','個数','決算','備考'));
			foreach($small['Item'] as $item):
				$itemStr = $item['item'];
				if(!empty($products[$item['id']])){
					$itemStr .= '(以下内訳)';
				}
				if(array_key_exists($item['id'],$itemsum)){
					if(!$itemflag[$item['id']]){
						$itemprice[$item['id']] = null;
						$itemcount[$item['id']] = null;
					}
					array_push($datas,array($itemStr,$itemprice[$item['id']],$itemcount[$item['id']],$itemsum[$item['id']]));
					if(array_key_exists($item['id'],$products)){
						foreach($products[$item['id']] as $pname => $pvalue):
							if(!$pvalue['flag']){
								$pvalue['price'] = null;
								$pvalue['count'] = null;
							}
							array_push($datas,array(' '.trim($pname),$pvalue['price'],$pvalue['count'],$pvalue['sum'],$pvalue['purpose']));
						endforeach;
					}
				}else{
					array_push($datas,array($itemStr,'','',''));
				}
			endforeach;
			if(!array_key_exists($small['id'],$smallsum)){
				$smallsum[$small['id']] = null;
			}
			array_push($datas,array('小計','','',$smallsum[$small['id']]));
			array_push($datas,array());
		endforeach;
		$this->layout = false;
		$this->set('data',$datas);
		$this->set('big',$bigs['Big']['big']);
	}
}
