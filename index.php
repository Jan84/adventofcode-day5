<?php
$list = [];
$rules = [];
$total = 0;
$total2 = 0;

$file = fopen("file.txt", "r");
while (!feof($file)) {
    $line = fgets($file);

    // IMPORTANT (remove space from the end)
    $line = trim(preg_replace('/\s+/', ' ', $line));
    array_push($list, explode(',', $line));
}
fclose($file);

$file = fopen("order.txt", "r");
while (!feof($file)) {
    $line = fgets($file);

    // IMPORTANT (remove space from the end)
    $line = trim(preg_replace('/\s+/', ' ', $line));
    array_push($rules, explode('|', $line));
}
fclose($file);

foreach ($list as $line) {
    $isValid = checkLineOrder($line, $rules);
    if ($isValid['success']) {
        // All good, get the middle number and add to the total
        $total += $line[round((count($line) - 1)/2)];
    } else {
        // Correct order and get middle value
        $corrected = updateOrder($line, $rules);
        $total2 += $corrected[round((count($corrected) - 1)/2)];
    }
}

function checkLineOrder($line, $rules) {
    foreach ($line as $key => $item) {
        $valuesOnTheRight = $key + 1 == count($line) ? NULL : array_slice($line, $key+1);

        $reverse_position = count($line) - ($key);
        $array_reverse = array_reverse($line);
        $sliced_reverse = array_slice($array_reverse, $reverse_position);

        $valuesOnTheLeft = $key > 0 ? $sliced_reverse : NULL;

        if ($valuesOnTheLeft) {
            foreach ($valuesOnTheLeft as $value) {
                // If reverse found, the order is not valid
                if (in_array([$item, $value], $rules)) {
                    return ['success' => false, 'item' => [array_search($value, $line), $key]];
                }
            }
        }

        if ($valuesOnTheRight) {
            foreach ($valuesOnTheRight as $value) {
                // If reverse found, the order is not valid
                if (in_array([$value, $item], $rules)) {
                    return ['success' => false, 'item' => [$key, array_search($value, $line)]];
                }
            }
        }
    }

    return ['success' => true];
}

function updateOrder($line, $rules) {
    $update = false;
    while(!$update) {
        $check = checkLineOrder($line, $rules);
        if ($check['success']) {
            $update = true;
        } else {
            // Update line
            $line = swap($line, $check['item'][0], $check['item'][1]);
        }
    }

    return $line;
}

function swap($line, $x, $y) {
    $temp = $line[$x];
    $line[$x] = $line[$y];
    $line[$y] = $temp;

    return $line;
}

echo $total;
echo '<br>';
echo $total2;
