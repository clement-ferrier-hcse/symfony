<?php
$S = dirname(__DIR__);
ob_start(); include $S.'/gen-diff.php'; ob_end_clean();
$lv = range(0,255,17); $rows=[];
$st=['black'=>0,'blackLow'=>0,'blackSat'=>0,'white'=>0,'whiteLow'=>0,'whiteSat'=>0,'hue'=>0]; $n=0;
foreach($lv as $r)foreach($lv as $g)foreach($lv as $b){ $n++; $a=implA($r,$g,$b); $c=implB($r,$g,$b); if($a===$c)continue; $rows[]=[$r,$g,$b,$a,$c];
  $low = 0.0===round(sat($r,$g,$b)/50);
  if($c===0){ $st['black']++; $st[$low?'blackLow':'blackSat']++; }
  elseif($c===7){ $st['white']++; $st[$low?'whiteLow':'whiteSat']++; }
  else $st['hue']++; }
$json = json_encode(['total'=>$n,'rows'=>$rows,'stats'=>$st]);
file_put_contents($S.'/ansi4-implementations-diff.html', str_replace('/*DATA*/', $json, file_get_contents(__DIR__.'/template.html')));
echo json_encode($st), " rows=", count($rows), "\n";
