<?php

class Dice
{
    private $topSideVal;

    public function getTopSideVal()
    {
        return $this->topSideVal;
    }

    public function roll()
    {
        $this->topSideVal =  rand(1, 6);
        return $this;
    }

    public function setTopSideVal($topSideVal)
    {
        $this->topSideVal = $topSideVal;
        return $this;
    }
}

class Player
{
    private $diceInCup = [], $name, $position, $point;

    public function __construct($numberOfDice, $position, $name = '')
    {
        $this->point = 0;
        $this->position = $position;
        $this->name = $name;

        for ($i = 0; $i < $numberOfDice; $i++) {
            array_push($this->diceInCup, new Dice());
        }
    }

    public function getDiceInCup()
    {
        return $this->diceInCup;
    }

    public function getName()
    {
        return $this->name;
    }

    public function getPosition()
    {
        return $this->position;
    }

    public function addPoint($point)
    {
        $this->point += $point;
    }

    public function getPoint()
    {
        return $this->point;
    }

    public function play()
    {
        foreach ($this->diceInCup as $dice) {
            $dice->roll();
        }
    }

    public function removeDice($key)
    {
        unset($this->diceInCup[$key]);
    }

    public function insertDice($dice)
    {
        array_push($this->diceInCup, $dice);
    }
}

class Game
{
    private $players = [], $round, $numberOfPlayer, $numberOfDicePerPlayer;

    const REMOVED_WHEN_DICE_TOP = 6, MOVE_WHEN_DICE_TOP = 1;

    public function __construct($numberOfPlayer, $numberOfDicePerPlayer)
    {
        $this->round = 0;
        $this->numberOfPlayer = $numberOfPlayer;
        $this->numberOfDicePerPlayer = $numberOfDicePerPlayer;

        for ($i = 0; $i < $this->numberOfPlayer; $i++) {
            $this->players[$i] = new Player($this->numberOfDicePerPlayer, $i, chr(65 + $i));
        }
    }

    private function displayRound()
    {
        echo "Giliran {$this->round} :\r\n";
        return $this;
    }

    private function displayTopSideDice($title = 'Lempar Dadu')
    {
        echo "{$title} :\r\n";
        foreach ($this->players as $player) {
            echo "Pemain #{$player->getName()} ({$player->getPoint()}): ";
            $diceTopSide = '';

            foreach ($player->getDiceInCup() as $dice) {
                $diceTopSide .= $dice->getTopSideVal() . ", ";
            }

            //hapus koma diakhir list angka
            echo rtrim($diceTopSide, ',') . "\r\n";
        }

        echo "\r\n";
        return $this;
    }

    public function displayWinner($player)
    {
        echo "Pemenang\r\n";
        echo "Pemain {$player->getName()} ({$player->getPoint()})\r\n";
        return $this;
    }

    public function start()
    {
        echo "Pemain = {$this->numberOfPlayer}, Dadu = {$this->numberOfDicePerPlayer}\r\n";

        // ulangi hingga ada pemenang
        while (true) {
            $this->round++;
            $diceCarryForward = [];

            foreach ($this->players as $player) {
                $player->play();
            }

            /* tampilkan hasil sebelum evaluasi */
            $this->displayRound()->displayTopSideDice();

            // cek tiap dadu pemain
            foreach ($this->players as $index => $player) {
                $tempDiceArray = [];

                foreach ($player->getDiceInCup() as $diceIndex => $dice) {
                    /* jika dadu angka 6 */
                    if ($dice->getTopSideVal() == self::REMOVED_WHEN_DICE_TOP) {
                        // tambah nilai pemain
                        $player->addPoint(1);
                        // hapus dadu
                        $player->removeDice($diceIndex);
                    }

                    /* jika dadu angka 1 */
                    if ($dice->getTopSideVal() == self::MOVE_WHEN_DICE_TOP) {
                        if ($player->getPosition() == ($this->numberOfPlayer - 1)) {
                            $this->players[0]->insertDice($dice);
                            $player->removeDice($diceIndex);
                        } else {
                            array_push($tempDiceArray, $dice);
                            $player->removeDice($diceIndex);
                        }
                    }
                }

                $diceCarryForward[$index + 1] = $tempDiceArray;

                if (array_key_exists($index, $diceCarryForward) && count($diceCarryForward[$index]) > 0) {
                    foreach ($diceCarryForward[$index] as $dice) {
                        $player->insertDice($dice);
                    }

                    $diceCarryForward = [];
                }
            }

            /* Tampilkan hasil evaluasi */
            $this->displayTopSideDice("Setelah Evaluasi");

            $playerHasDice = $this->numberOfPlayer;

            foreach ($this->players as $player) {
                if (count($player->getDiceInCup()) <= 0) {
                    $playerHasDice--;
                }
            }

            /* cek jika hanya satu pemain yang memiliki dadu */
            if ($playerHasDice == 1) {
                $this->displayWinner($this->getWinner());
                break;
            }
        }
    }

    private function getWinner()
    {
        $winner = null;
        $highscore = 0;
        foreach ($this->players as $player) {
            if ($player->getPoint() > $highscore) {
                $highscore = $player->getPoint();
                $winner = $player;
            }
        }

        return $winner;
    }
}

$game = new Game(3, 4);
$game->start();