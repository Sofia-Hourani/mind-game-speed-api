<?php
namespace App\Traits;
trait Equations{
    public function generateEquation($difficulty)
    {
        $operations=['+','-','*','/'];
        $equation=' ';
        switch ($difficulty) {
            case 1:
                $numCount = 3;
                $equation = rand(1, 9);

                for ($i = 1; $i < $numCount; $i++) {
                    $op = $operations[array_rand($operations)];
                    $num = rand(1, 9);
                    $equation .= " $op $num ";
                }
                break;
            case 2:
                $numCount = 4;
                $equation = rand(11, 99);

                for ($i = 1; $i < $numCount; $i++) {
                    $op = $operations[array_rand($operations)];
                    $num = rand(11, 99);
                    $equation .= " $op $num ";
                }
                break;
            case 3:
                $numCount = 5;
                $equation = rand(100, 999);

                for ($i = 1; $i < $numCount; $i++) {
                    $op = $operations[array_rand($operations)];
                    $num = rand(100, 999);
                    $equation .= " $op $num ";
                }
                break;
            case 4:
                $numCount = 6;
                $equation = rand(1000, 9999);

                for ($i = 1; $i < $numCount; $i++) {
                    $op = $operations[array_rand($operations)];
                    $num = rand(1000, 9999);
                    $equation .= " $op $num ";
                }
                break;
        }
        return  $equation;
    }
}
