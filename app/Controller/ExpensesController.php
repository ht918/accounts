﻿<?php

define('thisYEAR',2015); //年度始まりの年

class ExpensesController extends AppController {
	public $helpers = array('Html','Form','Csv');

	public $uses = array('User','Expense','Dept','Big','Small','Item');
	
	public function index(){
		$this->set('title_for_layout','ホーム');
		$this->User->id = $this->Auth->user('id');
		if(!$this->User->exists()){
			throw new NotFoundException(__('Invalid user'));
		}
		$this->set('user',$this->User->read());
	}

	public function emReplace($str){
		$str = str_replace("（","(",$str);
		$str = str_replace("）",")",$str);
		$str = mb_convert_kana($str,'rns');
		return $str;
	}

	public function beforeFilter() {
		parent::beforeFilter();
	}
	
	public function sumCheck($data){ //小計額の合計と合計額のチェック
		$sum = 0;
		for($i = 1;$i < $data['Expense']['n']; $i++){
			$sum += $data['Expense']['subtotal'.$i];
		}
		return $sum === (int)$data['Expense']['total'];
	}
	
	public function getMonthStart($data){
		$dDate = date('Y-m-d',strtotime(implode('-',$data['Expense']['date'])));
		return date('Y-m-01',strtotime($dDate));
	}

	public function getMonthEnd($data){
		$dDate = date('Y-m-d',strtotime(implode('-',$data['Expense']['date'])));
		return date('Y-m-t',strtotime($dDate));
	}
	
	public function getNewNo($data){
		$start = $this->getMonthStart($data);
		$end = $this->getMonthEnd($data);
		$conditions = array('date >=' => $start,'date <=' => $end);
		$numbers = $this->Expense->find('list',array('fields'=>array('no'),'conditions'=>$conditions));
		$numarr = array();
		foreach($numbers as $number):
			if(!in_array($number,$numarr)){
				array_push($numarr,$number);
			}
		endforeach;
		asort($numarr);
		$new_no = 1;
		foreach($numarr as $num):
			if((int)$num === $new_no){
				$new_no++;
			}else{
				break;
			}
		endforeach;
		return $new_no;
	}
	
	public function saveAllDatas($data){
		$data['Expense']['purpose'] = $this->emReplace($data['Expense']['purpose']);
		$data['Expense']['team'] = $this->emReplace($data['Expense']['team']);
		$savedata = array();
		for($i = 1;$i < $data['Expense']['n']; $i++){
			$wdata = $data;
			$wdata['Expense']['big_id'] = $data['Expense']['big_id'.$i];
			$wdata['Expense']['small_id'] = $data['Expense']['small_id'.$i];
			if($data['Expense']['item_id'.$i] === -1) $data['Expense']['item_id'.$i] = null;
			$wdata['Expense']['item_id'] = $data['Expense']['item_id'.$i];
			$wdata['Expense']['product'] = $this->emReplace($data['Expense']['product'.$i]);
			$wdata['Expense']['price'] = $data['Expense']['price'.$i];
			$wdata['Expense']['number'] = $data['Expense']['number'.$i];
			$wdata['Expense']['subtotal'] = $data['Expense']['subtotal'.$i];
			array_push($savedata,$wdata);
			$data['Expense']['total'] = null;
		}
		if($this->Expense->saveMany($savedata)){
			return true;
		}else{
			return false;
		}
	}

	public function add(){
		$this->set('title_for_layout','新規支出登録');
		if($this->request->is('post')){
			$this->request->data = h($this->request->data);
			if($this->sumCheck($this->request->data)){
				$this->request->data['Expense']['user_id'] = $this->Auth->user('id');
				$no = $this->getNewNo($this->request->data);
				$this->request->data['Expense']['no'] = $no;
				if($this->saveAllDatas($this->request->data)){
					$this->Session->setFlash('支出の登録が完了しました');
					$this->redirect(array('controller'=>'expenses','action'=>'view',date('m',strtotime($this->getMonthStart($this->request->data))).str_pad($no,3,'0',STR_PAD_LEFT)));
				}else{
					$this->Session->setFlash('支出の登録に失敗しました');
				}
			}else{
				$this->Session->setFlash('合計額と小計額の合計が一致していません');
			}
		}
		$this->set('depts',$this->Dept->find('list',array('fields'=>array('id','dept'))));
		$this->set('bigs',$this->Big->find('all'));
		$this->set('smalls',$this->Small->find('all'));
		$this->set('items',$this->Item->find('all'));
	}

	

