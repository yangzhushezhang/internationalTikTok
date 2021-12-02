<?php


function select_bet($time, $play_kinds_id)
{
    $return_data = [];

    $data_one = ["单", "双", "大", "小"];
    if ($play_kinds_id == 19) {
        $data_two = ["大单", "小单", "小双", "大双", "合"];
        $data_three = [0, 1, 2, 3, 4, 5, 6, 7, 8, 9];
    } else {
        $data_two = ["大单", "小单", "小双", "大双"];
        $data_three = [1, 2, 3, 4, 5, 6, 7, 8];
    }
    $times = rand(1, 7);
    var_dump($times);
//    $times = 4;
    if ($times == 1) {
        $return_data[] = $data_one[rand(0, count($data_one) - 1)];
    }
    if ($times == 2) {
        $return_data[] = $data_two[rand(0, count($data_two) - 1)];
    }
    if ($times == 3) {
        $return_data[] = $data_three[rand(0, count($data_three) - 1)];
    }


    if ($times == 4) {
        $four = [
            ["单", "小单"],
            ["单", "小单", 1],
            ["单", "小单", 3],
            ["单", "大单"],
            ["单", "大单", 7],
            ["单", "大单", 9],
        ];

        $return_data[] = $four[rand(0, count($four))];

    }
    if ($times == 5) {
        $four = [
            ["双", "小双"],
            ["双", "小双", 2],
            ["双", "小双", 4],
            ["双", "大双"],
            ["双", "大双", 8],
            ["双", "大双", 6],
        ];
        $return_data[] = $four[rand(0, count($four))];

    }
    if ($times == 6) {
        $four = [
            ["大", "大双"],
            ["大", "大双", 8],
            ["大", "大双", 6],
            ["大", "大单"],
            ["大", "大单", 7],
            ["大", "大单", 9],
        ];
        $return_data[] = $four[rand(0, count($four))];

    }
    if ($times == 7) {
        $four = [
            ["小", "小双"],
            ["小", "小双", 2],
            ["小", "小双", 4],
            ["小", "小单"],
            ["小", "小单", 3],
            ["小", "小单", 1],
        ];
        $return_data[] = $four[rand(0, count($four))];
    }

    return $return_data;
}

select_bet(1, 19);