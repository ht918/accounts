<h2><?php echo $month; ?>月度収支報告</h2>
<table>
<tr><th>No.</th><th>購入年月日</th><th>合計</th><th>局・チーム</th><th>部門</th><th>予算大項目</th><th>購入者</th><th>予算小項目</th><th>内訳</th><th>品名</th><th>単価</th><th>個数</th><th>小計</th><th>使用目的</th><th>支払い方法</th><th>備考</th></tr>
<?php
debug($datas);
foreach($datas as $key => $value){
	$key_id[$key] = $value['Expense']['date'];
}
array_multisort($key_id,SORT_ASC,$datas);
foreach($datas as $key => $value){
	$key_id[$key] = $value['Small']['date'];
}
array_multisort($key_id,SORT_ASC,$datas);
foreach($datas as $data):
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
	echo "<td>".$data['Expense']['team']."</td>";
	echo "<td>".$data['Dept']['dept']."</td>";
	echo "<td>".$data['Big']['big']."</td>";
	echo "<td>".$data['User']['name']."</td>";
	echo "<td>".$data['Small']['small']."</td>";
	echo "<td>".$data['Expense']['item']."</td>";
	echo "<td>".$data['Expense']['product']."</td>";
	echo "<td>\\".number_format($data['Expense']['price'])."</td>";
	echo "<td>".$data['Expense']['number']."</td>";
	echo "<td>\\".number_format($data['Expense']['subtotal'])."</td>";
	echo "<td>".$data['Expense']['purpose']."</td>";
	echo "<td>".$data['Expense']['method']."</td>";
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
	if($data['Expense']['approval']){
		if(!empty($str)){
			$str = $str.' | ';
		}
		$str = $str.'非承認項目';
	}
	echo "<td>".$str."</td>";
	echo "</tr>";
endforeach;
?>
</table>