	public function view($no = null){
		$this->set('title_for_layout','支出詳細');
		if(empty($no) || mb_strlen($no) !== 5){
			$this->Session->setFlash('該当No.の支出は存在しません');
			$this->redirect(array('controller'=>'users','action'=>'index'));
		}else{
			$month = mb_substr($no,0,2,'utf-8');
			$m_no = mb_substr($no,2,3,'utf-8');
			$this->set('no',$month.$m_no);
			if($month < 4){
				$year = thisYEAR+1;
			}else{
				$year = thisYEAR;
			}
			$start = date('Y-m-d',strtotime($year.'-'.$month.'-01'));
			$end = date('Y-m-d',strtotime($year.'-'.$month.'-01'.'+1 month')-60*60*24);
			$conditions = array('no'=>$m_no,'date >=' => $start,'date <=' => $end);
			$data = $this->Expense->find('all',array('conditions'=>$conditions));
			foreach($data as $key => $value):
				if(empty($value['Expense']['item_id'])){
					$cond = array('small_id' =>$value['Expense']['small_id']);
					if($this->Item->hasAny($cond)) $data[$key]['Expense']['item_id'] = -1;
				}
			endforeach;
			$this->set('data',$data);
			$conditions = array('id'=>$data[0]['Expense']['account_id']);
			$this->set('recUser',$this->User->find('first',array('conditions'=>$conditions)));
			$conditions = array('id'=>$this->Auth->user('id'));
			$this->set('user',$this->User->find('first',array('conditions'=>$conditions)));
			$this->set('month',mb_substr($no,0,2,'utf-8'));
			$this->set('m_no',mb_substr($no,2,3,'utf-8'));
		}
	}
	

