<h3><?php echo $userdata['User']['name']; ?></h3>
<div class = "title">メールアドレス</div>
<div class = "contents"><?php echo $userdata['User']['email']; ?></div>
<div class = "title">支出把握部署</div>
<?php
$posts = explode(PHP_EOL,$userdata['User']['post']);
if(empty($userdata['User']['post'])) echo '<div class="contents">なし</div>';
foreach($posts as $post):
echo '<div class = "contents">';
echo $post;
echo '</div>';
endforeach;
//debug($userdata);
echo $this->Html->link('編集',array('controller'=>'users','action'=>'edit',$userdata['User']['id']));
 ?>