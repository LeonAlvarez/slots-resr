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
        return [
            ['bet', null, InputOption::VALUE_OPTIONAL, 'The betting value for the game.', $this->bet_ammount],
            ['symbols', null, InputOption::VALUE_OPTIONAL, 'The betting value for the game.', $this->symbols],
            ['matchs_pay_rate', null, InputOption::VALUE_OPTIONAL, 'The betting value for the game.', $this->matchs_pay_rate],
        ];
    }

    /**
     * Generate the list of random symbols.
     *
     * @return array
     */
    protected function generateList()
    {
        
        $list = collect(range(0, $this->rows * $this->cols))
            ->map(function() {
                return $this->symbols[random_int(0, count($this->symbols) - 1)];
            });

        //$list = collect(['J', 'J', 'J', 'Q', 'K', 'cat', 'J', 'Q', 'monkey', 'bird', 'bird', 'bird', 'J', 'Q', 'A']);

        return $list;
    }

    /**
     * Generate slot board from symbols.
     *
     * @return array
     */
    protected function generateBoard($list)
    {
        $board = [];
        foreach ($list as $key => $symbol) {
            $row = floor($key / $this->cols);
            $col = ($key % $this->cols) * $this->rows;
            $board[$row + $col] = $symbol;
        } 

        return $board;
    }

    /**
     * Get the slot paylines with ammount of matches and win ammount.
     *
     * @return array
     */
    protected function getPaylines($board)
    {
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

    /**
     * Get matches and ammount won for a pay_line in a board.
     *
     * @return array
     */
    protected function getMatchesAndWonAmmount($pay_line,$board) 
    {
        $line = collect($pay_line)->transform(function($item) use ($board) {
            return $board[$item];
        });

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

        return compact('max_matches', 'win_ammount');
    }
}