	public function edit($no = null){
		$this->set('title_for_layout','支出編集');
		if(empty($no)){ //$noが指定されていなかった場合の処理
			$this->Session->setFlash('該当No.の支出は存在しません');
			$this->redirect(array('controller'=>'users','action'=>'index'));
		}else{
			$month = mb_substr($no,0,2,'utf-8'); //月を抽出(01~12)
			$m_no = mb_substr($no,2,3,'utf-8'); //支出証明書番号下3桁を抽出
			if($month < 4){ //1~3月なら年を+1
				$year = thisYEAR+1;
			}else{
				$year = thisYEAR;
			}
			/* 支出証明書番号が$noの支出を検索 */
			$start = date('Y-m-d',strtotime($year.'-'.$month.'-01'));
			$end = date('Y-m-d',strtotime($year.'-'.$month.'-01'.'+1 month')-60*60*24);
			$conditions = array('no'=>$m_no,'date >=' => $start,'date <=' => $end);
			$usercheck = $this->Expense->find('first',array('conditions'=>$conditions));
			
			/* ログイン中のユーザー情報を取得 */
			$usercond = array('id'=>$this->Auth->user('id'));
			$userdata = $this->User->find('first',array('conditions'=>$usercond));
			$userflag = false;

			/* ユーザーが該当支出の支出把握者かどうかをチェック */
			foreach(explode(PHP_EOL,$userdata['User']['post']) as $post):
				if($post == $usercheck['Expense']['team']) $userflag = true;
			endforeach;
			
			if(!($usercheck['Expense']['user_id'] == $this->Auth->user('id') || $userflag || $userdata['User']['role'] == ('admin' || 'account'))){ //支出者or支出把握者or管理者or会計局員でないなら
				$this->Session->setFlash('編集権限がありません');
				$this->redirect(array('controller'=>'users','action'=>'index'));
			}else if($usercheck['Expense']['admission'] && $userdata['User']['role'] != ('admin' || 'account')){ //支出把握済みで管理者でも会計局員でもないなら
				$this->Session->setFlash('編集権限がありません');
				$this->redirect(array('controller'=>'users','action'=>'index'));
			}else if($usercheck['Expense']['receipt'] && !($userdata['User']['role'] == ('admin' || 'account') || $userflag) ){ //受領済みで管理者でも会計局員でもないなら
				$this->Session->setFlash('編集権限がありません');
				$this->redirect(array('controller'=>'users','action'=>'index'));
			}
			if($this->request->is('get')){
				/* 現在の情報をセット */
				$this->set('no',$no);
				$this->set('datas',$this->Expense->find('all',array('conditions'=>$conditions)));
				$this->request->data = $this->Expense->find('first',array('conditions'=>$conditions));
				$this->set('depts',$this->Dept->find('list',array('fields'=>array('id','dept'))));
				$this->set('bigs',$this->Big->find('all'));
				$this->set('smalls',$this->Small->find('all'));
				$this->set('items',$this->Item->find('all'));
				if(empty($this->request->data)){
					$this->Session->setFlash('該当No.の支出は存在しません');
					$this->redirect(array('controller'=>'users','action'=>'index'));
				}
			}else{
				$this->request->data = h($this->request->data);
				/* 月の変更をチェック */
//				$month = $this->request->data['Expense']['date']['month']; //月を抽出(01~12)
				$month = $this->request->data['Expense']['month'];
				if($month < 4){ //1~3月なら年を+1
					$year = thisYEAR+1;
				}else{
					$year = thisYEAR;
				}
				$newNo = $no;
				$start = date('Y-m-d',strtotime($year.'-'.$month.'-01')); //元々の月
				$dDate = date('Y-m-d',strtotime(implode('-',$this->request->data['Expense']['date']))); //データの支出日
				if($start != date('Y-m-01',strtotime($dDate))){ //$noの月とデータの月が一致していなかったら
					$start = date('Y-m-01',strtotime($dDate));
					$end = date('Y-m-t',strtotime($dDate));
					$newconditions = array('date >=' => $start,'date <=' => $end);
					$numbers = $this->Expense->find('all',array('conditions'=>$newconditions));
					$max_no = 0;
					foreach ($numbers as $no):
						if($no['Expense']['no'] > $max_no) $max_no = $no['Expense']['no'];
					endforeach;
					$nums = array();
					for($i = 1;$i<$max_no;$i++){
						$nums[$i] = false;
					}
					foreach($numbers as $no):
						$nums[$no['Expense']['no']] = true;
					endforeach;
					$found = false;
					for($i = 1;$i<$max_no;$i++){
						if(!$nums[$i]){
							$found = true;
							break;
						}
					}
					if($found){
						$max_no = $i;
					}else{
						$max_no++;
					}
					$this->request->data['Expense']['no'] = $max_no;
					$newNo = date('m',strtotime($start)).str_pad($max_no,3,'0',STR_PAD_LEFT);
				}
				/* 合計額と小計額の合計をチェック */
				$sum = 0;
				for($i = 1;$i < $this->request->data['Expense']['n']; $i++){
					$sum += $this->request->data['Expense']['subtotal'.$i];
				}
				$this->Expense->validate['total']  =  array(array('rule' => array('naturalNumber',false),'message' => 'ゼロより大きい値を入力してください',));
				$uid = null;
				$this->request->data['Expense']['team'] = $this->emReplace($this->request->data['Expense']['team']);
				if($sum == $this->request->data['Expense']['total']){
						$ids = $this->Expense->find('all',array('conditions'=>$conditions));
						$tmp = 0;
						for($i = 1;$i < $this->request->data['Expense']['n']; $i++){
							$data = $this->request->data;
							$this->request->data['Expense']['total'] = null;
							if($data['Expense']['item_id'.$i] == -1){
								$data['Expense']['item_id'.$i] = null;
							}
							$data['Expense']['big_id'] = $data['Expense']['big_id'.$i];
							$data['Expense']['small_id'] = $data['Expense']['small_id'.$i];
							$data['Expense']['item_id'] = $data['Expense']['item_id'.$i];
							$data['Expense']['product'] = $this->emReplace($data['Expense']['product'.$i]);
							$data['Expense']['price'] = $data['Expense']['price'.$i];
							$data['Expense']['number'] = $data['Expense']['number'.$i];
							$data['Expense']['subtotal'] = $data['Expense']['subtotal'.$i];
							$data['Expense']['purpose'] = $this->emReplace($data['Expense']['purpose'.$i]);
							if(!empty($ids[$i-1]['Expense']['id'])){
								$this->Expense->id = $ids[$i-1]['Expense']['id'];
								if(empty($uid)) $uid = $this->Expense->read();
							}else{
								$this->Expense->create();
								if(empty($data['Expense']['user_id'])){
									$data['Expense']['user_id'] = $uid['Expense']['user_id'];
									$data['Expense']['admission'] = $uid['Expense']['admission'];
									$data['Expense']['receipt'] = $uid['Expense']['receipt'];
								}
							}
							if(!$this->Expense->save($data)){
								$this->set('no',$no);
								$this->set('datas',$this->Expense->find('all',array('conditions'=>$conditions)));
								$this->request->data = $this->Expense->find('first',array('conditions'=>$conditions));
								$this->set('depts',$this->Dept->find('list',array('fields'=>array('id','dept'))));
								$this->set('bigs',$this->Big->find('all'));
								$this->set('smalls',$this->Small->find('all'));
								if(empty($this->request->data)){
									$this->Session->setFlash('該当No.の支出は存在しません');
									$this->redirect(array('controller'=>'users','action'=>'index'));
								}
								return;
							}
							$this->Expense->validate['total'] = null;
							$tmp = $i;
						}
						for($j = $tmp;$j < count($ids);$j++){
							$this->Expense->id = $ids[$j]['Expense']['id'];
							$this->Expense->delete($ids[$j]['Expense']['id']);
						}
					$this->Session->setFlash('支出の編集に成功しました');
					$this->redirect(array('controller'=>'expenses','action'=>'view',$newNo));
				}else{
					$this->Session->setFlash('合計額と小計額の合計が一致していません');
					$this->set('no',$no);
					$this->set('datas',$this->Expense->find('all',array('conditions'=>$conditions)));
					$this->request->data = $this->Expense->find('first',array('conditions'=>$conditions));
					$this->set('depts',$this->Dept->find('list',array('fields'=>array('id','dept'))));
					$this->set('bigs',$this->Big->find('all'));
					$this->set('smalls',$this->Small->find('all'));
					if(empty($this->request->data)){
						$this->Session->setFlash('該当No.の支出は存在しません');
						$this->redirect(array('controller'=>'users','action'=>'index'));
					}
				}
			}
		}
	}
	
