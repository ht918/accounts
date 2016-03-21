<div id="users">
<h2>ようこそ！<?php echo h($user['User']['name']); ?>さん</h2>
<?php
debug($_SERVER);
if(!empty($expenses)){
	echo "<h3>登録済みの支出</h3>";
	$outputs = array();
	$key_id = array();
	$i = 0;
	foreach ($expenses as $expense):
		if(!empty($expense['Expense']['total'])){
			$outputs[$i]['total'] = $expense['Expense']['total'];
			$outputs[$i]['outNo'] = date('m',strtotime($expense['Expense']['date'])).str_pad($expense['Expense']['no'],3,'0',STR_PAD_LEFT);
			$outputs[$i]['no'] = $expense['Expense']['no'];
			$outputs[$i]['date'] = $expense['Expense']['date'];
			$i++;
		}
	endforeach;
	foreach($outputs as $key =>$value){
		$key_id[$key] = $value['outNo'];
	}
	array_multisort($key_id,SORT_DESC,$outputs);
	foreach ($outputs as $output):
		echo $this->Html->link($output['outNo'],array('controller'=>'expenses','action'=>'view',$output['outNo']));
		echo ' '.number_format($output['total']).'円 ('.$output['date'].')'.'<br />';
	endforeach;
}
echo "<h3>支出の登録</h3>";
echo $this->Html->link('支出登録',array('controller'=>'expenses','action'=>'add'));
echo "<br />";
$options = array();
for($i=3;$i<15;$i++){
	$month = $i%12 + 1;
	$options += array($month=>$month.'月');
}
if($user['User']['role'] == 'admin'){
	echo "<h3>管理者ツール</h3>";
	echo ' '.$this->Html->link('ユーザーリスト',array('action'=>'userlist')).'<br />';
	echo ' '.$this->Html->link('ユーザー検索',array('action'=>'search')).'<br />';
	echo ' '.$this->Html->link('部門登録',array('controller'=>'depts','action'=>'add')).'<br />';
	echo ' '.$this->Html->link('大項目登録',array('controller'=>'bigs','action'=>'add')).'<br />';
	echo ' '.$this->Html->link('小項目登録',array('controller'=>'smalls','action'=>'add')).'<br />';
	echo ' '.$this->Html->link('小項目一覧',array('controller'=>'smalls','action'=>'view')).'<br />';
	echo ' '.$this->Html->link('内訳登録',array('controller'=>'items','action'=>'add')).'<br />';
	echo ' '.$this->Html->link('内訳一覧',array('controller'=>'items','action'=>'view')).'<br />';
	echo ' '.$this->Html->link('予算額登録(小項目)',array('controller'=>'smalls','action'=>'budget')).'<br />';
	echo ' '.$this->Html->link('予算額登録(内訳)',array('controller'=>'items','action'=>'budget')).'<br />';
}
if($user['User']['role'] == 'admin' || $user['User']['role'] == 'account'){
	echo "<h3>会計局員ツール</h3>";
	echo $this->Form->create('Expense',array('type'=>'post','action'=>'book'));
	echo $this->Form->input('month',array('label'=>'支出帳簿閲覧','type'=>'select','options'=>$options));
	echo $this->Form->end('選択');
	echo $this->Form->create('User',array('type'=>'post','action'=>'refund'));
	echo $this->Form->input('searchname',array('label'=>'返金額確認'));
	echo $this->Form->end('ユーザー検索');
	echo $this->Form->create('Expense',array('type'=>'post','action'=>'download'));
	echo $this->Form->input('month',array('label'=>'支出帳簿DL','type'=>'select','options'=>$options));
	echo $this->Form->end('選択');
}
if(!empty($admissions)){
	echo "<h3>承認待ちの支出</h3>";
	$outputs = array();
	$key_id = array();
	$i = 0;
	$putted = array();
	foreach($admissions as $adms):
		foreach ($adms as $admission):
			$outNo = date('m',strtotime($admission['Expense']['date'])).str_pad($admission['Expense']['no'],3,'0',STR_PAD_LEFT);
			if(!in_array($outNo,$putted)){
				$outputs[$i]['total'] = $admission['Expense']['subtotal'];
				$outputs[$i]['outNo'] = $outNo;
				$outputs[$i]['no'] = $admission['Expense']['no'];
				$outputs[$i]['date'] = $admission['Expense']['date'];
				$putted[$i] = $outNo;
				$i++;
			}else{
				for($j = 0;$j<count($outputs);$j++){
					if($outputs[$j]['outNo'] == $outNo){
						$outputs[$j]['total'] += $admission['Expense']['subtotal'];
						break;
					}
				}
			}
		endforeach;
	endforeach;
	foreach($outputs as $key => $value){
		$key_id[$key] = $value['outNo'];
	}
	array_multisort($key_id,SORT_DESC,$outputs);
	foreach ($outputs as $output):
		echo $this->Html->link($output['outNo'],array('controller'=>'expenses','action'=>'view',$output['outNo']));
		echo ' '.number_format($output['total']).'円 ('.$output['date'].')';
		echo '<br />';
	endforeach;
}
if(!empty($admitted)){
	echo "<h3>承認済みの支出</h3>";
	$outputs = array();
	$key_id = array();
	$i = 0;
	$putted = array();
	debug($admitted);
	foreach($admitted as $adms):
		foreach ($adms as $admission):
			$outNo = date('m',strtotime($admission['Expense']['date'])).str_pad($admission['Expense']['no'],3,'0',STR_PAD_LEFT);
			if(!in_array($outNo,$putted)){
				$outputs[$i]['total'] = $admission['Expense']['subtotal'];
				$outputs[$i]['outNo'] = $outNo;
				$outputs[$i]['no'] = $admission['Expense']['no'];
				$outputs[$i]['date'] = $admission['Expense']['date'];
				$putted[$i] = $outNo;
				$i++;
			}else{
				for($j = 0;$j<count($outputs);$j++){
					if($outputs[$j]['outNo'] == $outNo){
						$outputs[$j]['total'] += $admission['Expense']['subtotal'];
						break;
					}
				}
			}
		endforeach;
	endforeach;
	foreach($outputs as $key =>$value){
		$key_id[$key] = $value['outNo'];
	}
	array_multisort($key_id,SORT_DESC,$outputs);
	foreach ($outputs as $output):
		echo $this->Html->link($output['outNo'],array('controller'=>'expenses','action'=>'view',$output['outNo']));
		echo ' '.number_format($output['total']).'円 ('.$output['date'].')';
		echo '<br />';
	endforeach;
}
?>
<?php
if(!empty($user['User']['post']) || $user['User']['role'] != 'author'){
	echo '<h3>支出額確認</h3>';
	echo $this->Html->link('支出額確認ページ',array('controller'=>'items','action'=>'check'));
}
?>
<h3>予算(参考)</h3>
<a href="https://sites.google.com/a/wasedasai.net/2015/home/kakukyokujouhou/kaikeikyoku/yu-suan" target="_blank">予算(URASTAページ)</a>
<p> 支出登録の際に参考にしてください。</p>