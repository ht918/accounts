<?php
	foreach($data as $line):
		$this->Csv->addRow($line);
	endforeach;
	
	$this->Csv->setFilename($filename);
	
	echo $this->Csv->render();