	public function detailedit($id = null){
		$this->Expense->id = $id;
		if($this->request->is('get')){
			$this->request->data = $this->Expense->read();
			$this->set('data',$this->Expense->read());
			$data = $this->Expense->read();
			$this->request->data['Expense']['allNo'] = date('m',strtotime($data['Expense']['date'])).str_pad($data['Expense']['no'],3,'0',STR_PAD_LEFT);
			$this->set('depts',$this->Dept->find('list',array('fields'=>array('id','dept'))));
			$this->set('bigs',$this->Big->find('all'));
			$this->set('smalls',$this->Small->find('all'));
			$this->set('items',$this->Item->find('all'));
		}else{
		
			$allNo = $this->request->data['Expense']['allNo'];

			$usercheck = $this->Expense->read();

			/* ログイン中のユーザー情報を取得 */
			$usercond = array('id'=>$this->Auth->user('id'));
			$userdata = $this->User->find('first',array('conditions'=>$usercond));
			$userflag = false;

			/* ユーザーが該当支出の支出把握者かどうかをチェック */
			foreach(explode(PHP_EOL,$userdata['User']['post']) as $post):
				if($post == $usercheck['Expense']['team']) $userflag = true;
			endforeach;

			if(!($usercheck['Expense']['user_id'] == $this->Auth->user('id') || $userflag || $userdata['User']['role'] == ('admin' || 'account'))){ //支出者or支出把握者or管理者or会計局員でないなら
				$this->Session->setFlash('編集権限がありません');
				$this->redirect(array('controller'=>'users','action'=>'index'));
			}else if($usercheck['Expense']['admission'] && $userdata['User']['role'] != ('admin' || 'account')){ //支出把握済みで管理者でも会計局員でもないなら
				$this->Session->setFlash('編集権限がありません');
				$this->redirect(array('controller'=>'users','action'=>'index'));
			}else if($usercheck['Expense']['receipt'] && !($userdata['User']['role'] == ('admin' || 'account') || $userflag) ){ //受領済みで管理者でも会計局員でもないなら
				$this->Session->setFlash('編集権限がありません');
				$this->redirect(array('controller'=>'users','action'=>'index'));
			}
			if($this->request->data['Expense']['item_id'] == -1) $this->request->data['Expense']['item_id'] = null;
			$this->request->data['Expense']['product'] = $this->emReplace($this->request->data['Expense']['product']);
			if($this->Expense->save($this->request->data)){
				$this->Session->setFlash('個別編集に成功しました');
				$this->redirect(array('action'=>'view',$allNo));
			}
		}

	}
	
