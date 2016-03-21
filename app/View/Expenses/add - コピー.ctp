<h2>支出新規登録</h2>
<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.7.0/jquery.min.js"></script>
<script type = "text/javascript" language="Javascript">
<!--
var n = 1;
var bigdatas = new Array();
<?php
foreach($bigs as $big):
	echo "bigdatas[".$big['Dept']['id']."] += \"<option value='".$big['Big']['id']."'>".$big['Big']['big']."</options>\"\n";
endforeach;
?>
var smalldatas = new Array();
<?php
foreach($smalls as $small):
	echo "smalldatas[".$small['Big']['id']."] += \"<option value='".$small['Small']['id']."'>".$small['Small']['small']."</options>\"\n";
endforeach;
?>

$(function(){
	$(document).ready(function(){
		var L = "last"+n;
		$("#dept").after('<table><tr id="'+L+'"></tr>')
		L = "#"+L;
		<?php
		$tmp = "'".str_replace(array("\r\n","\n","\r"), '', $this->Form->input("Expense.big_id",array("label"=>"大項目","type"=>"select","options"=>array())))."'";
		echo 'var str = '.$tmp.";\n";
		?>
		$(L).append('<td>'+str+'</td>')
		$("#ExpenseBigId").attr("name",$("#ExpenseBigId").attr("name").replace(/\]$/,n + "]"));
		$("#ExpenseBigId").attr("id","ExpenseBigId"+ n);
		<?php
		$tmp = "'".str_replace(array("\r\n","\n","\r"), '', $this->Form->input("Expense.small_id",array("label"=>"小項目","type"=>"select","options"=>array())))."'";
		echo 'var str = '.$tmp.";\n";
		?>
		$(L).append('<td>'+str+'</td>')
		$("#ExpenseSmallId").attr("name",$("#ExpenseSmallId").attr("name").replace(/\]$/,n + "]"));
		$("#ExpenseSmallId").attr("id","ExpenseSmallId"+ n);
		$(L).append('<td>'+'<?php echo $this->Form->input("Expense.item",array("label"=>"内訳")); ?>'+'</td>')
		$("#ExpenseItem").attr("name",$("#ExpenseItem").attr("name").replace(/\]$/,n + "]"));
		$("#ExpenseItem").attr("id","ExpenseItem"+ n);
		$(L).append('<td>'+'<?php echo $this->Form->input("Expense.product",array("label"=>"品名")); ?>'+'</td>')
		$("#ExpenseProduct").attr("name",$("#ExpenseProduct").attr("name").replace(/\]$/,n + "]"));
		$("#ExpenseProduct").attr("id","ExpenseProduct"+ n);
		$(L).append('<td>'+'<?php echo $this->Form->input("Expense.price",array("label"=>"単価","required"=>true)); ?>'+'</td>')
		$("#ExpensePrice").attr("name",$("#ExpensePrice").attr("name").replace(/\]$/,n + "]"));
		$("#ExpensePrice").attr("id","ExpensePrice"+ n);
		$(L).append('<td>'+'<?php echo $this->Form->input("Expense.number",array("label"=>"個数","required"=>true)); ?>'+'</td>')
		$("#ExpenseNumber").attr("name",$("#ExpenseNumber").attr("name").replace(/\]$/,n + "]"));
		$("#ExpenseNumber").attr("id","ExpenseNumber"+ n);
		$(L).append('<?php echo "<td>".$this->Form->input("Expense.subtotal",array("label"=>"小計","readonly"=>"readonly"))."</td>"; ?>')
		$("#ExpenseSubtotal").attr("name",$("#ExpenseSubtotal").attr("name").replace(/\]$/,n + "]"));
		$("#ExpenseSubtotal").attr("id","ExpenseSubtotal"+ n);
		var P = "#ExpensePrice" + n;
		var N = "#ExpenseNumber" + n;
		var S = "#ExpenseSubtotal" + n;
		var D = "#ExpenseDeptId";
		var B = "#ExpenseBigId" + n;
		var M = "#ExpenseSmallId" + n;
		$(P).blur(function(){
			$(S).val($(N).val() * $(P).val());
		});
		$(N).blur(function(){
			$(S).val($(N).val() * $(P).val());
		});
		$(B).append(bigdatas[$(D).val()]);
		$(M).append(smalldatas[$(B).val()]);
		$(D).change(function(){
			$(B).empty();
			$(B).append(bigdatas[$(D).val()]);
			$(M).empty();
			$(M).append(smalldatas[$(B).val()]);
		});
		$(B).change(function(){
			$(M).empty();
			$(M).append(smalldatas[$(B).val()]);
		});
		n++;
	});
	$("#add").click(function(){
		var L = "#last" + (n-1);
		var newL = "last"+n;
		$(L).after('<tr id="'+newL+'"></tr>')
		newL = "#"+newL;
		<?php
		$tmp = "'".str_replace(array("\r\n","\n","\r"), '', $this->Form->input("Expense.big_id",array("label"=>"大項目","type"=>"select","options"=>array())))."'";
		echo 'var str = '.$tmp."\n";
		?>
		$(newL).append('<td>'+str+'</td>')
		$("#ExpenseBigId").attr("name",$("#ExpenseBigId").attr("name").replace(/\]$/,n + "]"));
		$("#ExpenseBigId").attr("id","ExpenseBigId"+ n);
		<?php
		$tmp = "'".str_replace(array("\r\n","\n","\r"), '', $this->Form->input("Expense.small_id",array("label"=>"小項目","type"=>"select","options"=>array())))."'";
		echo 'var str = '.$tmp."\n";
		?>
		$(newL).append('<td>'+str+'</td>')
		$("#ExpenseSmallId").attr("name",$("#ExpenseSmallId").attr("name").replace(/\]$/,n + "]"));
		$("#ExpenseSmallId").attr("id","ExpenseSmallId"+ n);
		$(newL).append('<?php echo "<td>".$this->Form->input("Expense.item",array("label"=>"内訳"))."</td>"; ?>')
		$("#ExpenseItem").attr("name",$("#ExpenseItem").attr("name").replace(/\]$/,n + "]"));
		$("#ExpenseItem").attr("id","ExpenseItem"+ n);
		$(newL).append('<?php echo "<td>".$this->Form->input("Expense.product",array("label"=>"品名"))."</td>"; ?>')
		$("#ExpenseProduct").attr("name",$("#ExpenseProduct").attr("name").replace(/\]$/,n + "]"));
		$("#ExpenseProduct").attr("id","ExpenseProduct"+ n);
		$(newL).append('<?php echo "<td>".$this->Form->input("Expense.price",array("label"=>"単価","required"=>true))."</td>"; ?>')
		$("#ExpensePrice").attr("name",$("#ExpensePrice").attr("name").replace(/\]$/,n + "]"));
		$("#ExpensePrice").attr("id","ExpensePrice"+ n);
		$(newL).append('<?php echo "<td>".$this->Form->input("Expense.number",array("label"=>"個数","required"=>true))."</td>"; ?>')
		$("#ExpenseNumber").attr("name",$("#ExpenseNumber").attr("name").replace(/\]$/,n + "]"));
		$("#ExpenseNumber").attr("id","ExpenseNumber"+ n);
		$(newL).append('<?php echo "<td>".$this->Form->input("Expense.subtotal",array("label"=>"小計","readonly"=>"readonly"))."</td>"; ?>')
		$("#ExpenseSubtotal").attr("name",$("#ExpenseSubtotal").attr("name").replace(/\]$/,n + "]"));
		$("#ExpenseSubtotal").attr("id","ExpenseSubtotal"+ n);
		var P = "#ExpensePrice" + n;
		var N = "#ExpenseNumber" + n;
		var S = "#ExpenseSubtotal" + n;
		var D = "#ExpenseDeptId";
		var B = "#ExpenseBigId" + n;
		var M = "#ExpenseSmallId" + n;
		$(P).blur(function(){
			$(S).val($(N).val() * $(P).val());
		});
		$(N).blur(function(){
			$(S).val($(N).val() * $(P).val());
		});
		$(B).append(bigdatas[$(D).val()]);
		$(M).append(smalldatas[$(B).val()]);
		$(D).change(function(){
			$(B).empty();
			$(B).append(bigdatas[$(D).val()]);
			$(M).empty();
			$(M).append(smalldatas[$(B).val()]);
		});
		$(B).change(function(){
			$(M).empty();
			$(M).append(smalldatas[$(B).val()]);
		});
		n++;
	});
	$("#remove").click(function(){
		if(n>2){
			var L = "#last" + (n-1);
			$(L).remove();
			n--;
		}
	});
	$("form").submit(function(){
		$("#ExpenseN").val(n);
	});
});
-->
</script>
<?php
echo $this->Form->create('Expense');
echo $this->Form->input('total',array('label'=>'合計額','required'=>true));
echo $this->Form->input('date',array('type'=>'datetime','label'=>'購入日','dateFormat'=>'YMD','timeFormat'=>'none','monthNames'=>false,'minYear'=>'2015','maxYear'=>'2016'));
echo $this->Form->input('team',array('label'=>'局・チーム','required'=>true));
echo $this->Form->input('dept_id',array('label'=>'部門','type'=>'select','options'=>$depts,'div'=>array('id'=>'dept')));
echo "<input type = 'button' id = 'add' value = '項目追加' class='viewButton'>";
echo "<input type = 'button' id = 'remove' value = '項目削除' class='viewButton'>";
echo $this->Form->input('purpose',array('label'=>'使用目的','required'=>true));
echo $this->Form->input('method',array('label'=>'支払い方法','type'=>'select','options'=>array('立替'=>'立替','前払'=>'前払','振込'=>'振込'),'required'=>false));
echo $this->Form->hidden('n');
echo $this->Form->end('支出登録');

