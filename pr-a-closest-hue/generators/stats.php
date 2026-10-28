<?php
ob_start(); include __DIR__.'/gen-diff.php'; ob_end_clean();
$lv=range(0,255,17); $s=['greyW'=>0,'gate'=>0,'dark'=>0,'white'=>0,'hue'=>0,'nonneighbour'=>0,'gateSat0'=>0];
foreach($lv as $r)foreach($lv as $g)foreach($lv as $b){ $a=implA($r,$g,$b);$bb=implB($r,$g,$b); if($a===$bb)continue;
 if($bb===0){ if($r==$g&&$g==$b)$s['greyW']++; elseif(0.0===round(sat($r,$g,$b)/50))$s['gate']++; else $s['dark']++; }
 elseif($bb===7)$s['white']++;
 else { $s['hue']++; $d=abs(array_search($a,[1,3,2,6,4,5])-array_search($bb,[1,3,2,6,4,5])); if(!in_array($d,[1,5]))$s['nonneighbour']++; }
}
print_r($s);
// examples
