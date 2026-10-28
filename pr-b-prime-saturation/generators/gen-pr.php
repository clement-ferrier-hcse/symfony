<?php
spl_autoload_register(function ($c) { $p='/app/src/'.str_replace('\\','/',$c).'.php'; if (is_file($p)) require $p; });
use Symfony\Component\Console\Output\AnsiColorMode;
$rg = range(0, 255, 17); $bl = [0, 36, 73, 109, 146, 182, 219, 255]; $grids = [];
foreach ($bl as $b) { $g = []; foreach ($rg as $r) { $row = []; foreach ($rg as $gr) {
  $old = (int) (round($b / 255) << 2 | (round($gr / 255) << 1) | round($r / 255));
  $new = (int) AnsiColorMode::Ansi4->convertFromHexToAnsiColorCode(sprintf('#%02x%02x%02x', $r, $gr, $b));
  $row[] = [$old, $new]; } $g[] = $row; } $grids[] = $g; }
echo json_encode(['rg' => $rg, 'b' => $bl, 'grids' => $grids]);
