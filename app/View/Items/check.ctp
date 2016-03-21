<h2>支出額確認ページ</h2>
<ul>
<?php
$smallsum = array();
$itemsum = array();
foreach($expenses as $expense):
	if(!$expense['Expense']['approval']){
		if(!array_key_exists($expense['Expense']['small_id'],$smallsum)) $smallsum[$expense['Expense']['small_id']] = 0;
		$smallsum[$expense['Expense']['small_id']] += $expense['Expense']['subtotal'];
		if(!empty($expense['Expense']['item_id'])){
			if(!array_key_exists($expense['Expense']['item_id'],$itemsum)) $itemsum[$expense['Expense']['item_id']] = 0;
			$itemsum[$expense['Expense']['item_id']] += $expense['Expense']['subtotal'];
		}
	}
endforeach;
foreach($depts as $dept):
	echo '<a href = "#'.$dept['Dept']['id'].'">'.$dept['Dept']['dept'].'</a>';
endforeach;
echo '<br />';
foreach($depts as $dept):
	echo '<a name = "'.$dept['Dept']['id'].'"></a>';
	echo '<h3>'.$dept['Dept']['dept'].'</h3>';
	echo '<table>';
	$tmp = array('Big'=>'','Small'=>'');
	echo '<tr><th>大項目</th><th>小項目</th><th>内訳</th><th>支出額</th><th>予算額</th></tr>';
	foreach($dept['Big'] as $big):
		foreach($big['Small'] as $small):
			if($tmp['Big'] != $big['big']){
				$bigStr = $big['big'];
			}else{
				$bigStr = '';
			}
			if(!empty($small['Item'])){
				$smallStr = '(以下内訳)';
			}else{
				$smallStr = '';
			}
//			echo '<tr><td>'.$bigStr.'</td><td>'.$small['small'].$smallStr.'</td><td></td>';
			echo '<tr><td>'.$bigStr.'</td><td>'.$this->Html->link($small['small'],array('controller'=>'smalls','action'=>'smalllist',$small['id'])).$smallStr.'</td><td></td>';
			echo '<td>';
			if(!array_key_exists($small['id'],$smallsum)) $smallsum[$small['id']] = 0;
			if($smallsum[$small['id']] > $small['budget']){
				echo '<font color = "red">';
			}
			echo '\\'.number_format($smallsum[$small['id']]);
			if($smallsum[$small['id']] > $small['budget']){
				echo '</font>';
			}
			echo '</td><td>\\' .number_format($small['budget']) .'</td></tr>';
			if(!empty($small['Item'])){
				foreach($small['Item'] as $item):
					echo '<tr><td></td><td></td><td>'.$this->Html->link($item['item'],array('action'=>'itemlist',$item['id'])).'</td>';
					echo '<td>';
					if(!array_key_exists($item['id'],$itemsum)) $itemsum[$item['id']] = 0;
					if($itemsum[$item['id']] > $item['budget']){
						echo '<font color = "red">';
					}
					echo '\\'.number_format($itemsum[$item['id']]);
					if($itemsum[$item['id']] > $item['budget']){
						echo '</font>';
					}
					echo '</td><td>\\' .number_format($item['budget']) .'</td></tr>';
				endforeach;
			}
			$tmp['Big'] = $big['big'];
		endforeach;
	endforeach;
	echo '</table>';
endforeach;
