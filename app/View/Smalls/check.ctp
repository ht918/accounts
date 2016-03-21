<h2>支出額確認ページ</h2>
<ul>
<?php
$sum = array();
foreach($expenses as $expense):
	if(!$expense['Expense']['approval']){
		if(!array_key_exists($expense['Expense']['small_id'],$sum)) $sum[$expense['Expense']['small_id']] = 0;
		$sum[$expense['Expense']['small_id']] += $expense['Expense']['subtotal'];
	}
endforeach;
echo '<table>';
$tmp = array('Dept'=>'','Big'=>'');
echo '<tr><th>部門</th><th>大項目</th><th>小項目</th><th>支出額</th><th>予算額</th></tr>';
foreach($depts as $dept):
//	echo '<tr>'.$dept['Dept']['dept'].'</tr>';
	foreach($dept['Big'] as $big):
//		echo '<tr>'.$big['big'].'</tr>';
		foreach($big['Small'] as $small):
			if($tmp['Dept'] != $dept['Dept']['dept']){
				$deptStr = $dept['Dept']['dept'];
			}else{
				$deptStr = '';
			}
			if($tmp['Big'] != $big['big']){
				$bigStr = $big['big'];
			}else{
				$bigStr = '';
			}
			echo '<tr><td>'.$deptStr.'</td><td>'.$bigStr.'</td>';
			echo '<td>'.$small['small'].'</td>';
			echo '<td>';
			if(!array_key_exists($small['id'],$sum)) $sum[$small['id']] = 0;
			if($sum[$small['id']] > $small['budget']){
				echo '<font color = "red">';
			}
			echo '\\'.number_format($sum[$small['id']]);
			if($sum[$small['id']] > $small['budget']){
				echo '</font>';
			}
			echo '</td><td>\\' .number_format($small['budget']) .'</td></tr>';
			$tmp['Dept'] = $dept['Dept']['dept'];
			$tmp['Big'] = $big['big'];
		endforeach;
	endforeach;
endforeach;
echo '</table>';
