<?php
$options = getopt('a:');
$file = '../log/test_' .  $options['a'] . '_' . date('Hi');
for($i=0; $i<100; $i++){
    $line = date('H:i:s') . ' hello ' . $options['a'] . ' : ' . $i . "\n";
    echo $line;
    file_put_contents($file, $line, FILE_APPEND | LOCK_EX);
    sleep(1);
}