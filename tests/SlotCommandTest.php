<?php

use App\Console\Commands\SlotCommand;

class SlotCommandTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */
   /*  public function testExample()
    {
        $this->artisan('slot');
    } */

    public function testGenerateBoardFromList()
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

    public function testGetMatchesAndWonAmmount()
    {
        $slot_command = new SlotCommand();
        $mocked_payline = collect([0, 3, 6, 9, 12]);
        $mocked_board = collect([
            0 => 'J', 3 => 'J', 6 => 'J', 9 => 'Q', 12 => 'K',
            1 => 'cat', 4 => 'J', 7 => 'Q', 10 => 'monkey', 13 => 'bird',
            2 => 'bird', 5 => 'bird', 8 => 'J', 11 => 'Q', 14 => 'A'
        ]);

        $matches_and_won = $this->invokeMethod($slot_command, 'getMatchesAndWonAmmount', array($mocked_payline, $mocked_board));
        $expected_matches = collect(['max_matches' => 3, 'win_ammount' => 20]);
        $this->assertEmpty($expected_matches->diffAssoc($matches_and_won));
    }



    /**
     * Call protected/private method of a class.
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
