<?php
function sat(int $r,int $g,int $b): int { $r/=255.0;$g/=255.0;$b/=255.0;$v=max($r,$g,$b); if (0.0 === $diff = $v-min($r,$g,$b)) return 0; return (int)($diff*100/$v); }
function implB(int $r,int $g,int $b): int { if (0.0 === round(sat($r,$g,$b)/50)) return max($r,$g,$b)<128?0:7; return (int)((round($b/255)<<2)|(round($g/255)<<1)|round($r/255)); } // B' (d836a3509f)
function implA(int $r,int $g,int $b): int { $max=max($r,$g,$b); $delta=$max-min($r,$g,$b); if (0===$delta) return $max<128?0:7;
 $hue = match($max){ $r=>($g-$b)/$delta, $g=>($b-$r)/$delta+2, default=>($r-$g)/$delta+4 };
 return [5,1,3,2,6,4,5][(int)round($hue)+1]; }
$lv=[]; for($i=0;$i<16;$i++) $lv[]=(int)round($i*255/15);
$rows=[];$n=0;
foreach($lv as $r)foreach($lv as $g)foreach($lv as $b){ $n++; $a=implA($r,$g,$b);$bb=implB($r,$g,$b); if($a!==$bb) $rows[]=[$r,$g,$b,$a,$bb]; }
echo json_encode(['total'=>$n,'rows'=>$rows]);
