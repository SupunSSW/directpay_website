<?php

use App\Settings;
use Illuminate\Database\Seeder;

class SettingTableSeeder extends Seeder
{
    use DisableForeignKeys;
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->disableForeignKeys();

        Settings::create([
            'name' => 'isGenerateLink',
            'value' => 'true'
        ]);

        $this->enableForeignKeys();
    }
}
