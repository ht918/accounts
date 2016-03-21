<?php
$date = explode('-',$this->request->data['Expense']['date']);
$month = $date[1];
$this->request->data['Expense']['date'] = strtotime($this->request->data['Expense']['date']);
?>
<h2>支出情報編集</h2>
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
		echo 'var small = "' . $datas[0]['Expense']['small_id'].'";';
		echo "\n";
		echo 'var big = "' . $datas[0]['Expense']['big_id'].'";';
		echo "\n";
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
		$(L).append('<?php echo "<td>".$this->Form->input("Expense.purpose",array("label"=>"使用目的","value"=>mb_substr(json_encode($this->request->data["Expense"]["purpose"]),1,mb_strlen(json_encode($this->request->data["Expense"]["purpose"]))-2,"UTF-8")))."</td>"; ?>')
		$("#ExpensePurpose").attr("name",$("#ExpensePurpose").attr("name").replace(/\]$/,n + "]"));
		$("#ExpensePurpose").attr("id","ExpensePurpose"+ n);
		var P0 = "#ExpensePrice" + n;
		var N0 = "#ExpenseNumber" + n;
		var S0 = "#ExpenseSubtotal" + n;
		var D = "#ExpenseDeptId";
		var B = "#ExpenseBigId" + n;
		var M = "#ExpenseSmallId" + n;
		$(P0).blur(function(){
			$(S0).val($(N0).val() * $(P0).val());
		});
		$(N0).blur(function(){
			$(S0).val($(N0).val() * $(P0).val());
		});
		$(B).empty();
		$(B).append(bigdatas[$(D).val()]);
		$(B).val(big);
		$(M).empty();
		$(M).append(smalldatas[$(B).val()]);
		$(M).val(small);
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
		<?php
		$i = 0;
		foreach ($datas as $data):
			if($i != 0){
				echo 'var big = "' . $data['Expense']['big_id'].'";';
				echo "\n";
				echo 'var small = "' . $data['Expense']['small_id'].'";';
				echo "\n";
				echo 'var item = "' . $data['Expense']['item'].'";';
				echo "\n";
				echo 'var product = "' . $data['Expense']['product'].'";';
				echo "\n";
				echo 'var price = "' . $data['Expense']['price'].'";';
				echo "\n";
				echo 'var number = "' . $data['Expense']['number'].'";';
				echo "\n";
				echo 'var subtotal = "' . $data['Expense']['subtotal'].'";';
				echo "\n";
				echo 'var purpose = ' . json_encode($data['Expense']['purpose']).';';
				echo "\n";
				echo "var bigStr = '<td>" . str_replace(array("\r\n","\n","\r"), '',$this->Form->input("Expense.big_id",array("label"=>"大項目","type"=>"select","options"=>array())))."</td>';";
				echo "\n";
				echo "var smallStr = '<td>" . str_replace(array("\r\n","\n","\r"), '',$this->Form->input("Expense.small_id",array("label"=>"小項目","type"=>"select","options"=>array())))."</td>';";
				echo "\n";
				echo "var itemStr = '<td>" . $this->Form->input("Expense.item",array("label"=>"内訳"))."</td>';";
				echo "\n";
				echo "var productStr = '<td>" . $this->Form->input("Expense.product",array("label"=>"品名"))."</td>';";
				echo "\n";
				echo "var priceStr = '<td>" . $this->Form->input("Expense.price",array("label"=>"単価","required"=>true))."</td>';";
				echo "\n";
				echo "var numberStr = '<td>" . $this->Form->input("Expense.number",array("label"=>"個数","required"=>true))."</td>';";
				echo "\n";
				echo "var subtotalStr = '<td>" . $this->Form->input("Expense.subtotal",array("label"=>"小計","readonly"=>"readonly"))."</td>';";
				echo "\n";
				echo "var purposeStr = '<td>" . $this->Form->input("Expense.purpose",array("label"=>"使用目的","div"=>array("id"=>"last"),"value"=>""))."</td>';";
				echo "\n";
				echo "var P".($i+1)." = '#ExpensePrice".($i+1)."';\n";
				echo "var N".($i+1)." = '#ExpenseNumber".($i+1)."';\n";
				echo "var S".($i+1)." = '#ExpenseSubtotal".($i+1)."';\n";
				echo "var B".($i+1)." = '#ExpenseBigId".($i+1)."';\n";
				echo "var M".($i+1)." = '#ExpenseSmallId".($i+1)."';\n";
				$str = <<< 'EOT'
					var L = "#last" + (n-1);
					var newL = "last"+n;
					$(L).after('<tr id="'+newL+'"></tr>')
					newL = "#"+newL;
					$(newL).append(bigStr)
					$("#ExpenseBigId").attr("name",$("#ExpenseBigId").attr("name").replace(/\]$/,n + "]"));
					$("#ExpenseBigId").attr("id","ExpenseBigId"+ n);
					$(newL).append(smallStr)
					$("#ExpenseSmallId").attr("name",$("#ExpenseSmallId").attr("name").replace(/\]$/,n + "]"));
					$("#ExpenseSmallId").attr("id","ExpenseSmallId"+ n);
					$(newL).append(itemStr)
					$("#ExpenseItem").val(item);
					$("#ExpenseItem").attr("name",$("#ExpenseItem").attr("name").replace(/\]$/,n + "]"));
					$("#ExpenseItem").attr("id","ExpenseItem"+ n);
					$(newL).append(productStr)
					$("#ExpenseProduct").val(product);
					$("#ExpenseProduct").attr("name",$("#ExpenseProduct").attr("name").replace(/\]$/,n + "]"));
					$("#ExpenseProduct").attr("id","ExpenseProduct"+ n);
					$(newL).append(priceStr)
					$("#ExpensePrice").val(price);
					$("#ExpensePrice").attr("name",$("#ExpensePrice").attr("name").replace(/\]$/,n + "]"));
					$("#ExpensePrice").attr("id","ExpensePrice"+ n);
					$(newL).append(numberStr)
					$("#ExpenseNumber").val(number);
					$("#ExpenseNumber").attr("name",$("#ExpenseNumber").attr("name").replace(/\]$/,n + "]"));
					$("#ExpenseNumber").attr("id","ExpenseNumber"+ n);
					$(newL).append(subtotalStr)
					$("#ExpenseSubtotal").val(subtotal);
					$("#ExpenseSubtotal").attr("name",$("#ExpenseSubtotal").attr("name").replace(/\]$/,n + "]"));
					$("#ExpenseSubtotal").attr("id","ExpenseSubtotal"+ n);
					$(newL).append(purposeStr)
					$("#ExpensePurpose").val(purpose);
					$("#ExpensePurpose").attr("name",$("#ExpensePurpose").attr("name").replace(/\]$/,n + "]"));
					$("#ExpensePurpose").attr("id","ExpensePurpose"+ n);
					var D = "#ExpenseDeptId";
					n++;