	public function edituser(){
		if(empty($this->request->data['Expense']['no'])){
			$this->Session->setFlash('支出証明書Noを指定してください');
		}
		if(!empty($this->request->data['Expense']['username'])){
			$conditions = array('name LIKE'=>'%'.$this->request->data['Expense']['username'].'%');
			$this->set('userlist',$this->User->find('list',array('conditions'=>$conditions,'fields'=>array('id','name'))));
			$this->set('no',$this->request->data['Expense']['no']);
			debug($this->request->data);
			unset($this->request->data['Expense']['username']);
		}else{
			$no = $this->request->data['Expense']['no'];
			$month = mb_substr($no,0,2,'utf-8');
			$m_no = mb_substr($no,2,3,'utf-8');
			if($month < 4){
				$year = thisYEAR+1;
			}else{
				$year = thisYEAR;
			}
			$start = date('Y-m-d',strtotime($year.'-'.$month.'-01'));
			$end = date('Y-m-d',strtotime($year.'-'.$month.'-01'.'+1 month')-60*60*24);
			$conditions = array('no'=>$m_no,'date >=' => $start,'date <=' => $end);
			$datas = $this->Expense->find('all',array('conditions'=>$conditions));
			foreach($datas as $data):
				$this->Expense->id = $data['Expense']['id'];
				$newdata = $this->Expense->read();
				$newdata['Expense']['user_id'] = $this->request->data['Expense']['newuser'];
				$this->Expense->save($newdata);
			endforeach;
			$this->Session->setFlash('変更を保存しました');
			$this->redirect(array('action'=>'view',$no));
		}
	}
	
	public function admit($no = null) {
		$this->request->onlyAllow('post');
		if(empty($no)){
			$this->Session->setFlash('該当No.の支出は存在しません');
			$this->redirect(array('controller'=>'users','action'=>'index'));
		}else{
			$month = mb_substr($no,0,2,'utf-8');
			$m_no = mb_substr($no,2,3,'utf-8');
			if($month < 4){
				$year = thisYEAR+1;
			}else{
				$year = thisYEAR;
			}
			$start = date('Y-m-d',strtotime($year.'-'.$month.'-01'));
			$end = date('Y-m-d',strtotime($year.'-'.$month.'-01'.'+1 month')-60*60*24);

			$conditions = array('no'=>$m_no,'date >=' => $start,'date <=' => $end);
			
			$datas = $this->Expense->find('all',array('conditions'=>$conditions));
			
			foreach ($datas as $data):

				$this->Expense->id = $data['Expense']['id'];
				
				$this->Expense->read();
				
				$conditions = array('id'=>$this->Auth->user('id'));
				
				$user = $this->User->find('first',array('conditions'=>$conditions));
				
				$adCheck = false;
				
				$teams = explode(PHP_EOL,$user['User']['post']);
				
				foreach ($teams as $team):
					if($team == $data['Expense']['team']) $adCheck = true;
				endforeach;
				
				if($adCheck){
					$data = array();
					$data['Expense']['admission'] = true;
					if($this->Expense->save($data)){
					}else{
						$this->Session->setFlash('承認作業に失敗しました');
						$this->redirect(array('action'=>'view',$no));
					}
				}else{
//					$this->Session->setFlash('承認権限がありません');
//					$this->redirect(array('action'=>'view',$no));
				}
			endforeach;
			$this->Session->setFlash('支出を承認しました');
			$this->redirect(array('action'=>'view',$no));
		}
	}

	public function delete($no = null) {
		$this->request->onlyAllow('post');
		if(empty($no)){
			$this->Session->setFlash('該当No.の支出は存在しません');
			$this->redirect(array('controller'=>'users','action'=>'index'));
		}else{
			$month = mb_substr($no,0,2,'utf-8');
			$m_no = mb_substr($no,2,3,'utf-8');
			if($month < 4){
				$year = thisYEAR+1;
			}else{
				$year = thisYEAR;
			}
			$start = date('Y-m-d',strtotime($year.'-'.$month.'-01'));
			$end = date('Y-m-d',strtotime($year.'-'.$month.'-01'.'+1 month')-60*60*24);

			$conditions = array('no'=>$m_no,'date >=' => $start,'date <=' => $end);
			
			$datas = $this->Expense->find('all',array('conditions'=>$conditions));
			
			foreach ($datas as $data):
				$this->Expense->id = $data['Expense']['id'];
				$this->Expense->read();
				$conditions = array('id'=>$this->Auth->user('id'));
				$user = $this->User->find('first',array('conditions'=>$conditions));
				if($user['User']['role'] == 'admin' || (($this->Auth->user('id') == $data['Expense']['user_id']) && !$data['Expense']['admission'] && !$data['Expense']['receipt'] )){
					if($this->Expense->delete($data['Expense']['id'])){
					}else{
						$this->Session->setFlash('削除作業に失敗しました');
						$this->redirect(array('action'=>'view',$no));
					}
				}else{
					$this->Session->setFlash('削除権限がありません');
					$this->redirect(array('action'=>'view',$no));
				}
			endforeach;
			$this->Session->setFlash('支出を削除しました');
			$this->redirect(array('controller'=>'users','action'=>'index'));
		}
	}
	public function notapproval($no = null,$apFlag = false) {
		$this->request->onlyAllow('post');
		if(empty($no)){
			$this->Session->setFlash('該当No.の支出は存在しません');
			$this->redirect(array('controller'=>'users','action'=>'index'));
		}else{
			$month = mb_substr($no,0,2,'utf-8');
			$m_no = mb_substr($no,2,3,'utf-8');
			if($month < 4){
				$year = thisYEAR+1;
			}else{
				$year = thisYEAR;
			}
			$start = date('Y-m-d',strtotime($year.'-'.$month.'-01'));
			$end = date('Y-m-d',strtotime($year.'-'.$month.'-01'.'+1 month')-60*60*24);

			$conditions = array('no'=>$m_no,'date >=' => $start,'date <=' => $end);
			
			$datas = $this->Expense->find('all',array('conditions'=>$conditions));
			
			foreach ($datas as $data):
				$this->Expense->id = $data['Expense']['id'];
//				$this->Expense->read();
				$data['Expense']['approval'] = $apFlag;
				$conditions = array('id'=>$this->Auth->user('id'));
				$user = $this->User->find('first',array('conditions'=>$conditions));
				if($user['User']['role'] == 'admin'){
					if($this->Expense->save($data)){
					}else{
						$this->Session->setFlash('非承認作業に失敗しました');
						$this->redirect(array('action'=>'view',$no));
					}
				}else{
					$this->Session->setFlash('非承認権限がありません');
					$this->redirect(array('action'=>'view',$no));
				}
			endforeach;
			$this->Session->setFlash('支出の非承認チェックを変更しました');
			$this->redirect(array('action'=>'view',$no));
		}
	}
	
