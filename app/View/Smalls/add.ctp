<h2>新規小項目登録</h2>
<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.7.0/jquery.min.js"></script>
<script type = "text/javascript" language = "Javascript">
var bigdatas = new Array();
<?php
foreach($bigs as $big):
	echo "bigdatas[".$big['Dept']['id']."] += \"<option value='".$big['Big']['id']."'>".$big['Big']['big']."</options>\"\n";
endforeach;
?>
$(function($){
	$("#SmallDeptId").change(function(){
		$("#SmallBigId").empty();
		$("#SmallBigId").append(bigdatas[$("#SmallDeptId").val()]);
	});
});
$(document).ready(function(){
	$("#SmallBigId").append(bigdatas[$("#SmallDeptId").val()]);
});
</script>
<?php
$options = array();
echo $this->Form->create('Small');
echo $this->Form->input('dept_id',array('type'=>'select','options'=>$depts,'label'=>'部門'));
echo $this->Form->input('big_id',array('type'=>'select','options'=>$options,'label'=>'大項目'));
echo $this->Form->input('small',array('label'=>'小項目'));
echo $this->Form->end('登録');