EOT;
				echo $str;
				echo "$(B".($i+1).").empty();\n";
				echo "$(B".($i+1).").append(bigdatas[$(D).val()]);";
				echo "$(B".($i+1).").val(big);";
				echo "$(M".($i+1).").empty();";
				echo "$(M".($i+1).").append(smalldatas[$(B".($i+1).").val()]);";
				echo "$(M".($i+1).").val(small);";
				echo "$(N".($i+1).").blur(function(){ $(S".($i+1).").val($(N".($i+1).").val() * $(P".($i+1).").val());});";
				echo "$(P".($i+1).").blur(function(){ $(S".($i+1).").val($(N".($i+1).").val() * $(P".($i+1).").val());});";
				echo "$(D).change(function(){ $(B".($i+1).").empty(); $(B".($i+1).").append(bigdatas[$(D).val()]); $(M".($i+1).").empty(); $(M".($i+1).").append(smalldatas[$(B".($i+1).").val()]); });";
				echo "$(B".($i+1).").change(function(){ $(M".($i+1).").empty(); $(M".($i+1).").append(smalldatas[$(B".($i+1).").val()]); });";
				echo "\n";
			}
			$i++;
		endforeach;
		?>
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
		$(newL).append('<?php echo "<td>".$this->Form->input("Expense.item",array("label"=>"内訳","value"=>""))."</td>"; ?>')
		$("#ExpenseItem").attr("name",$("#ExpenseItem").attr("name").replace(/\]$/,n + "]"));
		$("#ExpenseItem").attr("id","ExpenseItem"+ n);
		$(newL).append('<?php echo "<td>".$this->Form->input("Expense.product",array("label"=>"品名","value"=>""))."</td>"; ?>')
		$("#ExpenseProduct").attr("name",$("#ExpenseProduct").attr("name").replace(/\]$/,n + "]"));
		$("#ExpenseProduct").attr("id","ExpenseProduct"+ n);
		$(newL).append('<?php echo "<td>".$this->Form->input("Expense.price",array("label"=>"単価","required"=>true,"value"=>""))."</td>"; ?>')
		$("#ExpensePrice").attr("name",$("#ExpensePrice").attr("name").replace(/\]$/,n + "]"));
		$("#ExpensePrice").attr("id","ExpensePrice"+ n);
		$(newL).append('<?php echo "<td>".$this->Form->input("Expense.number",array("label"=>"個数","required"=>true,"value"=>""))."</td>"; ?>')
		$("#ExpenseNumber").attr("name",$("#ExpenseNumber").attr("name").replace(/\]$/,n + "]"));
		$("#ExpenseNumber").attr("id","ExpenseNumber"+ n);
		$(newL).append('<?php echo "<td>".$this->Form->input("Expense.subtotal",array("label"=>"小計","readonly"=>"readonly","value"=>""))."</td>"; ?>')
		$("#ExpenseSubtotal").attr("name",$("#ExpenseSubtotal").attr("name").replace(/\]$/,n + "]"));
		$("#ExpenseSubtotal").attr("id","ExpenseSubtotal"+ n);
		$(newL).append('<?php echo "<td>".$this->Form->input("Expense.purpose",array("label"=>"使用目的","value"=>""))."</td>"; ?>')
		$("#ExpensePurpose").attr("name",$("#ExpensePurpose").attr("name").replace(/\]$/,n + "]"));
		$("#ExpensePurpose").attr("id","ExpensePurpose"+ n);
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
		$(M).append(smalldatas[1]);
		$(M).val("1");
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
echo $this->Form->hidden('no');
echo $this->Form->input('total',array('label'=>'合計額','required'=>true));
echo $this->Form->input('date',array('type'=>'datetime','label'=>'購入日','dateFormat'=>'YMD','timeFormat'=>'none','monthNames'=>false,'minYear'=>'2015','maxYear'=>'2016'));
echo $this->Form->hidden('month',array('value'=>$month));
echo $this->Form->input('team',array('label'=>'局・チーム','required'=>true));
echo $this->Form->input('dept_id',array('label'=>'部門','type'=>'select','options'=>$depts,'div'=>array('id'=>'dept')));
echo "<input type = 'button' id = 'add' value = '項目追加' class='viewButton'>";
echo "<input type = 'button' id = 'remove' value = '項目削除' class='viewButton'>";
echo $this->Form->input('method',array('label'=>'支払い方法','type'=>'select','options'=>array('立替'=>'立替','前払'=>'前払','振込'=>'振込'),'required'=>false));
echo $this->Form->hidden('n');
echo $this->Form->end('支出登録');

