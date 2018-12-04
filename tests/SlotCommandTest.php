<?php

use App\Console\Commands\SlotCommand;
use Mockery as m;

class SlotCommandTest extends TestCase
{
    public function testCommandOutput()
    {
        
    }

   /*  public function test_generate_board_from_list()
    {
        $slot_command = new SlotCommand();
        $mocked_list = collect(['J', 'J', 'J', 'Q', 'K', 'cat', 'J', 'Q', 'monkey', 'bird', 'bird', 'bird', 'J', 'Q', 'A']);
        $board = $this->invokeMethod($slot_command, 'generateBoard', array($mocked_list));

        $expected_board = collect([
            0 => 'J', 3 => 'J', 6 => 'J', 9 => 'Q', 12 => 'K',
            1 => 'cat', 4 => 'J', 7 => 'Q', 10 => 'monkey', 13 => 'bird',
            2 => 'bird', 5 => 'bird', 8 => 'J', 11 => 'Q', 14 => 'A'
        ]);

        $this->assertEmpty($expected_board->diffAssoc($board));
    }

    public function test_payline_with_3_matches()
    {
        $slot_command = new SlotCommand();
        $mocked_payline = collect([0, 3, 6, 9, 12]);
        $mocked_board = collect([
            0 => 'J', 3 => 'J', 6 => 'J', 9 => 'Q', 12 => 'K',
            1 => 'cat', 4 => 'J', 7 => 'Q', 10 => 'monkey', 13 => 'bird',
            2 => 'bird', 5 => 'bird', 8 => 'J', 11 => 'Q', 14 => 'A'
        ]);

        $matches_and_won = $this->invokeMethod($slot_command, 'getMatchesAndWonAmount', array($mocked_payline, $mocked_board));
        $expected_matches = collect(['max_matches' => 3, 'win_amount' => 20]);

        $this->assertEmpty($expected_matches->diffAssoc($matches_and_won));
    }

    public function test_payline_with_4_matches()
    {
        $slot_command = new SlotCommand();
        $mocked_payline = collect([2, 5, 8, 11, 14]);
        $mocked_board = collect([
            0 => 'J', 3 => 'J', 6 => 'J', 9 => 'Q', 12 => 'K',
            1 => 'cat', 4 => 'J', 7 => 'Q', 10 => 'monkey', 13 => 'bird',
            2 => 'bird', 5 => 'bird', 8 => 'bird', 11 => 'bird', 14 => 'K'
        ]);

        $matches_and_won = $this->invokeMethod($slot_command, 'getMatchesAndWonAmount', array($mocked_payline, $mocked_board));
        $expected_matches = collect(['max_matches' => 4, 'win_amount' => 200]);

        $this->assertEmpty($expected_matches->diffAssoc($matches_and_won));
    }

    public function test_payline_with_5_matches()
    {
        $slot_command = new SlotCommand();
        $mocked_payline = collect([2, 5, 8, 11, 14]);
        $mocked_board = collect([
            0 => 'J', 3 => 'J', 6 => 'J', 9 => 'Q', 12 => 'K',
            1 => 'cat', 4 => 'J', 7 => 'Q', 10 => 'monkey', 13 => 'bird',
            2 => 'monkey', 5 => 'monkey', 8 => 'monkey', 11 => 'monkey', 14 => 'monkey'
        ]);

        $matches_and_won = $this->invokeMethod($slot_command, 'getMatchesAndWonAmount', array($mocked_payline, $mocked_board));
        $expected_matches = collect(['max_matches' => 5, 'win_amount' => 1000]);

        $this->assertEmpty($expected_matches->diffAssoc($matches_and_won));
    }

    public function test_payline_with_no_matches()
    {
        $slot_command = new SlotCommand();
        $mocked_payline = collect([1, 4, 7, 10, 13]);
        $mocked_board = collect([
            0 => 'J', 3 => 'J', 6 => 'J', 9 => 'Q', 12 => 'K',
            1 => 'cat', 4 => 'J', 7 => 'Q', 10 => 'monkey', 13 => 'bird',
            2 => 'bird', 5 => 'bird', 8 => 'J', 11 => 'Q', 14 => 'A'
        ]);

        $matches_and_won = $this->invokeMethod($slot_command, 'getMatchesAndWonAmount', array($mocked_payline, $mocked_board));
        $expected_matches = collect(['max_matches' => 1, 'win_amount' => 0]);

        $this->assertEmpty($expected_matches->diffAssoc($matches_and_won));
    }

    public function test_get_board_paylines_with_win() {
        $slot_command = new SlotCommand();
        $mocked_board = collect([
            0 => 'J', 3 => 'J', 6 => 'J', 9 => 'Q', 12 => 'K',
            1 => 'cat', 4 => 'J', 7 => 'Q', 10 => 'monkey', 13 => 'bird',
            2 => 'bird', 5 => 'bird', 8 => 'J', 11 => 'Q', 14 => 'A'
        ]);

        $paylines_with_win = $this->invokeMethod($slot_command, 'getPaylinesWithWin', array($mocked_board));
        
        $expected_paylines_with_win = collect([
            [
                "pay_line" => [0, 3, 6, 9, 12],
                "max_matches"  => 3,
                "win_amount"  => 20.0
            ],
            [
                "pay_line" => [0, 4, 8, 10, 12],
                "max_matches" => 3,
                "win_amount" => 20.0
            ],
        ]);

        $this->assertEmpty($expected_paylines_with_win->filter(function ($payline, $index) use ($paylines_with_win) {
            return collect($payline)
                ->flatten()
                ->diffAssoc(collect($paylines_with_win[$index])->flatten())
                ->count();
        }));

    }
 */
    /**
     * Call protected/private method of a class to allow testing it.
     *
     * @param object &$object    Instantiated object that we will run method on.
     * @param string $methodName Method name to call
     * @param array  $parameters Array of parameters to pass into method.
     *
     * @return mixed Method return.
     */
    public function invokeMethod(&$object, $methodName, array $parameters = array())
    {
        $reflection = new \ReflectionClass(get_class($object));
        $method = $reflection->getMethod($methodName);
        $method->setAccessible(true);

        return $method->invokeArgs($object, $parameters);
    }
}
