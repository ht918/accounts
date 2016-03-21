<?php
	foreach($data as $line):
		$this->Csv->addRow($line);
	endforeach;
	
	$this->Csv->setFilename('返金一覧表.csv');
	
	echo $this->Csv->render();