	public function notapprovalid($id = null,$no = null,$apFlag = false) {
		$this->request->onlyAllow('post');
		if(empty($id)){
			$this->Session->setFlash('該当No.の支出は存在しません');
			$this->redirect(array('controller'=>'users','action'=>'index'));
		}else{
			$this->Expense->id = $id;
			$data['Expense']['approval'] = $apFlag;
			$conditions = array('id'=>$this->Auth->user('id'));
			$user = $this->User->find('first',array('conditions'=>$conditions));
			if($user['User']['role'] == 'admin'){
				if($this->Expense->save($data)){
				}else{
					$this->Session->setFlash('非承認作業に失敗しました');
					$this->redirect(array('action'=>'view',$no));
				}
			}else{
				$this->Session->setFlash('非承認権限がありません');
				$this->redirect(array('action'=>'view',$no));
			}
			$this->Session->setFlash('支出の非承認チェックを変更しました');
			$this->redirect(array('action'=>'view',$no));
		}
	}
	
	public function receipt($no = null) {
		$this->request->onlyAllow('post');
		if(empty($no)){
			$this->Session->setFlash('該当No.の支出は存在しません');
			$this->redirect(array('controller'=>'users','action'=>'index'));
		}else{
			$month = mb_substr($no,0,2,'utf-8');
			$m_no = mb_substr($no,2,3,'utf-8');
			if($month < 4){
				$year = thisYEAR+1;
			}else{
				$year = thisYEAR;
			}
			$start = date('Y-m-d',strtotime($year.'-'.$month.'-01'));
			$end = date('Y-m-d',strtotime($year.'-'.$month.'-01'.'+1 month')-60*60*24);

			$conditions = array('no'=>$m_no,'date >=' => $start,'date <=' => $end);
			
			$datas = $this->Expense->find('all',array('conditions'=>$conditions));
			
			foreach ($datas as $data):
				$this->Expense->id = $data['Expense']['id'];
				$this->Expense->read();
				$conditions = array('id'=>$this->Auth->user('id'));
				$user = $this->User->find('first',array('conditions'=>$conditions));
				if($user['User']['role'] == 'admin' || $user['User']['role'] == 'account'){
					$data = array();
					$data['Expense']['receipt'] = true;
					$data['Expense']['account_id'] = $this->Auth->user('id');
					if($this->Expense->save($data)){
					}else{
						$this->Session->setFlash('受理作業に失敗しました');
						$this->redirect(array('action'=>'view',$no));
					}
				}else{
					$this->Session->setFlash('受理権限がありません');
					$this->redirect(array('action'=>'view',$no));
				}
			endforeach;
			$this->Session->setFlash('領収証を受理しました');
			$this->redirect(array('action'=>'view',$no));
		}
	}
	
	
	public function book($month = null,$unreported = false){
		$this->set('title_for_layout','支出帳簿');
		if($this->request->is('post')){
			$this->set('postdata',$this->request->data);
//			$month = $this->params['url']['month']['month'];
			$this->redirect(array('action'=>'book',$this->request->data['Expense']['month']));
		}else{
			$conditions = array('id'=>$this->Auth->user('id'));
			$user = $this->User->find('first',array('conditions'=>$conditions));
			if(!($user['User']['role'] != 'admin' || $user['User']['role'] != 'account')){
				$this->Session->setFlash('閲覧権限がありません');
				$this->redirect(array('controller'=>'users','action'=>'index'));
			}else if($month == null){
				$this->Session->setFlash('月を選択してください');
				$this->redirect(array('controller'=>'users','action'=>'index'));
			}
			if($month < 4){
				$year = thisYEAR+1;
			}else{
				$year = thisYEAR;
			}
			$start = date('Y-m-d',strtotime($year.'-'.$month.'-01'));
			$end = date('Y-m-d',strtotime($year.'-'.$month.'-01'.'+1 month')-60*60*24);
			if($unreported){
				$conditions = array('date >=' => $start,'date <=' => $end,'reported' => false);
			}else{
				$conditions = array('date >=' => $start,'date <=' => $end);
			}
			
			$this->set('datas',$this->Expense->find('all',array('conditions'=>$conditions)));
			$this->set('month',$month);
			$this->set('user',$user);
			$this->set('unreported',$unreported);
		}
	}
	
