<?php
	foreach($data as $line):
		$this->Csv->addRow($line);
	endforeach;
	
	$this->Csv->setFilename('決算用データ.csv');
	
	echo $this->Csv->render();