<?php
spl_autoload_register(function ($c) { $p='/home/user/symfony/src/'.str_replace('\\','/',$c).'.php'; if (is_file($p)) require $p; });
use Symfony\Component\Console\Output\AnsiColorMode;
$levels = range(0, 255, 17);
$grids = [];
foreach ($levels as $b) { $g = [];
  foreach ($levels as $r) { $row = [];
    foreach ($levels as $gr) {
      $old = (int) (round($b / 255) << 2 | (round($gr / 255) << 1) | round($r / 255));
      $new = (int) AnsiColorMode::Ansi4->convertFromHexToAnsiColorCode(sprintf('#%02x%02x%02x', $r, $gr, $b));
      $row[] = [$old, $new];
    } $g[] = $row; }
  $grids[] = $g; }
echo json_encode(['levels' => $levels, 'grids' => $grids]);