	public function allReported($month = null){
		$this->User->id = $this->Auth->user('id');
		$usercheck = $this->User->read();
		if($usercheck['User']['role'] != 'admin'){
			$this->Session->setFlash('権限がありません');
			$this->redirect(array('action'=>'book',$month));
		}
		if(empty($month)){
			$this->Session->setFlash('月を指定してください');
			$this->redirect(array('controller'=>'users','action'=>'index'));
		}
		if($month < 4){
			$year = thisYEAR+1;
		}else{
			$year = thisYEAR;
		}
		$start = date('Y-m-d',strtotime($year.'-'.$month.'-01'));
		$end = date('Y-m-d',strtotime($year.'-'.$month.'-01'.'+1 month')-60*60*24);

		$conditions = array('date >=' => $start,'date <=' => $end);
		
		$datas = $this->Expense->find('all',array('conditions'=>$conditions));
		
		foreach ($datas as $data):
			$data['Expense']['reported'] = true;
			$this->Expense->id = $data['Expense']['id'];
			$this->Expense->save($data);
		endforeach;
		$this->redirect(array('controller'=>'expenses','action'=>'book',$month));
	}
	
	public function reported($no = null,$flag = false){
		if(empty($no)){
			$this->Session->setFlash('該当No.の支出は存在しません');
			$this->redirect(array('controller'=>'users','action'=>'index'));
		}else{
			$month = mb_substr($no,0,2,'utf-8');
			$m_no = mb_substr($no,2,3,'utf-8');
			if($month < 4){
				$year = thisYEAR+1;
			}else{
				$year = thisYEAR;
			}
			$start = date('Y-m-d',strtotime($year.'-'.$month.'-01'));
			$end = date('Y-m-d',strtotime($year.'-'.$month.'-01'.'+1 month')-60*60*24);

			$conditions = array('no'=>$m_no,'date >=' => $start,'date <=' => $end);
			
			$datas = $this->Expense->find('all',array('conditions'=>$conditions));
			
			foreach ($datas as $data):
				$this->Expense->id = $data['Expense']['id'];
				$this->Expense->read();
				$conditions = array('id'=>$this->Auth->user('id'));
				$user = $this->User->find('first',array('conditions'=>$conditions));
				if($user['User']['role'] == 'admin' || $user['User']['role'] == 'account'){
					$data = array();
					$data['Expense']['reported'] = $flag;
					if($this->Expense->save($data)){
					}else{
						$this->Session->setFlash('処理に失敗しました');
						$this->redirect(array('action'=>'view',$no));
					}
				}else{
					$this->Session->setFlash('処理権限がありません');
					$this->redirect(array('action'=>'view',$no));
				}
			endforeach;
			$this->Session->setFlash('処理しました');
			$this->redirect(array('action'=>'view',$no));
		}
	}

