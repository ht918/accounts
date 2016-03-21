<?php
if(!empty($userlist)){
	echo $this->Form->create('User');
	echo $this->Form->input('newuser',array('label'=>'氏名','type'=>'select','options'=>$userlist));
	echo $this->Form->end('決定');
}else{
	echo '<h2>'.h($user['User']['name']).'さんの返金情報</h2>';
	if(!empty($expenses)){
		$outputs = array();
		$key_id = array();
		$i = 0;
		$putted = array();
		$total = 0;
		foreach ($expenses as $expense):
			$outNo = date('m',strtotime($expense['Expense']['date'])).str_pad($expense['Expense']['no'],3,'0',STR_PAD_LEFT);
			if(!in_array($outNo,$putted)){
				$outputs[$i]['total'] = $expense['Expense']['subtotal'];
				$outputs[$i]['outNo'] = $outNo;
				$outputs[$i]['no'] = $expense['Expense']['no'];
				$outputs[$i]['date'] = $expense['Expense']['date'];
				$putted[$i] = $outNo;
				$i++;
			}else{
				for($j = 0;$j<count($outputs);$j++){
					if($outputs[$j]['outNo'] == $outNo){
						$outputs[$j]['total'] += $expense['Expense']['subtotal'];
						break;
					}
				}
			}
		endforeach;
		foreach($outputs as $key =>$value){
			$key_id[$key] = $value['outNo'];
			$total += $value['total'];
		}
		array_multisort($key_id,SORT_DESC,$outputs);
		echo '<h3>返金対象の支出(総額'.$total.'円)</h3>';
		foreach ($outputs as $output):
			echo $this->Html->link($output['outNo'],array('controller'=>'expenses','action'=>'view',$output['outNo']));
			echo ' '.number_format($output['total']).'円 ('.$output['date'].')'.'<br />';
		endforeach;
		if($authuser != 'author'){
			echo $this->Form->postLink('返金済み処理',array('action'=>'set_refunded',$user['User']['id']));
		}
	}else{
		echo '<h3>返金対象の支出がないか返金済みです</h3>';
	}
}
