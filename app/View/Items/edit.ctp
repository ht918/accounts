<h2>内訳編集</h2>
<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.7.0/jquery.min.js"></script>
<script type = "text/javascript" language = "Javascript">
var bigdatas = new Array();
var smalldatas = new Array();
<?php
foreach($bigs as $big):
	echo "bigdatas[".$big['Dept']['id']."] += \"<option value='".$big['Big']['id']."'>".$big['Big']['big']."</options>\"\n";
endforeach;
foreach($smalls as $small):
	echo "smalldatas[".$small['Big']['id']."] += \"<option value='".$small['Small']['id']."'>".$small['Small']['small']."</options>\"\n";
endforeach;
?>
$(function($){
	$("#ItemDeptId").change(function(){
		$("#ItemBigId").empty();
		$("#ItemBigId").append(bigdatas[$("#ItemDeptId").val()]);
		$("#ItemSmallId").empty();
		$("#ItemSmallId").append(smalldatas[$("#ItemBigId").val()]);
	});
	$("#ItemBigId").change(function(){
		$("#ItemSmallId").empty();
		$("#ItemSmallId").append(smalldatas[$("#ItemBigId").val()]);
	});
});
$(document).ready(function(){
	$("#ItemBigId").append(bigdatas[$("#ItemDeptId").val()]);
	$("#ItemSmallId").append(smalldatas[$("#ItemBigId").val()]);
});
</script>
<?php
$options = array();
echo $this->Form->create('Item',array('action'=>'edit'));
echo $this->Form->hidden('id');
echo $this->Form->input('dept_id',array('type'=>'select','options'=>$depts,'label'=>'部門'));
echo $this->Form->input('big_id',array('type'=>'select','options'=>$options,'label'=>'大項目'));
echo $this->Form->input('small_id',array('type'=>'select','options'=>$options,'label'=>'小項目'));
echo $this->Form->input('item',array('label'=>'内訳'));
echo $this->Form->end('登録');