	public function refunded($no = null,$id = null,$flag = false){
		if(empty($no) || empty($id)){
			$this->Session->setFlash('該当No.の支出は存在しません');
			$this->redirect(array('controller'=>'users','action'=>'index'));
		}else{
			$this->Expense->id = $id;
			$data = $this->Expense->read();
			if($flag && ($data['Expense']['method'] != '立替' || $data['Expense']['approval'])){
				$this->Session->setFlash('返金対象支出ではありません');
				$this->redirect(array('action'=>'view',$no));
			}
			$conditions = array('id'=>$this->Auth->user('id'));
			$user = $this->User->find('first',array('conditions'=>$conditions));
			if($user['User']['role'] == 'admin' || $user['User']['role'] == 'account'){
				$data['Expense']['refunded'] = $flag;
				if($this->Expense->save($data)){
				}else{
					$this->Session->setFlash('処理に失敗しました');
					$this->redirect(array('action'=>'view',$no));
				}
			}else{
				$this->Session->setFlash('処理権限がありません');
				$this->redirect(array('action'=>'view',$no));
			}
			$this->Session->setFlash('処理しました');
			$this->redirect(array('action'=>'view',$no));
		}
	}
	public function download($month = null){
		if($this->request->is('post')){
			$this->set('postdata',$this->request->data);
			$this->redirect(array('action'=>'download',$this->request->data['Expense']['month']));
		}else{
			$conditions = array('id'=>$this->Auth->user('id'));
			$user = $this->User->find('first',array('conditions'=>$conditions));
			if(!($user['User']['role'] != 'admin' || $user['User']['role'] != 'account')){
				$this->Session->setFlash('閲覧権限がありません');
				$this->redirect(array('controller'=>'users','action'=>'index'));
			}else if($month == null){
				$this->Session->setFlash('月を選択してください');
				$this->redirect(array('controller'=>'users','action'=>'index'));
			}
			if($month < 4){
				$year = thisYEAR+1;
			}else{
				$year = thisYEAR;
			}
			$start = date('Y-m-d',strtotime($year.'-'.$month.'-01'));
			$end = date('Y-m-d',strtotime($year.'-'.$month.'-01'.'+1 month')-60*60*24);
			$conditions = array('date >=' => $start,'date <=' => $end);
			$datas = $this->Expense->find('all',array('conditions'=>$conditions));
			foreach($datas as $key => $value){
				$key_id[$key] = $value['Expense']['no'];
			}
			if(!empty($datas)) array_multisort($key_id,SORT_ASC,$datas);
			$dataindex = array('No.','購入年月日','合計','局・チーム','部門','予算大項目','購入者','予算小項目','内訳','品名','単価','個数','小計','使用目的','支払い方法','備考','交通費対応','非承認項目');
			$this->set('filename',$month.'月度.csv');
			$tmpdata = array();
			array_push($tmpdata,$dataindex);
			foreach($datas as $data):
				if(empty($data['Expense']['total'])){
					$total = '0';
				}else{
					$total = number_format($data['Expense']['total']);
				}
				$str = "";
				if(!$data['Expense']['admission']){
					$str = $str.'把握者未署名';
				}
				if(!$data['Expense']['receipt']){
					if(!empty($str)){
						$str = $str.' | ';
					}
					$str = $str.'領収証未受領';
				}
				$writedata = array(
					str_pad($month,2,'0',STR_PAD_LEFT).str_pad($data['Expense']['no'],3,'0',STR_PAD_LEFT),
					$data['Expense']['date'],
					"\\".$total,
					$data['Expense']['team'],
					$data['Dept']['dept'],
					$data['Big']['big'],
					$data['User']['name'],
					$data['Small']['small'],
					$data['Item']['item'],
					$data['Expense']['product'],
					"\\".number_format($data['Expense']['price']),
					$data['Expense']['number'],
					"\\".number_format($data['Expense']['subtotal']),
					$data['Expense']['purpose'],
					$data['Expense']['method'],
					$str,
					"",
					$data['Expense']['approval'],
				);
				array_push($tmpdata,$writedata);
			endforeach;
			$this->set('data',$tmpdata);
			$this->layout = false;
		}
	}
	
	public function viewAllDatas(){
		$this->Expense->recursive = 1;
		if($this->Auth->user('role') !== 'admin'){
			$this->Session->setFlash('閲覧権限がありません');
			$this->redirect(array('controller'=>'users','action'=>'index'));
		}
		
		$this->set('datas',$this->Expense->find('all'));
	}
	
	public function deletebyid($id = null){
		if(!$this->request->is('post') || $this->Auth->user('role') !== 'admin' || $id === null){
			$this->Session->setFlash('閲覧権限がありません');
			$this->redirect(array('controller'=>'users','action'=>'index'));
		}
		
		if($this->Expense->delete($id)){
			$this->Session->setFlash('削除に成功しました');
			$this->redirect(array('controller'=>'expenses','action'=>'viewAllDatas'));
		}
	}
}
