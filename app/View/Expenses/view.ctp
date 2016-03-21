<h2>支出詳細</h2>
<?php
$allApFlag = true;
$apFlag = false;
foreach($data as $edata):
	if($edata['Expense']['approval']){
		$apFlag = true;
	}else{
		$allApFlag = false;
	}
endforeach;
if($allApFlag){
	echo '<h3>この項目は非承認項目です</h3>';
}else{
	if($apFlag){
		echo '<h3>この項目には非承認項目が含まれています</h3>';
	}
}
?>
<?php
$puFlag = true;
$teamFlag = true;
$deptFlag = true;
$str = null;
$tmpTeam = null;
$tmpDept = null;
foreach($data as $edata):
	if(empty($str)){
		$str = $edata['Expense']['purpose'];
	}else{
		if($str != $edata['Expense']['purpose']){
			$puFlag = false;
		}
	}
	if(empty($tmpTeam)){
		$tmpTeam = $edata['Expense']['team'];
	}else{
		if($tmpTeam != $edata['Expense']['team']){
			$teamFlag = false;
		}
	}
	if(empty($tmpDept)){
		$tmpDept = $edata['Dept']['dept'];
	}else{
		if($tmpDept != $edata['Dept']['dept']){
			$deptFlag = false;
		}
	}
	
endforeach;
?>
<div class = "title">No.</div>
<div class = "contents"><?php echo $no; ?></div>
<div class = "title">支出日</div>
<div class = "contents"><?php echo $data[0]['Expense']['date']; ?></div>
<div class = "title">合計</div>
<div class = "contents"><?php echo '\\'.number_format($data[0]['Expense']['total']); ?></div>
<div class = "title">支出者</div>
<div class = "contents"><?php echo $data[0]['User']['name']; ?></div>
<?php
if($teamFlag){
 echo '<div class = "title">予算所属</div>';
 echo '<div class = "contents">'.$data[0]['Expense']['team'].'</div>';
}
if($deptFlag){
 echo '<div class = "title">部門</div>';
 echo '<div class = "contents">'.$data[0]['Dept']['dept'].'</div>';
}
?>
<table>
<tr><?php if(!$teamFlag) echo "<th>予算所属</th>"; if(!$deptFlag) echo "<th>部門</th>"; ?><th>予算大項目</th><th>予算小項目</th><th>内訳</th><th>品名</th><th>単価</th><th>個数</th><th>小計</th><?php if(!$puFlag) echo "<th>使用目的</th>";?></tr>
<?php 
foreach($data as $edata):
	echo '<tr><td>';
	if(!$teamFlag){
		echo $edata['Expense']['team'];
		echo '</td><td>';
	}
	if(!$deptFlag){
		echo $edata['Dept']['dept'];
		echo '</td><td>';
	}
	echo $edata['Big']['big'];
	echo '</td><td>';
	echo $edata['Small']['small'];
	echo '</td><td>';
	if($edata['Expense']['item_id'] < 0){
		echo '<font color = "red">【内訳未設定】</font>';
	}else{
		echo $edata['Item']['item'];
	}
	echo '</td><td>';
	if(!$allApFlag && $edata['Expense']['approval']){
		echo '【非承認項目】';
	}
	if(empty($edata['Expense']['product'])){
		echo '<font color = "red">【品名未設定】</font>';
	}else{
		echo $edata['Expense']['product'];
	}
	echo '</td><td>';
	echo '\\'.number_format($edata['Expense']['price']);
	echo '</td><td>';
	echo number_format($edata['Expense']['number']);
	echo '</td><td>';
	echo '\\'.number_format($edata['Expense']['subtotal']);
	if(!$puFlag){
		echo '</td><td>';
		echo $edata['Expense']['purpose'];
	}
	echo '</td></tr>';
endforeach;
?>
</table>
<?php
if($puFlag){
	echo '<div class = "title">使用目的</div>';
	echo '<div class = "contents">'.str_replace(array("\r\n","\n","\r"),'<br />',$data[0]['Expense']['purpose']).'</div>';
}
?>
<div class = "title">支払い方法</div>
<div class = "contents"><?php echo $data[0]['Expense']['method']; ?></div>
<?php
 $admFlag = true;
 $recFlag = true;
 foreach($data as $edata):
 	if(!$edata['Expense']['admission']) $admFlag = false;
 	if(!$edata['Expense']['receipt']) $recFlag = false;
 endforeach;
 if($admFlag){
 	$adm = '承認済み';
 }else{
 	$adm = '未承認';
 }
 if($recFlag){
 	$rec = '受理済み';
 	if($user['User']['role'] == ('account' || 'admin') && !empty($recUser)){
 		$rec = $rec.'(担当:'.$recUser['User']['name'].')';
 	}
 }else{
 	$rec = '未受理';
 }
