<h2>支出情報編集</h2>
<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.7.0/jquery.min.js"></script>
<script type = "text/javascript" language="Javascript">
<!--
var bigdatas = new Array();
<?php
foreach($bigs as $big):
	echo "bigdatas[".$big['Dept']['id']."] += \"<option value='".$big['Big']['id']."'>".$big['Big']['big']."</options>\"\n";
endforeach;
?>
var smalldatas = new Array();
var itemdatas = new Array();
<?php
foreach($smalls as $small):
	echo "smalldatas[".$small['Big']['id']."] += \"<option value='".$small['Small']['id']."'>".$small['Small']['small']."</options>\"\n";
	if(empty($small['Item'])){
		echo "itemdatas[".$small['Small']['id']."] += \"<option value='-1'>(内訳なし)</options>\"\n";
	}
endforeach;
foreach($items as $item):
	echo "itemdatas[".$item['Small']['id']."] += \"<option value='".$item['Item']['id']."'>".$item['Item']['item']."</options>\"\n";
endforeach;
echo 'var big = "'.$data['Expense']['big_id'].'";';
echo "\n";
echo 'var small = "'.$data['Expense']['small_id'].'";';
echo "\n";
echo 'var item = "' . $data['Expense']['item_id'].'";';
echo "\n";
?>
var D = "#ExpenseDeptId";
var B = "#ExpenseBigId";
var M = "#ExpenseSmallId";
var I = "#ExpenseItemId";
$(function(){
	$(document).ready(function(){
		$(B).empty();
		$(B).append(bigdatas[$(D).val()]);
		$(B).val(big);
		$(M).empty();
		$(M).append(smalldatas[$(B).val()]);
		$(M).val(small);
		$(I).empty();
		$(I).append(itemdatas[$(M).val()]);
		$(I).val(item);
		$(D).change(function(){
			$(B).empty();
			$(B).append(bigdatas[$(D).val()]);
			$(M).empty();
			$(M).append(smalldatas[$(B).val()]);
			$(I).empty();
			$(I).append(itemdatas[$(M).val()]);
		});
		$(B).change(function(){
			$(M).empty();
			$(M).append(smalldatas[$(B).val()]);
			$(I).empty();
			$(I).append(itemdatas[$(M).val()]);
		});
		$(M).change(function(){
			$(I).empty();
			$(I).append(itemdatas[$(M).val()]);
		});
	});
});
-->
</script>


<?php
echo $this->Form->create('Expense');
echo $this->Form->input('team',array('label'=>'局・チーム','required'=>true));
echo $this->Form->input('dept_id',array('label'=>'部門','type'=>'select','options'=>$depts,'div'=>array('id'=>'dept')));
echo $this->Form->input('big_id',array('label'=>'大項目','type'=>'select','options'=>array()));
echo $this->Form->input('small_id',array('label'=>'小項目','type'=>'select','options'=>array()));
echo $this->Form->input('item_id',array('label'=>'内訳'));
echo $this->Form->input('product',array('label'=>'品名'));
echo $this->Form->input('purpose',array('label'=>'使用目的'));
echo $this->Form->hidden('allNo');
echo $this->Form->end('支出登録');

