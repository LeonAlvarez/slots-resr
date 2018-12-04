<?php 

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Illuminate\Support\Collection;

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
    protected $description = "Slot command generate a board and the payouts for a slot game \n";

    protected $bet_amount = 100;

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
        $this->comment($this->description);
        $list = $this->generateList();
        $board = $this->generateBoard($list);
        $pay_lines = $this->getPaylinesWithWin($board);

        $data = [
            'board' => $board->values(),
            'paylines' => $pay_lines->map(function($pay_line) {
                return (Object)[implode(' ', $pay_line['pay_line']) => $pay_line['max_matches']];
            }),
            'bet_amount' => $this->bet_amount,
            'total_win' => $pay_lines->sum('win_amount'),
        ];

        $this->comment('Slot game result:' . "\n");
        $this->info(json_encode($data));

        return json_encode($data);
    }

    /**
     * Generate the list of random symbols.
     *
     * @return array
     */
    protected function generateList()
    {
        $this->line("Generating items that will compound board .... . \n");

        $list = collect(range(0, $this->rows * $this->cols))
            ->map(function() {
                return $this->symbols[random_int(0, count($this->symbols) - 1)];
            });
    
        $this->info('List generated: ' . $list->implode(', ') . "\n");
        return $list;
    }

    /**
     * Generate slot board from symbols.
     *
     * @return array
     */
    protected function generateBoard($list)
    {
        $this->line("Generating board .... . \n");
        $board = [];
        foreach ($list as $key => $symbol) {
            $row = floor($key / $this->cols);
            $col = ($key % $this->cols) * $this->rows;
            $board[$row + $col] = $symbol;
        }
        $board = collect($board);

        $this->info('Board generated: ' . collect($board) . "\n");
        return $board;
    }

    /**
     * Get the slot paylines with amount of matches and win amount.
     *
     * @return array
     */
    protected function getPaylinesWithWin($board)
    {
        return collect($this->pay_lines)->map(function($pay_line) use ($board) {
            $matches_and_won_amount = $this->getMatchesAndWonAmount($pay_line, $board);
            return [
                'pay_line' => $pay_line,
                'max_matches' => $matches_and_won_amount['max_matches'],
                'win_amount' => $matches_and_won_amount['win_amount']
            ];
        })->filter(function($line) {
            return $line['max_matches'] >= 3;
        })->values();
    }

    /**
     * Get matches and amount won for a pay_line in a board.
     *
     * @return array
     */
    protected function getMatchesAndWonAmount($pay_line, $board) 
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

        $max_matches = max($matches);
        $win_amount = $max_matches >= 3 ? $this->matchs_pay_rate[$max_matches] * $this->bet_amount : 0;

        return compact('max_matches', 'win_amount');
    }
}