?>
<div class = "title">把握者署名</div>
<div class = "contents"><?php echo $adm; ?></div>
<div class = "title">領収証提出</div>
<div class = "contents"><?php echo $rec; ?></div>
<?php
if($data[0]['Expense']['method'] == '立替'){
	echo '<div class = "title">返金</div>';
	$refund = true;
	$refundNum = null;
	$refundCount = 0;
	foreach($data as $key => $edata):
		if(!$edata['Expense']['refunded']){
			$refund = false;
			$refundCount++;
			if(empty($refundNum)){
				$refundNum = ($key+1);
			}else{
				$refundNum = $refundNum . ',' . ($key+1);
			}
		}
	endforeach;
	echo '<div class = "contents">';
	if($refund){
		echo '返金済み';
	}else if($refundCount !== count($data)){
		echo $refundNum.'番目の項目分未返金';
	}else{
		echo '未返金';
	}
	echo '</div>';
}
$admFlag = true;
$posts = explode(PHP_EOL,$user['User']['post']);
foreach($data as $edata):
	if(!$edata['Expense']['admission'] && in_array($edata['Expense']['team'],$posts)){
		$admFlag = false;
		break;
	}
endforeach;
if(!$admFlag){
 echo '<h3>支出把握者メニュー</h3>';
 echo $this->Html->link('編集する',array('action'=>'edit',$no));
 echo $this->Form->postLink('把握者として承認する',array('action'=>'admit',$no));
}
if($user['User']['role']=='account' || $user['User']['role']=='admin'){
	 echo '<h3>会計局員メニュー</h3>';
	 echo $this->Html->link('編集する',array('action'=>'edit',$no));
	if(!$data[0]['Expense']['receipt'] ){
		 echo $this->Form->postLink('領収証を受理する',array('action'=>'receipt',$no));
	}
}
if($data[0]['Expense']['user_id'] == $user['User']['id'] && !($data[0]['Expense']['admission']) && !($data[0]['Expense']['receipt'])){
 echo '<h3>支出者メニュー</h3>';
 echo $this->Html->link('編集する',array('action'=>'edit',$no));
 echo $this->Form->postLink('削除する',array('action'=>'delete',$no),array('confirm'=>'削除してもよろしいですか？'));
}
if($user['User']['role'] == 'admin'){
	echo '<h3>管理者メニュー</h3>';
	echo '<h4>支出者変更</h4>';
	echo $this->Form->create('Expense',array('action'=>'edituser'));
	echo $this->Form->input('username',array('label'=>'支出者氏名'));
	echo $this->Form->hidden('no',array('value'=>$no));
	echo $this->Form->end('検索');
	echo '<h4>編集</h4>';
	echo $this->Html->link('編集する',array('action'=>'edit',$no));
	echo '<h4>個別編集</h4>';
	echo '<b>！注意！</b> 個別編集を行ったあとに通常編集を行うと個別編集前のデータに戻る可能性があります<br />';
	echo '<ul>';
	foreach($data as $edata):
		echo '<li>'.$edata['Expense']['product'].' '.$this->Html->link('個別編集する',array('action'=>'detailedit',$edata['Expense']['id'])).'</li>';
	endforeach;
	echo '</ul>';
	echo '<h4>非承認</h4>';
	echo $this->Form->postLink('全て非承認にする',array('action'=>'notapproval',$no,true),array('confirm'=>'非承認にしてもよろしいですか？'));
	echo $this->Form->postLink('全て非承認マークを外す',array('action'=>'notapproval',$no,false),array('confirm'=>'承認にしてもよろしいですか？'));
	echo '<ul>';
	foreach($data as $edata):
		if(!$edata['Expense']['approval']){
			echo '<li>'.$edata['Expense']['product'].' '.$this->Form->postLink('個別非承認にする',array('action'=>'notapprovalid',$edata['Expense']['id'],$no,true),array('confirm'=>'非承認にしてもよろしいですか？'));
		}else{
			echo '<li>'.$edata['Expense']['product'].' '.$this->Form->postLink('個別承認にする',array('action'=>'notapprovalid',$edata['Expense']['id'],$no,false),array('confirm'=>'承認にしてもよろしいですか？'));
		}
	endforeach;
	echo '</ul>';
	echo '<h4>収支報告</h4>';
	if(!$data[0]['Expense']['reported']){
		echo 'この項目は収支報告されていません<br />';
		echo $this->Html->link('収支報告済みにする',array('action'=>'reported',$no,true));
	}else{
		echo 'この項目は収支報告済みです<br />';
		echo $this->Html->link('収支報告済みマークを外す',array('action'=>'reported',$no,false));
	}
	echo '<h4>返金</h4>';
	echo '<ul>';
	foreach($data as $edata):
		if(!$edata['Expense']['refunded']){
			echo '<li>'.$edata['Expense']['product'].' '.$this->Html->link('返金済みにする',array('action'=>'refunded',$no,$edata['Expense']['id'],true));
		}else{
			echo '<li>'.$edata['Expense']['product'].' '.$this->Html->link('未返金にする',array('action'=>'refunded',$no,$edata['Expense']['id'],false));
		}
	endforeach;
	echo '</ul>';
	echo '<h4>削除</h4>';
	echo $this->Form->postLink('削除する',array('action'=>'delete',$no),array('confirm'=>'削除してもよろしいですか？'));
}
if($user['User']['role']=='account' || $user['User']['role']=='admin'){
	echo '<br />';
	if($m_no>1){
		echo $this->Html->link('前へ',array('action'=>'view',str_pad($month,2,'0',STR_PAD_LEFT).str_pad($m_no-1,3,'0',STR_PAD_LEFT)));
	}
	echo $this->Html->link('次へ',array('action'=>'view',str_pad($month,2,'0',STR_PAD_LEFT).str_pad($m_no+1,3,'0',STR_PAD_LEFT)));
}