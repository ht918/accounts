<h2>支出帳簿</h2>
<table>
<tr><th>No.</th><th>購入年月日</th><th>合計</th><th>局・チーム</th><th>部門</th><th>予算大項目</th><th>購入者</th><th>予算小項目</th><th>内訳</th><th>品名</th><th>単価</th><th>個数</th><th>小計</th><th>使用目的</th><th>支払い方法</th><th>備考</th><th>削除</th></tr>
<?php
foreach($datas as $data):
//	debug($data);
	$month = date("m",strtotime($data['Expense']['date']));
	$monthStr = str_pad($month,2,'0',STR_PAD_LEFT).str_pad($data['Expense']['no'],3,'0',STR_PAD_LEFT);
	echo "<tr>";
	echo "<td>".$this->Html->link($monthStr,array('action'=>'view',$monthStr))."</td>";
	echo "<td>".$data['Expense']['date']."</td>";
	if(empty($data['Expense']['total'])){
		$total = '-';
	}else{
		$total = number_format($data['Expense']['total']);
	}
	echo "<td>\\".$total."</td>";
	echo "<td nowrap>".mb_strimwidth($data['Expense']['team'],0,17,'...','utf-8')."</td>";
	echo "<td nowrap>".$data['Dept']['dept']."</td>";
	echo "<td>".mb_strimwidth($data['Big']['big'],0,15,'...','utf-8')."</td>";
	echo "<td nowrap>".$data['User']['name']."</td>";
	echo "<td>".mb_strimwidth($data['Small']['small'],0,21,'...','utf-8')."</td>";
	echo "<td>".mb_strimwidth($data['Item']['item'],0,21,'...','utf-8')."</td>";
	echo "<td>".mb_strimwidth($data['Expense']['product'],0,31,'...','utf-8')."</td>";
	echo "<td>\\".number_format($data['Expense']['price'])."</td>";
	echo "<td>".$data['Expense']['number']."</td>";
	echo "<td>\\".number_format($data['Expense']['subtotal'])."</td>";
	echo "<td>".mb_strimwidth($data['Expense']['purpose'],0,23,'...','utf-8')."</td>";
	echo "<td>".$data['Expense']['method']."</td>";
	$str = "";
	if(!$data['Expense']['admission']){
		$str = $str.'未署名';
	}
	if(!$data['Expense']['receipt']){
		if(!empty($str)){
			$str = $str.' | ';
		}
		$str = $str.'未受領';
	}
	if($data['Expense']['approval']){
		if(!empty($str)){
			$str = $str.' | ';
		}
		$str = $str.'非承認';
	}
	if(!$data['Expense']['reported']){
		if(!empty($str)){
			$str = $str.' | ';
		}
		$str = $str.'未報告';
	}
	if($data['Expense']['reported'] && !$data['Expense']['approval'] && $data['Expense']['admission'] && $data['Expense']['receipt'] && !$data['Expense']['refunded'] && $data['Expense']['method'] == '立替'){
		if(!empty($str)){
			$str = $str.' | ';
		}
		$str = $str.'未返金';
	}
	echo "<td>".$str."</td>";
	echo "<td>";
	echo $this->Form->postLink('削除',array('controller'=>'expenses','action'=>'deletebyid',$data['Expense']['id']),array('confirm'=>'削除しますか？'));
	echo "</td>";
	echo "</tr>";
endforeach;
?>
</table>
