<?php 

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;

class SlotCommand extends Command {

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'slot';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = "Give the board,paylines and win of slot game";

    protected $bet_ammount = 100;
    protected $cols = 5;
    protected $rows = 3;
    protected $matchs_pay_rate = [3 => 0.2 , 4 => 2, 5 => 10];

    protected $symbols = [9, 10, 'J', 'Q', 'K', 'A', 'C', 'D', 'M', 'B'];
    protected $pay_lines = [
        [0, 3, 6, 9, 12],
        [1, 4, 7, 10, 13],
        [2, 5, 8, 11, 14],
        [0, 4, 8, 10, 12],
        [2, 4, 6, 10, 14],
    ];

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {   
        $list = $this->generateList();
        $board = $this->generateBoard($list);
        $pay_lines = $this->getPaylines($board);
        $result = [
            'board' => array_values($board),
            'paylines' => $pay_lines->map(function($pay_line) {
                return (Object)[implode(' ', $pay_line['pay_line']) => $pay_line['max_matches']];
            })->values(),
            'bet_ammount' => $this->bet_ammount,
            'total_win' => $pay_lines->sum('win_ammount'),
        ];

        dump(json_encode($result));
    }

    /**
     * Get the console command options.
     *
     * @return array
     */
    protected function getOptions()
    {
        return array(
            array('bet', null, InputOption::VALUE_OPTIONAL, 'The betting value for the game.', 100),
        );
    }

    protected function generateList() {
        $list = [];
         /*  for ($i = 0; $i < $this->rows * $this->cols; $i++) {
            $list[] = $this->symbols[random_int(0, count($this->symbols) - 1)];
        } */

        $list = ['J', 'J', 'J', 'Q', 'K', 'cat', 'J', 'Q', 'monkey', 'bird', 'bird', 'bird', 'J', 'Q', 'A'];

        return $list;
    }

    protected function generateBoard(array $list) {
       
        $board = [];
        for ($i = 0; $i < count($list); $i++) {
            $row = floor( $i / $this->cols);
            $col = ($i % $this->cols) * $this->rows;
            $board[$row+$col] = $list[$i];
        }

        /* return [
            0 =>'J',    3 => 'J',   6 => 'J', 9 => 'Q',      12 => 'K', 
            1 => 'cat', 4 => 'J',   7 => 'Q', 10 =>'monkey', 13 =>'bird',
            2 => 'bird',5 =>'bird', 8 => 'J', 11 => 'Q',     14 => 'A'
        ]; */
        
        return $board;
    }

    protected function getPaylines(array $board) {
        return collect($this->pay_lines)->map(function($pay_line) use ($board) {
            $matches_and_won_ammount = $this->getMatchesAndWonAmmount($pay_line, $board);
            return [
                'pay_line' => $pay_line,
                'max_matches' => $matches_and_won_ammount['max_matches'],
                'win_ammount' => $matches_and_won_ammount['win_ammount']
            ];
        })->filter(function($line) {
            return $line['max_matches'];
        });
    }

    protected function getMatchesAndWonAmmount(array $pay_line, array $board) {
        $line = [];

        foreach ($pay_line as $key => $index) {
            $line[] = $board[$index];
        }

        /* $matches = array_fill_keys($this->symbols, 0);
        $current = $line[0];
        $current_cnt = 1;

        for ($i=1; $i <= count($line); $i++) { 
            if ($i < count($line) && $line[$i] === $current && $line[$i] === $line[$i - 1]) {
                $current_cnt++;
            } else {
                if($current_cnt >= 3 && $current_cnt > $matches[$current]) {
                    $matches[$current] = $current_cnt;
                }
                if($i < count($line)){
                    $current = $line[$i];
                    $current_cnt = 1;
                }
            }
        }; */

        $current = $line[0];
        $symbol_index = 0;
        $matches = array_fill(0, count($line), 0);

        foreach ($line as $symbol) {
            if($symbol != $current) {
                $current = $symbol;
                $symbol_index++;
            }
            else
                $matches[$symbol_index]++;
        }

        $max_matches = max($matches) >= 3 ? max($matches) : 0 ;
        $win_ammount = $max_matches >= 3 ? $this->matchs_pay_rate[$max_matches] * $this->bet_ammount : 0;
        return compact("max_matches", "win_ammount");

    }
}